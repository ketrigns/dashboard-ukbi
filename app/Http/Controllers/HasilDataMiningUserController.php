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

class HasilDataMiningUserController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data Jumlah Data per Cluster Usia (Pelajar, Mahasiswa, Umum)
        // Ambil daftar tahun
        $years = DatasetClusters::select('tahun_ujian')
            ->distinct()
            ->orderBy('tahun_ujian')
            ->pluck('tahun_ujian');

        // Ambil data jumlah cluster usia per tahun
        $cluster1 = DatasetClusters::where('cluster_usia', 1)
            ->selectRaw('tahun_ujian, COUNT(*) as total')
            ->groupBy('tahun_ujian')
            ->pluck('total', 'tahun_ujian');

        $cluster2 = DatasetClusters::where('cluster_usia', 2)
            ->selectRaw('tahun_ujian, COUNT(*) as total')
            ->groupBy('tahun_ujian')
            ->pluck('total', 'tahun_ujian');

        $cluster3 = DatasetClusters::where('cluster_usia', 3)
            ->selectRaw('tahun_ujian, COUNT(*) as total')
            ->groupBy('tahun_ujian')
            ->pluck('total', 'tahun_ujian');

        $jmlUsiaTiapCluster = DatasetClusters::selectRaw('cluster_usia, COUNT(*) as total')
            ->groupBy('cluster_usia')
            ->orderBy('cluster_usia')
            ->get();


        // Spider Chart Nilai UKBI Berdasarkan Cluster Usia
        $clusterRadar = DatasetClusters::selectRaw("
            cluster_usia,
            AVG(seksi_i) as avg_seksi_i,
            AVG(seksi_ii) as avg_seksi_ii,
            AVG(seksi_iii) as avg_seksi_iii
        ")
            ->groupBy('cluster_usia')
            ->orderBy('cluster_usia')
            ->get();

        $radarSeries = [
            'cluster1' => $clusterRadar->firstWhere('cluster_usia', 1)
                ? [
                    round($clusterRadar->firstWhere('cluster_usia', 1)->avg_seksi_i, 2),
                    round($clusterRadar->firstWhere('cluster_usia', 1)->avg_seksi_ii, 2),
                    round($clusterRadar->firstWhere('cluster_usia', 1)->avg_seksi_iii, 2),
                ] : [],

            'cluster2' => $clusterRadar->firstWhere('cluster_usia', 2)
                ? [
                    round($clusterRadar->firstWhere('cluster_usia', 2)->avg_seksi_i, 2),
                    round($clusterRadar->firstWhere('cluster_usia', 2)->avg_seksi_ii, 2),
                    round($clusterRadar->firstWhere('cluster_usia', 2)->avg_seksi_iii, 2),
                ] : [],

            'cluster3' => $clusterRadar->firstWhere('cluster_usia', 3)
                ? [
                    round($clusterRadar->firstWhere('cluster_usia', 3)->avg_seksi_i, 2),
                    round($clusterRadar->firstWhere('cluster_usia', 3)->avg_seksi_ii, 2),
                    round($clusterRadar->firstWhere('cluster_usia', 3)->avg_seksi_iii, 2),
                ] : [],
        ];

        $tableCentroidNilaiPerClusterUsia = CentroidUsia::all();

        // Heatmap nilai rata-rata UKBI Berdasarkan Cluster Usia
        $heatmapNilaiUkbiBerdasarkanCluster = CentroidUsia::all();

        // Barchart Jumlah Peserta UKBI Berdasarkan Jenis Kelamin
        $clusterLaki = DatasetClusters::whereRaw('LOWER(jenis_kelamin) = ?', ['laki-laki'])
            ->selectRaw('tahun_ujian, COUNT(*) as total')
            ->groupBy('tahun_ujian')
            ->pluck('total', 'tahun_ujian');

        $clusterPerempuan = DatasetClusters::whereRaw('LOWER(jenis_kelamin) = ?', ['perempuan'])
            ->selectRaw('tahun_ujian, COUNT(*) as total')
            ->groupBy('tahun_ujian')
            ->pluck('total', 'tahun_ujian');

        $jmlJKTiapCluster = DatasetClusters::selectRaw('jenis_kelamin, COUNT(*) as total')
            ->groupBy('jenis_kelamin')
            ->orderBy('jenis_kelamin')
            ->get();


        // Spiderchart Nilai UKBI berdasarkan Jenis Kelamin

        // ambil semua baris
        $centroidJenisKelamin = CentroidJenisKelamin::orderBy('jenis_kelamin')->get();

        // siapkan series untuk ApexRadar: satu series per jenis_kelamin
        $nilaiCentroidJK = $centroidJenisKelamin->map(function ($r) {
            return [
                'name' => $r->jenis_kelamin,
                'data' => [
                    (float) $r->seksi_i,
                    (float) $r->seksi_ii,
                    (float) $r->seksi_iii,
                ],
            ];
        })->toArray();

        // Ambil Table centroidKmeans
        $centroidKmeans = CentroidKmeans::all();

        // Ambil Table RataUsia
        $rataUsia = RataUsia::all();

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

        // 3. Ambil data filtered berdasarkan tahun
        $dataRaw = DatasetClusters::selectRaw('kota, cluster_kmeans, COUNT(*) as total')
            ->where('tahun_ujian', $tahun)
            ->groupBy('kota', 'cluster_kmeans')
            ->orderBy('kota')
            ->orderBy('cluster_kmeans')
            ->get();

        // 4. Ambil semua cluster yang ada (otomatis)
        $clusters = DatasetClusters::select('cluster_kmeans')
            ->distinct()
            ->orderBy('cluster_kmeans')
            ->pluck('cluster_kmeans')
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
                $rowOutput[$r->cluster_kmeans] = $r->total;
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

        return view('pages.user.data-mining', [
            'years' => $years,
            'cluster1' => $cluster1,
            'cluster2' => $cluster2,
            'cluster3' => $cluster3,
            'jmlUsiaTiapCluster' => $jmlUsiaTiapCluster,
            'deskripsi' => $deskripsi,
            'clusterRadar' => $clusterRadar,
            'radarSeries' => $radarSeries,
            'tableCentroidNilaiPerClusterUsia' => $tableCentroidNilaiPerClusterUsia,
            'heatmapNilaiUkbiBerdasarkanCluster' => $heatmapNilaiUkbiBerdasarkanCluster,
            'clusterLaki' => $clusterLaki,
            'clusterPerempuan' => $clusterPerempuan,
            'jmlJKTiapCluster' => $jmlJKTiapCluster,
            'nilaiCentroidJK' => $nilaiCentroidJK,
            'centroidJenisKelamin' => $centroidJenisKelamin,
            'centroidKmeans' => $centroidKmeans,
            'rataUsia' => $rataUsia,
            'tahunList' => $tahunList,
            'tahun' => $tahun,
            'clusters' => $clusters,
            'result' => $result,
            'kotaList' => $kotaList,
            'heatmapData' => $heatmapData,
        ]);
    }
}
