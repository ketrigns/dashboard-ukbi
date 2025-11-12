<?php

namespace App\Http\Controllers;

use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;

class TahunUserController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->date('tanggal_mulai') ?? Carbon::now()->subMonth();
        $endDate = $request->date('tanggal_selesai') ?? Carbon::now();

        $query = DataUkbi::whereBetween('tanggal_ujian', [
            $startDate->startOfDay(),
            $endDate->endOfDay()
        ]);

        $locations = (clone $query)
            ->select(
                'kota',
                'titik_koordinat_peta',
                DB::raw('COUNT(*) as total_peserta')
            )
            ->whereNotNull('kota')
            ->whereNotNull('titik_koordinat_peta')
            ->groupBy('kota', 'titik_koordinat_peta')
            ->get();

        $pelajar = (clone $query)
            ->where('terdaftar_sbg', 'like', '%pelajar%')
            ->count();

        $mahasiswa = (clone $query)
            ->where('terdaftar_sbg', 'like', '%mahasiswa%')
            ->count();

        $total = (clone $query)->count();

        $umum = $total - ($pelajar + $mahasiswa);

        $jmlPeujiPredikat = (clone $query)
            ->select(
                'predikat',
                DB::raw('count(*) as total')
            )
            ->groupBy('predikat')
            ->get();

        $jmlPeujiWilayah = (clone $query)
            ->select(
                'kota',
                DB::raw('count(*) as total')
            )
            ->groupBy('kota')
            ->get();
        
        
        return view('pages.user.tahun', [
            'startDate' => $startDate->format('Y-m-d'),
            'endDate'   => $endDate->format('Y-m-d'),
            'pelajar'   => $pelajar,
            'mahasiswa'   => $mahasiswa,
            'umum'   => $umum,
            'total'   => $total,
            'locations'   => $locations,
            'jmlPeujiPredikat'   => $jmlPeujiPredikat,
            'jmlPeujiWilayah'   => $jmlPeujiWilayah,
        ]);
    }
}
