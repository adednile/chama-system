<?php

namespace App\Services;

use App\Models\User;
use App\Models\Meeting;
use Carbon\Carbon;

class CreditScoringEngine
{
    /**
     * Calculate credit score based on metrics array.
     */
    public function score(array $metrics): float|int
    {
        $savings = $metrics['savings_consistency'] ?? 0;
        $repayment = $metrics['repayment_history'] ?? 0;
        $attendance = $metrics['attendance'] ?? 0;
        $duration = $metrics['membership_duration'] ?? 0;

        $weights = [
            'savings' => 0.4,
            'repayment' => 0.3,
            'attendance' => 0.2,
            'duration' => 0.1,
        ];

        $score = ($savings * $weights['savings'])
            + ($repayment * $weights['repayment'])
            + ($attendance * $weights['attendance'])
            + ($duration * $weights['duration']);

        return (int) round($score);
    }

    /**
     * Calculate credit score for a user based on:
     * - Savings consistency (40%)
     * - Repayment history (30%)
     * - Meeting attendance (20%)
     * - Membership duration (10%)
     */
    public function calculateScore(User $user): float
    {
        $savings = $this->savingsConsistency($user);
        $repayment = $this->repaymentHistory($user);
        $attendance = $this->attendanceScore($user);
        $duration = $this->membershipDuration($user);

        // Apply weights (can be configured per Chama, but we use defaults)
        $weights = $this->getWeights($user->chama);

        $score = ($savings * $weights['savings'])
            + ($repayment * $weights['repayment'])
            + ($attendance * $weights['attendance'])
            + ($duration * $weights['duration']);

        return round($score, 1);
    }

    /**
     * Get weights from Chama settings or use defaults.
     */
    private function getWeights($chama): array
    {
        if ($chama) {
            return [
                'savings' => $chama->savings_weight ?? 0.4,
                'repayment' => $chama->repayment_weight ?? 0.3,
                'attendance' => $chama->attendance_weight ?? 0.2,
                'duration' => 0.1, // can also be made configurable
            ];
        }

        // Default weights
        return [
            'savings' => 0.4,
            'repayment' => 0.3,
            'attendance' => 0.2,
            'duration' => 0.1,
        ];
    }

    /**
     * Savings consistency: % of last 6 months with at least one contribution
     */
    private function savingsConsistency(User $user): float
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $contributions = $user->contributions()
            ->where('contribution_date', '>=', $sixMonthsAgo)
            ->get();

        if ($contributions->isEmpty()) {
            return 0;
        }

        // Group by month
        $months = $contributions->groupBy(fn($c) => Carbon::parse($c->contribution_date)->format('Y-m'));
        $monthsWithContribution = $months->count();
        $expectedMonths = 6;

        return min(10, ($monthsWithContribution / $expectedMonths) * 10);
    }

    /**
     * Repayment history: % of on-time repayments
     */
    private function repaymentHistory(User $user): float
    {
        $loans = $user->loans()->whereIn('status', ['active', 'completed'])->get();

        if ($loans->isEmpty()) {
            return 10; // No history – assume perfect (or you can return 5 as neutral)
        }

        $totalInstallments = 0;
        $onTime = 0;

        foreach ($loans as $loan) {
            foreach ($loan->repayments as $repay) {
                $totalInstallments++;
                if (!$repay->is_late) {
                    $onTime++;
                }
            }
        }

        if ($totalInstallments === 0) {
            return 10;
        }

        return round(($onTime / $totalInstallments) * 10, 1);
    }

    /**
     * Meeting attendance: % of meetings attended
     */
    private function attendanceScore(User $user): float
    {
        // Count total meetings for this Chama
        $totalMeetings = Meeting::where('chama_id', $user->chama_id)->count();

        if ($totalMeetings === 0) {
            return 10; // No meetings recorded – assume perfect attendance
        }

        // Count how many meetings the user attended (present = true)
        $attended = $user->attendances()->where('present', true)->count();

        return round(($attended / $totalMeetings) * 10, 1);
    }

    /**
     * Membership duration: months as member, capped at 10
     */
    private function membershipDuration(User $user): float
    {
        $months = $user->created_at->diffInMonths(Carbon::now());
        return min(10, ($months / 12) * 10);
    }
}