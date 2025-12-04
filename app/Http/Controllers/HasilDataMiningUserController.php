<?php

namespace App\Http\Controllers;

use App\Models\DatasetClusters;
use App\Models\DeskripsiData;
use Illuminate\Http\Request;

class HasilDataMiningUserController extends Controller
{
    public function index()
    {
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

        $deskripsi = DeskripsiData::first();

        return view('pages.user.data-mining', [
            'years' => $years,
            'cluster1' => $cluster1,
            'cluster2' => $cluster2,
            'cluster3' => $cluster3,
            'jmlUsiaTiapCluster' => $jmlUsiaTiapCluster,
            'deskripsi' => $deskripsi,
        ]);
    }
}
