<?php

namespace App\Http\Controllers;
use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index(Request $request)
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

        // 1. PINDAHKAN ARRAY URUTAN KE ATAS SINI
        $urutanPredikat = [
            'Istimewa'      => 1,
            'Sangat Unggul' => 2,
            'Unggul'        => 3,
            'Madya'         => 4,
            'Semenjana'     => 5,
            'Marginal'      => 6,
            'Terbatas'      => 7
        ];

        // 2. AMBIL DAN URUTKAN DATA PREDIKAT PER KOTA
        $predikatPerKota = DataUkbi::select(
            'kota',
            'predikat',
            DB::raw('COUNT(*) as total_predikat')
        )
            ->whereNotNull('kota')
            ->groupBy('kota', 'predikat')
            ->get()
            // Urutkan datanya terlebih dahulu sebelum di-group by kota
            ->sortBy(function ($item) use ($urutanPredikat) {
                return $urutanPredikat[$item->predikat] ?? 99; 
            })
            ->groupBy('kota');

        // 3. GABUNGKAN HASILNYA (Otomatis sudah berurut karena langkah di atas)
        $locations = $locations->map(function ($loc) use ($predikatPerKota) {
            $predikat = $predikatPerKota[$loc->kota] ?? collect();
            $loc->predikat_detail = $predikat->mapWithKeys(fn($p) => [$p->predikat => $p->total_predikat]);
            return $loc;
        });

        $rawPredikat = DataUkbi::select('predikat', DB::raw('COUNT(*) as total'))
            ->groupBy('predikat')
            ->pluck('total', 'predikat');
        
        // 4. URUTKAN JUGA UNTUK PREDICAT COUNTS UTAMA
        $predikatCounts = $rawPredikat->sortBy(function ($value, $key) use ($urutanPredikat) {
            return $urutanPredikat[$key] ?? 99; 
        });

        $wilayahCounts = DataUkbi::select('kota', DB::raw('COUNT(*) as total'))
            ->groupBy('kota')
            ->pluck('total', 'kota');

        $kategoriCounts = DataUkbi::select('terdaftar_sbg', DB::raw('COUNT(*) as total'))
            ->groupBy('terdaftar_sbg')
            ->get();

        return view('pages.admin.dashboard.index', compact('pelajar', 'mahasiswa', 'umum', 'total', 'locations', 'predikatCounts', 'wilayahCounts', 'kategoriCounts'));
    }
}
