<?php

namespace App\Http\Controllers;

use App\Imports\ClustersMainImport;
use App\Models\CentroidJenisKelamin;
use App\Models\CentroidKmeans;
use App\Models\CentroidUsia;
use App\Models\DatasetClusters;
use App\Models\DeskripsiData;
use App\Models\HasilDataMining;
use App\Models\RataUsia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class HasilDataMiningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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


        // Mengambil Deskripsi
        $deskripsi = DeskripsiData::first();

        return view('pages.admin.hasil-data-mining.index', [
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
        ]);
    }

    public function handleImport(Request $request)
    {
        // 1. Validasi file
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            // 2. Ambil file
            $file = $request->file('file');

            // 3. Import
            Excel::import(new ClustersMainImport(), $file);

            return redirect()->back()->with('success', 'Data berhasil diimpor!');
        }

        // ERROR DARI VALIDASI EXCEL (row, kolom, dsb)
        catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $row = $failure->row(); // baris yang salah
                $attribute = $failure->attribute(); // kolom
                $errors = implode(", ", $failure->errors()); // pesan error
                $values = $failure->values();
                $value = $values[$attribute] ?? '-';

                $errorMessages[] =
                    "Baris $row (Kolom '$attribute'): $errors (Nilai: $value)";
            }

            return redirect()->back()->with('import_errors', $errorMessages);
        }

        // ERROR UMUM
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function saveDeskripsi(Request $request)
    {
        // Ambil 1 baris (jika belum ada, create kosong dulu)
        $deskripsi = DeskripsiData::first();

        if (!$deskripsi) {
            $deskripsi = DeskripsiData::create([]);
        }

        // Update PARSIAL (hanya field yg dikirim form)
        $data = [];

        if ($request->has('bar_chart_jml_data_per_cluster_usia')) {
            $data['bar_chart_jml_data_per_cluster_usia'] = $request->bar_chart_jml_data_per_cluster_usia;
        }

        if ($request->has('spider_nilai_ukbi_per_cluster_usia')) {
            $data['spider_nilai_ukbi_per_cluster_usia'] = $request->spider_nilai_ukbi_per_cluster_usia;
        }

        if ($request->has('heatmap_nilai_ukbi_per_cluster_usia')) {
            $data['heatmap_nilai_ukbi_per_cluster_usia'] = $request->heatmap_nilai_ukbi_per_cluster_usia;
        }

        if ($request->has('bar_chart_jml_data_per_jk')) {
            $data['bar_chart_jml_data_per_jk'] = $request->bar_chart_jml_data_per_jk;
        }

        if ($request->has('spider_nilai_ukbi_per_jk')) {
            $data['spider_nilai_ukbi_per_jk'] = $request->spider_nilai_ukbi_per_jk;
        }

        if ($request->has('heatmap_nilai_ukbi_per_jk')) {
            $data['heatmap_nilai_ukbi_per_jk'] = $request->heatmap_nilai_ukbi_per_jk;
        }

        if ($request->has('centroid_kmeans')) {
            $data['centroid_kmeans'] = $request->centroid_kmeans;
        }

        if ($request->has('rata_usia')) {
            $data['rata_usia'] = $request->rata_usia;
        }

        // lakukan update hanya pada field yang ada di $data
        if (!empty($data)) {
            $deskripsi->update($data);
        }

        return back()->with('success', 'Deskripsi berhasil disimpan!');
    }
}
