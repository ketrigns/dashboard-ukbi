<?php

namespace App\Http\Controllers;

use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardUserController extends Controller
{
    public function index()
    {
        $pelajar = DataUkbi::where('terdaftar_sbg', 'like', '%pelajar%')->count();

        $mahasiswa = DataUkbi::where('terdaftar_sbg', 'like', '%mahasiswa%')->count();

        $total = DataUkbi::count();
        $umum = $total - ($pelajar + $mahasiswa);

        $locations = DataUkbi::select(
            'kota',
            'titik_koordinat_peta',
            DB::raw('COUNT(*) as total_peserta')
        )
            ->whereNotNull('kota')
            ->whereNotNull('titik_koordinat_peta')
            ->groupBy('kota', 'titik_koordinat_peta')
            ->get();

        $predikatCounts = DataUkbi::select('predikat', DB::raw('COUNT(*) as total'))
            ->groupBy('predikat')
            ->pluck('total', 'predikat');

        $wilayahCounts = DataUkbi::select('kota', DB::raw('COUNT(*) as total'))
            ->groupBy('kota')
            ->pluck('total', 'kota');

        $kategoriCounts = DataUkbi::select('terdaftar_sbg', DB::raw('COUNT(*) as total'))
            ->groupBy('terdaftar_sbg')
            ->get();

        return view('pages.user.home', compact('pelajar', 'mahasiswa', 'umum', 'total', 'locations', 'predikatCounts', 'wilayahCounts', 'kategoriCounts'));
    }
}
