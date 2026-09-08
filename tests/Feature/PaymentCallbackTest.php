<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentCallbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.midtrans.server_key' => 'test-server-key']);
    }

    public function test_settlement_marks_only_an_approved_booking_as_paid(): void
    {
        [$booking, $payment] = $this->createBookingAndPayment();

        $response = $this->postJson('/midtrans-callback', $this->callbackPayload($payment));

        $response->assertOk();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'paid']);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'success',
            'midtrans_status' => 'settlement',
        ]);

        // Midtrans can deliver the same notification more than once.
        $this->postJson('/midtrans-callback', $this->callbackPayload($payment))->assertOk();
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'paid']);
    }

    public function test_callback_with_a_different_signed_amount_is_rejected(): void
    {
        [$booking, $payment] = $this->createBookingAndPayment();
        $payload = $this->callbackPayload($payment, ['gross_amount' => '150001.00']);

        $this->postJson('/midtrans-callback', $payload)->assertUnprocessable();

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'approved']);
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'pending']);
    }

    public function test_successful_card_capture_without_fraud_status_is_accepted(): void
    {
        [$booking, $payment] = $this->createBookingAndPayment();

        $this->postJson('/midtrans-callback', $this->callbackPayload($payment, [
            'transaction_status' => 'capture',
        ]))->assertOk();

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'paid']);
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'success']);
    }

    public function test_late_callback_cannot_regress_a_completed_booking(): void
    {
        [$booking, $payment] = $this->createBookingAndPayment('completed');

        $this->postJson('/midtrans-callback', $this->callbackPayload($payment))->assertOk();

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'completed']);
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'success']);
    }

    public function test_pending_callback_does_not_change_booking_to_pending(): void
    {
        [$booking, $payment] = $this->createBookingAndPayment();

        $this->postJson('/midtrans-callback', $this->callbackPayload($payment, [
            'transaction_status' => 'pending',
            'status_code' => '201',
        ]))->assertOk();

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'approved']);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'pending',
            'midtrans_status' => 'pending',
        ]);
    }

    public function test_signed_unknown_notification_is_acknowledged_without_changing_data(): void
    {
        $payload = [
            'order_id' => 'payment_notif_test_M793226842_b93b5f45-859c-4650-a188-46b681c6f6be',
            'status_code' => '200',
            'gross_amount' => '1050000.00',
            'transaction_status' => 'settlement',
        ];
        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('services.midtrans.server_key'),
        );

        $this->post('/midtrans-callback', $payload)
            ->assertOk()
            ->assertJson(['message' => 'Notification acknowledged']);

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_invalid_payload_returns_json_instead_of_redirecting(): void
    {
        $this->post('/midtrans-callback', [])
            ->assertUnprocessable()
            ->assertJson(['message' => 'Invalid notification payload']);
    }

    /** @return array{Booking, Payment} */
    private function createBookingAndPayment(string $bookingStatus = 'approved'): array
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
            'status' => $bookingStatus,
        ]);
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => 150000,
            'payment_method' => 'midtrans',
            'status' => 'pending',
            'order_id' => 'CARUNA-'.$booking->id.'-testpayment000001',
        ]);

        return [$booking, $payment];
    }

    /** @param array<string, string> $overrides */
    private function callbackPayload(Payment $payment, array $overrides = []): array
    {
        $payload = array_merge([
            'order_id' => $payment->order_id,
            'status_code' => '200',
            'gross_amount' => '150000.00',
            'transaction_status' => 'settlement',
            'transaction_id' => 'transaction-'.$payment->id,
        ], $overrides);

        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('services.midtrans.server_key'),
        );

        return $payload;
    }
}
