<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_owner_can_review_a_completed_service(): void
    {
        [$client, $caregiver, $booking] = $this->createBooking();

        $this->actingAs($client)
            ->post(route('reviews.store', $booking), [
                'rating' => 4,
                'comment' => 'Caregiver datang tepat waktu dan sangat membantu.',
            ])
            ->assertRedirect(route('bookings.index'));

        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'user_id' => $client->id,
            'caregiver_id' => $caregiver->id,
            'rating' => 4,
            'comment' => 'Caregiver datang tepat waktu dan sangat membantu.',
        ]);
    }

    public function test_review_is_only_allowed_for_completed_bookings(): void
    {
        [$client, $caregiver, $booking] = $this->createBooking('ongoing');

        $this->actingAs($client)
            ->from(route('bookings.index'))
            ->post(route('reviews.store', $booking), ['rating' => 5])
            ->assertRedirect(route('bookings.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_another_client_cannot_review_someone_elses_booking(): void
    {
        [$client, $caregiver, $booking] = $this->createBooking();
        $otherClient = User::factory()->create(['role' => 'client']);

        $this->actingAs($otherClient)
            ->from(route('bookings.index'))
            ->post(route('reviews.store', $booking), ['rating' => 5])
            ->assertRedirect(route('bookings.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_a_booking_can_only_have_one_review(): void
    {
        [$client, $caregiver, $booking] = $this->createBooking();

        $this->actingAs($client)
            ->post(route('reviews.store', $booking), ['rating' => 5])
            ->assertRedirect(route('bookings.index'));

        $this->actingAs($client)
            ->from(route('bookings.index'))
            ->post(route('reviews.store', $booking), ['rating' => 1])
            ->assertRedirect(route('bookings.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'rating' => 5,
        ]);
    }

    public function test_caregiver_profile_displays_rating_from_reviews(): void
    {
        [$client, $caregiver, $booking] = $this->createBooking();

        Review::create([
            'booking_id' => $booking->id,
            'user_id' => $client->id,
            'caregiver_id' => $caregiver->id,
            'rating' => 4,
        ]);

        $this->actingAs($client)
            ->get(route('caregivers.show', $caregiver))
            ->assertOk()
            ->assertSee('4.0')
            ->assertSee('1 Ulasan');
    }

    /** @return array{User, Caregiver, Booking} */
    private function createBooking(string $status = 'completed'): array
    {
        $client = User::factory()->create(['role' => 'client']);
        $caregiverUser = User::factory()->create(['role' => 'caregiver']);
        $caregiver = Caregiver::create([
            'user_id' => $caregiverUser->id,
            'specialization' => 'Perawatan lansia',
            'price_per_day' => 150000,
            'gender' => 'Perempuan',
            'is_verified' => true,
            'is_available' => true,
        ]);
        $patient = Patient::create([
            'user_id' => $client->id,
            'full_name' => 'Pasien Uji',
            'gender' => 'Perempuan',
            'birth_date' => '1950-01-01',
            'health_condition' => 'Memerlukan pendampingan',
            'emergency_contact' => '081234567890',
        ]);
        $booking = Booking::create([
            'user_id' => $client->id,
            'caregiver_id' => $caregiver->id,
            'patient_id' => $patient->id,
            'service_address' => 'Jl. Pengujian No. 1, Jakarta',
            'start_date' => now()->addDay()->toDateString(),
            'total_days' => 1,
            'snapshot_price' => 150000,
            'total_amount' => 150000,
            'status' => $status,
        ]);

        return [$client, $caregiver, $booking];
    }
}
