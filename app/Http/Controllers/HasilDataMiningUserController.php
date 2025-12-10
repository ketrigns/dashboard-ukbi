<?php

namespace App\Http\Controllers;

use App\Models\DatasetClusters;
use App\Imports\ClustersMainImport;
use App\Models\CentroidJenisKelamin;
use App\Models\CentroidKmeans;
use App\Models\CentroidUsia;
use App\Models\DeskripsiData;
use App\Models\RataUsia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilDataMiningUserController extends Controller
{
    public function index(Request $request)
    {
        // Ambil Table centroidKmeans
        $centroidKmeans = CentroidKmeans::all();

        // Cluster per tahun
        // 1. Ambil semua tahun unik dari tabel (descending)
        $tahunList = DatasetClusters::select('tahun_ujian')
            ->distinct()
            ->orderByDesc('tahun_ujian')
            ->pluck('tahun_ujian');

        // 2. Tentukan tahun yang dipakai
        //    Kalau user pilih → pakai pilihannya, 
        //    kalau tidak → pakai tahun terbesar (default)
        $tahun = $request->tahun ?? $tahunList->first();
        $tahunUsia = $request->tahunUsia ?? $tahunList->first();
        $tahunJK = $request->tahunJK ?? $tahunList->first();

        // 3. Ambil data filtered berdasarkan tahun
        $dataRaw = DatasetClusters::selectRaw('kota, cluster, COUNT(*) as total')
            ->where('tahun_ujian', $tahun)
            ->groupBy('kota', 'cluster')
            ->orderBy('kota')
            ->orderBy('cluster')
            ->get();

        // 4. Ambil semua cluster yang ada (otomatis)
        $clusters = DatasetClusters::select('cluster')
            ->distinct()
            ->orderBy('cluster')
            ->pluck('cluster')
            ->toArray();

        // 5. Generate pivot table
        $result = [];
        foreach ($dataRaw->groupBy('kota') as $kota => $rows) {
            $rowOutput = [];

            // default = 0
            foreach ($clusters as $c) {
                $rowOutput[$c] = 0;
            }

            // isi data cluster sebenarnya
            foreach ($rows as $r) {
                $rowOutput[$r->cluster] = $r->total;
            }

            // total peserta per kota
            $rowOutput['total_peserta'] = array_sum($rowOutput);

            $result[$kota] = $rowOutput;
        }

        // --- Generate data untuk heatmap Highcharts ---
        $heatmapData = [];
        $kotaList = array_keys($result); // urutan kota untuk yAxis

        foreach ($kotaList as $yIndex => $kota) {
            foreach ($clusters as $xIndex => $cluster) {
                $heatmapData[] = [
                    $xIndex,                    // x = cluster index
                    $yIndex,                    // y = kota index
                    $result[$kota][$cluster] ?? 0,  // value
                ];
            }
        }

        // Mengambil Deskripsi
        $deskripsi = DeskripsiData::first();

        // Ambil Data Cluster Usia
        $usiaGroups = [
            'Mahasiswa',
            'Pelajar',
            'Umum',
        ];

        // Ambil daftar cluster (xAxis)
        $clustersUsia = DatasetClusters::query()
            ->where('tahun_ujian', $tahunUsia)
            ->whereNotNull('cluster')
            ->distinct()
            ->orderBy('cluster')
            ->pluck('cluster')
            ->toArray();

        // CASE kategori usia (yAxis)
        $ageCase = "
        CASE
        WHEN usia BETWEEN 19 AND 25 THEN 'Mahasiswa'
            WHEN usia BETWEEN 10 AND 18 THEN 'Pelajar'
            ELSE 'Umum'
        END
    ";

        // Hitung jumlah peserta per (kategori_usia, cluster)
        $rowsUsia = DatasetClusters::query()
            ->select(
                DB::raw("$ageCase AS kategori_usia"),
                'cluster',
                DB::raw('COUNT(*) AS total')
            )
            ->where('tahun_ujian', $tahunUsia)
            ->whereNotNull('cluster')
            ->groupBy(DB::raw($ageCase), 'cluster')
            ->get();

        // Index mapper untuk Highcharts
        $clusterIndex = array_flip($clustersUsia);     // clusterValue => xIndex
        $usiaIndex    = array_flip($usiaGroups);  // usiaLabel => yIndex

        // Isi default 0 untuk semua kombinasi
        $heatmapUsiaData = [];
        foreach ($clustersUsia as $x => $c) {
            foreach ($usiaGroups as $y => $u) {
                $heatmapUsiaData[] = [$x, $y, 0];
            }
        }

        // Update nilai berdasarkan hasil query
        // (kunci: [x,y] -> position)
        $pos = [];
        foreach ($heatmapUsiaData as $i => $p) {
            $pos[$p[0] . '-' . $p[1]] = $i;
        }

        foreach ($rowsUsia as $r) {
            $x = $clusterIndex[$r->cluster] ?? null;
            $y = $usiaIndex[$r->kategori_usia] ?? null;
            if ($x !== null && $y !== null) {
                $heatmapUsiaData[$pos[$x . '-' . $y]][2] = (int) $r->total;
            }
        }

        // Tabel cluster usia
        $resultUsia = [];
        foreach ($usiaGroups as $u) {
            foreach ($clustersUsia as $c) {
                $resultUsia[$u][$c] = 0;
            }
            $resultUsia[$u]['total_peserta'] = 0;
        }

        // isi dari query
        foreach ($rowsUsia as $r) {
            $u = $r->kategori_usia;
            $c = $r->cluster;
            $total = (int) $r->total;

            $resultUsia[$u][$c] = $total;
            $resultUsia[$u]['total_peserta'] += $total;
        }

        // Ambil Data Cluster Jenis Kelamin
        $jkGroups = ['Laki-laki', 'Perempuan'];

        // ambil cluster untuk tahun itu
        $clustersJK = DatasetClusters::query()
            ->where('tahun_ujian', $tahunJK)
            ->whereNotNull('cluster')
            ->distinct()
            ->orderBy('cluster')
            ->pluck('cluster')
            ->toArray();

        // hitung jumlah peserta per (jenis_kelamin, cluster)
        $rowsJK = DatasetClusters::query()
            ->select('jenis_kelamin', 'cluster', DB::raw('COUNT(*) AS total'))
            ->where('tahun_ujian', $tahunJK)
            ->whereNotNull('cluster')
            ->whereIn('jenis_kelamin', $jkGroups)
            ->groupBy('jenis_kelamin', 'cluster')
            ->get();

        // buat heatmap data [xIndex, yIndex, value]
        $clusterIndexJK = array_flip($clustersJK);
        $jkIndex = array_flip($jkGroups);

        $heatmapJKData = [];
        foreach ($clustersJK as $x => $c) {
            foreach ($jkGroups as $y => $g) {
                $heatmapJKData[] = [$x, $y, 0];
            }
        }

        // isi nilainya
        $pos = [];
        foreach ($heatmapJKData as $i => $p) $pos[$p[0] . '-' . $p[1]] = $i;

        foreach ($rowsJK as $r) {
            $x = $clusterIndexJK[$r->cluster];
            $y = $jkIndex[$r->jenis_kelamin];
            $heatmapJKData[$pos[$x . '-' . $y]][2] = (int) $r->total;
        }

        // Tabel JK x Cluster
        $resultJK = [];
        foreach ($jkGroups as $jk) {
            foreach ($clustersJK as $c) {
                $resultJK[$jk][$c] = 0;
            }
            $resultJK[$jk]['total_peserta'] = 0;
        }

        foreach ($rowsJK as $r) {
            $jk = $r->jenis_kelamin;   // 'Laki-laki' / 'Perempuan'
            $c  = $r->cluster;
            $total = (int) $r->total;

            $resultJK[$jk][$c] = $total;
            $resultJK[$jk]['total_peserta'] += $total;
        }

        return view('pages.user.data-mining', [
            'tahunList' => $tahunList,
            'tahun' => $tahun,
            'tahunUsia' => $tahunUsia,
            'centroidKmeans' => $centroidKmeans,
            'clusters' => $clusters,
            'deskripsi' => $deskripsi,
            'result' => $result,
            'kotaList' => $kotaList,
            'heatmapData' => $heatmapData,
            'clustersUsia' => $clustersUsia,
            'heatmapUsiaData' => $heatmapUsiaData,
            'usiaGroups' => $usiaGroups,
            'resultUsia' => $resultUsia,
            'tahunJK' => $tahunJK,
            'clustersJK' => $clustersJK,
            'jkGroups' => $jkGroups,
            'heatmapJKData' => $heatmapJKData,
            'resultJK' => $resultJK,
        ]);
    }
}
