<?php

namespace App\Http\Controllers;

use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DashboardUserController extends Controller
{
    public function index()
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

        return view('pages.user.home', compact('pelajar', 'mahasiswa', 'umum', 'total', 'locations', 'predikatCounts', 'wilayahCounts', 'kategoriCounts'));
    }

    public function exportExcel()
    {
        // --- 1. AMBIL DATA DARI DATABASE ---
        $pelajar = DataUkbi::where('terdaftar_sbg', 'like', '%pelajar%')->count();
        $mahasiswa = DataUkbi::where('terdaftar_sbg', 'like', '%mahasiswa%')->count();
        $total = DataUkbi::count();
        $umum = $total - ($pelajar + $mahasiswa);

        $rawPredikat = DataUkbi::select('predikat', DB::raw('COUNT(*) as total'))
            ->groupBy('predikat')
            ->pluck('total', 'predikat');
        
        $urutanPredikat = [
            'Istimewa'      => 1,
            'Sangat Unggul' => 2,
            'Unggul'        => 3,
            'Madya'         => 4,
            'Semenjana'     => 5,
            'Marginal'      => 6,
            'Terbatas'      => 7
        ];

        $predikatCounts = $rawPredikat->sortBy(function ($value, $key) use ($urutanPredikat) {
            return $urutanPredikat[$key] ?? 99; // Jika ada predikat di luar list, taruh di paling bawah
        });

        $kategoriCounts = DataUkbi::select('terdaftar_sbg', DB::raw('COUNT(*) as total'))
            ->groupBy('terdaftar_sbg')->pluck('total', 'terdaftar_sbg');

        $wilayahCounts = DataUkbi::select('kota', DB::raw('COUNT(*) as total'))
            ->groupBy('kota')->pluck('total', 'kota');


        // --- 2. MULAI MEMBUAT EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // -- BAGIAN A: JUDUL & RINGKASAN (SUMMARY) --
        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI DATA UKBI');
        $sheet->mergeCells('A1:C1'); // Gabungkan sel judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Header Ringkasan
        $sheet->setCellValue('A3', 'RINGKASAN TOTAL PEUJI');
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $sheet->setCellValue('A4', 'Jumlah Peuji Pelajar');
        $sheet->setCellValue('B4', $pelajar);

        $sheet->setCellValue('A5', 'Jumlah Peuji Mahasiswa');
        $sheet->setCellValue('B5', $mahasiswa);

        $sheet->setCellValue('A6', 'Jumlah Peuji Umum');
        $sheet->setCellValue('B6', $umum);

        $sheet->setCellValue('A7', 'JUMLAH PEUJI');
        $sheet->setCellValue('B7', $total);
        $sheet->getStyle('A7:B7')->getFont()->setBold(true); // Bold total


        // -- VARIABEL UNTUK MENGATUR POSISI BARIS --
        // Kita mulai tabel berikutnya di baris ke-10
        $currentRow = 10; 


        // -- BAGIAN B: TABEL BERDASARKAN PREDIKAT --
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PEUJI BERDASARKAN PREDIKAT');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++; // Pindah ke baris header tabel

        // Header Tabel
        $sheet->setCellValue('A' . $currentRow, 'Predikat');
        $sheet->setCellValue('B' . $currentRow, 'Jumlah');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;

        // Isi Data Predikat
        foreach ($predikatCounts as $predikat => $jumlah) {
            $sheet->setCellValue('A' . $currentRow, $predikat);
            $sheet->setCellValue('B' . $currentRow, $jumlah);
            $currentRow++;
        }

        // Beri jarak 2 baris kosong sebelum tabel berikutnya
        $currentRow += 2;


        // -- BAGIAN C: TABEL BERDASARKAN KATEGORI --
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PEUJI BERDASARKAN KATEGORI');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Kategori');
        $sheet->setCellValue('B' . $currentRow, 'Jumlah');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;

        foreach ($kategoriCounts as $kategori => $jumlah) {
            $sheet->setCellValue('A' . $currentRow, $kategori);
            $sheet->setCellValue('B' . $currentRow, $jumlah);
            $currentRow++;
        }

        $currentRow += 2; // Jarak lagi


        // -- BAGIAN D: TABEL BERDASARKAN WILAYAH --
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PEUJI BERDASARKAN WILAYAH');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        $sheet->setCellValue('A' . $currentRow, 'Kota / Kabupaten');
        $sheet->setCellValue('B' . $currentRow, 'Jumlah');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;

        foreach ($wilayahCounts as $kota => $jumlah) {
            $sheet->setCellValue('A' . $currentRow, $kota);
            $sheet->setCellValue('B' . $currentRow, $jumlah);
            $currentRow++;
        }

        // -- AUTOSIZE KOLOM AGAR RAPI --
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // --- 3. OUTPUT DOWNLOAD FILE ---
        $fileName = 'Data_Dashboard.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName) .'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
