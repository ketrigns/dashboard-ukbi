<?php

namespace App\Http\Controllers;

use App\Imports\DataUkbiImport;
use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class DataUkbiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DataUkbi::latest()->paginate(10);
        return view('pages.admin.dashboard.index', compact('data'));
    }

    public function handleImport(Request $request)
    {
        // 1. Validasi file yang di-upload
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv', 
        ]);

        try {
            // 2. Dapatkan file dari request
            $file = $request->file('file');

            // 3. Lakukan impor menggunakan class DataUkbiImport
            Excel::import(new DataUkbiImport, $file);

            // 4. Redirect kembali dengan pesan sukses
            return redirect()->back()->with('success', 'Data UKBI berhasil diimpor!');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $values = $failure->values();
                $attribute = $failure->attribute();
                $value = $values[$attribute] ?? '-';

                $errorMessages[] = "Baris " . $failure->row() .
                    " (Kolom: " . $attribute . "): " .
                    implode(", ", $failure->errors()) .
                    " (Nilai: " . $value . ")";
            }

            return redirect()->back()->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
            // Tangkap error umum lainnya
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kota = DataUkbi::select('kota', 'titik_koordinat_peta')->distinct()->get();
        $terdaftarSbg = DataUkbi::select('terdaftar_sbg')->distinct()->get();
        $instansi = DataUkbi::select('instansi')->distinct()->get();
        $predikat = DataUkbi::select('predikat')->distinct()->get();

        return view('pages.admin.dashboard.create', compact('kota', 'terdaftarSbg', 'instansi', 'predikat'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DataUkbi::create($request->all());
        return redirect()->route('data-ukbi.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataUkbi $dataUkbi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataUkbi $dataUkbi)
    {
        $kota = DataUkbi::select('kota', 'titik_koordinat_peta')->distinct()->get();
        $terdaftarSbg = DataUkbi::select('terdaftar_sbg')->distinct()->get();
        $instansi = DataUkbi::select('instansi')->distinct()->get();
        $predikat = DataUkbi::select('predikat')->distinct()->get();

        return view('pages.admin.dashboard.edit', compact('dataUkbi', 'kota', 'terdaftarSbg', 'instansi', 'predikat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataUkbi $dataUkbi)
    {
        $dataUkbi->update($request->all());

        // 3️⃣ Redirect kembali dengan pesan sukses
        return redirect()->route('data-ukbi.index')
            ->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataUkbi $dataUkbi)
    {
        $dataUkbi->delete();
        return redirect()->route('data-ukbi.index')->with('success', 'Data berhasil dihapus');
    }
}
