@extends('layouts.admin.app')

@section('content')
    <div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
        <h4 class="text-default-900 text-lg font-semibold">Data UKBI</h4>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-2">
        @if (session('success'))
            <div id="toast-success"
                class="flex items-center w-72 p-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded-lg shadow-lg animate-fade-in">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-green-600" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 111.414-1.414L8.414 12.172l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div id="toast-error"
                class="flex items-center w-72 p-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg shadow-lg animate-fade-in">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-red-600" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-4h2v2H9v-2zm0-8h2v6H9V6z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('import_errors'))
            <div id="toast-import"
                class="w-80 p-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg shadow-lg animate-fade-in">
                <p class="font-semibold mb-2">Ditemukan beberapa error pada file Anda:</p>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Animasi Fade Out -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toasts = document.querySelectorAll('#toast-container > div');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-x-5');
                    setTimeout(() => toast.remove(), 700);
                }, 3000); // tampil 3 detik
            });
        });
    </script>

    <!-- Tambahkan animasi sederhana Tailwind -->
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        .transition-opacity {
            transition: opacity 0.7s, transform 0.7s;
        }
    </style>


    <div class="mt-8">
        {{-- Tombol Tambah Data (opsional) --}}
        <div class="flex justify-end">
            <a href="{{ route('data-ukbi.create') }}" class="btn bg-primary text-white mb-4">+ Tambah Data</a>
        </div>
        {{-- ALERTS --}}

        {{-- CARD DATA UKBI --}}
        <div class="card overflow-hidden">
            <div class="card-header flex justify-between gap-4">
                <div>
                    <h4 class="card-title">Data UKBI</h4>

                </div>
                <form action="{{ route('data-ukbi.import.handle') }}" method="POST" enctype="multipart/form-data"
                    class="flex items-center gap-2">
                    @csrf
                    <input type="file" name="file" id="file" class="border rounded px-2 py-1 text-sm cursor-pointer"
                        required>
                    <button type="submit" class="btn bg-primary text-white text-sm px-4 py-2">Import File</button>
                </form>
            </div>
            <form action="{{ route('data-ukbi.index') }}" method="GET" class="flex w-full p-4">
  <input 
    type="text" 
    name="search"
    value="{{ request('search') }}"
    placeholder="Cari Nama, No Pendaftaran, Kota, atau Instansi..."
    class="w-full rounded-l-md border border-r-0 border-gray-300 px-4 py-2 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500"
  />

  <button 
    type="submit"
    class="-ml-px rounded-r-md border border-gray-300 bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500">
    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24"><path fill="#ffffff" d="m19.6 21l-6.3-6.3q-.75.6-1.725.95T9.5 16q-2.725 0-4.612-1.888T3 9.5t1.888-4.612T9.5 3t4.613 1.888T16 9.5q0 1.1-.35 2.075T14.7 13.3l6.3 6.3zM9.5 14q1.875 0 3.188-1.312T14 9.5t-1.312-3.187T9.5 5T6.313 6.313T5 9.5t1.313 3.188T9.5 14"/></svg>
  </button>
</form>


            <div class="overflow-x-auto border">
                <table class="min-w-full divide-y divide-default-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">No</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>No Pendaftaran</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'no_pendaftaran',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Tanggal Ujian</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'tanggal_ujian',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Nama Peserta</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'nama_peserta',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>

                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Terdaftar Sebagai</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'terdaftar_sbg',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Jenis Kelamin</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'jenis_kelamin',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Tempat Lahir</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'tempat_lahir',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Tanggal Lahir</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'tanggal_lahir',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Kota</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'kota',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Titik Koordinat</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'titik_koordinat_peta',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Instansi</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'instansi',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Seksi 1</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'seksi_1',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Seksi 2</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'seksi_2',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Seksi 3</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'seksi_3',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Seksi 4</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'seksi_4',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Seksi 5</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'seksi_5',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Skor</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'skor',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500 whitespace-nowrap">
                                <div class="flex items-center gap-2">
  <h1>Predikat</h1>
  <a href="{{ route('data-ukbi.index', [
      'sort' => 'predikat',
      'direction' => request('direction') === 'asc' ? 'desc' : 'asc',
      'search' => request('search') // biar search tetap nyangkut
  ]) }}" class="cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
      viewBox="0 0 24 24"
      class="{{ request('sort') === 'nama_peserta' && request('direction') === 'asc' ? 'rotate-180' : '' }} transition-transform">
      <path fill="#64748b" d="M6.293 4.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L8 7.414V19a1 1 0 1 1-2 0V7.414L3.707 9.707a1 1 0 0 1-1.414-1.414zM16 16.586V5a1 1 0 1 1 2 0v11.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414z"/>
    </svg>
  </a>
</div>
                            
                            </tbody>
                            </th>
                            <th class="px-6 py-3 text-end text-sm text-default-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $index => $item)
                            <tr class="odd:bg-white even:bg-default-100 hover:bg-default-100">
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $data->firstItem() + $index }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->no_pendaftaran ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->tanggal_ujian ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->nama_peserta ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->terdaftar_sbg ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->jenis_kelamin ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->tempat_lahir ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->tanggal_lahir ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->kota ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->titik_koordinat_peta ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->instansi ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->seksi_1 ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->seksi_2 ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->seksi_3 ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->seksi_4 ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->seksi_5 ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->skor ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-default-800 whitespace-nowrap">{{ $item->predikat ?? '-' }}</td>
                                <td class="px-6 py-4 text-end text-sm">
                                    <div class="flex justify-end mt-2">
                                        <a href="{{ route('data-ukbi.edit', $item) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24">
                                                <g fill="none" stroke="#4f46e5" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2">
                                                    <path d="M7 7H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1" />
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0-2.97-2.97L9 12v3h3zM16 5l3 3" />
                                                </g>
                                            </svg>
                                        </a>
                                        <form action="{{ route('data-ukbi.destroy', $item->id) }}" method="POST"
                                            class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-800 delete-btn"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                                    viewBox="0 0 24 24">
                                                    <path fill="#e11d48"
                                                        d="M7 21q-.825 0-1.412-.587T5 19V6H4V4h5V3h6v1h5v2h-1v13q0 .825-.587 1.413T17 21zm2-4h2V8H9zm4 0h2V8h-2z" />
                                                </svg></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data.</td>
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
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // --- LOADING SAAT IMPORT FILE ---
        const importForm = document.querySelector('form[action="{{ route('data-ukbi.import.handle') }}"]');

        if (importForm) {
            importForm.addEventListener('submit', function (e) {
                // Tampilkan SweetAlert Loading
                Swal.fire({
                    title: 'Mengupload...',
                    text: 'Mohon tunggu, file sedang diproses.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        }

        // --- KONFIRMASI DELETE ---
        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data ini tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
