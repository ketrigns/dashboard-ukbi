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

        // Ambil jumlah predikat per kota
        $predikatPerKota = DataUkbi::select(
            'kota',
            'predikat',
            DB::raw('COUNT(*) as total_predikat')
        )
            ->whereNotNull('kota')
            ->groupBy('kota', 'predikat')
            ->get()
            ->groupBy('kota');

        // Gabungkan hasilnya
        $locations = $locations->map(function ($loc) use ($predikatPerKota) {
            $predikat = $predikatPerKota[$loc->kota] ?? collect();
            $loc->predikat_detail = $predikat->mapWithKeys(fn($p) => [$p->predikat => $p->total_predikat]);
            return $loc;
        });

        return view('pages.user.wilayah', compact('wilayahPerTahun', 'jmlPeujiWilayah', 'locations'));
    }
}
