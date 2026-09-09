<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CaregiverProfileController extends Controller
{
    /**
     * Update the daily rate for the authenticated caregiver only.
     */
    public function updateRate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'price_per_day' => ['required', 'integer', 'min:1', 'max:9999999999'],
        ]);

        $caregiver = $request->user()->caregiver;

        abort_unless($caregiver, 404);

        $caregiver->update([
            'price_per_day' => $data['price_per_day'],
        ]);

        return Redirect::route('profile.edit')->with('status', 'caregiver-rate-updated');
    }
}
