<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaymentCallbackController extends Controller
{
    public function receive(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'string', 'max:255'],
            'status_code' => ['required', 'string', 'max:10'],
            'gross_amount' => ['required', 'regex:/^\d+(?:\.\d{1,2})?$/'],
            'signature_key' => ['required', 'string', 'size:128'],
            'transaction_status' => ['required', Rule::in([
                'authorize', 'capture', 'settlement', 'pending', 'deny', 'cancel', 'expire', 'failure',
                'refund', 'partial_refund', 'chargeback', 'partial_chargeback',
            ])],
            'transaction_id' => ['nullable', 'string', 'max:64'],
            'fraud_status' => ['nullable', Rule::in(['accept', 'challenge', 'deny'])],
        ]);

        if ($validator->fails()) {
            Log::warning('Midtrans callback has an invalid payload.', [
                'validation_errors' => $validator->errors()->keys(),
            ]);

            return response()->json(['message' => 'Invalid notification payload'], 422);
        }

        $payload = $validator->validated();

        $serverKey = config('services.midtrans.server_key');

        if (! $serverKey) {
            Log::critical('Midtrans callback received without a configured server key.');

            return response()->json(['message' => 'Payment service is unavailable'], 503);
        }

        $signature = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$serverKey);

        if (! hash_equals($signature, $payload['signature_key'])) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        return DB::transaction(function () use ($payload) {
            $payment = Payment::where('order_id', $payload['order_id'])->lockForUpdate()->first();

            if (! $payment) {
                Log::warning('Midtrans callback references an unknown payment.', [
                    'order_id' => $payload['order_id'],
                ]);

                return response()->json(['message' => 'Notification acknowledged']);
            }

            if ($this->toCents($payload['gross_amount']) !== $this->toCents((string) $payment->amount)) {
                Log::warning('Midtrans callback amount does not match payment.', [
                    'order_id' => $payload['order_id'],
                    'payment_id' => $payment->id,
                ]);

                return response()->json(['message' => 'Invalid payment amount'], 422);
            }

            $booking = Booking::lockForUpdate()->find($payment->booking_id);

            if (! $booking) {
                Log::critical('Payment references a missing booking.', ['payment_id' => $payment->id]);

                return response()->json(['message' => 'Booking not found'], 404);
            }

            $callbackAttributes = [
                'midtrans_status' => $payload['transaction_status'],
                'last_callback_at' => now(),
            ];

            if (isset($payload['transaction_id'])) {
                $callbackAttributes['transaction_id'] = $payload['transaction_id'];
            }

            if (isset($payload['fraud_status'])) {
                $callbackAttributes['fraud_status'] = $payload['fraud_status'];
            }

            $isSuccessful = $payload['status_code'] === '200'
                && ($payload['transaction_status'] === 'settlement'
                    || ($payload['transaction_status'] === 'capture'
                        && ($payload['fraud_status'] ?? 'accept') === 'accept'));

            if ($isSuccessful) {
                $payment->update($callbackAttributes + [
                    'status' => 'success',
                    'payment_date' => $payment->payment_date ?? now(),
                ]);

                if ($booking->status === 'approved') {
                    $booking->update(['status' => 'paid']);
                } elseif ($booking->status !== 'paid') {
                    Log::warning('Successful Midtrans callback ignored because booking is no longer payable.', [
                        'booking_id' => $booking->id,
                        'booking_status' => $booking->status,
                        'payment_id' => $payment->id,
                    ]);
                }
            } elseif (in_array($payload['transaction_status'], ['deny', 'cancel', 'expire', 'failure'], true)) {
                if ($payment->status !== 'success') {
                    $payment->update($callbackAttributes + ['status' => 'failed']);
                } else {
                    $payment->update($callbackAttributes);
                }
            } else {

                $payment->update($callbackAttributes);
            }

            return response()->json(['message' => 'Callback handled successfully']);
        });
    }

    private function toCents(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }
}
