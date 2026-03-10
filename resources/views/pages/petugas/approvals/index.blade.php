@extends('layouts.admin.app')

@section('content')
    <div class="flex items-center gap-3 text-sm font-semibold mb-5">
        <p class="text-sm font-bold text-default-900">Izin Ubah Data UKBI</p>
    </div>

    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-2">
        @if (session('success'))
            <div id="toast-success" class="flex items-center w-72 p-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded-lg shadow-lg animate-fade-in">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 111.414-1.414L8.414 12.172l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toasts = document.querySelectorAll('#toast-container > div');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-x-5');
                    setTimeout(() => toast.remove(), 700);
                }, 3000); 
            });
        });
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        .transition-opacity { transition: opacity 0.7s, transform 0.7s; }
    </style>

    <div class="mt-8">
        <div class="card overflow-hidden">
            <div class="card-header flex justify-between gap-4">
                <div>
                    <h4 class="card-title">Riwayat Usulan Anda</h4>
                </div>
            </div>

            <div class="overflow-x-auto border">
                <table id="myTable" class="min-w-full divide-y divide-default-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap w-10">No</th>
                            
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">Data UKBI</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">Keterangan</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">Status</th>
                            <th class="px-6 py-3 text-center text-sm text-default-500 whitespace-nowrap"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $index => $item)
                            @php
                                $isDeleteRequest = isset($item->data_usulan['jenis_pengajuan']) && $item->data_usulan['jenis_pengajuan'] === 'HAPUS DATA';
                            @endphp
                            <tr class="odd:bg-white even:bg-default-100 hover:bg-default-100">
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $data->firstItem() + $index }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">
                                    {{ $item->dataUkbi->no_pendaftaran ?? '-' }} <br>
                                    <span class="text-gray-500 text-xs">{{ $item->dataUkbi->nama_peserta ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    @if($isDeleteRequest)
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-md text-xs font-semibold">Hapus Data</span>
                                    @else
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-semibold">Edit Data</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    @if($item->status == 'pending')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-md text-xs font-semibold">Menunggu Admin</span>
                                    @elseif($item->status == 'disetujui')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-semibold">Disetujui</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-md text-xs font-semibold">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-sm">
                                    @if($item->status == 'pending' || $item->status == 'ditolak')
                                        <button type="button" onclick="openModal('modal-detail-{{ $item->id }}')" class="flex items-center justify-center gap-1 mx-auto transition font-medium px-3 py-1.5 rounded-md text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/></svg>
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Anda belum pernah mengajukan usulan perubahan data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $data->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- KUMPULAN MODAL DETAIL (HANYA VIEW, TANPA TOMBOL AKSI) --}}
    @foreach ($data as $item)
        @if($item->status == 'pending' || $item->status == 'ditolak')
        @php
            $isDeleteRequest = isset($item->data_usulan['jenis_pengajuan']) && $item->data_usulan['jenis_pengajuan'] === 'HAPUS DATA';
        @endphp
        <div id="modal-detail-{{ $item->id }}" class="fixed inset-0 z-[100] hidden bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="relative w-full max-w-3xl bg-white shadow-lg rounded-lg mx-4 my-8">
                
                <div class="flex justify-between items-center border-b p-4 rounded-t {{ $isDeleteRequest ? 'bg-red-50' : '' }}">
                    <h3 class="text-lg font-semibold {{ $isDeleteRequest ? 'text-red-700' : 'text-gray-900' }}">
                        @if($isDeleteRequest)
                            Permintaan Hapus Data: {{ $item->dataUkbi->nama_peserta ?? '-' }}
                        @else
                            Detail Usulan: {{ $item->dataUkbi->nama_peserta ?? '-' }}
                        @endif
                    </h3>
                    <button type="button" onclick="closeModal('modal-detail-{{ $item->id }}')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    
                    {{-- Status Message --}}
                    @if($item->status == 'pending')
                        <div class="p-3 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-200">
                            Usulan ini sedang menunggu peninjauan dari Admin.
                        </div>
                    @elseif($item->status == 'ditolak')
                        <div class="p-3 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                            Mohon maaf, usulan ini ditolak oleh Admin.
                        </div>
                    @endif
                    
                    @if($isDeleteRequest)
                        {{-- GRID TAMPILAN FULL DATA (UNTUK PERMINTAAN HAPUS) --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-5 text-left">
                            <h5 class="text-sm font-bold text-gray-700 border-b pb-2 mb-4 uppercase tracking-wider">Rincian Data Peserta</h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">No Pendaftaran</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->no_pendaftaran ?? '-' }}</span></div>
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tanggal Ujian</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->tanggal_ujian ?? '-' }}</span></div>
                                
                                <div class="md:col-span-2"><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nama Peserta</span><span class="text-sm text-gray-900 font-medium">{{ $item->dataUkbi->nama_peserta ?? '-' }}</span></div>
                                
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Terdaftar Sebagai</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->terdaftar_sbg ?? '-' }}</span></div>
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Jenis Kelamin</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->jenis_kelamin ?? '-' }}</span></div>
                                
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tempat Lahir</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->tempat_lahir ?? '-' }}</span></div>
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tanggal Lahir</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->tanggal_lahir ?? '-' }}</span></div>
                                
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kota</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->kota ?? '-' }}</span></div>
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Instansi</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->instansi ?? '-' }}</span></div>
                                
                                <div class="md:col-span-2"><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Titik Koordinat Peta</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->titik_koordinat_peta ?? '-' }}</span></div>
                            </div>

                            <h5 class="text-sm font-bold text-gray-700 border-b pb-2 mb-4 mt-6 uppercase tracking-wider">Rincian Nilai & Predikat</h5>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-y-4 gap-x-4 mb-4">
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Seksi 1</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->seksi_1 ?? '-' }}</span></div>
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Seksi 2</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->seksi_2 ?? '-' }}</span></div>
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Seksi 3</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->seksi_3 ?? '-' }}</span></div>
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Seksi 4</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->seksi_4 ?? '-' }}</span></div>
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Seksi 5</span><span class="text-sm text-gray-900">{{ $item->dataUkbi->seksi_5 ?? '-' }}</span></div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-100 p-3 rounded">
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Skor Total</span><span class="text-lg font-bold text-indigo-600">{{ $item->dataUkbi->skor ?? '-' }}</span></div>
                                <div><span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Predikat</span><span class="text-lg font-bold text-indigo-600">{{ $item->dataUkbi->predikat ?? '-' }}</span></div>
                            </div>
                        </div>

                    @else
                        {{-- TABEL KOMPARASI (UNTUK PERMINTAAN EDIT) --}}
                        <table class="min-w-full text-sm text-left border rounded-md">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 border font-medium text-gray-700">Nama Kolom</th>
                                    <th class="px-4 py-2 border font-medium text-gray-700 w-2/5">Data Lama (Saat Ini)</th>
                                    <th class="px-4 py-2 border font-medium text-gray-700 w-2/5">Data Usulan Anda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(is_array($item->data_usulan))
                                    @foreach($item->data_usulan as $key => $newValue)
                                        @php
                                            $oldValue = $item->dataUkbi->{$key} ?? '';
                                            $isChanged = (string)$oldValue !== (string)$newValue;
                                        @endphp
                                        <tr class="border-b {{ $isChanged ? 'bg-yellow-50' : '' }}">
                                            <td class="px-4 py-2 border font-medium text-gray-800 capitalize">
                                                {{ str_replace('_', ' ', $key) }}
                                            </td>
                                            <td class="px-4 py-2 border {{ $isChanged ? 'text-red-500' : 'text-gray-600' }}">
                                                {{ $oldValue ?: '-' }}
                                            </td>
                                            <td class="px-4 py-2 border {{ $isChanged ? 'text-green-600 font-semibold' : 'text-gray-600' }}">
                                                {{ $newValue ?: '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- FOOTER MODAL (Hanya ada tombol Tutup untuk petugas) --}}
                <div class="flex items-center justify-end p-4 border-t border-gray-200 rounded-b bg-gray-50">
                    <button type="button" onclick="closeModal('modal-detail-{{ $item->id }}')" class="text-gray-600 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-300 text-sm font-medium px-2 py-2 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="m4 10l-.707.707L2.586 10l.707-.707zm17 8a1 1 0 1 1-2 0zM8.293 15.707l-5-5l1.414-1.414l5 5zm-5-6.414l5-5l1.414 1.414l-5 5zM4 9h10v2H4zm17 7v2h-2v-2zm-7-7a7 7 0 0 1 7 7h-2a5 5 0 0 0-5-5z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endforeach

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('bg-gray-900')) {
            event.target.classList.add('hidden');
        }
    }
</script>
@endsection