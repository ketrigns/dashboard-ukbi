@extends('layouts.admin.app')

@section('content')
  <div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
        <h4 class="text-default-900 text-lg font-semibold">Persetujuan Akses Petugas</h4>
    </div>

    {{-- Alert Success / Error (Bisa pakai yang sudah ada di layout utama jika ada) --}}
    @if (session('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="card overflow-hidden">
        <div class="card-header">
            <h4 class="card-title">Daftar Antrean Permintaan Akses</h4>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-default-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-start text-sm text-default-500">No</th>
                        <th class="px-6 py-3 text-start text-sm text-default-500">Nama Petugas</th>
                        <th class="px-6 py-3 text-start text-sm text-default-500">Waktu Request</th>
                        <th class="px-6 py-3 text-center text-sm text-default-500">Status</th>
                        <th class="px-6 py-3 text-end text-sm text-default-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $index => $req)
                        <tr class="odd:bg-white even:bg-default-100 hover:bg-default-200">
                            <td class="px-6 py-4 text-sm text-default-800">{{ $requests->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-sm text-default-800">
                                <div class="font-medium">{{ $req->user->name ?? 'User Tidak Ditemukan' }}</div>
                                <div class="text-xs text-default-500">{{ $req->user->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-default-800">
                                {{ $req->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm">
                                @if($req->status === 'pending')
                                    <span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Menunggu</span>
                                @elseif($req->status === 'approved')
                                    <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">Disetujui</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-end">
                                @if($req->status === 'pending')
                                    <div class="flex justify-end gap-2">
                                        {{-- Tombol Approve --}}
                                        <form action="{{ route('admin.approvals.approve', $req->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1.5 rounded" onclick="return confirm('Setujui akses untuk petugas ini?')">
                                                Setujui
                                            </button>
                                        </form>

                                        {{-- Tombol Reject --}}
                                        <form action="{{ route('admin.approvals.reject', $req->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded" onclick="return confirm('Tolak akses petugas ini?')">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada permintaan akses saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4">
            {{ $requests->links() }}
        </div>
    </div>
@endsection