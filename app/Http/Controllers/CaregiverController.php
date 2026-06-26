<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caregiver;

class CaregiverController extends Controller
{
    public function index()
    {
        $caregivers = Caregiver::with('user')
                        ->where('is_verified', true)
                        ->where('is_available', true)
                        ->get();

        return view('caregivers.index', compact('caregivers'));
    }

    public function show(Caregiver $caregiver)
    {
        // PENTING: Gunakan eager loading untuk menarik data review dan usernya
        // agar tidak terjadi N+1 Query (query berulang yang bikin web lemot)
        $caregiver->load(['user', 'reviews' => function($query) {
            $query->latest(); 
        }, 'reviews.user']);

        return view('caregivers.show', compact('caregiver'));
    }
}
