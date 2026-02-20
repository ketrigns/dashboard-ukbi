<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function create()
    {
        // Arahkan ke dashboard jika user sudah login
        if (Auth::check()) {
            return redirect('/admin/dashboard'); 
        }
        return view('auth.login');
    }

    /**
     * Menangani proses login.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        // 2. Coba lakukan otentikasi
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // 3. Jika berhasil, regenerasi session
            $request->session()->regenerate();

            // 4. Redirect ke halaman yang dituju (misal: dashboard)
            return redirect()->intended('/admin/dashboard');
        }

        // 5. Jika gagal, kirim error kembali ke halaman login
        throw ValidationException::withMessages([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Menangani proses logout.
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}