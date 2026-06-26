<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Caregiver; // Import Model Caregiver
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import DB untuk transaksi

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        // 1. Validasi Input Klien
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // 2. Keamanan: Pastikan pesanan ini milik klien & status selesai
        if ($booking->user_id !== Auth::id() || $booking->status !== 'completed') {
            return back()->with('error', 'Aksi tidak diizinkan.');
        }

        // 3. Cegah Ulasan Ganda
        if ($booking->review) {
            return back()->with('error', 'Anda sudah memberikan ulasan.');
        }

        try {
            // Gunakan Transaction agar jika salah satu gagal, semua dibatalkan
            DB::transaction(function () use ($request, $booking) {
                
                // 4. Simpan ke Tabel Reviews
                Review::create([
                    'booking_id' => $booking->id,
                    'user_id' => Auth::id(),
                    'caregiver_id' => $booking->caregiver_id,
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                ]);

                // 5. HITUNG ULANG RATA-RATA (Logika Sinkronisasi)
                $caregiver = Caregiver::lockForUpdate()->find($booking->caregiver_id);
                
                // Ambil semua review perawat ini dan hitung rata-ratanya
                $stats = Review::where('caregiver_id', $caregiver->id)
                    ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_count')
                    ->first();

                // 6. Update Tabel Caregivers dengan angka terbaru
                $caregiver->update([
                    'average_rating' => $stats->avg_rating,
                    'total_reviews' => $stats->total_count
                ]);
            });

            return back()->with('success', 'Terima kasih! Ulasan Anda telah memperbarui profil perawat.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan ulasan.');
        }
    }
}