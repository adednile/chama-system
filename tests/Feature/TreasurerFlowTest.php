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
            'type' => 'late_contribution',
            'status' => 'pending',
            'due_date' => now()->addDays(5)->toDateString(),
            'description' => 'Late savings contribution',
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

    public function test_member_cannot_parse_mismatching_fine_payment_sms(): void
    {
        $chama = Chama::create(['name' => 'Ruby Chama']);
        $member = User::factory()->create(['role' => 'member', 'chama_id' => $chama->id]);
        $fine = \App\Models\Fine::create([
            'user_id' => $member->id,
            'chama_id' => $chama->id,
            'amount' => 500.00,
            'type' => 'late_contribution',
            'status' => 'pending',
            'due_date' => now()->addDays(5)->toDateString(),
            'description' => 'Late savings contribution',
        ]);

        $response = $this->actingAs($member)->postJson("/member/contributions/parse-sms", [
            'message' => 'QXK8Y9T0R2 Confirmed. Ksh 300.00 received from SENDER NAME on 2026-06-21.',
            'payment_type' => 'fine_payment',
            'fine_id' => $fine->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'The payment amount (KES 300.00) does not match the exact fine amount (KES 500.00). Partial payments are not allowed.',
        ]);
    }

    public function test_treasurer_cannot_match_mismatching_fine_payment(): void
    {
        $chama = Chama::create(['name' => 'Ruby Chama']);
        $treasurer = User::factory()->create(['role' => 'treasurer', 'chama_id' => $chama->id]);
        $member = User::factory()->create(['role' => 'member', 'chama_id' => $chama->id]);
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
            'amount' => 300,
            'sender' => 'MEMBER DOE 0712345678',
            'transaction_code' => 'TXN789XYZ',
            'message' => 'Confirmed Ksh 300 received from MEMBER DOE.',
            'status' => 'unmapped',
        ]);

        $response = $this->actingAs($treasurer)->post("/treasurer/sms-parser/{$tx->id}/match", [
            'user_id' => $member->id,
            'payment_type' => 'fine_payment',
            'fine_id' => $fine->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'The payment amount (KES 300.00) does not match the exact fine amount (KES 500.00). Partial payments are not allowed.',
        ]);
    }

    public function test_loan_outstanding_balance_includes_interest_after_approval(): void
    {
        $chama = Chama::create([
            'name' => 'Interest Chama',
            'interest_rate_pct' => 10.00,
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
            'term_months' => 12,
            'status' => 'pending',
            'credit_score' => 7.0,
            'outstanding_balance' => 10000,
            'interest_rate' => 10.00,
        ]);

        // Approve loan
        $response = $this->actingAs($treasurer)->post("/treasurer/loans/{$loan->id}/approve");
        $response->assertRedirect();

        $freshLoan = $loan->fresh();
        $this->assertEquals('active', $freshLoan->status);
        $this->assertGreaterThan(10000.00, (float) $freshLoan->outstanding_balance);

        // Verify that a repayment of exactly 10000 (the principal) leaves the loan active with a balance
        $tx = \App\Models\MappedMpesaTransaction::create([
            'user_id' => $treasurer->id,
            'chama_id' => $chama->id,
            'amount' => 10000,
            'sender' => 'M-PESA SENDER',
            'transaction_code' => 'MPE1234567',
            'message' => 'Confirmed. Ksh 10,000 received from SENDER.',
            'status' => 'unmapped',
            'payment_type' => 'loan_repayment',
        ]);

        $matchResponse = $this->actingAs($treasurer)->post("/treasurer/sms-parser/{$tx->id}/match", [
            'user_id' => $member->id,
            'payment_type' => 'loan_repayment',
            'loan_id' => $freshLoan->id,
        ]);

        $matchResponse->assertStatus(200);
        
        $finalLoan = $freshLoan->fresh();
        $this->assertEquals('active', $finalLoan->status);
        $this->assertGreaterThan(0, (float) $finalLoan->outstanding_balance);
    }
}
