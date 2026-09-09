<?php

namespace App\Http\Controllers;

use App\Models\Caregiver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCaregiverController extends Controller
{
    public function index(): View
    {
        $caregivers = Caregiver::query()
            ->with('user')
            ->latest()
            ->paginate(15);

        return view('admin.caregivers.index', compact('caregivers'));
    }

    public function updateVerification(Request $request, Caregiver $caregiver): RedirectResponse
    {
        $data = $request->validate([
            'verification_status' => ['required', Rule::in([
                Caregiver::VERIFICATION_VERIFIED,
                Caregiver::VERIFICATION_REJECTED,
            ])],
            'rejection_reason' => [
                Rule::requiredIf($request->input('verification_status') === Caregiver::VERIFICATION_REJECTED),
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $isVerified = $data['verification_status'] === Caregiver::VERIFICATION_VERIFIED;

        $caregiver->update([
            'verification_status' => $data['verification_status'],
            'rejection_reason' => $isVerified ? null : $data['rejection_reason'],
            'is_verified' => $isVerified,
            'is_available' => $isVerified,
        ]);

        return back()->with(
            'success',
            $isVerified
                ? 'Caregiver berhasil diverifikasi dan sekarang dapat menerima pesanan.'
                : 'Pendaftaran caregiver ditolak. Alasan penolakan telah disimpan.',
        );
    }
}
