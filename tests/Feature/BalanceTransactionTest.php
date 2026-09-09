<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Caregiver;
use App\Models\Patient;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BalanceTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_withdrawal_submission_is_processed_only_once(): void
    {
        [, $caregiverUser, $caregiver] = $this->createUsersAndCaregiver(200000);
        $payload = [
            'amount' => 50000,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Caregiver Uji',
            'idempotency_key' => (string) Str::uuid(),
        ];

        $this->actingAs($caregiverUser)
            ->post(route('withdrawals.store'), $payload)
            ->assertSessionHas('success');

        $this->actingAs($caregiverUser)
            ->post(route('withdrawals.store'), $payload)
            ->assertSessionHas('error');

        $this->assertDatabaseHas('caregivers', ['id' => $caregiver->id, 'balance' => 150000]);
        $this->assertDatabaseCount('withdrawals', 1);
        $this->assertDatabaseCount('wallet_transactions', 1);
    }

    public function test_rejected_withdrawal_is_refunded_only_once(): void
    {
        [$admin, $caregiverUser, $caregiver] = $this->createUsersAndCaregiver(150000);
        $withdrawal = Withdrawal::create([
            'caregiver_id' => $caregiver->id,
            'amount' => 50000,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Caregiver Uji',
            'idempotency_key' => (string) Str::uuid(),
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.withdrawals.reject', $withdrawal))
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->post(route('admin.withdrawals.reject', $withdrawal))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('withdrawals', ['id' => $withdrawal->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('caregivers', ['id' => $caregiver->id, 'balance' => 200000]);
        $this->assertDatabaseCount('wallet_transactions', 1);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $caregiverUser->id,
            'type' => 'credit',
            'amount' => 50000,
            'reference_id' => (string) $withdrawal->id,
        ]);
    }

    public function test_approved_withdrawal_cannot_later_be_refunded(): void
    {
        [$admin, , $caregiver] = $this->createUsersAndCaregiver(150000);
        $withdrawal = Withdrawal::create([
            'caregiver_id' => $caregiver->id,
            'amount' => 50000,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Caregiver Uji',
            'idempotency_key' => (string) Str::uuid(),
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.withdrawals.approve', $withdrawal))
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->post(route('admin.withdrawals.reject', $withdrawal))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('withdrawals', ['id' => $withdrawal->id, 'status' => 'approved']);
        $this->assertDatabaseHas('caregivers', ['id' => $caregiver->id, 'balance' => 150000]);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_completed_booking_credits_caregiver_only_once(): void
    {
        [, $caregiverUser, $caregiver] = $this->createUsersAndCaregiver(0);
        $client = User::factory()->create(['role' => 'client']);
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
            'snapshot_price' => 100000,
            'total_amount' => 100000,
            'status' => 'waiting_confirmation',
        ]);

        $this->actingAs($client)
            ->patch(route('bookings.confirm_finish', $booking))
            ->assertRedirect(route('bookings.index'));

        $this->actingAs($client)
            ->patch(route('bookings.confirm_finish', $booking))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'completed']);
        $this->assertDatabaseHas('caregivers', ['id' => $caregiver->id, 'balance' => 90000]);
        $this->assertDatabaseCount('wallet_transactions', 1);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $caregiverUser->id,
            'type' => 'credit',
            'amount' => 90000,
            'reference_id' => (string) $booking->id,
        ]);
    }

    /** @return array{User, User, Caregiver} */
    private function createUsersAndCaregiver(int $balance): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $caregiverUser = User::factory()->create(['role' => 'caregiver']);
        $caregiver = Caregiver::create([
            'user_id' => $caregiverUser->id,
            'specialization' => 'Perawatan lansia',
            'price_per_day' => 100000,
            'gender' => 'Perempuan',
            'balance' => $balance,
            'is_verified' => true,
            'is_available' => true,
        ]);

        return [$admin, $caregiverUser, $caregiver];
    }
}
