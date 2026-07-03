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

    public function test_treasurer_can_match_transaction_to_fine(): void
    {
        $chama = Chama::create([
            'name' => 'Diamond Chama',
        ]);
        $treasurer = User::factory()->create([
            'role' => 'treasurer',
            'chama_id' => $chama->id,
        ]);
        $member = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);
        $fine = \App\Models\Fine::create([
            'user_id' => $member->id,
            'chama_id' => $chama->id,
            'amount' => 500.00,
            'type' => 'late_meeting',
            'status' => 'pending',
            'due_date' => now()->addDays(5)->toDateString(),
            'description' => 'Late to Meeting 5',
        ]);
        $tx = MappedMpesaTransaction::create([
            'user_id' => $treasurer->id,
            'amount' => 500,
            'sender' => 'MEMBER DOE 0712345678',
            'transaction_code' => 'TXN789XYZ',
            'message' => 'Confirmed Ksh 500 received from MEMBER DOE.',
            'status' => 'unmapped',
        ]);

        $response = $this->actingAs($treasurer)->post("/treasurer/sms-parser/{$tx->id}/match", [
            'user_id' => $member->id,
            'payment_type' => 'fine_payment',
            'fine_id' => $fine->id,
        ]);

        $response->assertJson(['success' => true]);
        $this->assertEquals('mapped', $tx->fresh()->status);
        $this->assertEquals('fine_payment', $tx->fresh()->payment_type);
        $this->assertEquals($fine->id, $tx->fresh()->fine_id);

        $this->assertEquals('paid', $fine->fresh()->status);
        $this->assertNotNull($fine->fresh()->paid_at);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $member->id,
            'chama_id' => $chama->id,
            'type' => 'fine_paid',
            'amount' => 500.00,
            'reference' => 'TXN789XYZ',
        ]);
    }

    public function test_member_can_parse_fine_payment_sms(): void
    {
        $chama = Chama::create([
            'name' => 'Ruby Chama',
        ]);
        $member = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);
        $fine = \App\Models\Fine::create([
            'user_id' => $member->id,
            'chama_id' => $chama->id,
            'amount' => 300.00,
            'type' => 'missed_meeting',
            'status' => 'pending',
            'due_date' => now()->addDays(5)->toDateString(),
            'description' => 'Missed Meeting 3',
        ]);

        $response = $this->actingAs($member)->postJson("/member/contributions/parse-sms", [
            'message' => 'QXK8Y9T0R2 Confirmed. Ksh 300.00 received from SENDER NAME on 2026-06-21.',
            'payment_type' => 'fine_payment',
            'fine_id' => $fine->id,
        ]);

        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('mapped_mpesa_transactions', [
            'user_id' => $member->id,
            'payment_type' => 'fine_payment',
            'fine_id' => $fine->id,
            'amount' => 300.00,
            'transaction_code' => 'QXK8Y9T0R2',
        ]);
    }
}
