<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Caregiver;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class AdminWithdrawalController extends Controller
{
    public function index()
    {
        // Menampilkan semua penarikan dari yang terbaru
        $withdrawals = Withdrawal::with('caregiver.user')->latest()->get();
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function approve(Request $request, string $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Hanya penarikan berstatus pending yang bisa diproses.');
        }

        // Ubah status jadi disetujui (saldo sudah dipotong di awal, jadi tidak perlu DB::transaction di sini)
        $withdrawal->update(['status' => 'approved']); 

        return back()->with('success', 'Penarikan disetujui! Jangan lupa transfer ke rekening perawat ya.');
    }

    public function reject(Request $request, string $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Hanya penarikan berstatus pending yang bisa ditolak.');
        }

        try {
            DB::transaction(function () use ($withdrawal) {
                // 1. Ubah status penarikan jadi ditolak
                $withdrawal->update(['status' => 'rejected']);

                // 2. Kunci tabel caregiver dan kembalikan uangnya (Refund)
                $caregiver = Caregiver::where('id', $withdrawal->caregiver_id)->lockForUpdate()->firstOrFail();
                $caregiver->balance += $withdrawal->amount;
                $caregiver->save();

                // 3. Catat di Buku Mutasi sebagai Uang Masuk (Refund)
                WalletTransaction::create([
                    'user_id' => $caregiver->user_id,
                    'type' => 'credit',
                    'amount' => $withdrawal->amount,
                    'description' => 'Refund penarikan dana yang ditolak (TRX-' . str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) . ')',
                    'reference_id' => $withdrawal->id
                ]);
            });

            return back()->with('success', 'Penarikan ditolak. Saldo telah dikembalikan (refund) ke dompet perawat.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses refund: ' . $e->getMessage());
        }
    }
}