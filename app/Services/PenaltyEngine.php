<?php

namespace App\Services;

use App\Models\Fine;
use App\Models\Chama;
use App\Models\User;
use Carbon\Carbon;

class PenaltyEngine
{
    public function applyDailyPenalties(): int
    {
        $chamas = Chama::all();
        $totalFines = 0;

        foreach ($chamas as $chama) {
            $totalFines += $this->applyLateContributions($chama);
            $totalFines += $this->applyLateRepayments($chama);
        }

        return $totalFines;
    }

    private function applyLateContributions(Chama $chama): int
    {
        $count = 0;
        $cutoff = $chama->collection_cutoff ? Carbon::parse($chama->collection_cutoff) : Carbon::now()->startOfMonth()->addDays(5);
        $penaltyPerDay = $chama->late_penalty_flat ?? 50; // daily fine

        $users = $chama->users()->where('role', 'member')->get();

        foreach ($users as $user) {
            // Check if user made a contribution this month before cutoff
            $hasPaid = $user->contributions()
                ->where('contribution_date', '>=', Carbon::now()->startOfMonth())
                ->where('contribution_date', '<=', $cutoff)
                ->exists();

            if (!$hasPaid && Carbon::now()->gt($cutoff)) {
                $daysLate = Carbon::now()->diffInDays($cutoff);
                $fineAmount = $daysLate * $penaltyPerDay;

                if ($fineAmount > 0) {
                    // Check if a fine for this month already exists
                    $existing = Fine::where('user_id', $user->id)
                        ->where('chama_id', $chama->id)
                        ->where('type', 'late_contribution')
                        ->where('billing_cycle', Carbon::now()->format('Y-m'))
                        ->first();

                    if (!$existing) {
                        Fine::create([
                            'user_id' => $user->id,
                            'chama_id' => $chama->id,
                            'amount' => $fineAmount,
                            'type' => 'late_contribution',
                            'status' => 'unpaid',
                            'due_date' => now()->toDateString(),
                            'description' => "Late contribution for " . Carbon::now()->format('F Y'),
                            'billing_cycle' => Carbon::now()->format('Y-m'),
                        ]);
                        $user->update(['account_status' => 'overdue']);
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    private function applyLateRepayments(Chama $chama): int
    {
        $count = 0;
        $loans = $chama->loans()->where('status', 'active')->get();

        foreach ($loans as $loan) {
            // Find the earliest unpaid amortization schedule
            $schedule = $loan->amortizationSchedule()
                ->where('payment_status', 'unpaid')
                ->orderBy('due_date')
                ->first();

            if ($schedule && Carbon::now()->gt($schedule->due_date)) {
                $daysLate = Carbon::now()->diffInDays($schedule->due_date);
                $penaltyPerDay = $chama->late_penalty_flat ?? 50;
                $fineAmount = $daysLate * $penaltyPerDay;

                if ($fineAmount > 0) {
                    // Check for existing fine for this installment
                    $existing = Fine::where('user_id', $loan->user_id)
                        ->where('chama_id', $chama->id)
                        ->where('type', 'late_repayment')
                        ->where('reference_id', $schedule->id)
                        ->first();

                    if (!$existing) {
                        Fine::create([
                            'user_id' => $loan->user_id,
                            'chama_id' => $chama->id,
                            'amount' => $fineAmount,
                            'type' => 'late_repayment',
                            'status' => 'unpaid',
                            'due_date' => now()->toDateString(),
                            'description' => "Late repayment for loan #{$loan->id} - installment #{$schedule->installment_no}",
                            'reference_id' => $schedule->id,
                            'billing_cycle' => Carbon::now()->format('Y-m'),
                        ]);
                        $loan->user->update(['account_status' => 'overdue']);
                        $count++;
                    }
                }
            }
        }

        return $count;
    }
}