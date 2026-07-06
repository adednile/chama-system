<?php

namespace Tests\Feature;

use App\Models\Chama;
use App\Models\User;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_view_loan_application_page(): void
    {
        $chama = Chama::create([
            'name' => 'Gold Chama',
            'min_credit_score' => 5.0,
        ]);
        $user = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);

        $response = $this->actingAs($user)->get('/member/loans');

        $response->assertStatus(200);
        $response->assertSee('Apply for a Loan');
    }

    public function test_member_can_apply_for_loan_successfully(): void
    {
        $chama = Chama::create([
            'name' => 'Gold Chama',
            'min_credit_score' => 1.0,
        ]);
        $user = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);

        // Seed contributions so the user has individual borrowing limit (3 * 2000 = 6000)
        // and the group has available cash pool (2000)
        \App\Models\Contribution::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'amount' => 2000,
            'contribution_date' => now()->toDateString(),
            'source' => 'manual',
        ]);
        
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'amount' => 2000,
            'posted_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/member/loans', [
            'amount' => 1500,
            'term_months' => 12,
            'reason' => 'Business setup',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loans', [
            'user_id' => $user->id,
            'amount' => 1500.00,
            'status' => 'pending',
        ]);
    }

    public function test_member_cannot_apply_for_loan_exceeding_individual_limit(): void
    {
        $chama = Chama::create([
            'name' => 'Gold Chama',
            'min_credit_score' => 1.0,
        ]);
        $user = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);

        // User savings = 1000, limit = 3000. Group cash pool = 10000.
        // User attempts to borrow 5000.
        \App\Models\Contribution::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'amount' => 10000,
            'contribution_date' => now()->toDateString(),
        ]);
        
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'amount' => 1000,
            'posted_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/member/loans', [
            'amount' => 5000,
            'term_months' => 12,
            'reason' => 'Too expensive',
        ]);

        $response->assertSessionHas('error');
        $response->assertSessionHas('error', function($val) {
            return str_contains($val, 'exceeds your individual borrowing limit');
        });
        $this->assertDatabaseMissing('loans', [
            'user_id' => $user->id,
            'amount' => 5000.00,
        ]);
    }

    public function test_member_cannot_apply_for_loan_exceeding_chama_cash_pool(): void
    {
        $chama = Chama::create([
            'name' => 'Gold Chama',
            'min_credit_score' => 1.0,
        ]);
        $user = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);

        // User savings = 5000, limit = 15000. Group cash pool = 1000.
        // User attempts to borrow 4000.
        \App\Models\Contribution::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'amount' => 1000,
            'contribution_date' => now()->toDateString(),
        ]);
        
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'amount' => 5000,
            'posted_at' => now(),
        ]);

        $response = $this->actingAs($user)->post('/member/loans', [
            'amount' => 4000,
            'term_months' => 12,
            'reason' => 'Exceeds group capacity',
        ]);

        $response->assertSessionHas('error');
        $response->assertSessionHas('error', function($val) {
            return str_contains($val, "exceeds the Chama's available cash pool");
        });
        $this->assertDatabaseMissing('loans', [
            'user_id' => $user->id,
            'amount' => 4000.00,
        ]);
    }

    public function test_borrowing_multiplier_tier_a(): void
    {
        $chama = Chama::create([
            'name' => 'Ruby Chama',
            'contribution_target' => 1000.00,
            'min_credit_score' => 1.0,
        ]);
        $user = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);

        // Seed 5 contributions of KES 1000 each in the last 5 months
        for ($i = 0; $i < 5; $i++) {
            \App\Models\Contribution::create([
                'user_id' => $user->id,
                'chama_id' => $chama->id,
                'amount' => 1000.00,
                'contribution_date' => \Carbon\Carbon::now()->subMonths($i)->toDateString(),
            ]);
        }

        // Add savings transaction
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'amount' => 5000.00,
            'posted_at' => now(),
        ]);

        // Seed some cash in Chama reserves so the loan doesn't exceed cash pool
        \App\Models\Contribution::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'amount' => 20000.00,
            'contribution_date' => \Carbon\Carbon::now()->toDateString(),
        ]);

        $scoringEngine = new \App\Services\CreditScoringEngine();
        $this->assertEquals(3.5, $scoringEngine->calculateBorrowingMultiplier($user));

        // Enforce borrowing limit (5000 * 3.5 = 17500). Let's attempt 18000.
        $response = $this->actingAs($user)->post('/member/loans', [
            'amount' => 18000,
            'term_months' => 12,
            'reason' => 'Too high',
        ]);
        $response->assertSessionHas('error');
        $response->assertSessionHas('error', function ($val) {
            return str_contains($val, 'exceeds your individual borrowing limit of 3.5x');
        });

        // Let's attempt 17000 (below 17500)
        $response2 = $this->actingAs($user)->post('/member/loans', [
            'amount' => 17000,
            'term_months' => 12,
            'reason' => 'Allowed',
        ]);
        $response2->assertSessionHasNoErrors();
    }

    public function test_borrowing_multiplier_tier_b(): void
    {
        $chama = Chama::create([
            'name' => 'Ruby Chama',
            'contribution_target' => 1000.00,
            'min_credit_score' => 1.0,
        ]);
        $user = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);

        // Seed 3 contributions of KES 1000 each in the last 3 months
        for ($i = 0; $i < 3; $i++) {
            \App\Models\Contribution::create([
                'user_id' => $user->id,
                'chama_id' => $chama->id,
                'amount' => 1000.00,
                'contribution_date' => \Carbon\Carbon::now()->subMonths($i)->toDateString(),
            ]);
        }

        // Add savings transaction
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'amount' => 3000.00,
            'posted_at' => now(),
        ]);

        $scoringEngine = new \App\Services\CreditScoringEngine();
        $this->assertEquals(3.0, $scoringEngine->calculateBorrowingMultiplier($user));

        // Limit is 3000 * 3.0 = 9000. Attempt 10000.
        $response = $this->actingAs($user)->post('/member/loans', [
            'amount' => 10000,
            'term_months' => 12,
            'reason' => 'Too high',
        ]);
        $response->assertSessionHas('error');
        $response->assertSessionHas('error', function ($val) {
            return str_contains($val, 'exceeds your individual borrowing limit of 3x');
        });
    }

    public function test_borrowing_multiplier_tier_c(): void
    {
        $chama = Chama::create([
            'name' => 'Ruby Chama',
            'contribution_target' => 1000.00,
            'min_credit_score' => 1.0,
        ]);
        $user = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);

        // Seed 1 contribution of KES 1000 
        \App\Models\Contribution::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'amount' => 1000.00,
            'contribution_date' => \Carbon\Carbon::now()->toDateString(),
        ]);

        // Add savings transaction
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'chama_id' => $chama->id,
            'type' => 'contribution',
            'amount' => 3000.00,
            'posted_at' => now(),
        ]);

        $scoringEngine = new \App\Services\CreditScoringEngine();
        $this->assertEquals(1.5, $scoringEngine->calculateBorrowingMultiplier($user));

        // Limit is 3000 * 1.5 = 4500. Attempt 5000.
        $response = $this->actingAs($user)->post('/member/loans', [
            'amount' => 5000,
            'term_months' => 12,
            'reason' => 'Too high',
        ]);
        $response->assertSessionHas('error');
        $response->assertSessionHas('error', function ($val) {
            return str_contains($val, 'exceeds your individual borrowing limit of 1.5x');
        });
    }
}
