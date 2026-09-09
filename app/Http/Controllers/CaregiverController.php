<?php

namespace App\Http\Controllers;

use App\Models\Caregiver;

class CaregiverController extends Controller
{
    public function index()
    {
        $caregivers = Caregiver::query()
            ->with('user')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('is_verified', true)
            ->where('is_available', true)
            ->get();

        return view('caregivers.index', compact('caregivers'));
    }

    public function show(Caregiver $caregiver)
    {
        $caregiver->load([
            'user',
            'reviews' => fn ($query) => $query->latest(),
            'reviews.user',
        ]);
        $caregiver->loadCount('reviews');
        $caregiver->loadAvg('reviews', 'rating');

        return view('caregivers.show', compact('caregiver'));
    }
}
