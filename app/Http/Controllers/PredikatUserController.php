<?php

namespace App\Http\Controllers;

use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PredikatUserController extends Controller
{
    public function index(Request $request)
{
    $wilayah = $request->input('wilayah');

    // 1. Buat referensi urutan predikat
    $urutanPredikat = [
        'Istimewa'      => 1,
        'Sangat Unggul' => 2,
        'Unggul'        => 3,
        'Madya'         => 4,
        'Semenjana'     => 5,
        'Marginal'      => 6,
        'Terbatas'      => 7
    ];

    // 2. Terapkan pengurutan pada masing-masing query

    // Untuk per tahun, kita urutkan berdasarkan tahun dulu, baru predikatnya
    $predikatPerTahun = DataUkbi::select(
        'predikat',
        DB::raw("YEAR(tanggal_ujian) AS tahun"),
        DB::raw('count(*) as total')
    )
        ->when($wilayah, fn($q) => $q->where('kota', $wilayah))
        ->whereNotNull('tanggal_ujian')
        ->groupBy('tahun', 'predikat')
        ->orderBy('tahun', 'ASC')
        ->get()
        ->sort(function ($a, $b) use ($urutanPredikat) {
            // Jika tahunnya sama, urutkan berdasarkan predikat
            if ($a->tahun == $b->tahun) {
                return ($urutanPredikat[$a->predikat] ?? 99) <=> ($urutanPredikat[$b->predikat] ?? 99);
            }
            // Jika beda, biarkan berurut berdasarkan tahun (ASC)
            return $a->tahun <=> $b->tahun;
        })->values();

    $jmlPeujiPredikat = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
        ->select(
            'predikat',
            DB::raw('count(*) as total')
        )
        ->groupBy('predikat')
        ->get()
        ->sortBy(fn($item) => $urutanPredikat[$item->predikat] ?? 99)->values();

    $rerataSkorPredikat = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
        ->select(
            'predikat',
            DB::raw('ROUND(AVG(skor), 2) as rerata')
        )
        ->groupBy('predikat')
        ->get()
        ->sortBy(fn($item) => $urutanPredikat[$item->predikat] ?? 99)->values();


    $jmlPeujiMhs = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
        ->select(
            'predikat',
            DB::raw('count(*) as total')
        )
        ->where('terdaftar_sbg', 'like', '%mahasiswa%')
        ->groupBy('predikat') 
        ->get()
        ->sortBy(fn($item) => $urutanPredikat[$item->predikat] ?? 99)->values();

    $jmlPeujiUmum = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->select(
                'predikat',
                DB::raw('count(*) as total')
            )
            ->where('terdaftar_sbg', 'not like', '%pelajar%')
            ->where('terdaftar_sbg', 'not like', '%mahasiswa%')
            ->groupBy('predikat')
            ->get()
            ->sortBy(fn($item) => $urutanPredikat[$item->predikat] ?? 99)->values();
        
    $jmlPeujiPelajar = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
        ->select(
            'predikat',
            DB::raw('count(*) as total')
        )
        ->where('terdaftar_sbg', 'like', '%pelajar%')
        ->groupBy('predikat')
        ->get()
        ->sortBy(fn($item) => $urutanPredikat[$item->predikat] ?? 99)->values();

    $jmlPeujiPerPelajar = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
        ->select(
            'predikat',
            'terdaftar_sbg',
            DB::raw('count(*) as total')
        )
        ->where('terdaftar_sbg', 'like', '%pelajar%')
        ->groupBy('predikat', 'terdaftar_sbg')
        ->get()
        ->sortBy(fn($item) => $urutanPredikat[$item->predikat] ?? 99);

    $groupedData = $jmlPeujiPerPelajar->groupBy('terdaftar_sbg');

    $allWilayah = DataUkbi::select(
        'kota'
    )->distinct()->pluck('kota');

    return view('pages.user.predikat', compact('predikatPerTahun', 'jmlPeujiPredikat', 'rerataSkorPredikat', 'jmlPeujiMhs', 'jmlPeujiUmum', 'jmlPeujiPelajar', 'groupedData', 'allWilayah'));
}

    public function exportExcel(Request $request)
    {
        $wilayah = $request->input('wilayah');

        // --- 1. AMBIL DATA DARI DATABASE ---
        
        // Predikat per Tahun
        $predikatPerTahun = DataUkbi::select('predikat', DB::raw("YEAR(tanggal_ujian) AS tahun"), DB::raw('count(*) as total'))
            ->when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->whereNotNull('tanggal_ujian')->groupBy('tahun', 'predikat')->get();

        // Total per Predikat
        $jmlPeujiPredikat = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->select('predikat', DB::raw('count(*) as total'))->groupBy('predikat')->pluck('total', 'predikat');

        // Rerata Skor per Predikat
        $rerataSkorPredikat = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->select('predikat', DB::raw('ROUND(AVG(skor), 2) as rerata'))->groupBy('predikat')->pluck('rerata', 'predikat');

        // Kategori Utama
        $jmlPeujiMhs = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'like', '%mahasiswa%')
            ->select('predikat', DB::raw('count(*) as total'))->groupBy('predikat')->pluck('total', 'predikat');

        $jmlPeujiUmum = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'not like', '%pelajar%')->where('terdaftar_sbg', 'not like', '%mahasiswa%')
            ->select('predikat', DB::raw('count(*) as total'))->groupBy('predikat')->pluck('total', 'predikat');

        $jmlPeujiPelajar = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'like', '%pelajar%')
            ->select('predikat', DB::raw('count(*) as total'))->groupBy('predikat')->pluck('total', 'predikat');

        // Detail Pelajar
        $jmlPeujiPerPelajar = DataUkbi::when($wilayah, fn($q) => $q->where('kota', $wilayah))
            ->where('terdaftar_sbg', 'like', '%pelajar%')
            ->select('predikat', 'terdaftar_sbg', DB::raw('count(*) as total'))
            ->groupBy('predikat', 'terdaftar_sbg')->get();


        // --- 2. PERSIAPAN DATA PIVOT & SORTING ---
        $urutanPredikat = ['Istimewa', 'Sangat Unggul', 'Unggul', 'Madya', 'Semenjana', 'Marginal', 'Terbatas', 'Tidak Berpredikat'];
        
        // Pivot Tahun
        $years = $predikatPerTahun->pluck('tahun')->unique()->sort()->values()->toArray();
        $pivotTahun = [];
        foreach ($predikatPerTahun as $item) {
            $pivotTahun[$item->predikat][$item->tahun] = $item->total;
        }

        // Pivot Detail Pelajar
        $subKategoriPelajar = $jmlPeujiPerPelajar->pluck('terdaftar_sbg')->unique()->sort()->values()->toArray();
        $pivotPelajar = [];
        foreach ($jmlPeujiPerPelajar as $item) {
            $pivotPelajar[$item->predikat][$item->terdaftar_sbg] = $item->total;
        }

        // --- 3. MULAI MEMBUAT EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Predikat');

        // Judul
        $sheet->setCellValue('A1', 'LAPORAN DATA UKBI BERDASARKAN PREDIKAT');
        if($wilayah) $sheet->setCellValue('A2', 'WILAYAH: ' . strtoupper($wilayah));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);


        // ================= TABEL 1 =================
        $currentRow = 4;
        $sheet->setCellValue('A' . $currentRow, 'RINGKASAN TOTAL & RATA-RATA SKOR');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        $sheet->setCellValue('A' . $currentRow, 'Predikat');
        $sheet->setCellValue('B' . $currentRow, 'Total Peuji');
        $sheet->setCellValue('C' . $currentRow, 'Rata-rata Skor');
        $sheet->getStyle("A$currentRow:C$currentRow")->getFont()->setBold(true);
        $currentRow++;

        foreach ($urutanPredikat as $predikat) {
            $sheet->setCellValue('A' . $currentRow, $predikat);
            $sheet->setCellValue('B' . $currentRow, $jmlPeujiPredikat[$predikat] ?? 0);
            $sheet->setCellValue('C' . $currentRow, $rerataSkorPredikat[$predikat] ?? 0);
            $currentRow++;
        }


        // ================= TABEL 2 =================
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PREDIKAT PER TAHUN');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        $sheet->setCellValue('A' . $currentRow, 'Predikat');
        $colLetter = 'B';
        foreach ($years as $year) {
            $sheet->setCellValue($colLetter . $currentRow, $year);
            $sheet->getStyle($colLetter . $currentRow)->getFont()->setBold(true);
            $colLetter++;
        }
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        foreach ($urutanPredikat as $predikat) {
            $sheet->setCellValue('A' . $currentRow, $predikat);
            $colLetter = 'B';
            foreach ($years as $year) {
                $sheet->setCellValue($colLetter . $currentRow, $pivotTahun[$predikat][$year] ?? 0);
                $colLetter++;
            }
            $currentRow++;
        }


        // ================= TABEL 3 =================
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PREDIKAT BERDASARKAN KATEGORI UTAMA');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        $sheet->setCellValue('A' . $currentRow, 'Predikat');
        $sheet->setCellValue('B' . $currentRow, 'Pelajar');
        $sheet->setCellValue('C' . $currentRow, 'Mahasiswa');
        $sheet->setCellValue('D' . $currentRow, 'Umum');
        $sheet->getStyle("A$currentRow:D$currentRow")->getFont()->setBold(true);
        $currentRow++;

        foreach ($urutanPredikat as $predikat) {
            $sheet->setCellValue('A' . $currentRow, $predikat);
            $sheet->setCellValue('B' . $currentRow, $jmlPeujiPelajar[$predikat] ?? 0);
            $sheet->setCellValue('C' . $currentRow, $jmlPeujiMhs[$predikat] ?? 0);
            $sheet->setCellValue('D' . $currentRow, $jmlPeujiUmum[$predikat] ?? 0);
            $currentRow++;
        }


        // ================= TABEL 4 =================
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'DETAIL PREDIKAT KATEGORI PELAJAR');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        $sheet->setCellValue('A' . $currentRow, 'Predikat');
        $colLetter = 'B';
        foreach ($subKategoriPelajar as $subKat) {
            $sheet->setCellValue($colLetter . $currentRow, $subKat);
            $sheet->getStyle($colLetter . $currentRow)->getFont()->setBold(true);
            $colLetter++;
        }
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        foreach ($urutanPredikat as $predikat) {
            $sheet->setCellValue('A' . $currentRow, $predikat);
            $colLetter = 'B';
            foreach ($subKategoriPelajar as $subKat) {
                $sheet->setCellValue($colLetter . $currentRow, $pivotPelajar[$predikat][$subKat] ?? 0);
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
        $fileName = 'Data_Predikat.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
