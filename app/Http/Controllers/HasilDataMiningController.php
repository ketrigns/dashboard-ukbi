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

        return view('pages.admin.hasil-data-mining.index', [
            'centroidKmeans' => $centroidKmeans,
            'deskripsi' => $deskripsi,
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
