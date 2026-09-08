<?php

namespace App\Http\Controllers;

use App\Models\Caregiver;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminWithdrawalController extends Controller
{
    public function index()
    {
        // Menampilkan semua penarikan dari yang terbaru
        $withdrawals = Withdrawal::with('caregiver.user')->latest()->get();

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function approve(string $id)
    {
        Withdrawal::findOrFail($id);

        $approved = DB::transaction(function () use ($id) {
            $withdrawal = Withdrawal::lockForUpdate()->findOrFail($id);

            if ($withdrawal->status !== 'pending') {
                return false;
            }

            $withdrawal->update(['status' => 'approved']);

            return true;
        });

        if (! $approved) {
            return back()->with('error', 'Hanya penarikan berstatus pending yang bisa diproses.');
        }

        return back()->with('success', 'Penarikan disetujui! Jangan lupa transfer ke rekening perawat ya.');
    }

    public function reject(string $id)
    {
        Withdrawal::findOrFail($id);

        try {
            $rejected = DB::transaction(function () use ($id) {
                $withdrawal = Withdrawal::lockForUpdate()->findOrFail($id);

                if ($withdrawal->status !== 'pending') {
                    return false;
                }

                $withdrawal->update(['status' => 'rejected']);

                $caregiver = Caregiver::where('id', $withdrawal->caregiver_id)->lockForUpdate()->firstOrFail();
                $caregiver->balance += $withdrawal->amount;
                $caregiver->save();

                WalletTransaction::create([
                    'user_id' => $caregiver->user_id,
                    'type' => 'credit',
                    'amount' => $withdrawal->amount,
                    'description' => 'Refund penarikan dana yang ditolak (TRX-'.str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT).')',
                    'reference_id' => $withdrawal->id,
                ]);

                return true;
            });

            if (! $rejected) {
                return back()->with('error', 'Hanya penarikan berstatus pending yang bisa ditolak.');
            }

            return back()->with('success', 'Penarikan ditolak. Saldo telah dikembalikan (refund) ke dompet perawat.');
        } catch (\Throwable $exception) {
            Log::error('Withdrawal rejection could not be processed.', [
                'withdrawal_id' => $id,
                'exception' => $exception,
            ]);

            return back()->with('error', 'Terjadi kesalahan sistem saat memproses refund.');
        }
    }
}
