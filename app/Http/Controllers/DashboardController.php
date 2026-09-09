<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Withdrawal;
use App\Models\Caregiver;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $userRole = Auth::user()->role;

        if ($userRole === 'admin') {
            $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
            
            $activeCaregivers = Caregiver::count(); 
            
            $completedBookings = Booking::where('status', 'completed')->count(); 

            return view('admin.dashboard', compact('pendingWithdrawals', 'activeCaregivers', 'completedBookings'));
        } 
        elseif ($userRole === 'caregiver') {
            return view('caregivers.dashboard', [
                'caregiver' => Auth::user()->caregiver,
            ]);
        } 
        else {
            return view('client.dashboard');
        }
    }
}
