<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\AmortizationSchedule;

use App\Models\Loan;
use App\Models\Repayment;
use App\Services\CreditScoringEngine;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $chama = $user->chama;

        $loans = Loan::query()
            ->where('user_id', $user->id)
            ->where('chama_id', $user->chama_id)
            ->latest()
            ->get();

        // Replicate the same computation as DashboardService so the view has the right context.
        $savingsBalance  = \App\Models\Transaction::where('user_id', $user->id)
            ->where('chama_id', $user->chama_id)
            ->where('type', 'contribution')
            ->sum('amount');

        $outstandingLoan = Loan::where('user_id', $user->id)
            ->where('chama_id', $user->chama_id)
            ->where('status', 'active')
            ->sum('outstanding_balance');

        $unpaidFines = \App\Models\Fine::where('user_id', $user->id)
            ->where('chama_id', $user->chama_id)
            ->where('status', 'pending')
            ->sum('amount');

        $loanLimit       = $savingsBalance * 3;
        $canApplyForLoan = !($outstandingLoan > 0 || $unpaidFines > 0);
        $interestRate    = $chama->interest_rate_pct ?? 5.00;

        return view('Member.loan-application', compact('loans', 'loanLimit', 'canApplyForLoan', 'interestRate'));
    }

    public function store(Request $request, CreditScoringEngine $scoringEngine)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
        'term_months' => 'required|integer|min:1|max:36',
        'reason' => 'required|string',
    ]);

    $user = Auth::user();
    $chama = $user->chama;

    if ($user->account_status === 'overdue') {
        return redirect()->back()->with('error', 'Loan request blocked: Your account status is Overdue. Please clear all outstanding penalties.');
    }

    // Check individual limit (3x savings contributions)
    $savingsBalance = \App\Models\Transaction::where('user_id', $user->id)
        ->where('chama_id', $chama->id)
        ->where('type', 'contribution')
        ->sum('amount');
    $individualLimit = $savingsBalance * 3;

    if ($request->amount > $individualLimit) {
        return redirect()->back()->with('error', 'Loan request blocked: The requested amount exceeds your individual borrowing limit of 3x your savings (Ksh ' . number_format($individualLimit, 2) . ').');
    }

    // Check group cash reserves pool limit
    $contributions = \App\Models\Contribution::where('chama_id', $chama->id)->sum('amount');
    $repayments = \App\Models\Repayment::whereHas('loan', function ($q) use ($chama) {
        $q->where('chama_id', $chama->id);
    })->sum('repayment_amount');
    $finesPaid = \App\Models\Fine::where('chama_id', $chama->id)
        ->where('status', 'paid')
        ->sum('amount');
    $loansDisbursed = \App\Models\Loan::where('chama_id', $chama->id)
        ->whereIn('status', ['active', 'completed'])
        ->sum('amount');

    $availableCashPool = ($contributions + $repayments + $finesPaid) - $loansDisbursed;

    if ($request->amount > $availableCashPool) {
        return redirect()->back()->with('error', 'Loan request blocked: The requested amount exceeds the Chama\'s available cash pool (Ksh ' . number_format($availableCashPool, 2) . ').');
    }

    // ✅ Compute real credit score using the engine
    $score = $scoringEngine->calculateScore($user);

    $threshold = $chama->min_credit_score ?? 6.0;

    // Determine status
    $status = 'pending';
    $rejectionReason = null;
    if ($score < $threshold) {
        $status = 'rejected';
        $rejectionReason = "Credit score ($score) below minimum threshold ($threshold).";
    }

    $loan = Loan::create([
        'user_id' => $user->id,
        'chama_id' => $chama->id,
        'amount' => round($request->amount, 2),
        'interest_rate' => $chama->interest_rate_pct ?? 5.00,
        'term_months' => $request->term_months,
        'status' => $status,
        'credit_score' => $score,
        'rejection_reason' => $rejectionReason,
        'outstanding_balance' => $status === 'pending' ? round($request->amount, 2) : 0,
        'reason' => $request->reason,
    ]);

    if ($status === 'pending') {
        return redirect()->back()->with('success', 'Loan application submitted for treasurer review.');
    } else {
        return redirect()->back()->with('error', $rejectionReason);
    }
}

    public function approve(Loan $loan)
{
    $loan->status = 'active';
    $loan->approved_by = Auth::id();
    $loan->approved_at = now();
    $loan->outstanding_balance = $loan->amount;
    $loan->maturity_date = Carbon::now()->addMonths($loan->term_months);
    $loan->save();

    // Generate amortization schedule
    $this->generateAmortizationSchedule($loan);

    // Record ledger entry
    $ledgerService = new LedgerService();
    $ledgerService->record(
        'loan_disbursement', 
        $loan->user_id, 
        $loan->chama_id, 
        $loan->amount, 
        'Loan disbursed - approved by ' . Auth::user()->name,
        $loan->id
    );

    return redirect()->back()->with('success', 'Loan approved and amortization schedule created.');
}

    public function pending()
{
    $pendingLoans = Loan::where('status', 'pending')
        ->where('chama_id', Auth::user()->chama_id)
        ->with('user')
        ->latest()
        ->get();

    return view('Treasurer.pending-loans', compact('pendingLoans'));
}

