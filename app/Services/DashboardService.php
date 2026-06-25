<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get all data for the treasurer dashboard.
     */
    public function getTreasurerData(int $chamaId): array
    {
        return [
            'totalSavings'       => Contribution::where('chama_id', $chamaId)->sum('amount'),
            'activeLoans'        => Loan::where('chama_id', $chamaId)->where('status', 'active')->sum('outstanding_balance'),
            'activeLoansCount'   => Loan::where('chama_id', $chamaId)->where('status', 'active')->count(),
            'pendingApplications' => Loan::where('chama_id', $chamaId)->where('status', 'pending')->count(),
            'pendingLoanList'    => Loan::where('chama_id', $chamaId)->where('status', 'pending')->with('user')->latest()->get(),
            'totalFines'         => Fine::where('chama_id', $chamaId)->where('status', 'pending')->sum('amount'),
            'unpaidFinesCount'   => Fine::where('chama_id', $chamaId)->where('status', 'pending')->count(),
            'recentTransactions' => Transaction::where('chama_id', $chamaId)->with('user')->latest()->limit(20)->get(),
        ];
    }

    /**
     * Get all data for the member dashboard.
     */
    public function getMemberData(int $userId, int $chamaId): array
    {
        $savingsBalance = Transaction::where('user_id', $userId)
            ->where('chama_id', $chamaId)
            ->where('type', 'contribution')
            ->sum('amount');

        $outstandingLoan = Loan::where('user_id', $userId)
            ->where('chama_id', $chamaId)
            ->where('status', 'active')
            ->sum('outstanding_balance');

        $unpaidFines = Fine::where('user_id', $userId)
            ->where('chama_id', $chamaId)
            ->where('status', 'pending')
            ->sum('amount');

        $loanLimit = $savingsBalance * 3; // Example rule

        // Eligibility checks
        $canApplyForLoan = !($outstandingLoan > 0 || $unpaidFines > 0);
        $loanIneligibilityReason = null;
        if ($outstandingLoan > 0) {
            $loanIneligibilityReason = 'You have an outstanding loan.';
        } elseif ($unpaidFines > 0) {
            $loanIneligibilityReason = 'You have unpaid fines.';
        }

        $activeLoan = Loan::where('user_id', $userId)
            ->where('chama_id', $chamaId)
            ->where('status', 'active')
            ->first();

        $pendingMpesa = \App\Models\MappedMpesaTransaction::where('user_id', $userId)
            ->where('status', 'unmapped')
            ->latest()
            ->get();

        return [
            'savingsBalance'       => $savingsBalance,
            'loanLimit'            => $loanLimit,
            'outstandingLoan'      => $outstandingLoan,
            'unpaidFines'          => $unpaidFines,
            'canApplyForLoan'      => $canApplyForLoan,
            'loanIneligibilityReason' => $loanIneligibilityReason,
            'recentTransactions'   => Transaction::where('user_id', $userId)->where('chama_id', $chamaId)->latest()->limit(20)->get(),
            'activeLoan'           => $activeLoan,
            'pendingMpesa'         => $pendingMpesa,
        ];
    }
}