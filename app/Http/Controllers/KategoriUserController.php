<?php

namespace App\Http\Controllers;

use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class KategoriUserController extends Controller
{
    public function index(Request $request)
    {
        $wilayah = $request->input('wilayah');

        $qTotal = DataUkbi::query();
        if ($wilayah) {
            $qTotal->where('kota', $wilayah);
        }
        $total = $qTotal->count();

        $pelajar = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'like', '%pelajar%')->count();

        $mahasiswa = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'like', '%mahasiswa%')->count();

        $umum = $total - ($pelajar + $mahasiswa);

        $kategoriPerTahun = DataUkbi::select(
            DB::raw("YEAR(tanggal_ujian) AS tahun"),
            DB::raw("terdaftar_sbg as kategori"),
            DB::raw("COUNT(*) AS total")
        )
            ->when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->whereNotNull('tanggal_ujian')
            ->groupBy('tahun', 'kategori')
            ->orderBy('tahun', 'ASC')
            ->get();

        $pelajarCounts = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'like', '%pelajar%')
            ->orWhere('terdaftar_sbg', 'like', '%mahasiswa%')
            ->select('terdaftar_sbg', DB::raw('COUNT(*) as total'))
            ->groupBy('terdaftar_sbg')
            ->pluck('total', 'terdaftar_sbg');

        $mahasiswaCounts = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->select(
                'terdaftar_sbg',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('terdaftar_sbg')
            ->pluck('total', 'terdaftar_sbg');

        // dd($mahasiswaCounts);

        $umumCounts = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'not like', '%pelajar%')
            ->where('terdaftar_sbg', 'not like', '%mahasiswa%')
            ->select(
                'terdaftar_sbg',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('terdaftar_sbg')
            ->pluck('total', 'terdaftar_sbg');

        // dd($umumCounts);

        $allWilayah = DataUkbi::select(
            'kota'
        )->distinct()->pluck('kota');

        // dd($wilayah);

        return view('pages.user.kategori', compact('total', 'pelajar', 'mahasiswa', 'umum', 'kategoriPerTahun', 'pelajarCounts', 'umumCounts', 'allWilayah'));
    }
}
