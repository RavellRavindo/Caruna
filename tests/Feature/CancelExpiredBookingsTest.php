<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelExpiredBookingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_cancels_only_bookings_that_are_still_expired_in_the_same_status(): void
    {
        $pendingBooking = $this->createBooking('pending');
        $approvedBooking = $this->createBooking('approved');
        $activeBooking = $this->createBooking('approved');

        Booking::whereKey($pendingBooking->id)->update([
            'created_at' => now()->subHours(13),
        ]);
        Booking::whereKey($approvedBooking->id)->update([
            'updated_at' => now()->subHours(13),
        ]);

        $this->artisan('app:cancel-expired-bookings')
            ->assertExitCode(0);

        $this->assertDatabaseHas('bookings', ['id' => $pendingBooking->id, 'status' => 'canceled']);
        $this->assertDatabaseHas('bookings', ['id' => $approvedBooking->id, 'status' => 'canceled']);
        $this->assertDatabaseHas('bookings', ['id' => $activeBooking->id, 'status' => 'approved']);
    }

    private function createBooking(string $status): Booking
    {
        $client = User::factory()->create(['role' => 'client']);
        $caregiverUser = User::factory()->create(['role' => 'caregiver']);
        $caregiver = Caregiver::create([
            'user_id' => $caregiverUser->id,
            'specialization' => 'Perawatan lansia',
            'price_per_day' => 150000,
            'gender' => 'Perempuan',
        ]);
        $patient = Patient::create([
            'user_id' => $client->id,
            'full_name' => 'Pasien Uji',
            'gender' => 'Perempuan',
            'birth_date' => '1950-01-01',
            'health_condition' => 'Memerlukan pendampingan',
            'emergency_contact' => '081234567890',
        ]);

        return Booking::create([
            'user_id' => $client->id,
            'caregiver_id' => $caregiver->id,
            'patient_id' => $patient->id,
            'start_date' => now()->addDay()->toDateString(),
            'total_days' => 1,
            'snapshot_price' => 150000,
            'total_amount' => 150000,
            'status' => $status,
        ]);
    }
}
