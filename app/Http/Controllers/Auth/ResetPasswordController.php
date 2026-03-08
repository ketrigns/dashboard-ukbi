<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    // Menampilkan halaman form input password baru
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    // Memproses update password di database
    public function reset(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password'    => [
                'required', 
                'string', 
                'min:6', 
                // Regex ini memastikan ada minimal 1 huruf, 1 angka, dan 1 simbol
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/',
                'confirmed' 
            ],
        ], [
            'password.min'      => 'Password minimal harus 6 karakter.',
            'password.regex'    => 'Password baru harus mengandung kombinasi huruf, angka, dan simbol (contoh: @, #, !).',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            
            'token.required'     => 'Token Wajib Ada.',
            'email.required'    => 'Email wajib diisi.',
        ]);

        // 2. Lakukan reset password menggunakan Password Broker
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Update password yang sudah di-hash
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                // (Opsional) Trigger event bahwa password berhasil direset
                event(new PasswordReset($user));
            }
        );

        // 3. Cek status dan arahkan user
        if ($status == Password::PASSWORD_RESET) {
            // Jika sukses, arahkan ke halaman login dengan pesan sukses
            return redirect()->route('login')->with('status', __($status));
        }

        // Jika gagal (misal token kadaluarsa), kembalikan dengan pesan error
        return back()->withErrors(['email' => [__($status)]]);
    }
}