<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class CancelExpiredBookings extends Command
{
    // Nama perintah yang akan dipanggil
    protected $signature = 'app:cancel-expired-bookings';

    // Deskripsi tugas robot
    protected $description = 'Membatalkan pesanan pending (belum direspon) & approved (belum dibayar) yang melewati batas waktu 12 jam';

    public function handle()
    {
        // KONDISI 1: Pesanan PENDING (Perawat belum respon selama 12 jam sejak dibuat)
        $expiredPending = Booking::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(12))
            ->get();

        $countPending = 0;
        foreach ($expiredPending as $booking) {
            $booking->update(['status' => 'canceled']);
            $countPending++;
        }

        // KONDISI 2: Pesanan APPROVED (Klien belum bayar selama 12 jam sejak statusnya disetujui)
        // Kita menggunakan 'updated_at' karena kita menghitung 12 jam sejak perawat menekan tombol "Terima"
        $expiredApproved = Booking::where('status', 'approved')
            ->where('updated_at', '<', now()->subHours(12))
            ->get();

        $countApproved = 0;
        foreach ($expiredApproved as $booking) {
            $booking->update(['status' => 'canceled']);
            $countApproved++;
        }

        // Hitung total dan berikan laporan ke terminal
        $total = $countPending + $countApproved;
        $this->info("Pembersihan selesai! {$countPending} dibatalkan (Perawat AFK) dan {$countApproved} dibatalkan (Klien telat bayar). Total: {$total}");
    }
}