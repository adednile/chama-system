<?php

namespace App\Http\Controllers;

use App\Models\AmortizationSchedule;
use App\Models\Contribution;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\MappedMpesaTransaction;
use App\Models\Repayment;
use App\Models\User;
use App\Services\LedgerService;
use App\Services\MpesaSMSParser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MpesaParserController extends Controller
{
    public function index()
    {
        $transactions = MappedMpesaTransaction::whereHas('user', function ($query) {
            $query->where('chama_id', Auth::user()->chama_id);
        })->with('user', 'loan')->latest()->get();

        // Load members with their active loan and pending fines in 3 queries (no N+1)
        $memberIds = User::where('role', 'member')
            ->where('chama_id', Auth::user()->chama_id)
            ->pluck('id');

        $activeLoans = Loan::whereIn('user_id', $memberIds)
            ->whereIn('status', ['active', 'overdue'])
            ->get(['id', 'user_id', 'outstanding_balance', 'amount'])
            ->keyBy('user_id');

        $pendingFines = Fine::whereIn('user_id', $memberIds)
            ->where('status', 'pending')
            ->get(['id', 'user_id', 'amount', 'type', 'description'])
            ->groupBy('user_id');

        $members = User::whereIn('id', $memberIds)
            ->orderBy('name')
            ->get()
            ->map(function ($member) use ($activeLoans, $pendingFines) {
                $member->active_loan = $activeLoans->get($member->id);
                $member->pending_fines = $pendingFines->get($member->id) ?? collect();
                return $member;
            });

        return view('Treasurer.sms-parser', compact('transactions', 'members'));
    }

    public function store(Request $request, MpesaSMSParser $parser)
    {
        $data = $request->validate([
            'message'      => ['required', 'string'],
            'payment_type' => ['nullable', 'string', 'in:contribution,loan_repayment,fine_payment'],
            'loan_id'      => ['nullable', 'integer', 'exists:loans,id'],
            'fine_id'      => ['nullable', 'integer', 'exists:fines,id'],
        ]);

        $parsed = $parser->parse($data['message']);

        if (empty($parsed['transaction_code']) || floatval($parsed['amount']) <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse a valid M-Pesa transaction code or amount. Please check the SMS text.',
            ], 422);
        }

        if ($parsed['transaction_code']) {
            $exists = MappedMpesaTransaction::where('transaction_code', $parsed['transaction_code'])->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This M-Pesa transaction code has already been submitted.',
                ], 422);
            }
        }

        MappedMpesaTransaction::create([
            'user_id'          => Auth::id(),
            'amount'           => $parsed['amount'],
            'sender'           => $parsed['sender'],
            'transaction_code' => $parsed['transaction_code'],
            'message'          => $parsed['message'],
            'status'           => 'unmapped',
            'payment_type'     => $data['payment_type'] ?? 'contribution',
            'loan_id'          => $data['loan_id'] ?? null,
            'fine_id'          => $data['fine_id'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'amount'           => number_format($parsed['amount'], 2),
                'sender'           => $parsed['sender'],
                'transaction_code' => $parsed['transaction_code'],
                'date'             => $parsed['date'] ?? now()->toDateString(),
            ],
        ]);
    }

    /**
     * Match an unmapped transaction to a member.
     * The treasurer may override payment_type and loan_id at this step.
     */
    public function match(MappedMpesaTransaction $tx, Request $request, LedgerService $ledgerService): JsonResponse
    {
        if ($tx->user->chama_id !== Auth::user()->chama_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $data = $request->validate([
            'user_id'      => ['required', 'exists:users,id'],
            'payment_type' => ['sometimes', 'in:contribution,loan_repayment,fine_payment'],
            'loan_id'      => ['sometimes', 'nullable', 'exists:loans,id'],
            'fine_id'      => ['sometimes', 'nullable', 'exists:fines,id'],
        ]);

        if ($tx->status !== 'unmapped') {
            return response()->json([
                'success' => false,
                'message' => 'Transaction has already been processed.',
            ], 422);
        }

        $user        = User::findOrFail($data['user_id']);
        $paymentType = $data['payment_type'] ?? $tx->payment_type ?? 'contribution';
        $loanId      = $data['loan_id'] ?? $tx->loan_id ?? null;

        if ($paymentType === 'loan_repayment') {
            return $this->applyAsLoanRepayment($tx, $user, $loanId, $ledgerService);
        }

        if ($paymentType === 'fine_payment') {
            $fineId = $request->input('fine_id') ?? $tx->fine_id ?? null;
            return $this->applyAsFinePayment($tx, $user, $fineId, $ledgerService);
        }

        return $this->applyAsContribution($tx, $user, $ledgerService);
    }

    public function reject(MappedMpesaTransaction $tx): JsonResponse
    {
        if ($tx->user->chama_id !== Auth::user()->chama_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        if ($tx->status !== 'unmapped') {
            return response()->json([
                'success' => false,
                'message' => 'Transaction has already been processed.',
            ], 422);
        }

        $tx->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Transaction rejected.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function applyAsContribution(
        MappedMpesaTransaction $tx,
        User $user,
        LedgerService $ledgerService
    ): JsonResponse {
        Contribution::create([
            'user_id'           => $user->id,
            'chama_id'          => $user->chama_id,
            'amount'            => $tx->amount,
            'contribution_date' => now()->toDateString(),
            'source'            => 'mpesa',
            'reference'         => $tx->transaction_code,
            'notes'             => 'M-Pesa SMS matched from ' . ($tx->sender ?? 'unknown'),
        ]);

        $ledgerService->record(
            'contribution',
            $user->id,
            $user->chama_id,
            (float) $tx->amount,
            'Savings contribution via M-Pesa SMS',
            $tx->transaction_code
        );

        $tx->update([
            'status'       => 'mapped',
            'user_id'      => $user->id,
            'payment_type' => 'contribution',
        ]);

        return response()->json([
            'success' => true,
            'message' => "KES " . number_format($tx->amount, 2) . " recorded as savings contribution for {$user->name}.",
        ]);
    }

    private function applyAsLoanRepayment(
        MappedMpesaTransaction $tx,
        User $user,
        ?int $loanId,
        LedgerService $ledgerService
    ): JsonResponse {
        // Prefer the specified loan; fall back to any active loan for this member
        $loan = $loanId
            ? Loan::where('id', $loanId)->where('user_id', $user->id)->whereIn('status', ['active', 'overdue'])->first()
            : null;

        if (!$loan) {
            $loan = Loan::where('user_id', $user->id)->whereIn('status', ['active', 'overdue'])->first();
        }

        if (!$loan) {
            return response()->json([
                'success' => false,
                'message' => "{$user->name} has no active loan. Please apply this payment as a Savings Contribution instead.",
            ], 422);
        }

        $amountPaid = round((float) $tx->amount, 2);

        // Find the earliest unpaid installment
        $schedule = $loan->amortizationSchedule()
            ->where('payment_status', 'unpaid')
            ->orderBy('due_date')
            ->first();

        $isLate = false;

        if ($schedule) {
            $isLate = Carbon::now()->gt($schedule->due_date);
            $schedule->payment_status = 'paid';
            $schedule->save();
        }

        // Record the repayment
        Repayment::create([
            'loan_id'           => $loan->id,
            'repayment_amount'  => $amountPaid,
            'repayment_date'    => now()->toDateString(),
            'remaining_balance' => max($loan->outstanding_balance - $amountPaid, 0),
            'is_late'           => $isLate,
        ]);

        // Reduce loan outstanding balance
        $loan->outstanding_balance = max($loan->outstanding_balance - $amountPaid, 0);
        if ($loan->outstanding_balance <= 0) {
            $loan->status    = 'completed';
            $loan->repaid_at = now();
        }
        $loan->save();

        $ledgerService->record(
            'repayment',
            $user->id,
            $user->chama_id,
            $amountPaid,
            $isLate ? 'Loan repayment via M-Pesa SMS (LATE)' : 'Loan repayment via M-Pesa SMS',
            $tx->transaction_code
        );

        $tx->update([
            'status'       => 'mapped',
            'user_id'      => $user->id,
            'payment_type' => 'loan_repayment',
            'loan_id'      => $loan->id,
        ]);

        $statusNote = $loan->status === 'completed' ? ' Loan is now fully paid off! 🎉' : '';

        return response()->json([
            'success' => true,
            'message' => "KES " . number_format($amountPaid, 2) . " applied as loan repayment for {$user->name}.{$statusNote}",
        ]);
    }

    private function applyAsFinePayment(
        MappedMpesaTransaction $tx,
        User $user,
        ?int $fineId,
        LedgerService $ledgerService
    ): JsonResponse {
        $fine = $fineId
            ? Fine::where('id', $fineId)->where('user_id', $user->id)->where('status', 'pending')->first()
            : null;

        if (!$fine) {
            $fine = Fine::where('user_id', $user->id)->where('status', 'pending')->first();
        }

        if (!$fine) {
            return response()->json([
                'success' => false,
                'message' => "{$user->name} has no pending fines. Please apply this payment as a Savings Contribution instead.",
            ], 422);
        }

        $amountPaid = round((float) $tx->amount, 2);
        $fineAmount = round((float) $fine->amount, 2);

        if ($amountPaid !== $fineAmount) {
            return response()->json([
                'success' => false,
                'message' => "The payment amount (KES " . number_format($amountPaid, 2) . ") does not match the exact fine amount (KES " . number_format($fineAmount, 2) . "). Partial payments are not allowed.",
            ], 422);
        }

        // Mark the fine as paid
        $fine->status = 'paid';
        $fine->paid_at = now();
        $fine->save();

        // Record the transaction
        $ledgerService->record(
            'fine_paid',
            $user->id,
            $user->chama_id,
            $amountPaid,
            'Fine payment via M-Pesa SMS - ' . ($fine->description ?? 'Penalty'),
            $tx->transaction_code
        );

        $tx->update([
            'status'       => 'mapped',
            'user_id'      => $user->id,
            'payment_type' => 'fine_payment',
            'fine_id'      => $fine->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => "KES " . number_format($amountPaid, 2) . " applied as fine payment for {$user->name}.",
        ]);
    }
}
