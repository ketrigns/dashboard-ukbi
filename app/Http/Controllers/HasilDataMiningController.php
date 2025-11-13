<?php

namespace App\Http\Controllers;

use App\Models\HasilDataMining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HasilDataMiningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = HasilDataMining::latest()->get();
        return view('pages.admin.hasil-data-mining.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = HasilDataMining::latest()->get();
        if ($data->isEmpty()) {
            return view('pages.admin.hasil-data-mining.create');
        } else {
            return view('pages.admin.hasil-data-mining.index', compact('data'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'gambar' => 'required|image',
        ]);

        $path = $request->file('gambar')->store('hasil_data_mining', 'public');

        HasilDataMining::create([
            'gambar' => $path,
            'deskripsi' => $request->deskripsi
        ]);

        return redirect()->route('hasil-data-mining.index')->with('success', 'Gambar berhasil diupload!');
    }

    /**
     * Display the specified resource.
     */
    public function show(HasilDataMining $hasilDataMining)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HasilDataMining $hasilDataMining)
    {
        $data = HasilDataMining::latest()->get();
        if ($data->isEmpty()) {
            return view('pages.admin.hasil-data-mining.index', compact('data'));
        } else {
            return view('pages.admin.hasil-data-mining.edit', compact('hasilDataMining'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HasilDataMining $hasilDataMining)
    {
        // 1️⃣ Validasi input
        $request->validate([
            'gambar' => 'nullable|image',
        ]);

        // 2️⃣ Jika ada file gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama dari storage jika ada
            if ($hasilDataMining->gambar && Storage::disk('public')->exists($hasilDataMining->gambar)) {
                Storage::disk('public')->delete($hasilDataMining->gambar);
            }

            // Upload gambar baru
            $path = $request->file('gambar')->store('hasil_data_mining', 'public');

            // Update path ke database
            $hasilDataMining->update([
                'gambar' => $path,
                'deskripsi' => $request->deskripsi
            ]);
        } else {
            $hasilDataMining->update([
                'deskripsi' => $request->deskripsi
            ]);
        }

        // 3️⃣ Redirect kembali dengan pesan sukses
        return redirect()->route('hasil-data-mining.index')->with('success', 'Gambar berhasil diperbarui!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HasilDataMining $hasilDataMining)
    {
        if ($hasilDataMining->gambar && Storage::disk('public')->exists($hasilDataMining->gambar)) {
            Storage::disk('public')->delete($hasilDataMining->gambar);
        }

        $hasilDataMining->delete();

        return redirect()->route('hasil-data-mining.index')->with('success', 'Gambar berhasil dihapus!');
    }
}
