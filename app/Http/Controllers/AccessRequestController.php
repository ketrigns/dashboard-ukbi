<?php

namespace App\Http\Controllers;

use App\Models\UserRequest;
use Illuminate\Http\Request;

class AccessRequestController extends Controller
{
    public function store(Request $request)
    {
        // Pastikan hanya petugas yang bisa minta izin
        if (auth()->user()->role !== 'petugas') {
            return back()->with('error', 'Hanya petugas yang dapat meminta izin.');
        }

        // Cek apakah sudah ada request pending agar tidak double
        if (auth()->user()->hasPendingAccessRequest()) {
            return back()->with('error', 'Anda sudah memiliki permintaan yang sedang menunggu.');
        }

        // Simpan request ke database
        UserRequest::create([
            'user_id' => auth()->id(),
            'status' => 'pending'
        ]);

        return back()->with('success', 'Permintaan akses berhasil dikirim ke Admin.');
    }
}
