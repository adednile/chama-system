<?php

namespace Tests\Feature;

use App\Models\Chama;
use App\Models\User;
use App\Models\MappedMpesaTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberSmsParserTest extends TestCase
{
    use RefreshDatabase;

    private Chama $chama;
    private User $member;
    private User $treasurer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chama = Chama::create(['name' => 'Gold Chama']);
        $this->member = User::factory()->create([
            'role' => 'member',
            'chama_id' => $this->chama->id,
        ]);
        $this->treasurer = User::factory()->create([
            'role' => 'treasurer',
            'chama_id' => $this->chama->id,
        ]);
    }

    public function test_member_can_parse_valid_mpesa_sms(): void
    {
        $smsText = "KQA2B3C4D5 Confirmed. Ksh 5,000.00 received from JOHN DOE 0712345678 on 2026-06-15.";

        $response = $this->actingAs($this->member)
            ->postJson(route('member.contributions.parseSms'), [
                'message' => $smsText,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'amount' => '5,000.00',
                'sender' => 'JOHN DOE',
                'transaction_code' => 'KQA2B3C4D5',
            ]
        ]);

        $this->assertDatabaseHas('mapped_mpesa_transactions', [
            'user_id' => $this->member->id,
            'amount' => 5000.00,
            'transaction_code' => 'KQA2B3C4D5',
            'status' => 'unmapped',
        ]);
    }

    public function test_member_cannot_parse_duplicate_transaction_code(): void
    {
        MappedMpesaTransaction::create([
            'user_id' => $this->member->id,
            'amount' => 5000.00,
            'sender' => 'JOHN DOE',
            'transaction_code' => 'KQA2B3C4D5',
            'message' => 'Some message',
            'status' => 'unmapped',
        ]);

        $smsText = "KQA2B3C4D5 Confirmed. Ksh 5,000.00 received from JOHN DOE 0712345678 on 2026-06-15.";

        $response = $this->actingAs($this->member)
            ->postJson(route('member.contributions.parseSms'), [
                'message' => $smsText,
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'This M-Pesa transaction code has already been submitted.',
        ]);
    }

    public function test_member_cannot_parse_invalid_gibberish_sms(): void
    {
        $response = $this->actingAs($this->member)
            ->postJson(route('member.contributions.parseSms'), [
                'message' => 'This is just some random chat message without payment details.',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Failed to parse a valid M-Pesa transaction code or amount. Please check the SMS text.',
        ]);
    }

    public function test_member_dashboard_displays_pending_transactions(): void
    {
        MappedMpesaTransaction::create([
            'user_id' => $this->member->id,
            'amount' => 1500.00,
            'sender' => 'JOHN DOE',
            'transaction_code' => 'XYZ987ABC',
            'message' => 'Ksh 1,500.00 received...',
            'status' => 'unmapped',
        ]);

        $response = $this->actingAs($this->member)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Pending Verification (1)');
        $response->assertSee('XYZ987ABC');
        $response->assertSee('KES 1,500.00');
    }
}
