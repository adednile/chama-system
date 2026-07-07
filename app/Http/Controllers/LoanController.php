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
    public function index(CreditScoringEngine $scoringEngine)
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
            ->whereIn('status', ['active', 'overdue'])
            ->sum('outstanding_balance');

        $unpaidFines = \App\Models\Fine::where('user_id', $user->id)
            ->where('chama_id', $user->chama_id)
            ->where('status', 'pending')
            ->sum('amount');

        $multiplier      = $scoringEngine->calculateBorrowingMultiplier($user);
        $loanLimit       = $savingsBalance * $multiplier;
        $canApplyForLoan = !($outstandingLoan > 0 || $unpaidFines > 0);
        $interestRate    = $chama->interest_rate_pct ?? 5.00;

        return view('Member.loan-application', compact('loans', 'loanLimit', 'multiplier', 'canApplyForLoan', 'interestRate'));
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

    // Check individual limit (dynamic multiplier x savings contributions)
    $savingsBalance = \App\Models\Transaction::where('user_id', $user->id)
        ->where('chama_id', $chama->id)
        ->where('type', 'contribution')
        ->sum('amount');
    $multiplier = $scoringEngine->calculateBorrowingMultiplier($user);
    $individualLimit = $savingsBalance * $multiplier;

    if ($request->amount > $individualLimit) {
        return redirect()->back()->with('error', 'Loan request blocked: The requested amount exceeds your individual borrowing limit of ' . $multiplier . 'x your savings (Ksh ' . number_format($individualLimit, 2) . ').');
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
        ->whereIn('status', ['active', 'completed', 'overdue'])
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
    $loan->maturity_date = Carbon::now()->addMonths($loan->term_months);

    // Generate amortization schedule and return total payback amount (Principal + Interest)
    $totalPayback = $this->generateAmortizationSchedule($loan);
    $loan->outstanding_balance = $totalPayback;
    $loan->save();

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
    $chamaId = Auth::user()->chama_id;

    $pendingLoans = Loan::where('status', 'pending')
        ->where('chama_id', $chamaId)
        ->with('user')
        ->latest()
        ->get();

    // Check group cash reserves pool limit
    $contributions = \App\Models\Contribution::where('chama_id', $chamaId)->sum('amount');
    $repayments = \App\Models\Repayment::whereHas('loan', function ($q) use ($chamaId) {
        $q->where('chama_id', $chamaId);
    })->sum('repayment_amount');
    $finesPaid = \App\Models\Fine::where('chama_id', $chamaId)
        ->where('status', 'paid')
        ->sum('amount');
    $loansDisbursed = \App\Models\Loan::where('chama_id', $chamaId)
        ->whereIn('status', ['active', 'completed', 'overdue'])
        ->sum('amount');

    $availableCashPool = ($contributions + $repayments + $finesPaid) - $loansDisbursed;

    return view('Treasurer.pending-loans', compact('pendingLoans', 'availableCashPool', 'loansDisbursed'));
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

private function generateAmortizationSchedule(Loan $loan): float
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
    $totalPayback = 0.0;

    for ($i = 1; $i <= $months; $i++) {
        $interest = $balance * $monthlyRate;
        $principalPortion = $emi - $interest;
        $balance -= $principalPortion;

        $pRound = round($principalPortion, 2);
        $iRound = round($interest, 2);

        AmortizationSchedule::create([
            'loan_id' => $loan->id,
            'installment_no' => $i,
            'due_date' => $dueDate->toDateString(),
            'principal_portion' => $pRound,
            'interest_portion' => $iRound,
            'balance_after' => max(round($balance, 2), 0),
            'payment_status' => 'unpaid',
        ]);

        $totalPayback += ($pRound + $iRound);

        $dueDate->addMonth();
    }

    return round($totalPayback, 2);
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
