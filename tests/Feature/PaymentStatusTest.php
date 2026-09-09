<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_owner_can_poll_a_confirmed_payment(): void
    {
        [$client, $booking] = $this->createBooking('paid');
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => 150000,
            'payment_method' => 'midtrans',
            'status' => 'success',
            'midtrans_status' => 'settlement',
            'order_id' => 'CARUNA-'.$booking->id.'-status-test-paid',
        ]);

        $this->actingAs($client)
            ->getJson(route('bookings.payment.status', $booking))
            ->assertOk()
            ->assertJson([
                'booking_status' => 'paid',
                'payment_status' => 'success',
                'midtrans_status' => 'settlement',
                'payment_confirmed' => true,
            ]);
    }

    public function test_client_cannot_poll_another_clients_booking(): void
    {
        [, $booking] = $this->createBooking();
        $otherClient = User::factory()->create(['role' => 'client']);

        $this->actingAs($otherClient)
            ->getJson(route('bookings.payment.status', $booking))
            ->assertNotFound();
    }

    public function test_payment_success_page_waits_for_the_callback(): void
    {
        [$client, $booking] = $this->createBooking();

        $this->actingAs($client)
            ->get(route('bookings.payment.success', $booking))
            ->assertOk()
            ->assertViewIs('bookings.payment-success')
            ->assertSee('Memverifikasi pembayaran');
    }

    public function test_payment_success_page_redirects_when_payment_is_already_confirmed(): void
    {
        [$client, $booking] = $this->createBooking('paid');

        $this->actingAs($client)
            ->get(route('bookings.payment.success', $booking))
            ->assertRedirect(route('bookings.index'))
            ->assertSessionHas('success', 'Pembayaran telah dikonfirmasi.');
    }

    public function test_booking_owner_can_see_when_a_late_payment_requires_a_refund(): void
    {
        [$client, $booking] = $this->createBooking('canceled');
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => 150000,
            'payment_method' => 'midtrans',
            'status' => 'success',
            'midtrans_status' => 'settlement',
            'reconciliation_status' => Payment::RECONCILIATION_REFUND_REQUIRED,
            'order_id' => 'CARUNA-'.$booking->id.'-status-test-refund',
        ]);

        $this->actingAs($client)
            ->getJson(route('bookings.payment.status', $booking))
            ->assertOk()
            ->assertJson([
                'booking_status' => 'canceled',
                'payment_status' => 'success',
                'reconciliation_status' => Payment::RECONCILIATION_REFUND_REQUIRED,
                'payment_confirmed' => false,
            ]);
    }

    public function test_expired_approved_booking_cannot_open_a_new_payment(): void
    {
        [$client, $booking] = $this->createBooking();
        Booking::whereKey($booking->id)->update([
            'updated_at' => now()->subHours(13),
        ]);

        $this->actingAs($client)
            ->get(route('bookings.payment', $booking))
            ->assertRedirect(route('bookings.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'canceled',
        ]);
        $this->assertDatabaseCount('payments', 0);
    }

    /** @return array{User, Booking} */
    private function createBooking(string $status = 'approved'): array
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

        $booking = Booking::create([
            'user_id' => $client->id,
            'caregiver_id' => $caregiver->id,
            'patient_id' => $patient->id,
            'start_date' => now()->addDay()->toDateString(),
            'total_days' => 1,
            'snapshot_price' => 150000,
            'total_amount' => 150000,
            'status' => $status,
        ]);

        return [$client, $booking];
    }
}
