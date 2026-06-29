<?php

namespace Tests\Feature;

use App\Models\Chama;
use App\Models\User;
use App\Models\Loan;
use App\Models\MappedMpesaTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreasurerFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_treasurer_can_view_pending_loans(): void
    {
        $chama = Chama::create([
            'name' => 'Gold Chama',
        ]);
        $treasurer = User::factory()->create([
            'role' => 'treasurer',
            'chama_id' => $chama->id,
        ]);

        $response = $this->actingAs($treasurer)->get('/treasurer/loans/pending');

        $response->assertStatus(200);
        $response->assertSee('Pending Loan Approvals');
    }

    public function test_treasurer_can_approve_loan(): void
    {
        $chama = Chama::create([
            'name' => 'Gold Chama',
        ]);
        $treasurer = User::factory()->create([
            'role' => 'treasurer',
            'chama_id' => $chama->id,
        ]);
        $member = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);
        $loan = Loan::create([
            'user_id' => $member->id,
            'chama_id' => $chama->id,
            'amount' => 10000,
            'term_months' => 6,
            'status' => 'pending',
            'credit_score' => 7.0,
            'outstanding_balance' => 10000,
        ]);

        $response = $this->actingAs($treasurer)->post("/treasurer/loans/{$loan->id}/approve");

        $response->assertRedirect();
        $this->assertEquals('active', $loan->fresh()->status);
    }

    public function test_treasurer_can_reject_loan(): void
    {
        $chama = Chama::create([
            'name' => 'Gold Chama',
        ]);
        $treasurer = User::factory()->create([
            'role' => 'treasurer',
            'chama_id' => $chama->id,
        ]);
        $member = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);
        $loan = Loan::create([
            'user_id' => $member->id,
            'chama_id' => $chama->id,
            'amount' => 10000,
            'term_months' => 6,
            'status' => 'pending',
            'credit_score' => 7.0,
            'outstanding_balance' => 10000,
        ]);

        $response = $this->actingAs($treasurer)->post("/treasurer/loans/{$loan->id}/reject", [
            'reason' => 'Poor savings consistency',
        ]);

        $response->assertRedirect();
        $this->assertEquals('rejected', $loan->fresh()->status);
        $this->assertEquals('Poor savings consistency', $loan->fresh()->rejection_reason);
    }

    public function test_treasurer_can_match_unmapped_transaction(): void
    {
        $chama = Chama::create([
            'name' => 'Gold Chama',
        ]);
        $treasurer = User::factory()->create([
            'role' => 'treasurer',
            'chama_id' => $chama->id,
        ]);
        $member = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);
        $tx = MappedMpesaTransaction::create([
            'user_id' => $treasurer->id,
            'amount' => 2000,
            'sender' => 'JOHN DOE 0712345678',
            'transaction_code' => 'QWE123RTY',
            'message' => 'Confirmed Ksh 2,000 received from JOHN DOE.',
            'status' => 'unmapped',
        ]);

        $response = $this->actingAs($treasurer)->post("/treasurer/sms-parser/{$tx->id}/match", [
            'user_id' => $member->id,
        ]);

        $response->assertJson(['success' => true]);
        $this->assertEquals('mapped', $tx->fresh()->status);
        $this->assertEquals($member->id, $tx->fresh()->user_id);
        
        $this->assertDatabaseHas('contributions', [
            'user_id' => $member->id,
            'amount' => 2000.00,
            'reference' => 'QWE123RTY',
        ]);
    }
}
