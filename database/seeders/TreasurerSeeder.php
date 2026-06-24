<?php

namespace Database\Seeders;

use App\Models\Chama;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TreasurerSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure a Chama exists
        $chama = Chama::first();
        if (!$chama) {
            $chama = Chama::create([
                'name' => 'Default Chama',
                'location' => 'Nairobi',
                'currency' => 'KES',
                'contribution_target' => 5000.00,
                'late_penalty_flat' => 50.00,
                'interest_rate_pct' => 5.00,
                'min_credit_score' => 6.0,
                'savings_weight' => 0.4,
                'attendance_weight' => 0.2,
                'repayment_weight' => 0.3,
            ]);
        }

        // Create treasurer if not exists
        User::firstOrCreate(
            ['email' => 'treasurer@chama.com'],
            [
                'name' => 'John Treasurer',
                'password' => Hash::make('password'),
                'role' => 'treasurer',
                'chama_id' => $chama->id,
                'national_id' => '12345678',
                'phone' => '0712345678',
                'is_verified' => true,
                'account_status' => 'active',
            ]
        );

        $this->command->info('Treasurer created: treasurer@chama.com / password');
    }
}