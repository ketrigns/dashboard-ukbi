<?php

namespace App\Http\Controllers;

use App\Imports\DataUkbiImport;
use App\Models\DataUkbi;
use App\Models\PengajuanPerubahanUkbi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class DataUkbiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DataUkbi::query();

        // 🔍 Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_peserta', 'like', "%{$search}%")
                    ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                    ->orWhere('kota', 'like', "%{$search}%")
                    ->orWhere('instansi', 'like', "%{$search}%");
            });
        }

        // ⬆️ Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $data = $query->orderBy($sort, $direction)->paginate(20);

        return view('pages.admin.data-ukbi.index', compact('data'));
    }


    public function handleImport(Request $request)
    {
        // 1. Validasi file yang di-upload
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            // 2. Dapatkan file dari request
            $file = $request->file('file');

            // 3. Lakukan impor menggunakan class DataUkbiImport
            Excel::import(new DataUkbiImport, $file);

            // 4. Redirect kembali dengan pesan sukses
            return redirect()->back()->with('success', 'Data UKBI berhasil diimpor!');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $values = $failure->values();
                $attribute = $failure->attribute();
                $value = $values[$attribute] ?? '-';

                $errorMessages[] = "Baris " . $failure->row() .
                    " (Kolom: " . $attribute . "): " .
                    implode(", ", $failure->errors()) .
                    " (Nilai: " . $value . ")";
            }

            return redirect()->back()->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
            // Tangkap error umum lainnya
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kota = DataUkbi::select('kota', 'titik_koordinat_peta')->distinct()->get();
        $terdaftarSbg = DataUkbi::select('terdaftar_sbg')->distinct()->get();
        $instansi = DataUkbi::select('instansi')->distinct()->get();
        // 1. Definisikan bobot urutan predikat
        $urutanPredikat = [
            'Istimewa'          => 1,
            'Sangat Unggul'     => 2,
            'Unggul'            => 3,
            'Madya'             => 4,
            'Semenjana'         => 5,
            'Marginal'          => 6,
            'Terbatas'          => 7,
            'Tidak Berpredikat' => 8
        ];

        // 2. Ambil data dari database, lalu urutkan berdasarkan bobot di atas
        $predikat = DataUkbi::select('predikat')
            ->whereNotNull('predikat') // Opsional: pastikan tidak mengambil null
            ->distinct()
            ->get()
            ->sortBy(function ($item) use ($urutanPredikat) {
                // Jika predikat ada di daftar, gunakan bobotnya. Jika tidak ada, taruh di paling bawah (99).
                return $urutanPredikat[$item->predikat] ?? 99;
            })
            ->values();

        return view('pages.admin.data-ukbi.create', compact('kota', 'terdaftarSbg', 'instansi', 'predikat'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DataUkbi::create($request->all());
        return redirect()->route('data-ukbi.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataUkbi $dataUkbi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataUkbi $dataUkbi)
    {
        $kota = DataUkbi::select('kota', 'titik_koordinat_peta')->distinct()->get();
        $terdaftarSbg = DataUkbi::select('terdaftar_sbg')->distinct()->get();
        $instansi = DataUkbi::select('instansi')->distinct()->get();
        $predikat = DataUkbi::select('predikat')->distinct()->get();

        return view('pages.admin.data-ukbi.edit', compact('dataUkbi', 'kota', 'terdaftarSbg', 'instansi', 'predikat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataUkbi $dataUkbi)
    {
        $dataUkbi->update($request->all());

        // 3️⃣ Redirect kembali dengan pesan sukses
        return redirect()->route('data-ukbi.index')
            ->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataUkbi $dataUkbi)
    {
        $dataUkbi->delete();
        return redirect()->route('data-ukbi.index')->with('success', 'Data berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        
        if ($ids) {
            $idsArray = explode(',', $ids);
            DataUkbi::whereIn('id', $idsArray)->delete();
            
            return redirect()->back()->with('success', 'Data terpilih berhasil dihapus.');
        }
        
        return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
    }

    /**
     * UNTUK PETUGAS: Menyimpan usulan perubahan sebagai draft
     */
    public function proposeUpdate(Request $request, DataUkbi $dataUkbi)
    {
        // 1. Ambil semua data inputan form KECUALI token CSRF dan Method PUT/PATCH
        $dataInput = $request->except(['_token', '_method']);

        // (Opsional tapi disarankan) Kamu bisa lakukan validasi form di sini
        // $request->validate([...]);

        // 2. Simpan ke tabel sementara
        PengajuanPerubahanUkbi::create([
            'data_ukbi_id' => $dataUkbi->id,
            'petugas_id'   => auth()->id(), // ID user yang sedang login
            'data_usulan'  => $dataInput,   // Otomatis jadi JSON karena ada $casts di Model
            'status'       => 'pending'
        ]);

        return redirect()->route('data-ukbi.index')->with('success', 'Usulan perubahan berhasil dikirim. Menunggu persetujuan Admin.');
    }

    public function proposeDelete(DataUkbi $dataUkbi)
    {
        // Simpan ke tabel sementara, dengan JSON yang menandakan ini adalah aksi HAPUS
        PengajuanPerubahanUkbi::create([
            'data_ukbi_id' => $dataUkbi->id,
            'petugas_id'   => auth()->id(),
            'data_usulan'  => ['jenis_pengajuan' => 'HAPUS DATA'], // Penanda khusus
            'status'       => 'pending'
        ]);

        return redirect()->back()->with('success', 'Permintaan hapus data berhasil dikirim ke Admin.');
    }

    public function riwayatPengajuan()
    {
        // Ambil data pengajuan HANYA milik petugas yang sedang login
        $data = PengajuanPerubahanUkbi::with('dataUkbi')
            ->where('petugas_id', Auth::id())
            ->latest() // Urutkan dari yang paling baru
            ->paginate(10);

        // Pastikan path view disesuaikan dengan folder kamu
        return view('pages.petugas.approvals.index', compact('data'));
    }

    public function approvalIndex(Request $request)
    {
        // Mengambil data pengajuan beserta relasinya (Eager Loading)
        // with() digunakan agar query lebih efisien dan tidak terjadi N+1 problem
        $data = PengajuanPerubahanUkbi::with(['petugas', 'dataUkbi'])
            ->latest() // Mengurutkan dari yang terbaru (berdasarkan created_at)
            ->paginate(10); // Menampilkan 10 data per halaman

        // Pastikan path view disesuaikan dengan folder tempat kamu menyimpan file Blade-nya
        return view('pages.admin.approvals.index', compact('data'));
    }

    /**
     * UNTUK ADMIN: Menyetujui usulan dan menimpa data utama
     */
    public function approveUpdate($idPengajuan)
    {
        $pengajuan = PengajuanPerubahanUkbi::findOrFail($idPengajuan);
        $dataUkbi = DataUkbi::findOrFail($pengajuan->data_ukbi_id);
        // Cek apakah ini pengajuan hapus
        if (isset($pengajuan->data_usulan['jenis_pengajuan']) && $pengajuan->data_usulan['jenis_pengajuan'] === 'HAPUS DATA') {
            $dataUkbi->delete(); 
            return redirect()->back()->with('success', 'Usulan penghapusan disetujui. Data telah dihapus permanen.');
        } 
        // Jika bukan hapus, berarti ini pengajuan EDIT (seperti biasa)
        $dataUkbi->update($pengajuan->data_usulan);
        $pengajuan->update(['status' => 'disetujui']);
        return redirect()->back()->with('success', 'Perubahan data berhasil disetujui!');
    }
    
    /**
     * UNTUK ADMIN: Menolak usulan
     */
    public function rejectUpdate($idPengajuan)
    {
        $pengajuan = PengajuanPerubahanUkbi::findOrFail($idPengajuan);
        $pengajuan->update(['status' => 'ditolak']);

        return redirect()->back()->with('success', 'Usulan perubahan berhasil ditolak.');
    }
}
