<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class LedgerService
{
    public function record(string $type, int $userId, int $chamaId, float $amount, ?string $description = null, ?string $reference = null): Transaction
    {
        return DB::transaction(function () use ($type, $userId, $chamaId, $amount, $description, $reference): Transaction {
            $transaction = Transaction::create([
                'user_id' => $userId,
                'chama_id' => $chamaId,
                'type' => $type,
                'amount' => round($amount, 2),
                'description' => $description,
                'reference' => $reference,
                'posted_at' => now(),
            ]);

            return $transaction;
        });
    }

    public function balance(int $userId, int $chamaId): float
    {
        $credit = Transaction::query()
            ->where('user_id', $userId)
            ->where('chama_id', $chamaId)
            ->whereIn('type', ['contribution', 'loan_approved', 'fine_paid'])
            ->sum('amount');

        $debit = Transaction::query()
            ->where('user_id', $userId)
            ->where('chama_id', $chamaId)
            ->whereIn('type', ['loan_disbursement', 'fine_assessed', 'repayment'])
            ->sum('amount');

        return round((float) $credit - (float) $debit, 2);
    }

    public function verifyLedgerIntegrity(int $chamaId): bool
    {
        // Total cash inflows from sub-systems: contributions + repayments + paid fines
        $contributions = \App\Models\Contribution::where('chama_id', $chamaId)->sum('amount');
        
        $repayments = \App\Models\Repayment::whereHas('loan', function ($q) use ($chamaId) {
            $q->where('chama_id', $chamaId);
        })->sum('repayment_amount');

        $finesPaid = \App\Models\Fine::where('chama_id', $chamaId)
            ->where('status', 'paid')
            ->sum('amount');

        $inflows = $contributions + $repayments + $finesPaid;

        // Total cash outflows: disbursed loans
        $loansDisbursed = \App\Models\Loan::where('chama_id', $chamaId)
            ->whereIn('status', ['active', 'completed'])
            ->sum('amount');

        $computedCashPool = $inflows - $loansDisbursed;

        // Sum of cash movements recorded in transactions table
        $creditTx = Transaction::where('chama_id', $chamaId)
            ->whereIn('type', ['contribution', 'repayment', 'fine_paid'])
            ->sum('amount');

        $debitTx = Transaction::where('chama_id', $chamaId)
            ->whereIn('type', ['loan_disbursement'])
            ->sum('amount');

        $variance = abs(($creditTx - $debitTx) - $computedCashPool);

        return $variance < 0.01;
    }
}