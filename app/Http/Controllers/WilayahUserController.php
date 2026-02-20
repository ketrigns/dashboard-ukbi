<?php

namespace App\Http\Controllers;

use App\Models\DataUkbi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;

class WilayahUserController extends Controller
{
    public function index()
    {
        $wilayahPerTahun = DataUkbi::select(
            'kota',
            DB::raw("YEAR(tanggal_ujian) AS tahun"),
            DB::raw('count(*) as total')
        )
            ->whereNotNull('tanggal_ujian')
            ->groupBy('tahun', 'kota')
            ->orderBy('tahun', 'ASC')
            ->get();

        $jmlPeujiWilayah = DataUkbi::select(
            'kota',
            DB::raw('count(*) as total')
        )
            ->groupBy('kota')
            ->get();

        $locations = DataUkbi::select(
            'kota',
            'titik_koordinat_peta',
            DB::raw('COUNT(*) as total_peserta')
        )
            ->whereNotNull('kota')
            ->whereNotNull('titik_koordinat_peta')
            ->groupBy('kota', 'titik_koordinat_peta')
            ->get();

        // Ambil jumlah predikat per kota
        $predikatPerKota = DataUkbi::select(
            'kota',
            'predikat',
            DB::raw('COUNT(*) as total_predikat')
        )
            ->whereNotNull('kota')
            ->groupBy('kota', 'predikat')
            ->get()
            ->groupBy('kota');

        // Gabungkan hasilnya
        $locations = $locations->map(function ($loc) use ($predikatPerKota) {
            $predikat = $predikatPerKota[$loc->kota] ?? collect();
            $loc->predikat_detail = $predikat->mapWithKeys(fn($p) => [$p->predikat => $p->total_predikat]);
            return $loc;
        });

        return view('pages.user.wilayah', compact('wilayahPerTahun', 'jmlPeujiWilayah', 'locations'));
    }

    public function exportExcel()
    {
        // --- 1. AMBIL DATA DARI DATABASE ---
        
        // Data Peuji per Wilayah per Tahun
        $wilayahPerTahun = DataUkbi::select(
                'kota',
                DB::raw("YEAR(tanggal_ujian) AS tahun"),
                DB::raw('count(*) as total')
            )
            ->whereNotNull('tanggal_ujian')
            ->whereNotNull('kota')
            ->groupBy('tahun', 'kota')
            ->orderBy('tahun', 'ASC')
            ->get();

        // Data Total Peuji per Wilayah
        $jmlPeujiWilayah = DataUkbi::select(
                'kota',
                DB::raw('count(*) as total')
            )
            ->whereNotNull('kota')
            ->groupBy('kota')
            ->pluck('total', 'kota'); // Gunakan pluck agar mudah diakses: $jmlPeujiWilayah['Jambi'] = 100

        // Data Sebaran Predikat per Wilayah
        $predikatPerKota = DataUkbi::select(
                'kota',
                'predikat',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('kota')
            ->groupBy('kota', 'predikat')
            ->get();


        // --- 2. PERSIAPAN DATA PIVOT & SORTING ---
        
        // Ambil semua daftar kota yang unik dan urutkan sesuai abjad
        $kotas = $jmlPeujiWilayah->keys()->sort()->values()->toArray();
        
        // Daftar Tahun unik
        $years = $wilayahPerTahun->pluck('tahun')->unique()->sort()->values()->toArray();
        
        // Urutan Baku Predikat (Termasuk 'Tidak Berpredikat')
        $urutanPredikat = [
            'Istimewa', 'Sangat Unggul', 'Unggul', 'Madya', 
            'Semenjana', 'Marginal', 'Terbatas', 'Tidak Berpredikat'
        ];

        // Pivot Data Tahun
        $pivotTahun = [];
        foreach ($wilayahPerTahun as $item) {
            $pivotTahun[$item->kota][$item->tahun] = $item->total;
        }

        // Pivot Data Predikat
        $pivotPredikat = [];
        foreach ($predikatPerKota as $item) {
            $pivotPredikat[$item->kota][$item->predikat] = $item->total;
        }


        // --- 3. MULAI MEMBUAT EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Wilayah');

        // Judul Utama
        $sheet->setCellValue('A1', 'LAPORAN DATA UKBI BERDASARKAN WILAYAH (KOTA/KABUPATEN)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);


        // ================= TABEL 1 =================
        $currentRow = 4;
        $sheet->setCellValue('A' . $currentRow, 'TOTAL PEUJI PER WILAYAH');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        $sheet->setCellValue('A' . $currentRow, 'Wilayah (Kota/Kabupaten)');
        $sheet->setCellValue('B' . $currentRow, 'Total Peuji');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;

        foreach ($kotas as $kota) {
            $sheet->setCellValue('A' . $currentRow, $kota);
            $sheet->setCellValue('B' . $currentRow, $jmlPeujiWilayah[$kota] ?? 0);
            $currentRow++;
        }


        // ================= TABEL 2 =================
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PEUJI PER WILAYAH BERDASARKAN TAHUN');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        $sheet->setCellValue('A' . $currentRow, 'Wilayah (Kota/Kabupaten)');
        $colLetter = 'B';
        foreach ($years as $year) {
            $sheet->setCellValue($colLetter . $currentRow, $year);
            $sheet->getStyle($colLetter . $currentRow)->getFont()->setBold(true);
            $colLetter++;
        }
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        foreach ($kotas as $kota) {
            $sheet->setCellValue('A' . $currentRow, $kota);
            $colLetter = 'B';
            foreach ($years as $year) {
                $sheet->setCellValue($colLetter . $currentRow, $pivotTahun[$kota][$year] ?? 0);
                $colLetter++;
            }
            $currentRow++;
        }


        // ================= TABEL 3 =================
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'SEBARAN PREDIKAT PER WILAYAH');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        $sheet->setCellValue('A' . $currentRow, 'Wilayah (Kota/Kabupaten)');
        $colLetter = 'B';
        foreach ($urutanPredikat as $predikat) {
            $sheet->setCellValue($colLetter . $currentRow, $predikat);
            $sheet->getStyle($colLetter . $currentRow)->getFont()->setBold(true);
            $colLetter++;
        }
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        foreach ($kotas as $kota) {
            $sheet->setCellValue('A' . $currentRow, $kota);
            $colLetter = 'B';
            foreach ($urutanPredikat as $predikat) {
                $sheet->setCellValue($colLetter . $currentRow, $pivotPredikat[$kota][$predikat] ?? 0);
                $colLetter++;
            }
            $currentRow++;
        }

        // --- AUTOSIZE SEMUA KOLOM ---
        $maxCol = $sheet->getHighestDataColumn();
        foreach (range('A', $maxCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // --- 4. OUTPUT ---
        $fileName = 'Data_Wilayah.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
