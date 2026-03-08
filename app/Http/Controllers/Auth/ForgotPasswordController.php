<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    // Menampilkan halaman form input email
    public function showLinkRequestForm()
    {
        // Pastikan nama view sesuai dengan yang kamu buat sebelumnya
        return view('auth.forgot-password'); 
    }

    // Memproses pengiriman link reset password
    public function sendResetLinkEmail(Request $request)
    {
        // 1. Validasi input email
        $request->validate(['email' => 'required|email']);

        // 2. Kirim link menggunakan bawaan Laravel Password Broker
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // 3. Cek status pengiriman dan kembalikan respon
        if ($status == Password::RESET_LINK_SENT) {
            return back()->with(['status' => __($status)]);
        }

        return back()->withErrors(['email' => __($status)]);
    }
}