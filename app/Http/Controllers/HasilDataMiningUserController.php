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
    public function index()
    {
        // Ambil Table centroidKmeans
        $centroidKmeans = CentroidKmeans::all();
        // Mengambil Deskripsi
        $deskripsi = DeskripsiData::first();

        // Peta Tematik Persebaran Cluster
        // 1. Ambil data (Sama seperti sebelumnya)
        $stats = DatasetClusters::select('kota', 'cluster', DB::raw('count(*) as total'))
            ->groupBy('kota', 'cluster')
            ->orderBy('kota', 'asc')
            ->get();

        $cityClusters = [];

        foreach ($stats as $row) {
            $kotaRaw = strtoupper(trim($row->kota));

            // Logika cari mayoritas (Sama seperti sebelumnya)
            if (!isset($cityClusters[$kotaRaw]) || $row->total > $cityClusters[$kotaRaw]['total']) {
                $cityClusters[$kotaRaw] = [
                    'cluster' => $row->cluster,
                    'total' => $row->total // <--- KITA BUTUH INI UNTUK HOVER
                ];
            }
        }

        $mappedData = $cityClusters;

        // SMALL MULTIPLES MAP
        $statsTahun = DatasetClusters::select('tahun_ujian', 'kota', 'cluster', DB::raw('count(*) as total'))
            ->groupBy('tahun_ujian', 'kota', 'cluster')
            ->orderBy('tahun_ujian', 'desc') // Tahun terbaru di atas
            ->get();

        $dataPerTahun = [];

        foreach ($statsTahun as $row) {
            $tahun = $row->tahun_ujian;
            $kotaRaw = strtoupper(trim($row->kota));

            // Buat array tahun jika belum ada
            if (!isset($dataPerTahun[$tahun])) {
                $dataPerTahun[$tahun] = [];
            }

            // Voting Mayoritas (Scope per Tahun)
            if (!isset($dataPerTahun[$tahun][$kotaRaw]) || $row->total > $dataPerTahun[$tahun][$kotaRaw]['total']) {
                $dataPerTahun[$tahun][$kotaRaw] = [
                    'cluster' => $row->cluster,
                    'total' => $row->total
                ];
            }
        }

        return view('pages.user.data-mining', [
            'centroidKmeans' => $centroidKmeans,
            'deskripsi' => $deskripsi,
            'mappedData' => $mappedData,
            'dataPerTahun' => $dataPerTahun,
        ]);
    }
}
