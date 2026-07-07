<?php

namespace Database\Seeders;

use App\Models\Chama;
use App\Models\User;
use App\Models\Contribution;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\MappedMpesaTransaction;
use App\Models\Repayment;
use App\Models\Meeting;
use App\Models\Attendance;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoScenariosSeeder extends Seeder
{
    public function run(): void
    {
        // 1. If Upendo Chama exists, clean it up manually to allow re-running
        $chama = Chama::where('name', 'Upendo Chama')->first();
        if ($chama) {
            \Schema::disableForeignKeyConstraints();
            $userIds = User::where('chama_id', $chama->id)->pluck('id');
            Repayment::whereIn('loan_id', Loan::whereIn('user_id', $userIds)->pluck('id'))->delete();
            \App\Models\AmortizationSchedule::whereIn('loan_id', Loan::whereIn('user_id', $userIds)->pluck('id'))->delete();
            Fine::whereIn('user_id', $userIds)->delete();
            Loan::whereIn('user_id', $userIds)->delete();
            Contribution::whereIn('user_id', $userIds)->delete();
            Attendance::whereIn('user_id', $userIds)->delete();
            Meeting::where('chama_id', $chama->id)->delete();
            MappedMpesaTransaction::whereIn('user_id', $userIds)->delete();
            Transaction::where('chama_id', $chama->id)->delete();
            User::where('chama_id', $chama->id)->delete();
            $chama->delete();
            \Schema::enableForeignKeyConstraints();
        }

        // 2. Create the Chama Configuration
        $chama = Chama::create([
            'name' => 'Upendo Chama',
            'location' => 'Mombasa',
            'description' => 'Upendo Self Help Group Demo',
            'currency' => 'KES',
            'min_credit_score' => 5.5,
            'interest_rate_pct' => 6.0,
            'savings_weight' => 0.40,
            'attendance_weight' => 0.20,
            'repayment_weight' => 0.40,
            'contribution_target' => 2500.00,
        ]);

        // 3. Define Users
        // Treasurer
        $treasurer = User::create([
            'name' => 'Upendo Treasurer',
            'email' => 'upendo.treasurer@example.com',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'chama_id' => $chama->id,
            'email_verified_at' => now(),
        ]);

        // Member A: Alice Excellent
        $alice = User::create([
            'name' => 'Alice Excellent',
            'email' => 'upendo.memberA@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'chama_id' => $chama->id,
            'email_verified_at' => now(),
            'created_at' => Carbon::now()->subMonths(6),
        ]);

        // Member B: Bob Average
        $bob = User::create([
            'name' => 'Bob Average',
            'email' => 'upendo.memberB@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'chama_id' => $chama->id,
            'email_verified_at' => now(),
            'created_at' => Carbon::now()->subMonths(5),
        ]);

        // Member C: Charlie Overdue
        $charlie = User::create([
            'name' => 'Charlie Overdue',
            'email' => 'upendo.memberC@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'chama_id' => $chama->id,
            'email_verified_at' => now(),
            'created_at' => Carbon::now()->subMonths(6),
        ]);

        // 4. Create past meetings (5 monthly meetings in the past, 1 today)
        $meetings = [];
        for ($i = 5; $i >= 1; $i--) {
            $meetings[] = Meeting::create([
                'chama_id' => $chama->id,
                'meeting_date' => Carbon::now()->subMonths($i)->toDateString(),
                'meeting_type' => 'monthly',
                'notes' => "Regular monthly meeting - Month $i",
            ]);
        }

        // Today's scheduled meeting
        Meeting::create([
            'chama_id' => $chama->id,
            'meeting_date' => Carbon::now()->toDateString(),
            'meeting_type' => 'monthly',
            'notes' => 'Today\'s meeting - Demo Session',
        ]);

        // 5. Seed Attendance records
        foreach ($meetings as $idx => $meeting) {
            $mNum = $idx + 1; // Month index (1 to 5)

            // Alice Excellent: Present in all meetings (100% attendance of past meetings)
            Attendance::create([
                'meeting_id' => $meeting->id,
                'user_id' => $alice->id,
                'present' => true,
            ]);

            // Bob Average: Present in meetings 1, 2, 3, absent in 4, 5
            Attendance::create([
                'meeting_id' => $meeting->id,
                'user_id' => $bob->id,
                'present' => ($mNum <= 3),
            ]);

            // Charlie Overdue: Present in meeting 1 only
            Attendance::create([
                'meeting_id' => $meeting->id,
                'user_id' => $charlie->id,
                'present' => ($mNum === 1),
            ]);
        }

        // 6. Seed Savings Contributions
        // Alice: Ksh 3,000 every month for 6 months (Perfect savings, meets target Ksh 2,500)
        for ($i = 6; $i >= 1; $i--) {
            Contribution::create([
                'user_id' => $alice->id,
                'chama_id' => $chama->id,
                'amount' => 3000.00,
                'contribution_date' => Carbon::now()->subMonths($i)->toDateString(),
                'source' => 'manual',
                'reference' => "ALICE-C$i",
                'notes' => "Savings Month $i",
            ]);

            Transaction::create([
                'user_id' => $alice->id,
                'chama_id' => $chama->id,
                'type' => 'contribution',
                'amount' => 3000.00,
                'description' => "Savings Contribution - Month $i",
                'reference' => "ALICE-C$i",
                'posted_at' => Carbon::now()->subMonths($i)->toDateString(),
            ]);
        }

        // Bob: Ksh 2,500 in 3 of the last 5 months (Month 4, 3, 2)
        for ($i = 4; $i >= 2; $i--) {
            Contribution::create([
                'user_id' => $bob->id,
                'chama_id' => $chama->id,
                'amount' => 2500.00,
                'contribution_date' => Carbon::now()->subMonths($i)->toDateString(),
                'source' => 'manual',
                'reference' => "BOB-C$i",
                'notes' => "Savings Month $i",
            ]);

            Transaction::create([
                'user_id' => $bob->id,
                'chama_id' => $chama->id,
                'type' => 'contribution',
                'amount' => 2500.00,
                'description' => "Savings Contribution - Month $i",
                'reference' => "BOB-C$i",
                'posted_at' => Carbon::now()->subMonths($i)->toDateString(),
            ]);
        }

        // Charlie: Ksh 2,500 only once (Month 5)
        Contribution::create([
            'user_id' => $charlie->id,
            'chama_id' => $chama->id,
            'amount' => 2500.00,
            'contribution_date' => Carbon::now()->subMonths(5)->toDateString(),
            'source' => 'manual',
            'reference' => 'CHARLIE-C1',
            'notes' => 'Savings Month 1',
        ]);
        Transaction::create([
            'user_id' => $charlie->id,
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'amount' => 2500.00,
            'description' => 'Savings Contribution - Month 1',
            'reference' => 'CHARLIE-C1',
            'posted_at' => Carbon::now()->subMonths(5)->toDateString(),
        ]);

        // 7. Seed Fines and Penalties
        // Charlie: Ksh 600 overdue fine
        Fine::create([
            'user_id' => $charlie->id,
            'chama_id' => $chama->id,
            'amount' => 600.00,
            'type' => 'late_contribution',
            'status' => 'pending',
            'due_date' => Carbon::now()->subMonths(1)->toDateString(),
            'description' => 'Unpaid penalty for late savings contribution',
        ]);

        // 8. Amortization Schedule Helper
        $generateSchedule = function ($loan) {
            $monthlyRate = ($loan->interest_rate / 100) / 12;
            $months = $loan->term_months;
            $principal = $loan->amount;

            if ($monthlyRate > 0) {
                $emi = $principal * $monthlyRate * pow(1 + $monthlyRate, $months) / (pow(1 + $monthlyRate, $months) - 1);
            } else {
                $emi = $principal / $months;
            }

            $balance = $principal;
            $dueDate = Carbon::parse($loan->approved_at)->addMonth();

            for ($i = 1; $i <= $months; $i++) {
                $interest = $balance * $monthlyRate;
                $principalPortion = $emi - $interest;
                $balance -= $principalPortion;

                \App\Models\AmortizationSchedule::create([
                    'loan_id'           => $loan->id,
                    'installment_no'    => $i,
                    'due_date'          => $dueDate->toDateString(),
                    'principal_portion' => round($principalPortion, 2),
                    'interest_portion'  => round($interest, 2),
                    'balance_after'     => max(round($balance, 2), 0),
                    'payment_status'    => $loan->status === 'completed' ? 'paid' : ($i === 1 ? 'paid' : 'unpaid'),
                ]);

                $dueDate->addMonth();
            }
        };

        // 9. Seed Loans and Repayments

        // Alice: Completed Loan of Ksh 12,000, 3 months term, 6.0% interest.
        $loanAlice = Loan::create([
            'user_id' => $alice->id,
            'chama_id' => $chama->id,
            'amount' => 12000.00,
            'term_months' => 3,
            'interest_rate' => 6.00,
            'status' => 'completed',
            'reason' => 'Business expansion',
            'approved_amount' => 12000.00,
            'approved_at' => Carbon::now()->subMonths(4),
            'repaid_at' => Carbon::now()->subMonths(1),
            'credit_score' => 9.2,
            'outstanding_balance' => 0.00,
            'maturity_date' => Carbon::now()->subMonths(1),
        ]);
        $generateSchedule($loanAlice);

        // Repayments for Alice: 3 installments on time
        $emiAlice = round((12000 * (0.06/12) * pow(1 + 0.06/12, 3)) / (pow(1 + 0.06/12, 3) - 1), 2);
        for ($i = 3; $i >= 1; $i--) {
            Repayment::create([
                'loan_id' => $loanAlice->id,
                'repayment_amount' => $emiAlice,
                'repayment_date' => Carbon::now()->subMonths($i + 1)->toDateString(),
                'remaining_balance' => round(12000 * (1 + 0.06) - ($emiAlice * (4 - $i)), 2),
                'is_late' => false,
            ]);

            Transaction::create([
                'user_id' => $alice->id,
                'chama_id' => $chama->id,
                'type' => 'repayment',
                'amount' => $emiAlice,
                'description' => "Loan Repayment - Installment " . (4 - $i),
                'posted_at' => Carbon::now()->subMonths($i + 1)->toDateString(),
            ]);
        }

        // Record Alice's loan disbursement transaction
        Transaction::create([
            'user_id' => $alice->id,
            'chama_id' => $chama->id,
            'type' => 'loan_disbursement',
            'amount' => 12000.00,
            'description' => 'Loan disbursement for Alice Excellent',
            'posted_at' => Carbon::now()->subMonths(4)->toDateString(),
        ]);

        // Bob: Pending Loan Application of Ksh 8,000
        Loan::create([
            'user_id' => $bob->id,
            'chama_id' => $chama->id,
            'amount' => 8000.00,
            'term_months' => 4,
            'interest_rate' => 6.00,
            'status' => 'pending',
            'reason' => 'School fees payment',
            'credit_score' => 6.0,
            'outstanding_balance' => 8000.00,
        ]);

        // Charlie: Overdue Loan of Ksh 10,000, 3 months term, 6.0% interest.
        $loanCharlie = Loan::create([
            'user_id' => $charlie->id,
            'chama_id' => $chama->id,
            'amount' => 10000.00,
            'term_months' => 3,
            'interest_rate' => 6.00,
            'status' => 'overdue',
            'reason' => 'Emergency medical expenses',
            'approved_amount' => 10000.00,
            'approved_at' => Carbon::now()->subMonths(3),
            'credit_score' => 4.5,
            'outstanding_balance' => 6000.00,
            'maturity_date' => Carbon::now()->subDays(10),
        ]);
        $generateSchedule($loanCharlie);

        // Repayment 1: On-time (Ksh 4,000)
        Repayment::create([
            'loan_id' => $loanCharlie->id,
            'repayment_amount' => 4000.00,
            'repayment_date' => Carbon::now()->subMonths(2)->toDateString(),
            'remaining_balance' => 6000.00,
            'is_late' => false,
        ]);
        Transaction::create([
            'user_id' => $charlie->id,
            'chama_id' => $chama->id,
            'type' => 'repayment',
            'amount' => 4000.00,
            'description' => 'Loan Repayment - Installment 1',
            'posted_at' => Carbon::now()->subMonths(2)->toDateString(),
        ]);

        // Repayment 2: Late (Ksh 2,000)
        Repayment::create([
            'loan_id' => $loanCharlie->id,
            'repayment_amount' => 2000.00,
            'repayment_date' => Carbon::now()->subDays(5)->toDateString(),
            'remaining_balance' => 4000.00,
            'is_late' => true,
        ]);
        Transaction::create([
            'user_id' => $charlie->id,
            'chama_id' => $chama->id,
            'type' => 'repayment',
            'amount' => 2000.00,
            'description' => 'Loan Repayment - Installment 2 (Late)',
            'posted_at' => Carbon::now()->subDays(5)->toDateString(),
        ]);

        // Record Charlie's loan disbursement transaction
        Transaction::create([
            'user_id' => $charlie->id,
            'chama_id' => $chama->id,
            'type' => 'loan_disbursement',
            'amount' => 10000.00,
            'description' => 'Loan disbursement for Charlie Overdue',
            'posted_at' => Carbon::now()->subMonths(3)->toDateString(),
        ]);

        // Auto-rejected loan request for Charlie
        Loan::create([
            'user_id' => $charlie->id,
            'chama_id' => $chama->id,
            'amount' => 5000.00,
            'term_months' => 3,
            'interest_rate' => 6.00,
            'status' => 'rejected',
            'reason' => 'Farming inputs purchase',
            'credit_score' => 2.7,
            'rejection_reason' => 'Credit score (2.7) below minimum threshold (5.5).',
            'outstanding_balance' => 0.00,
        ]);

        // 10. Seed M-Pesa SMS transactions
        // Transaction from Bob (Ksh 2,500 contribution, unmapped)
        MappedMpesaTransaction::create([
            'user_id' => $bob->id,
            'amount' => 2500.00,
            'sender' => $bob->name,
            'transaction_code' => 'KUP1029384',
            'message' => "KUP1029384 Confirmed. KES 2,500.00 received from BOB AVERAGE 254722222222. Ref: Savings Contribution.",
            'status' => 'unmapped',
            'payment_type' => 'contribution',
        ]);

        // Transaction from unregistered sender (Ksh 3,000 contribution, unmapped)
        MappedMpesaTransaction::create([
            'user_id' => $treasurer->id,
            'amount' => 3000.00,
            'sender' => 'DANIEL NDWIGA',
            'transaction_code' => 'KUP9988776',
            'message' => "KUP9988776 Confirmed. KES 3,000.00 received from DANIEL NDWIGA 254733333333. Ref: Group savings.",
            'status' => 'unmapped',
            'payment_type' => 'contribution',
        ]);

    }
}
