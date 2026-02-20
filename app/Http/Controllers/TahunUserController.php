<?php

namespace App\Http\Controllers;

use App\Models\DataUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TahunUserController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil tanggal minimum dan maksimum dari database
        $minDate = DataUkbi::min('tanggal_ujian');
        $maxDate = DataUkbi::max('tanggal_ujian');

        // 2. Set startDate dan endDate
        // Jika ada request, gunakan request. Jika tidak, gunakan dari database.
        // Fallback ke hari ini jika database kebetulan masih kosong.
        $startDate = $request->date('tanggal_mulai') ?? ($minDate ? Carbon::parse($minDate) : Carbon::now());
        $endDate = $request->date('tanggal_selesai') ?? ($maxDate ? Carbon::parse($maxDate) : Carbon::now());

        // 3. Buat base query berdasarkan rentang tanggal
        $query = DataUkbi::whereBetween('tanggal_ujian', [
            $startDate->startOfDay(),
            $endDate->endOfDay()
        ]);

        $locations = (clone $query)
            ->select(
                'kota',
                'titik_koordinat_peta',
                DB::raw('COUNT(*) as total_peserta')
            )
            ->whereNotNull('kota')
            ->whereNotNull('titik_koordinat_peta')
            ->groupBy('kota', 'titik_koordinat_peta')
            ->get();

        // 🔹 PERBAIKAN: Gunakan (clone $query) agar filter tanggal juga berlaku di rincian predikat peta
        $predikatPerKota = (clone $query)
            ->select(
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

        $pelajar = (clone $query)
            ->where('terdaftar_sbg', 'like', '%pelajar%')
            ->count();

        $mahasiswa = (clone $query)
            ->where('terdaftar_sbg', 'like', '%mahasiswa%')
            ->count();

        $total = (clone $query)->count();

        $umum = $total - ($pelajar + $mahasiswa);

        $jmlPeujiPredikat = (clone $query)
            ->select(
                'predikat',
                DB::raw('count(*) as total')
            )
            ->groupBy('predikat')
            ->get();

        $jmlPeujiWilayah = (clone $query)
            ->select(
                'kota',
                DB::raw('count(*) as total')
            )
            ->groupBy('kota')
            ->get();

        $kategoriCounts = (clone $query)
            ->select('terdaftar_sbg', DB::raw('COUNT(*) as total'))
            ->groupBy('terdaftar_sbg')
            ->get();

        return view('pages.user.tahun', [
            'startDate' => $startDate->format('Y-m-d'),
            'endDate'   => $endDate->format('Y-m-d'),
            'pelajar'   => $pelajar,
            'mahasiswa' => $mahasiswa,
            'umum'      => $umum,
            'total'     => $total,
            'locations' => $locations,
            'jmlPeujiPredikat' => $jmlPeujiPredikat,
            'jmlPeujiWilayah'  => $jmlPeujiWilayah,
            'kategoriCounts'   => $kategoriCounts,
        ]);
    }

    public function exportExcel(Request $request)
    {
        // --- 1. SET FILTER TANGGAL (Sama persis dengan method index) ---
        $minDate = DataUkbi::min('tanggal_ujian');
        $maxDate = DataUkbi::max('tanggal_ujian');

        $startDate = $request->date('tanggal_mulai') ?? ($minDate ? Carbon::parse($minDate) : Carbon::now());
        $endDate = $request->date('tanggal_selesai') ?? ($maxDate ? Carbon::parse($maxDate) : Carbon::now());

        $query = DataUkbi::whereBetween('tanggal_ujian', [
            $startDate->startOfDay(),
            $endDate->endOfDay()
        ]);

        // --- 2. AMBIL DATA ---
        // Kategori Utama
        $pelajar = (clone $query)->where('terdaftar_sbg', 'like', '%pelajar%')->count();
        $mahasiswa = (clone $query)->where('terdaftar_sbg', 'like', '%mahasiswa%')->count();
        $total = (clone $query)->count();
        $umum = $total - ($pelajar + $mahasiswa);

        // Data Predikat
        $jmlPeujiPredikat = (clone $query)
            ->select('predikat', DB::raw('count(*) as total'))
            ->groupBy('predikat')
            ->pluck('total', 'predikat');

        // Data Wilayah
        $jmlPeujiWilayah = (clone $query)
            ->select('kota', DB::raw('count(*) as total'))
            ->whereNotNull('kota')
            ->groupBy('kota')
            ->orderBy('total', 'desc')
            ->get();

        // Data Sub-Kategori
        $kategoriCounts = (clone $query)
            ->select('terdaftar_sbg', DB::raw('COUNT(*) as total'))
            ->groupBy('terdaftar_sbg')
            ->orderBy('total', 'desc')
            ->get();

        // 🔹 TAMBAHAN BARU: Data Predikat per Wilayah (Terdampak Filter Tanggal)
        $predikatPerWilayah = (clone $query)
            ->select('kota', 'predikat', DB::raw('COUNT(*) as total'))
            ->whereNotNull('kota')
            ->groupBy('kota', 'predikat')
            ->get();

        // 🔹 Buat array pivot [Kota][Predikat] = Total
        $pivotWilayahPredikat = [];
        foreach ($predikatPerWilayah as $item) {
            $pivotWilayahPredikat[$item->kota][$item->predikat] = $item->total;
        }

        // Ambil daftar kota unik dan urutkan abjad untuk baris Tabel 5
        $kotas = $jmlPeujiWilayah->pluck('kota')->sort()->values()->toArray();

        // Urutan Baku Predikat
        $urutanPredikat = [
            'Istimewa', 'Sangat Unggul', 'Unggul', 'Madya', 
            'Semenjana', 'Marginal', 'Terbatas', 'Tidak Berpredikat'
        ];

        // --- 3. MULAI MEMBUAT EXCEL ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Rentang Waktu');

        // Judul Utama
        $sheet->setCellValue('A1', 'LAPORAN DATA UKBI BERDASARKAN RENTANG WAKTU');
        $sheet->setCellValue('A2', 'Periode: ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'));
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);

        // ================= TABEL 1: RINGKASAN KATEGORI =================
        $currentRow = 4;
        $sheet->setCellValue('A' . $currentRow, 'RINGKASAN KATEGORI');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        $sheet->setCellValue('A' . $currentRow, 'Kategori');
        $sheet->setCellValue('B' . $currentRow, 'Total Peuji');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;

        $kategoriData = [
            'Pelajar' => $pelajar,
            'Mahasiswa' => $mahasiswa,
            'Umum' => $umum,
            'Total Keseluruhan' => $total
        ];

        foreach ($kategoriData as $kategori => $jml) {
            $sheet->setCellValue('A' . $currentRow, $kategori);
            $sheet->setCellValue('B' . $currentRow, $jml);
            if($kategori == 'Total Keseluruhan') {
                $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
            }
            $currentRow++;
        }

        // ================= TABEL 2: BERDASARKAN PREDIKAT =================
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PEUJI BERDASARKAN PREDIKAT');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        $sheet->setCellValue('A' . $currentRow, 'Predikat');
        $sheet->setCellValue('B' . $currentRow, 'Total Peuji');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;

        foreach ($urutanPredikat as $predikat) {
            $sheet->setCellValue('A' . $currentRow, $predikat);
            $sheet->setCellValue('B' . $currentRow, $jmlPeujiPredikat[$predikat] ?? 0);
            $currentRow++;
        }

        // ================= TABEL 3: BERDASARKAN WILAYAH =================
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'JUMLAH PEUJI BERDASARKAN WILAYAH');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        $sheet->setCellValue('A' . $currentRow, 'Wilayah (Kota/Kabupaten)');
        $sheet->setCellValue('B' . $currentRow, 'Total Peuji');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;

        foreach ($jmlPeujiWilayah as $item) {
            $sheet->setCellValue('A' . $currentRow, $item->kota);
            $sheet->setCellValue('B' . $currentRow, $item->total);
            $currentRow++;
        }

        // ================= TABEL 4: DETAIL SUB-KATEGORI =================
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'DETAIL SUB-KATEGORI (Terdaftar Sebagai)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;
        
        $sheet->setCellValue('A' . $currentRow, 'Terdaftar Sebagai');
        $sheet->setCellValue('B' . $currentRow, 'Total Peuji');
        $sheet->getStyle("A$currentRow:B$currentRow")->getFont()->setBold(true);
        $currentRow++;

        foreach ($kategoriCounts as $item) {
            $sheet->setCellValue('A' . $currentRow, $item->terdaftar_sbg);
            $sheet->setCellValue('B' . $currentRow, $item->total);
            $currentRow++;
        }

        // ================= 🔹 TABEL 5: PREDIKAT PER WILAYAH =================
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'SEBARAN PREDIKAT PER WILAYAH');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        // Header Kolom Predikat
        $sheet->setCellValue('A' . $currentRow, 'Wilayah (Kota/Kabupaten)');
        $colLetter = 'B';
        foreach ($urutanPredikat as $predikat) {
            $sheet->setCellValue($colLetter . $currentRow, $predikat);
            $sheet->getStyle($colLetter . $currentRow)->getFont()->setBold(true);
            $colLetter++;
        }
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        // Isi Data
        foreach ($kotas as $kota) {
            $sheet->setCellValue('A' . $currentRow, $kota);
            $colLetter = 'B';
            foreach ($urutanPredikat as $predikat) {
                // Ambil datanya dari array Pivot. Jika tidak ada, isi 0.
                $sheet->setCellValue($colLetter . $currentRow, $pivotWilayahPredikat[$kota][$predikat] ?? 0);
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
        $fileName = 'Data_Tahun.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
