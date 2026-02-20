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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    public function exportExcel()
    {
        // --- 1. AMBIL DATA & LOGIKA MAYORITAS ---
        
        // Ambil Table centroidKmeans
        $centroidKmeans = CentroidKmeans::all();

        // Peta Tematik Persebaran Cluster (Keseluruhan)
        $stats = DatasetClusters::select('kota', 'cluster', DB::raw('count(*) as total'))
            ->groupBy('kota', 'cluster')
            ->orderBy('kota', 'asc')
            ->get();

        $cityClusters = [];
        foreach ($stats as $row) {
            $kotaRaw = strtoupper(trim($row->kota));
            // Logika cari mayoritas
            if (!isset($cityClusters[$kotaRaw]) || $row->total > $cityClusters[$kotaRaw]['total']) {
                $cityClusters[$kotaRaw] = [
                    'cluster' => $row->cluster,
                    'total' => $row->total 
                ];
            }
        }
        ksort($cityClusters); // Urutkan abjad berdasarkan nama kota

        // Small Multiples Map (Per Tahun)
        $statsTahun = DatasetClusters::select('tahun_ujian', 'kota', 'cluster', DB::raw('count(*) as total'))
            ->groupBy('tahun_ujian', 'kota', 'cluster')
            ->get();

        $dataPerTahun = [];
        $yearsList = []; // Array untuk menampung deretan tahun
        
        foreach ($statsTahun as $row) {
            $tahun = $row->tahun_ujian;
            $kotaRaw = strtoupper(trim($row->kota));

            // Simpan tahun jika belum ada di list (untuk header kolom nanti)
            if (!in_array($tahun, $yearsList) && !is_null($tahun)) {
                $yearsList[] = $tahun;
            }

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
        // Urutkan tahun dari terkecil ke terbesar (kiri ke kanan)
        sort($yearsList);


        // --- 2. MULAI MEMBUAT EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Data Mining');

        // Judul Utama
        $currentRow = 1;
        $sheet->setCellValue('A' . $currentRow, 'LAPORAN HASIL DATA MINING (CLUSTERING)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
        $currentRow += 2;

        // ================= TABEL 1: CENTROID K-MEANS =================
        if ($centroidKmeans->isNotEmpty()) {
            $sheet->setCellValue('A' . $currentRow, 'NILAI CENTROID K-MEANS');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $currentRow++;

            // Ambil nama kolom
            $firstCentroid = $centroidKmeans->first()->toArray();
            // Buang kolom id, timestamps, dan 'cluster' (jika sudah ada di DB agar tidak dobel)
            $excludeCols = ['id', 'created_at', 'updated_at', 'cluster']; 
            $columns = array_diff(array_keys($firstCentroid), $excludeCols);

            // 🔹 Header Tabel Centroid (Menambahkan 'CLUSTER' di sebelah kiri sekali)
            $sheet->setCellValue('A' . $currentRow, 'CLUSTER');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);

            $colLetter = 'B';
            foreach ($columns as $col) {
                // Rapikan nama kolom (cth: skor_membaca jadi SKOR MEMBACA)
                $sheet->setCellValue($colLetter . $currentRow, strtoupper(str_replace('_', ' ', $col)));
                $sheet->getStyle($colLetter . $currentRow)->getFont()->setBold(true);
                $colLetter++;
            }
            $currentRow++;

            // 🔹 Isi Data Centroid
            foreach ($centroidKmeans->values() as $index => $centroid) {
                // Tulis label C1, C2, dst. di kolom A
                $clusterLabel = $centroid->cluster ?? 'C' . ($index + 1);
                $sheet->setCellValue('A' . $currentRow, $clusterLabel);

                $colLetter = 'B';
                foreach ($columns as $col) {
                    $sheet->setCellValue($colLetter . $currentRow, $centroid->$col);
                    $colLetter++;
                }
                $currentRow++;
            }
            $currentRow += 2;
        }


        // ================= TABEL 2: PERSEBARAN KESELURUHAN =================
        $sheet->setCellValue('A' . $currentRow, 'DOMINAN CLUSTER PER KOTA (KESELURUHAN)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Kota / Kabupaten');
        $sheet->setCellValue('B' . $currentRow, 'Cluster Dominan');
        $sheet->setCellValue('C' . $currentRow, 'Total Data (Voting)');
        $sheet->getStyle("A$currentRow:C$currentRow")->getFont()->setBold(true);
        $currentRow++;

        foreach ($cityClusters as $kota => $data) {
            $sheet->setCellValue('A' . $currentRow, $kota);
            $sheet->setCellValue('B' . $currentRow, $data['cluster']);
            $sheet->setCellValue('C' . $currentRow, $data['total']);
            $currentRow++;
        }
        $currentRow += 2;


        // ================= TABEL 3: PERSEBARAN PER TAHUN (BERDERET KE KANAN) =================
        $sheet->setCellValue('A' . $currentRow, 'DOMINAN CLUSTER PER KOTA (BERDASARKAN TAHUN)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        // Header Tabel 3
        $sheet->setCellValue('A' . $currentRow, 'Kota / Kabupaten');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);

        $colLetter = 'B';
        foreach ($yearsList as $year) {
            $sheet->setCellValue($colLetter . $currentRow, $year);
            $sheet->getStyle($colLetter . $currentRow)->getFont()->setBold(true);
            $colLetter++;
        }
        $currentRow++;

        // Isi Data Tabel 3
        // Gunakan $cityClusters agar semua kota tetap ter-listing di sebelah kiri
        foreach ($cityClusters as $kota => $overallData) {
            $sheet->setCellValue('A' . $currentRow, $kota);

            $colLetter = 'B';
            foreach ($yearsList as $year) {
                // 🔹 PERBAIKAN: Cek isset pada variabel aslinya, lalu gabungkan stringnya setelah tanda tanya (?)
                $clusterVal = isset($dataPerTahun[$year][$kota]) ? "Cluster " . $dataPerTahun[$year][$kota]['cluster'] : '-';
                
                $sheet->setCellValue($colLetter . $currentRow, $clusterVal);
                $colLetter++;
            }
            $currentRow++;
        }

        // --- AUTOSIZE SEMUA KOLOM ---
        $maxCol = $sheet->getHighestDataColumn();
        foreach (range('A', $maxCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // --- 3. OUTPUT ---
        $fileName = 'Hasil_Data_Mining.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
