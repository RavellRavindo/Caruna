<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Caregiver;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;

class WithdrawalController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|min:50000', 
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string', 
        ]);

        $userId = Auth::id();

        try {
            DB::transaction(function () use ($userId, $request) {
                
                $caregiver = Caregiver::where('user_id', $userId)->lockForUpdate()->firstOrFail();

                if ($caregiver->balance < $request->amount) {
                    throw new \Exception('Saldo tidak mencukupi untuk penarikan ini.');
                }

                $caregiver->balance -= $request->amount;
                $caregiver->save();

                $withdrawal = Withdrawal::create([
                    'caregiver_id' => $caregiver->id, 
                    'amount' => $request->amount,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name, 
                    'status' => 'pending'
                ]);

                WalletTransaction::create([
                    'user_id' => $userId,
                    'type' => 'debit',
                    'amount' => $request->amount,
                    'description' => 'Penarikan Dana ke ' . $request->bank_name,
                    'reference_id' => $withdrawal->id
                ]);
            });

            return back()->with('success', 'Permintaan penarikan berhasil diajukan! Saldo Anda telah dipotong.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