public function reject(Loan $loan, Request $request)
{
    $request->validate([
        'reason' => 'nullable|string|max:500',
    ]);

    $loan->status = 'rejected';
    $loan->rejection_reason = $request->input('reason', 'Rejected by treasurer.');
    $loan->save();

    return redirect()->back()->with('success', 'Loan application rejected.');
}

private function generateAmortizationSchedule(Loan $loan): void
{
    $monthlyRate = ($loan->interest_rate / 100) / 12;
    $months = $loan->term_months;
    $principal = $loan->amount;
    
    // Calculate EMI (Equated Monthly Installment)
    if ($monthlyRate > 0) {
        $emi = $principal * $monthlyRate * pow(1 + $monthlyRate, $months) / (pow(1 + $monthlyRate, $months) - 1);
    } else {
        $emi = $principal / $months;
    }
    
    $balance = $principal;
    $dueDate = Carbon::now()->addMonth();

    for ($i = 1; $i <= $months; $i++) {
        $interest = $balance * $monthlyRate;
        $principalPortion = $emi - $interest;
        $balance -= $principalPortion;

        AmortizationSchedule::create([
            'loan_id' => $loan->id,
            'installment_no' => $i,
            'due_date' => $dueDate->toDateString(),
            'principal_portion' => round($principalPortion, 2),
            'interest_portion' => round($interest, 2),
            'balance_after' => max(round($balance, 2), 0),
            'payment_status' => 'unpaid',
        ]);

        $dueDate->addMonth();
    }
}

    public function repay(Loan $loan, Request $request, LedgerService $ledgerService)
{
    $request->validate([
        'amount' => 'required|numeric|min:1',
    ]);

    // Find the earliest unpaid installment
    $schedule = $loan->amortizationSchedule()
        ->where('payment_status', 'unpaid')
        ->orderBy('due_date')
        ->first();

    if (!$schedule) {
        return redirect()->back()->with('error', 'No pending installments. This loan may already be fully paid.');
    }

    $amountPaid = round($request->amount, 2);
    $dueAmount = $schedule->principal_portion + $schedule->interest_portion;

    // Allow full payment only (or you can allow partial payments)
    if ($amountPaid < $dueAmount) {
        return redirect()->back()->with('error', "Amount must cover the full installment of Ksh " . number_format($dueAmount, 2));
    }

    // Mark schedule as paid
    $schedule->payment_status = 'paid';
    $schedule->save();

    // Determine if payment is late
    $isLate = Carbon::now()->gt($schedule->due_date);
    
    // Record repayment
    $repayment = Repayment::create([
        'loan_id' => $loan->id,
        'repayment_amount' => $amountPaid,
        'repayment_date' => now()->toDateString(),
        'remaining_balance' => $loan->outstanding_balance - $amountPaid,
        'is_late' => $isLate,
    ]);

    // Update loan outstanding balance
    $loan->outstanding_balance = max($loan->outstanding_balance - $amountPaid, 0);
    
    // If fully paid, mark as completed
    if ($loan->outstanding_balance <= 0) {
        $loan->status = 'completed';
        $loan->repaid_at = now();
    }
    $loan->save();

    // Record ledger entry
    $ledgerService->record(
        'repayment',
        $loan->user_id,
        $loan->chama_id,
        $amountPaid,
        $isLate ? 'Loan repayment (LATE)' : 'Loan repayment (ON TIME)',
        $loan->id
    );

    return redirect()->back()->with('success', 'Repayment recorded successfully.');
}
}
