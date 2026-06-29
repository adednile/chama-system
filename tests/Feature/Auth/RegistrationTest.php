<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $chama = \App\Models\Chama::create([
            'name' => 'Seeded Chama Name',
            'location' => 'Mombasa',
        ]);

        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewHas('chamas');
        $response->assertSee('Seeded Chama Name');
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
