<?php

namespace App\Http\Controllers;

use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KategoriUserController extends Controller
{
    public function index(Request $request)
    {
        $wilayah = $request->input('wilayah');

        $qTotal = DataUkbi::query();
        if ($wilayah) {
            $qTotal->where('kota', $wilayah);
        }
        $total = $qTotal->count();

        $pelajar = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'like', '%pelajar%')->count();

        $mahasiswa = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'like', '%mahasiswa%')->count();

        $umum = $total - ($pelajar + $mahasiswa);

        $kategoriPerTahun = DataUkbi::select(
            DB::raw("YEAR(tanggal_ujian) AS tahun"),
            DB::raw("terdaftar_sbg as kategori"),
            DB::raw("COUNT(*) AS total")
        )
            ->when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->whereNotNull('tanggal_ujian')
            ->groupBy('tahun', 'kategori')
            ->orderBy('tahun', 'ASC')
            ->get();

        $pelajarCounts = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where(function ($q) {
                $q->where('terdaftar_sbg', 'like', '%pelajar%')
                    ->orWhere('terdaftar_sbg', 'like', '%mahasiswa%');
            })
            ->select('terdaftar_sbg', DB::raw('COUNT(*) as total'))
            ->groupBy('terdaftar_sbg')
            ->pluck('total', 'terdaftar_sbg');


        $umumCounts = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'not like', '%pelajar%')
            ->where('terdaftar_sbg', 'not like', '%mahasiswa%')
            ->select(
                'terdaftar_sbg',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('terdaftar_sbg')
            ->pluck('total', 'terdaftar_sbg');

        // dd($umumCounts);

        $allWilayah = DataUkbi::select(
            'kota'
        )->distinct()->pluck('kota');

        // dd($wilayah);

        return view('pages.user.kategori', compact('total', 'pelajar', 'mahasiswa', 'umum', 'kategoriPerTahun', 'pelajarCounts', 'umumCounts', 'allWilayah'));
    }

    public function exportExcel(Request $request)
    {
        $wilayah = $request->input('wilayah');

        // --- 1. AMBIL DATA ---
        $qTotal = DataUkbi::query();
        if ($wilayah) { $qTotal->where('kota', $wilayah); }
        $total = $qTotal->count();

        $pelajar = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'like', '%pelajar%')->count();

        $mahasiswa = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'like', '%mahasiswa%')->count();

        $umum = $total - ($pelajar + $mahasiswa);

        // --- DATA TABEL 2 (Pivot Tahun ke Samping) ---
        $kategoriPerTahun = DataUkbi::select(
                DB::raw("YEAR(tanggal_ujian) AS tahun"),
                'terdaftar_sbg',
                DB::raw("COUNT(*) AS total")
            )
            ->when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->whereNotNull('tanggal_ujian')
            ->groupBy('tahun', 'terdaftar_sbg')
            ->orderBy('tahun', 'ASC') // Urutkan tahun dari terkecil ke terbesar
            ->get();

        // Ambil daftar tahun unik untuk dijadikan header kolom
        $years = $kategoriPerTahun->pluck('tahun')->unique()->sort()->values()->toArray();

        // Buat array penampung data pivot: [Nama Kategori => [Tahun => Jumlah]]
        $pivotData = [];
        foreach ($kategoriPerTahun as $item) {
            $pivotData[$item->terdaftar_sbg][$item->tahun] = $item->total;
        }

        // Data Tabel 3 (Pelajar & Mahasiswa)
        $pelajarCounts = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where(fn($q) => $q->where('terdaftar_sbg', 'like', '%pelajar%')->orWhere('terdaftar_sbg', 'like', '%mahasiswa%'))
            ->select('terdaftar_sbg', DB::raw('COUNT(*) as total'))
            ->groupBy('terdaftar_sbg')->pluck('total', 'terdaftar_sbg');

        // Data Tabel 4 (Umum)
        $umumCounts = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'not like', '%pelajar%')
            ->where('terdaftar_sbg', 'not like', '%mahasiswa%')
            ->select('terdaftar_sbg', DB::raw('COUNT(*) as total'))
            ->groupBy('terdaftar_sbg')->pluck('total', 'terdaftar_sbg');

        // --- 2. PROSES EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kategori');

        // Judul Utama
        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI KATEGORI UKBI');
        if($wilayah) $sheet->setCellValue('A2', 'WILAYAH: ' . strtoupper($wilayah));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // --- TABEL 1: RINGKASAN JUMLAH PEUJI ---
        $currentRow = 4;
        $sheet->setCellValue('A' . $currentRow, 'RINGKASAN JUMLAH PEUJI');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'Keterangan'); $sheet->setCellValue('B' . $currentRow, 'Jumlah');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'Jumlah Peuji Pelajar'); $sheet->setCellValue('B' . $currentRow, $pelajar); $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'Jumlah Peuji Mahasiswa'); $sheet->setCellValue('B' . $currentRow, $mahasiswa); $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'Jumlah Peuji Umum'); $sheet->setCellValue('B' . $currentRow, $umum); $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'Jumlah Peuji'); $sheet->setCellValue('B' . $currentRow, $total);
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);

        // --- TABEL 2: JUMLAH PEUJI BERDASARKAN KATEGORI ---
        $currentRow += 3;
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PEUJI BERDASARKAN KATEGORI');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        // Header Tabel 2
        $sheet->setCellValue('A' . $currentRow, 'Kategori');
        $colLetter = 'B';
        foreach ($years as $year) {
            $sheet->setCellValue($colLetter . $currentRow, $year); // Kolom 2021, 2022, dst.
            $sheet->getStyle($colLetter . $currentRow)->getFont()->setBold(true);
            $colLetter++;
        }
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        // Isi Data Tabel 2
        foreach ($pivotData as $kategori => $dataTahun) {
            $sheet->setCellValue('A' . $currentRow, $kategori);
            
            $colLetter = 'B';
            foreach ($years as $year) {
                $jumlah = $dataTahun[$year] ?? 0; // Jika tidak ada data di tahun itu, tampilkan 0
                $sheet->setCellValue($colLetter . $currentRow, $jumlah);
                $colLetter++;
            }
            $currentRow++;
        }

        // --- TABEL 3: KATEGORI MAHASISWA DAN PELAJAR ---
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PEUJI KATEGORI MAHASISWA DAN PELAJAR');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'Nama Kategori'); $sheet->setCellValue('B' . $currentRow, 'Jumlah');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;
        foreach ($pelajarCounts as $kat => $jml) {
            $sheet->setCellValue('A' . $currentRow, $kat); $sheet->setCellValue('B' . $currentRow, $jml);
            $currentRow++;
        }

        // --- TABEL 4: BUKAN KATEGORI MAHASISWA DAN PELAJAR ---
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PEUJI KATEGORI UMUM');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'Nama Kategori'); $sheet->setCellValue('B' . $currentRow, 'Jumlah');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;
        foreach ($umumCounts as $kat => $jml) {
            $sheet->setCellValue('A' . $currentRow, $kat); $sheet->setCellValue('B' . $currentRow, $jml);
            $currentRow++;
        }

        // Autosize semua kolom yang terpakai (Dari A sampai huruf terakhir yang digunakan di Tabel 2)
        $maxCol = $sheet->getHighestDataColumn();
        foreach (range('A', $maxCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // --- 3. OUTPUT ---
        $fileName = 'Data_Kategori.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
