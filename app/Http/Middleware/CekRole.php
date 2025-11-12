<?php

// app/Http/Middleware/CekRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    // ...$roles akan menampung 'admin', 'pegawai'
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek dulu apakah user sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        // 2. Ambil data user yang login
        $user = Auth::user();

        // 3. Cek jika role user ada di dalam daftar $roles
        if (in_array($user->role, $roles)) {
            // Cocok, lanjutkan request
            return $next($request);
        }

        // 4. Tidak cocok, lempar error 403 (Akses Ditolak)
        return abort(403, 'AKSES DITOLAK: Anda tidak memiliki wewenang.');
    }
}
