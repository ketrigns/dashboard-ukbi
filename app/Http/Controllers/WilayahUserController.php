<?php

namespace App\Http\Controllers;

use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahUserController extends Controller
{
    public function index()
    {
        $wilayahPerTahun = DataUkbi::select(
            'kota',
            DB::raw("YEAR(tanggal_ujian) AS tahun"),
            DB::raw('count(*) as total')
        )
            ->whereNotNull('tanggal_ujian')
            ->groupBy('tahun', 'kota')
            ->orderBy('tahun', 'ASC')
            ->get();

        $jmlPeujiWilayah = DataUkbi::select(
            'kota',
            DB::raw('count(*) as total')
        )
            ->groupBy('kota')
            ->get();

        $locations = DataUkbi::select(
            'kota',
            'titik_koordinat_peta',
            DB::raw('COUNT(*) as total_peserta')
        )
            ->whereNotNull('kota')
            ->whereNotNull('titik_koordinat_peta')
            ->groupBy('kota', 'titik_koordinat_peta')
            ->get();

        return view('pages.user.wilayah', compact('wilayahPerTahun', 'jmlPeujiWilayah', 'locations'));
    }
}
