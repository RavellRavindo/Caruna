<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentReconciliationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_record_a_completed_manual_refund(): void
    {
        $payment = $this->createPaymentRequiringRefund();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.payments.reconciliation.index'))
            ->patch(route('admin.payments.reconciliation.refunded', $payment), [
                'refund_reference' => 'MIDTRANS-REF-20260910-001',
            ])
            ->assertRedirect(route('admin.payments.reconciliation.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'reconciliation_status' => Payment::RECONCILIATION_REFUNDED,
            'refund_reference' => 'MIDTRANS-REF-20260910-001',
        ]);
    }

    public function test_refund_reference_is_required_when_admin_closes_reconciliation(): void
    {
        $payment = $this->createPaymentRequiringRefund();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.payments.reconciliation.index'))
            ->patch(route('admin.payments.reconciliation.refunded', $payment), [])
            ->assertRedirect(route('admin.payments.reconciliation.index'))
            ->assertSessionHasErrors('refund_reference');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'reconciliation_status' => Payment::RECONCILIATION_REFUND_REQUIRED,
        ]);
    }

    private function createPaymentRequiringRefund(): Payment
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
            'status' => 'canceled',
        ]);

        return Payment::create([
            'booking_id' => $booking->id,
            'amount' => 150000,
            'payment_method' => 'midtrans',
            'status' => 'success',
            'order_id' => 'CARUNA-'.$booking->id.'-refund-test',
            'reconciliation_status' => Payment::RECONCILIATION_REFUND_REQUIRED,
        ]);
    }
}
