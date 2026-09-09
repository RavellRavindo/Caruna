<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredBookings extends Command
{
    // Nama perintah yang akan dipanggil
    protected $signature = 'app:cancel-expired-bookings';

    // Deskripsi tugas robot
    protected $description = 'Membatalkan pesanan pending (belum direspon) & approved (belum dibayar) yang melewati batas waktu 12 jam';

    public function handle(): int
    {
        $cutoff = now()->subHours(12);

        // KONDISI 1: Pesanan PENDING (Perawat belum respon selama 12 jam sejak dibuat)
        $expiredPending = Booking::where('status', 'pending')
            ->where('created_at', '<', $cutoff)
            ->pluck('id');

        $countPending = $this->cancelBookings($expiredPending, 'pending', 'created_at', $cutoff);

        // KONDISI 2: Pesanan APPROVED (Klien belum bayar selama 12 jam sejak statusnya disetujui)
        // Kita menggunakan 'updated_at' karena kita menghitung 12 jam sejak perawat menekan tombol "Terima"
        $expiredApproved = Booking::where('status', 'approved')
            ->where('updated_at', '<', $cutoff)
            ->pluck('id');

        $countApproved = $this->cancelBookings($expiredApproved, 'approved', 'updated_at', $cutoff);

        // Hitung total dan berikan laporan ke terminal
        $total = $countPending + $countApproved;
        $this->info("Pembersihan selesai! {$countPending} dibatalkan (Perawat AFK) dan {$countApproved} dibatalkan (Klien telat bayar). Total: {$total}");

        return self::SUCCESS;
    }

    /**
     * Re-check each candidate under a row lock so a callback or another request
     * cannot overwrite a newer booking state.
     *
     * @param  iterable<int>  $bookingIds
     */
    private function cancelBookings(iterable $bookingIds, string $status, string $timestampColumn, \DateTimeInterface $cutoff): int
    {
        $cancelled = 0;

        foreach ($bookingIds as $bookingId) {
            $wasCancelled = DB::transaction(function () use ($bookingId, $status, $timestampColumn, $cutoff): bool {
                $booking = Booking::lockForUpdate()->find($bookingId);

                if (! $booking || $booking->status !== $status || $booking->{$timestampColumn}->greaterThanOrEqualTo($cutoff)) {
                    return false;
                }

                $booking->update(['status' => 'canceled']);

                return true;
            });

            $cancelled += (int) $wasCancelled;
        }

        return $cancelled;
    }
}
