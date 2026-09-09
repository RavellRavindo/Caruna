<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($booking->user_id !== Auth::id() || $booking->status !== 'completed') {
            return back()->with('error', 'Aksi tidak diizinkan.');
        }

        try {
            $created = DB::transaction(function () use ($booking, $data) {
                $lockedBooking = Booking::query()
                    ->lockForUpdate()
                    ->findOrFail($booking->id);

                // Recheck state inside the transaction to prevent duplicate submissions.
                if ($lockedBooking->user_id !== Auth::id() || $lockedBooking->status !== 'completed') {
                    return false;
                }

                if ($lockedBooking->review()->exists()) {
                    return false;
                }

                $comment = trim((string) ($data['comment'] ?? ''));

                $lockedBooking->review()->create([
                    'user_id' => Auth::id(),
                    'caregiver_id' => $lockedBooking->caregiver_id,
                    'rating' => $data['rating'],
                    'comment' => $comment === '' ? null : $comment,
                ]);

                return true;
            });

            if (! $created) {
                return back()->with('error', 'Ulasan hanya dapat diberikan satu kali untuk layanan yang sudah selesai.');
            }

            return redirect()->route('bookings.index')->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
        } catch (\Throwable $exception) {
            Log::error('Review could not be saved.', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'exception' => $exception,
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menyimpan ulasan.');
        }
    }
}
