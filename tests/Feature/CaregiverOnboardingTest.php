<?php

namespace Tests\Feature;

use App\Models\Caregiver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaregiverOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_caregiver_cannot_access_operational_pages_or_public_profile(): void
    {
        [$caregiverUser, $caregiver] = $this->createPendingCaregiver();
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($caregiverUser)
            ->get(route('caregiver.wallet'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($client)
            ->get(route('caregivers.show', $caregiver))
            ->assertNotFound();
    }

    public function test_admin_can_verify_a_pending_caregiver_and_activate_the_account(): void
    {
        [$caregiverUser, $caregiver] = $this->createPendingCaregiver();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.caregivers.index'))
            ->patch(route('admin.caregivers.verification.update', $caregiver), [
                'verification_status' => Caregiver::VERIFICATION_VERIFIED,
            ])
            ->assertRedirect(route('admin.caregivers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('caregivers', [
            'id' => $caregiver->id,
            'verification_status' => Caregiver::VERIFICATION_VERIFIED,
            'is_verified' => true,
            'is_available' => true,
        ]);

        $this->actingAs($caregiverUser)
            ->get(route('caregiver.wallet'))
            ->assertOk();
    }

    public function test_admin_can_reject_a_pending_caregiver_with_a_reason(): void
    {
        [, $caregiver] = $this->createPendingCaregiver();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.caregivers.index'))
            ->patch(route('admin.caregivers.verification.update', $caregiver), [
                'verification_status' => Caregiver::VERIFICATION_REJECTED,
                'rejection_reason' => 'Data pengalaman kerja belum lengkap.',
            ])
            ->assertRedirect(route('admin.caregivers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('caregivers', [
            'id' => $caregiver->id,
            'verification_status' => Caregiver::VERIFICATION_REJECTED,
            'rejection_reason' => 'Data pengalaman kerja belum lengkap.',
            'is_verified' => false,
            'is_available' => false,
        ]);
    }

    /** @return array{User, Caregiver} */
    private function createPendingCaregiver(): array
    {
        $user = User::factory()->create(['role' => 'caregiver']);
        $caregiver = Caregiver::create([
            'user_id' => $user->id,
            'specialization' => 'Perawatan lansia',
            'price_per_day' => 150000,
            'experience_years' => 3,
            'gender' => 'Perempuan',
            'is_available' => false,
            'is_verified' => false,
            'verification_status' => Caregiver::VERIFICATION_PENDING,
        ]);

        return [$user, $caregiver];
    }
}
