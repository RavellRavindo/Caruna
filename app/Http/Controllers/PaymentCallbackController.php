<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class PaymentCallbackController extends Controller
{
    public function receive(Request $request)
    {
        // 1. Ambil Server Key dari .env
        $serverKey = env('MIDTRANS_SERVER_KEY');
        
        // 2. Buat Hash Signature Key untuk mencocokkan "tanda tangan" asli Midtrans
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        // 3. Verifikasi Keamanan: Jika tanda tangan tidak cocok, berarti ada hacker yang mencoba menembak API kita!
        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 4. Ekstrak ID Booking dari order_id (karena format kita: CARUNA-{id}-{time})
        $orderIdParts = explode('-', $request->order_id);
        $bookingId = $orderIdParts[1]; 

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // 5. Update Status Berdasarkan Respons Midtrans
        $transactionStatus = $request->transaction_status;

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            // Pembayaran Berhasil
            $booking->update(['status' => 'paid']);
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            // Pembayaran Gagal / Kadaluarsa
            $booking->update(['status' => 'rejected']); // atau bisa buat status 'cancelled' di database-mu
        } elseif ($transactionStatus == 'pending') {
            // Masih menunggu pembayaran
            $booking->update(['status' => 'pending']);
        }

        return response()->json(['message' => 'Callback handled successfully'], 200);
    }
}