<?php

namespace Tests\Feature;

use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_a_booking_for_its_own_patient(): void
    {
        [$client, $caregiver] = $this->createClientAndCaregiver();
        $patient = $this->createPatient($client);

        $response = $this->actingAs($client)->post(route('bookings.store'), $this->bookingPayload($caregiver, $patient));

        $response->assertRedirect(route('bookings.index'));
        $this->assertDatabaseHas('bookings', [
            'user_id' => $client->id,
            'caregiver_id' => $caregiver->id,
            'patient_id' => $patient->id,
            'status' => 'pending',
        ]);
    }

    public function test_client_cannot_create_a_booking_for_another_users_patient(): void
    {
        [$client, $caregiver] = $this->createClientAndCaregiver();
        $otherClient = User::factory()->create(['role' => 'client']);
        $otherPatient = $this->createPatient($otherClient);

        $response = $this->actingAs($client)
            ->from(route('bookings.index'))
            ->post(route('bookings.store'), $this->bookingPayload($caregiver, $otherPatient));

        $response
            ->assertRedirect(route('bookings.index'))
            ->assertSessionHasErrors('patient_id');
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_client_cannot_open_a_booking_form_for_an_unavailable_caregiver(): void
    {
        [$client, $caregiver] = $this->createClientAndCaregiver(isAvailable: false);
        $this->createPatient($client);

        $this->actingAs($client)
            ->get(route('bookings.create', $caregiver))
            ->assertRedirect(route('caregivers.index'))
            ->assertSessionHas('error');
    }

    public function test_client_cannot_submit_a_booking_for_an_unverified_caregiver(): void
    {
        [$client, $caregiver] = $this->createClientAndCaregiver(isVerified: false);
        $patient = $this->createPatient($client);

        $this->actingAs($client)
            ->from(route('caregivers.index'))
            ->post(route('bookings.store'), $this->bookingPayload($caregiver, $patient))
            ->assertRedirect(route('caregivers.index'))
            ->assertSessionHasErrors('caregiver_id');

        $this->assertDatabaseCount('bookings', 0);
    }

    /** @return array{User, Caregiver} */
    private function createClientAndCaregiver(bool $isVerified = true, bool $isAvailable = true): array
    {
        $client = User::factory()->create(['role' => 'client']);
        $caregiverUser = User::factory()->create(['role' => 'caregiver']);
        $caregiver = Caregiver::create([
            'user_id' => $caregiverUser->id,
            'specialization' => 'Perawatan lansia',
            'price_per_day' => 150000,
            'gender' => 'Perempuan',
            'is_verified' => $isVerified,
            'is_available' => $isAvailable,
        ]);

        return [$client, $caregiver];
    }

    private function createPatient(User $user): Patient
    {
        return Patient::create([
            'user_id' => $user->id,
            'full_name' => 'Pasien Uji',
            'gender' => 'Perempuan',
            'birth_date' => '1950-01-01',
            'health_condition' => 'Memerlukan pendampingan',
            'emergency_contact' => '081234567890',
        ]);
    }

    /** @return array<string, int|string> */
    private function bookingPayload(Caregiver $caregiver, Patient $patient): array
    {
        return [
            'caregiver_id' => $caregiver->id,
            'patient_id' => $patient->id,
            'start_date' => now()->addDay()->toDateString(),
            'total_days' => 1,
        ];
    }
}
