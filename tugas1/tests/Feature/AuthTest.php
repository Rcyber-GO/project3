<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_user_can_login_and_see_dashboard(): void
    {
        User::factory()->create([
            'username' => 'budi',
            'password' => 'password123',
            'nama_lengkap' => 'Budi Santoso',
        ]);

        $response = $this->post('/login', [
            'username' => 'budi',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Selamat datang, Budi Santoso!');
    }

    public function test_login_with_wrong_credentials_shows_error(): void
    {
        User::factory()->create([
            'username' => 'budi',
            'password' => 'password123',
            'nama_lengkap' => 'Budi Santoso',
        ]);

        $this->from('/login')
            ->post('/login', [
                'username' => 'budi',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('login');
    }

    public function test_seeded_users_have_hashed_passwords(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 2);
        $this->assertTrue(Hash::check('budi123', User::where('username', 'budi')->value('password')));
        $this->assertTrue(Hash::check('siti123', User::where('username', 'siti')->value('password')));
    }

    public function test_authenticated_user_can_logout_and_session_is_invalidated(): void
    {
        $user = User::factory()->create([
            'username' => 'budi',
            'password' => 'password123',
        ]);

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_user_visiting_login_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect('/dashboard');
    }
}
