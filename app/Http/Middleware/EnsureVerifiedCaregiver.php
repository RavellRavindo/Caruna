<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerifiedCaregiver
{
    /**
     * Block caregiver operational pages until an administrator has verified the profile.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $caregiver = $request->user()?->caregiver;

        if (! $caregiver || ! $caregiver->isVerified()) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Akun caregiver Anda belum diverifikasi. Anda belum dapat menerima pesanan atau menggunakan dompet.');
        }

        return $next($request);
    }
}
