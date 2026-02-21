<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRequest;
use Illuminate\Http\Request;

class AccessApprovalController extends Controller
{
    // Menampilkan halaman daftar request
    public function index()
    {
        // Ambil semua request beserta data usernya, urutkan dari yang terbaru
        $requests = UserRequest::with('user')->latest()->paginate(10);
        
        return view('pages.admin.approvals.index', compact('requests'));
    }

    // Menyetujui request
    public function approve($id)
    {
        $userRequest = UserRequest::findOrFail($id);
        
        // Ubah status menjadi approved
        $userRequest->update(['status' => 'approved']);

        return back()->with('success', 'Akses CRUD untuk Petugas berhasil disetujui.');
    }

    // Menolak request
    public function reject($id)
    {
        $userRequest = UserRequest::findOrFail($id);
        
        // Ubah status menjadi rejected
        $userRequest->update(['status' => 'rejected']);

        return back()->with('success', 'Permintaan akses ditolak.');
    }
}
