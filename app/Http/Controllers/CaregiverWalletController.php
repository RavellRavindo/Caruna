<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletTransaction;

class CaregiverWalletController extends Controller
{
    public function index()
    {
        // Pagar keamanan: Tendang jika yang mencoba masuk BUKAN perawat
        if (Auth::user()->role !== 'caregiver') {
            abort(403, 'Akses Ditolak. Halaman ini khusus untuk Perawat.');
        }

        $caregiver = Auth::user()->caregiver;
        
        $mutations = WalletTransaction::where('user_id', Auth::id())
                        ->latest()
                        ->paginate(10);

        // Pastikan nama view-nya sesuai dengan folder milikmu
        // Jika foldernya bernama "caregiver", gunakan 'caregiver.wallet'
        // Jika foldernya bernama "caregivers", gunakan 'caregivers.wallet'
        return view('caregivers.wallet', compact('caregiver', 'mutations'));
    }
}
