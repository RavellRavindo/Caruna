<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPaymentReconciliationController extends Controller
{
    public function index(): View
    {
        $payments = Payment::query()
            ->with(['booking.user', 'booking.caregiver.user'])
            ->where('reconciliation_status', Payment::RECONCILIATION_REFUND_REQUIRED)
            ->latest('last_callback_at')
            ->paginate(15);

        return view('admin.payments.reconciliation', compact('payments'));
    }

    /**
     * Records a refund that has already been processed in Midtrans or the
     * applicable payment channel. This does not initiate a refund API call.
     */
    public function markRefunded(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate([
            'refund_reference' => ['required', 'string', 'max:255'],
        ]);

        $updated = DB::transaction(function () use ($payment, $data): bool {
            $lockedPayment = Payment::lockForUpdate()->findOrFail($payment->id);

            if (! $lockedPayment->requiresRefund()) {
                return false;
            }

            $lockedPayment->update([
                'reconciliation_status' => Payment::RECONCILIATION_REFUNDED,
                'refund_reference' => $data['refund_reference'],
                'reconciliation_note' => 'Refund dikonfirmasi manual oleh admin.',
                'reconciled_at' => now(),
            ]);

            return true;
        });

        if (! $updated) {
            return back()->with('error', 'Refund ini sudah tidak memerlukan tindakan atau telah diproses.');
        }

        return back()->with('success', 'Refund berhasil dicatat. Pastikan dana sudah benar-benar dikembalikan melalui Midtrans atau kanal pembayaran terkait.');
    }
}
