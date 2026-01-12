<?php

namespace App\Http\Controllers;

use App\Imports\ClustersMainImport;
use App\Models\CentroidJenisKelamin;
use App\Models\CentroidKmeans;
use App\Models\CentroidUsia;
use App\Models\DatasetClusters;
use App\Models\DeskripsiData;
use App\Models\RataUsia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class HasilDataMiningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

        // JANGAN DI-FLATTEN/MAP LAGI. Biarkan bentuk array-nya lengkap.
        // Hasil: ['KOTA JAMBI' => ['cluster' => 1, 'total' => 205], ...]
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

        return view('pages.admin.hasil-data-mining.index', [
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
            'mappedData' => $mappedData,
            'dataPerTahun' => $dataPerTahun,
        ]);
    }

    public function handleImport(Request $request)
    {
        // 1. Validasi file
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();

            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($path);
            $sheetNames = $spreadsheet->getSheetNames();

            // 2. CEK KEBERADAAN SHEET (VALIDASI)
            $hasClusterSheet  = in_array('Data_Cluster_All_Year', $sheetNames);
            $hasCentroidSheet = in_array('Centroid_KMeans', $sheetNames); // Pastikan nama di Excel "Centroid_KMeans"

            // LOGIKA: Jika TIDAK ADA sheet cluster DAN TIDAK ADA sheet centroid -> ERROR
            if (!$hasClusterSheet && !$hasCentroidSheet) {
                throw new \Exception("File Excel tidak valid! Tidak ditemukan sheet 'Data_Cluster_All_Year' ataupun 'Centroid_KMeans'.");
            }

            Excel::import(new ClustersMainImport($sheetNames), $file);

            return redirect()->back()->with('success', 'Data berhasil diimpor sesuai sheet yang ditemukan!');
        }

        // ERROR DARI VALIDASI EXCEL
        catch (ValidationException $e) {
            // ... (Logika error handling kamu yang lama, copy paste disini) ...
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(", ", $failure->errors());
                $values = $failure->values();
                $value = $values[$attribute] ?? '-';
                $errorMessages[] = "Baris $row (Kolom '$attribute'): $errors (Nilai: $value)";
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

        // Update data (hanya field yg dikirim form)
        $data = [];

        if ($request->has('centroid_kmeans')) {
            $data['centroid_kmeans'] = $request->centroid_kmeans;
        }

        // lakukan update hanya pada field yang ada di $data
        if (!empty($data)) {
            $deskripsi->update($data);
        }

        return back()->with('success', 'Deskripsi berhasil disimpan!');
    }
}
