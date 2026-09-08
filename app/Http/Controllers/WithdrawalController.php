<?php

namespace App\Http\Controllers;

use App\Models\Caregiver;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WithdrawalController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:50000'],
            'bank_name' => ['required', Rule::in(['BCA', 'Mandiri', 'BNI', 'BRI', 'GoPay', 'OVO', 'DANA'])],
            'account_number' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:255'],
            'idempotency_key' => ['required', 'uuid'],
        ]);

        $userId = Auth::id();

        try {
            $result = DB::transaction(function () use ($data, $userId) {
                $caregiver = Caregiver::where('user_id', $userId)->lockForUpdate()->firstOrFail();

                $existingWithdrawal = Withdrawal::query()
                    ->where('caregiver_id', $caregiver->id)
                    ->where('idempotency_key', $data['idempotency_key'])
                    ->first();

                if ($existingWithdrawal) {
                    return 'duplicate';
                }

                if ($caregiver->balance < $data['amount']) {
                    return 'insufficient_balance';
                }

                $caregiver->balance -= $data['amount'];
                $caregiver->save();

                $withdrawal = Withdrawal::create([
                    'caregiver_id' => $caregiver->id,
                    'amount' => $data['amount'],
                    'bank_name' => $data['bank_name'],
                    'account_number' => $data['account_number'],
                    'account_name' => $data['account_name'],
                    'idempotency_key' => $data['idempotency_key'],
                    'status' => 'pending',
                ]);

                WalletTransaction::create([
                    'user_id' => $userId,
                    'type' => 'debit',
                    'amount' => $data['amount'],
                    'description' => 'Penarikan Dana ke '.$data['bank_name'],
                    'reference_id' => $withdrawal->id,
                ]);

                return 'created';
            });

            if ($result === 'duplicate') {
                return back()->with('error', 'Permintaan penarikan ini sudah pernah diproses.');
            }

            if ($result === 'insufficient_balance') {
                return back()->with('error', 'Saldo tidak mencukupi untuk penarikan ini.');
            }

            return back()->with('success', 'Permintaan penarikan berhasil diajukan! Saldo Anda telah dipotong.');
        } catch (\Throwable $exception) {
            Log::error('Withdrawal request could not be processed.', [
                'user_id' => $userId,
                'exception' => $exception,
            ]);

            return back()->with('error', 'Permintaan penarikan belum dapat diproses. Silakan coba lagi.');
        }
    }
}
