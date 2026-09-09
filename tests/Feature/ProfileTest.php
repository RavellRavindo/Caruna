<?php

namespace Tests\Feature;

use App\Models\Caregiver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone_number' => '081234567890',
                'address' => 'Jl. Pengujian No. 1, Jakarta',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('081234567890', $user->phone_number);
        $this->assertSame('Jl. Pengujian No. 1, Jakarta', $user->address);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_caregiver_can_update_only_their_own_daily_rate(): void
    {
        $caregiverUser = User::factory()->create(['role' => 'caregiver']);
        $caregiver = Caregiver::create([
            'user_id' => $caregiverUser->id,
            'specialization' => 'Perawatan lansia',
            'price_per_day' => 150000,
            'experience_years' => 3,
            'gender' => 'Perempuan',
            'is_available' => true,
            'is_verified' => true,
        ]);

        $this->actingAs($caregiverUser)
            ->patch(route('profile.caregiver-rate.update'), [
                'price_per_day' => 225000,
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'caregiver-rate-updated');

        $this->assertDatabaseHas('caregivers', [
            'id' => $caregiver->id,
            'price_per_day' => 225000,
        ]);
    }

    public function test_daily_rate_must_be_a_positive_integer(): void
    {
        $caregiverUser = User::factory()->create(['role' => 'caregiver']);
        Caregiver::create([
            'user_id' => $caregiverUser->id,
            'specialization' => 'Perawatan lansia',
            'price_per_day' => 150000,
            'experience_years' => 3,
            'gender' => 'Perempuan',
            'is_available' => true,
            'is_verified' => true,
        ]);

        $this->actingAs($caregiverUser)
            ->from(route('profile.edit'))
            ->patch(route('profile.caregiver-rate.update'), [
                'price_per_day' => 0,
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('price_per_day');
    }

    public function test_client_cannot_update_a_caregiver_daily_rate(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)
            ->patch(route('profile.caregiver-rate.update'), [
                'price_per_day' => 225000,
            ])
            ->assertForbidden();
    }
}
