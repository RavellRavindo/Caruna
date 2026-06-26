<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    // Tambahkan parameter $role di fungsinya
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Jika role user yang login TIDAK SAMA dengan role yang diminta rute, tendang!
        if (Auth::user()->role !== $role) {
            abort(403, 'Akses Ditolak. Halaman ini bukan untuk role Anda.');
        }

        return $next($request);
    }
}
