<?php

namespace App\Http\Controllers;

use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PredikatUserController extends Controller
{
    public function index(Request $request) {
        $wilayah = $request->input('wilayah');

        // $kategoriPerTahun = DataUkbi::select(
        //     DB::raw("YEAR(tanggal_ujian) AS tahun"),
        //     DB::raw("
        //     CASE
        //         WHEN terdaftar_sbg LIKE '%mahasiswa%' THEN 'Mahasiswa'
        //         WHEN terdaftar_sbg LIKE '%pelajar%' THEN 'Pelajar'
        //         ELSE 'Umum'
        //     END AS kategori
        // "),
        //     DB::raw("COUNT(*) AS total")
        // )
        //     ->when($wilayah, fn($q) => $q->where('kota', $wilayah))
        //     ->whereNotNull('tanggal_ujian')
        //     ->groupBy('tahun', 'kategori')
        //     ->orderBy('tahun', 'ASC')
        //     ->get();

        $predikatPerTahun = DataUkbi::select(
            'predikat',
            DB::raw("YEAR(tanggal_ujian) AS tahun"),
            DB::raw('count(*) as total')
        )
            ->when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->whereNotNull('tanggal_ujian')
            ->groupBy('tahun', 'predikat')
            ->orderBy('tahun', 'ASC')
            ->get();

        $jmlPeujiPredikat = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->select(
                'predikat',
                DB::raw('count(*) as total')
            )
            ->groupBy('predikat')
            ->get();

        

        return view('pages.user.predikat', compact('predikatPerTahun', 'jmlPeujiPredikat'));
    }
}
