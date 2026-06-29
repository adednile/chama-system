<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_user_sees_member_dashboard(): void
    {
        $chama = \App\Models\Chama::create([
            'name' => 'Gold Chama',
        ]);
        $user = User::factory()->create([
            'role' => 'member',
            'chama_id' => $chama->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Member Dashboard');
    }

    public function test_treasurer_user_sees_treasurer_dashboard(): void
    {
        $chama = \App\Models\Chama::create([
            'name' => 'Gold Chama',
        ]);
        $user = User::factory()->create([
            'role' => 'treasurer',
            'chama_id' => $chama->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Treasurer Dashboard');
    }
}
