<?php
# database/seeders/DemoTransactionSeeder.php

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

class DemoTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clear transactional tables to avoid overlap
        \Schema::disableForeignKeyConstraints();
        Repayment::truncate();
        Fine::truncate();
        Loan::truncate();
        Contribution::truncate();
        Attendance::truncate();
        Meeting::truncate();
        MappedMpesaTransaction::truncate();
        Transaction::truncate();
        \DB::table('amortization_schedules')->truncate();
        \Schema::enableForeignKeyConstraints();

        // 2. Fetch the Chama and ensure users are created with descriptive names
        $chama = Chama::updateOrCreate(
            ['name' => 'Mwangaza Chama'],
            [
                'location' => 'Nairobi',
                'description' => 'Mwangaza Self Help Group Ledger',
                'currency' => 'KES',
                'min_credit_score' => 5.0,
                'interest_rate_pct' => 5.0,
                'savings_weight' => 0.40,
                'attendance_weight' => 0.20,
                'repayment_weight' => 0.40,
            ]
        );

        // Define users
        $treasurer = User::updateOrCreate(
            ['email' => 'treasurer@example.com'],
            [
                'name' => 'Treasurer One',
                'password' => Hash::make('password'),
                'role' => 'treasurer',
                'chama_id' => $chama->id,
                'email_verified_at' => now(),
            ]
        );

        // Member 1: Perfect credit
        $m1 = User::updateOrCreate(
            ['email' => 'member1@example.com'],
            [
                'name' => 'Member One (Perfect Credit)',
                'password' => Hash::make('password'),
                'role' => 'member',
                'chama_id' => $chama->id,
                'email_verified_at' => now(),
                'created_at' => Carbon::now()->subMonths(6),
            ]
        );

        // Member 2: Poor credit / Overdue / Blocked
        $m2 = User::updateOrCreate(
            ['email' => 'member2@example.com'],
            [
                'name' => 'Member Two (Overdue & Blocked)',
                'password' => Hash::make('password'),
                'role' => 'member',
                'chama_id' => $chama->id,
                'email_verified_at' => now(),
                'created_at' => Carbon::now()->subMonths(6),
            ]
        );

        // Member 3: Normal credit / Pending Loan Application
        $m3 = User::updateOrCreate(
            ['email' => 'member3@example.com'],
            [
                'name' => 'Member Three (Pending Loan)',
                'password' => Hash::make('password'),
                'role' => 'member',
                'chama_id' => $chama->id,
                'email_verified_at' => now(),
                'created_at' => Carbon::now()->subMonths(3),
            ]
        );

        // Member 4: New member / SMS Parser demo
        $m4 = User::updateOrCreate(
            ['email' => 'member4@example.com'],
            [
                'name' => 'Member Four (SMS Parser Demo)',
                'password' => Hash::make('password'),
                'role' => 'member',
                'chama_id' => $chama->id,
                'email_verified_at' => now(),
                'created_at' => Carbon::now()->subMonths(1),
            ]
        );

        // Member 5: Fines and Attendance demo
        $m5 = User::updateOrCreate(
            ['email' => 'member5@example.com'],
            [
                'name' => 'Member Five (Fines Demo)',
                'password' => Hash::make('password'),
                'role' => 'member',
                'chama_id' => $chama->id,
                'email_verified_at' => now(),
                'created_at' => Carbon::now()->subMonths(4),
            ]
        );

        $members = [$m1, $m2, $m3, $m4, $m5];

        // 3. Create 5 monthly meetings in the past
        $meetings = [];
        for ($i = 5; $i >= 1; $i--) {
            $meetings[] = Meeting::create([
                'chama_id' => $chama->id,
                'meeting_date' => Carbon::now()->subMonths($i)->toDateString(),
                'meeting_type' => 'monthly',
                'notes' => "Regular monthly meeting - Month $i",
            ]);
        }

        // Today's meeting (scheduled, not yet held / no attendance checked)
        Meeting::create([
            'chama_id' => $chama->id,
            'meeting_date' => Carbon::now()->toDateString(),
            'meeting_type' => 'monthly',
            'notes' => 'Today\'s meeting - Attendance tracking demo',
        ]);

        // 4. Seed Attendance records
        // M1: Present in all 5
        // M2: Present in Meeting 1, Absent in others
        // M3: Present in Meetings 3, 4, 5 (joined later)
        // M4: Present in Meeting 5 (joined later)
        // M5: Present in 1, 2, 3, Missed 4, 5
        foreach ($meetings as $idx => $meeting) {
            $mNum = $idx + 1; // Month index (1 to 5)

            // Member 1
            Attendance::create(['meeting_id' => $meeting->id, 'user_id' => $m1->id, 'present' => true]);

            // Member 2
            Attendance::create(['meeting_id' => $meeting->id, 'user_id' => $m2->id, 'present' => ($mNum === 1)]);

            // Member 3 (joined 3 months ago)
            if ($mNum >= 3) {
                Attendance::create(['meeting_id' => $meeting->id, 'user_id' => $m3->id, 'present' => true]);
            }

            // Member 4 (joined 1 month ago)
            if ($mNum >= 5) {
                Attendance::create(['meeting_id' => $meeting->id, 'user_id' => $m4->id, 'present' => true]);
            }

            // Member 5
            Attendance::create(['meeting_id' => $meeting->id, 'user_id' => $m5->id, 'present' => ($mNum <= 3)]);
        }

        // 5. Seed Contributions (Savings Consistency)
        // Member 1: Contribution in all 5 months (KES 2,000 each)
        for ($i = 5; $i >= 1; $i--) {
            Contribution::create([
                'user_id' => $m1->id,
                'chama_id' => $chama->id,
                'amount' => 2000.00,
                'contribution_date' => Carbon::now()->subMonths($i)->toDateString(),
                'source' => 'manual',
                'reference' => "M1-C$i",
                'notes' => "Savings Month $i",
            ]);
        }

        // Member 2: Only 1 contribution 5 months ago
        Contribution::create([
            'user_id' => $m2->id,
            'chama_id' => $chama->id,
            'amount' => 2000.00,
            'contribution_date' => Carbon::now()->subMonths(5)->toDateString(),
            'source' => 'manual',
            'reference' => 'M2-C1',
            'notes' => 'Savings Month 1',
        ]);

        // Member 3: 3 contributions
        for ($i = 3; $i >= 1; $i--) {
            Contribution::create([
                'user_id' => $m3->id,
                'chama_id' => $chama->id,
                'amount' => 2000.00,
                'contribution_date' => Carbon::now()->subMonths($i)->toDateString(),
                'source' => 'manual',
                'reference' => "M3-C$i",
                'notes' => "Savings Month $i",
            ]);
        }

        // Member 4: None (awaiting matching)

        // Member 5: 3 contributions, missed last 2
        for ($i = 4; $i >= 2; $i--) {
            Contribution::create([
                'user_id' => $m5->id,
                'chama_id' => $chama->id,
                'amount' => 2000.00,
                'contribution_date' => Carbon::now()->subMonths($i)->toDateString(),
                'source' => 'manual',
                'reference' => "M5-C$i",
                'notes' => "Savings Month $i",
            ]);
        }

        // 6. Seed Fines and Penalties
        // Member 2: Overdue fine
        Fine::create([
            'user_id' => $m2->id,
            'chama_id' => $chama->id,
            'amount' => 500.00,
            'type' => 'late_contribution',
            'status' => 'unpaid',
            'due_date' => Carbon::now()->subMonths(1)->toDateString(),
            'description' => 'Unpaid penalty for late savings contribution',
        ]);

        // Member 5: Fines for missed meetings
        Fine::create([
            'user_id' => $m5->id,
            'chama_id' => $chama->id,
            'amount' => 150.00,
            'type' => 'missed_meeting',
            'status' => 'unpaid',
            'due_date' => Carbon::now()->subMonths(1)->toDateString(),
            'description' => 'Penalty for missing Meeting 4',
        ]);

        Fine::create([
            'user_id' => $m5->id,
            'chama_id' => $chama->id,
            'amount' => 150.00,
            'type' => 'missed_meeting',
            'status' => 'unpaid',
            'due_date' => Carbon::now()->toDateString(),
            'description' => 'Penalty for missing Meeting 5',
        ]);

        // 7. Seed Loans and Repayments
        // Member 1: Completed Loan (KES 15,000, 5% interest, 3 months, term completed)
        $loanM1 = Loan::create([
            'user_id' => $m1->id,
            'chama_id' => $chama->id,
            'amount' => 15000.00,
            'term_months' => 3,
            'interest_rate' => 5.00,
            'status' => 'completed',
            'reason' => 'Business expansion',
            'approved_amount' => 15000.00,
            'approved_at' => Carbon::now()->subMonths(4),
            'repaid_at' => Carbon::now()->subMonths(1),
            'credit_score' => 9.2,
            'outstanding_balance' => 0.00,
            'maturity_date' => Carbon::now()->subMonths(1),
        ]);

        // 3 repayments for Member 1's loan
        for ($i = 3; $i >= 1; $i--) {
            Repayment::create([
                'loan_id' => $loanM1->id,
                'repayment_amount' => 5250.00,
                'repayment_date' => Carbon::now()->subMonths($i + 1)->toDateString(),
                'remaining_balance' => 15750.00 - (5250.00 * (4 - $i)),
            ]);
        }

        // Member 2: Overdue / Poor Credit active loan
        $loanM2 = Loan::create([
            'user_id' => $m2->id,
            'chama_id' => $chama->id,
            'amount' => 10000.00,
            'term_months' => 3,
            'interest_rate' => 5.00,
            'status' => 'overdue',
            'reason' => 'Emergency repairs',
            'approved_amount' => 10000.00,
            'approved_at' => Carbon::now()->subMonths(3),
            'credit_score' => 4.5,
            'outstanding_balance' => 7000.00,
            'maturity_date' => Carbon::now()->subDays(10), // Passed maturity!
        ]);

        // Repayment history showing late/missed payment
        Repayment::create([
            'loan_id' => $loanM2->id,
            'repayment_amount' => 3500.00,
            'repayment_date' => Carbon::now()->subMonths(2)->toDateString(),
            'remaining_balance' => 7000.00,
        ]);

        // Member 3: Pending Loan Application (KES 8,000)
        Loan::create([
            'user_id' => $m3->id,
            'chama_id' => $chama->id,
            'amount' => 8000.00,
            'term_months' => 4,
            'interest_rate' => 5.00,
            'status' => 'pending',
            'reason' => 'Agricultural farm inputs purchase',
            'credit_score' => 7.8,
            'outstanding_balance' => 0.00,
        ]);

        // 8. Seed Mmapped Mpesa Transactions (Unmapped queue)
        // Transaction 1: For Member 4 (matching KES 2,000 savings contribution)
        MappedMpesaTransaction::create([
            'user_id' => $m4->id,
            'amount' => 2000.00,
            'sender' => $m4->name,
            'transaction_code' => 'KLA8923471',
            'message' => "KLA8923471 Confirmed. KES 2,000.00 received from MEMBER FOUR (SMS Parser Demo) 254712345678. Ref: Contribution.",
            'status' => 'unmapped',
            'payment_type' => 'contribution',
        ]);

        // Transaction 2: For Member 2 (repaying overdue loan KES 7,000)
        MappedMpesaTransaction::create([
            'user_id' => $m2->id,
            'amount' => 7000.00,
            'sender' => $m2->name,
            'transaction_code' => 'KLA9981245',
            'message' => "KLA9981245 Confirmed. KES 7,000.00 received from MEMBER TWO (Overdue & Blocked) 254787654321. Ref: Loan Repayment.",
            'status' => 'unmapped',
            'payment_type' => 'loan_repayment',
        ]);

        // Transaction 3: Unknown / Unregistered number
        MappedMpesaTransaction::create([
            'user_id' => $treasurer->id,
            'amount' => 3000.00,
            'sender' => 'ELIZABETH AWUOR',
            'transaction_code' => 'KLA0192847',
            'message' => "KLA0192847 Confirmed. KES 3,000.00 received from ELIZABETH AWUOR 254799000111. Ref: Guest Payment.",
            'status' => 'unmapped',
            'payment_type' => 'contribution',
        ]);

        // 9. Seed the generic double-entry ledger transactions to align ledger stats
        // We record chama cash pool starting balance (Treasurer injection / starting balance)
        Transaction::create([
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'user_id' => $treasurer->id,
            'amount' => 100000.00,
            'posted_at' => Carbon::now()->subMonths(6)->toDateString(),
            'description' => 'Opening balance initialization',
        ]);

        // M1 contributions
        Transaction::create([
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'user_id' => $m1->id,
            'amount' => 10000.00,
            'posted_at' => Carbon::now()->subMonths(2)->toDateString(),
            'description' => 'Total Member 1 savings contributions',
        ]);

        // M2 contributions
        Transaction::create([
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'user_id' => $m2->id,
            'amount' => 2000.00,
            'posted_at' => Carbon::now()->subMonths(5)->toDateString(),
            'description' => 'Member 2 savings contributions',
        ]);

        // M3 contributions
        Transaction::create([
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'user_id' => $m3->id,
            'amount' => 6000.00,
            'posted_at' => Carbon::now()->subMonths(1)->toDateString(),
            'description' => 'Member 3 savings contributions',
        ]);

        // M5 contributions
        Transaction::create([
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'user_id' => $m5->id,
            'amount' => 6000.00,
            'posted_at' => Carbon::now()->subMonths(2)->toDateString(),
            'description' => 'Member 5 savings contributions',
        ]);

        // Loan Disbursals:
        // Loan M1 (15000)
        Transaction::create([
            'chama_id' => $chama->id,
            'type' => 'loan_disbursal',
            'user_id' => $m1->id,
            'amount' => -15000.00,
            'posted_at' => Carbon::now()->subMonths(4)->toDateString(),
            'description' => 'Disbursed Loan M1',
        ]);
        // Loan M2 (10000)
        Transaction::create([
            'chama_id' => $chama->id,
            'type' => 'loan_disbursal',
            'user_id' => $m2->id,
            'amount' => -10000.00,
            'posted_at' => Carbon::now()->subMonths(3)->toDateString(),
            'description' => 'Disbursed Loan M2',
        ]);

        // Loan Repayments:
        // M1 repayments (15750 total)
        Transaction::create([
            'chama_id' => $chama->id,
            'type' => 'loan_repayment',
            'user_id' => $m1->id,
            'amount' => 15750.00,
            'posted_at' => Carbon::now()->subMonths(1)->toDateString(),
            'description' => 'Total Loan M1 repayments',
        ]);

        // M2 repayments (3500 total)
        Transaction::create([
            'chama_id' => $chama->id,
            'type' => 'loan_repayment',
            'user_id' => $m2->id,
            'amount' => 3500.00,
            'posted_at' => Carbon::now()->subMonths(2)->toDateString(),
            'description' => 'Loan M2 repayment installment',
        ]);
    }
}