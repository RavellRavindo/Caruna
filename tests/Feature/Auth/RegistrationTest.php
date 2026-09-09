<?php

namespace Tests\Feature\Auth;

use App\Models\Caregiver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'client',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'client',
        ]);
        $this->assertDatabaseMissing('caregivers', [
            'user_id' => User::where('email', 'test@example.com')->value('id'),
        ]);
    }

    public function test_caregiver_registration_creates_a_pending_caregiver_profile(): void
    {
        $response = $this->post('/register', [
            'name' => 'Caregiver Baru',
            'email' => 'caregiver@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'caregiver',
            'specialization' => 'Perawatan lansia',
            'price_per_day' => 175000,
            'experience_years' => 4,
            'gender' => 'Perempuan',
            'about_me' => 'Berpengalaman mendampingi lansia.',
        ]);

        $user = User::where('email', 'caregiver@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('caregivers', [
            'user_id' => $user->id,
            'specialization' => 'Perawatan lansia',
            'verification_status' => Caregiver::VERIFICATION_PENDING,
            'is_verified' => false,
            'is_available' => false,
        ]);
    }
}
