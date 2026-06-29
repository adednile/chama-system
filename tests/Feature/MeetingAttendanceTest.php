<?php

namespace Tests\Feature;

use App\Models\Chama;
use App\Models\User;
use App\Models\Meeting;
use App\Models\Attendance;
use App\Services\CreditScoringEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private Chama $chama;
    private User $treasurer;
    private User $member1;
    private User $member2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chama = Chama::create([
            'name' => 'Testing Chama',
            'min_credit_score' => 6.0,
            'savings_weight' => 0.4,
            'attendance_weight' => 0.3,
            'repayment_weight' => 0.3,
        ]);

        $this->treasurer = User::factory()->create([
            'role' => 'treasurer',
            'chama_id' => $this->chama->id,
        ]);

        $this->member1 = User::factory()->create([
            'name' => 'Member One',
            'role' => 'member',
            'chama_id' => $this->chama->id,
        ]);

        $this->member2 = User::factory()->create([
            'name' => 'Member Two',
            'role' => 'member',
            'chama_id' => $this->chama->id,
        ]);
    }

    public function test_treasurer_can_view_meetings_index(): void
    {
        $meeting = Meeting::create([
            'chama_id' => $this->chama->id,
            'meeting_date' => now()->toDateString(),
            'meeting_type' => 'regular',
            'notes' => 'Discuss budgeting',
        ]);

        $response = $this->actingAs($this->treasurer)->get('/treasurer/meetings');

        $response->assertStatus(200);
        $response->assertSee('Discuss budgeting');
        $response->assertSee('Meetings Management');
    }

    public function test_treasurer_can_create_meeting(): void
    {
        $response = $this->actingAs($this->treasurer)->post('/treasurer/meetings', [
            'meeting_date' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'meeting_type' => 'agm',
            'notes' => 'Annual general planning',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('meetings', [
            'chama_id' => $this->chama->id,
            'meeting_type' => 'agm',
            'notes' => 'Annual general planning',
        ]);

        $meeting = Meeting::where('meeting_type', 'agm')->first();
        // Check that attendance records were initialized
        $this->assertDatabaseHas('attendances', [
            'meeting_id' => $meeting->id,
            'user_id' => $this->member1->id,
            'present' => false,
        ]);
    }

    public function test_treasurer_can_view_and_save_attendance(): void
    {
        $meeting = Meeting::create([
            'chama_id' => $this->chama->id,
            'meeting_date' => now()->toDateString(),
            'meeting_type' => 'special',
        ]);

        // Access attendance checklist page
        $response = $this->actingAs($this->treasurer)->get("/treasurer/meetings/{$meeting->id}/attendance");
        $response->assertStatus(200);
        $response->assertSee($this->member1->name);

        // Save attendance marking Member 1 present, Member 2 absent
        $response = $this->actingAs($this->treasurer)->post("/treasurer/meetings/{$meeting->id}/attendance", [
            'present' => [$this->member1->id]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue(Attendance::where('meeting_id', $meeting->id)->where('user_id', $this->member1->id)->first()->present);
        $this->assertFalse(Attendance::where('meeting_id', $meeting->id)->where('user_id', $this->member2->id)->first()->present);
    }

    public function test_member_can_view_personal_attendance_statement(): void
    {
        $meeting1 = Meeting::create([
            'chama_id' => $this->chama->id,
            'meeting_date' => now()->subDays(5)->toDateString(),
            'meeting_type' => 'regular',
        ]);

        $meeting2 = Meeting::create([
            'chama_id' => $this->chama->id,
            'meeting_date' => now()->subDays(2)->toDateString(),
            'meeting_type' => 'regular',
        ]);

        Attendance::create(['meeting_id' => $meeting1->id, 'user_id' => $this->member1->id, 'present' => true]);
        Attendance::create(['meeting_id' => $meeting2->id, 'user_id' => $this->member1->id, 'present' => false]);

        $response = $this->actingAs($this->member1)->get('/member/attendance');

        $response->assertStatus(200);
        $response->assertSee('50%'); // 1 out of 2 meetings attended
        $response->assertSee('Personal Tracking');
    }

    public function test_credit_scoring_engine_incorporates_attendance(): void
    {
        $meeting1 = Meeting::create([
            'chama_id' => $this->chama->id,
            'meeting_date' => now()->subDays(5)->toDateString(),
            'meeting_type' => 'regular',
        ]);

        $meeting2 = Meeting::create([
            'chama_id' => $this->chama->id,
            'meeting_date' => now()->subDays(2)->toDateString(),
            'meeting_type' => 'regular',
        ]);

        // Case 1: Member 1 has 100% attendance (2/2)
        Attendance::create(['meeting_id' => $meeting1->id, 'user_id' => $this->member1->id, 'present' => true]);
        Attendance::create(['meeting_id' => $meeting2->id, 'user_id' => $this->member1->id, 'present' => true]);

        // Case 2: Member 2 has 50% attendance (1/2)
        Attendance::create(['meeting_id' => $meeting1->id, 'user_id' => $this->member2->id, 'present' => true]);
        Attendance::create(['meeting_id' => $meeting2->id, 'user_id' => $this->member2->id, 'present' => false]);

        $engine = new CreditScoringEngine();
        $score1 = $engine->calculateScore($this->member1);
        $score2 = $engine->calculateScore($this->member2);

        // Since Member 1 attended all meetings, their score should be higher than Member 2,
        // holding all other metrics (membership duration = 0, no repayment, no savings) equal.
        $this->assertGreaterThan($score2, $score1);
    }

    public function test_treasurer_can_postpone_meeting(): void
    {
        $meeting = Meeting::create([
            'chama_id' => $this->chama->id,
            'meeting_date' => now()->toDateString(),
            'meeting_type' => 'regular',
        ]);

        $newDate = now()->addDays(10)->format('Y-m-d\TH:i');

        $response = $this->actingAs($this->treasurer)->patch("/treasurer/meetings/{$meeting->id}", [
            'meeting_date' => $newDate,
            'meeting_type' => 'agm',
            'notes' => 'Postponed due to holiday',
        ]);

        $response->assertRedirect();
        $this->assertEquals('agm', $meeting->fresh()->meeting_type);
        $this->assertEquals('Postponed due to holiday', $meeting->fresh()->notes);
    }

    public function test_treasurer_can_cancel_meeting(): void
    {
        $meeting = Meeting::create([
            'chama_id' => $this->chama->id,
            'meeting_date' => now()->toDateString(),
            'meeting_type' => 'regular',
        ]);

        Attendance::create(['meeting_id' => $meeting->id, 'user_id' => $this->member1->id, 'present' => true]);

        $response = $this->actingAs($this->treasurer)->delete("/treasurer/meetings/{$meeting->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('meetings', ['id' => $meeting->id]);
        $this->assertDatabaseMissing('attendances', ['meeting_id' => $meeting->id]);
    }
}
