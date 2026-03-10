@extends('layouts.admin.app')

@section('content')
    <div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
        <div class="flex items-center gap-3 text-sm font-semibold mb-5">
            <p class="text-sm font-bold text-default-900">Manajemen Pengguna</p>
        </div>

        @if(auth()->user()->canManageUsers())
            <a href="{{ route('users.create') }}" class="btn bg-primary text-white">+ Tambah Pengguna</a>
        @endif
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
        @if (session('error'))
            <div id="toast-error" class="flex items-center w-72 p-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg shadow-lg animate-fade-in">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-4h2v2H9v-2zm0-8h2v6H9V6z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('error') }}</span>
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
        @keyframes fadeIn { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        .transition-opacity { transition: opacity 0.7s, transform 0.7s; }
    </style>

    <div class="mt-8">
        <div class="card overflow-hidden">
            <div class="card-header ">
                <h4 class="card-title">Daftar Pengguna</h4>
            </div>
            <div>
                <button type="button" id="bulk-delete-btn" style="display: none;"
                    class="mx-4! my-2! btn bg-red-600 hover:bg-red-700 text-white p-2 rounded-md shadow-sm transition-all"
                    title="Hapus Data Terpilih">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M7 21q-.825 0-1.412-.587T5 19V6H4V4h5V3h6v1h5v2h-1v13q0 .825-.587 1.413T17 21zm2-4h2V8H9zm4 0h2V8h-2z" />
                    </svg>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-default-200">
                    <thead>
                        <tr>
                            {{-- Checkbox Select All --}}
                            <th class="px-6 py-3 text-start whitespace-nowrap w-10">
                                @if(auth()->user()->canManageUsers())
                                    <input type="checkbox" id="select_all_ids" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                @endif
                            </th>
                            <th class="px-6 py-3 text-start text-sm text-default-500">Foto</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500">Nama</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500">Email</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500">NIP</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500">Role</th>
                            <th class="px-6 py-3 text-end text-sm text-default-500"></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            <tr class="odd:bg-white even:bg-default-100 hover:bg-default-200" id="tr_{{ $user->id }}">
                                {{-- Checkbox Individual --}}
                                <td class="px-6 py-4">
                                    {{-- Cek: Jangan tampilkan checkbox untuk diri sendiri agar tidak terhapus --}}
                                    @if(auth()->user()->canManageUsers() && auth()->id() !== $user->id)
                                        <input type="checkbox" name="ids" class="checkbox_ids rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" value="{{ $user->id }}">
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if ($user->profile_pic)
                                        <img src="{{ asset('storage/' . $user->profile_pic) }}"
                                            class="w-12 h-12 rounded-full object-cover border shadow">
                                    @else
                                        <img src="{{ asset('assets/images/gbr-admin.jpeg') }}"
                                            class="w-12 h-12 rounded-full object-cover border shadow">
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-default-800">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm text-default-800">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm text-default-800">{{ $user->nip }}</td>

                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-purple-100 text-blue-700' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-end">
                                        <div class="flex justify-end gap-3">
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('users.edit', $user->id) }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
                                                    <g fill="none" stroke="#4f46e5" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                        <path d="M7 7H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1" />
                                                        <path d="M20.385 6.585a2.1 2.1 0 0 0-2.97-2.97L9 12v3h3zM16 5l3 3" />
                                                    </g>
                                                </svg>
                                            </a>

                                            {{-- Tombol Delete Single --}}
                                            @if(auth()->id() !== $user->id)
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="delete-form inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="delete-btn">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
                                                            <path fill="#e11d48" d="M7 21q-.825 0-1.412-.587T5 19V6H4V4h5V3h6v1h5v2h-1v13q0 .825-.587 1.413T17 21zm2-4h2V8H9zm4 0h2V8h-2z" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada pengguna.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Form Hidden untuk Bulk Delete --}}
    <form id="form-bulk-delete" action="{{ route('users.bulk_delete') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
        <input type="hidden" name="ids" id="bulk_delete_ids">
    </form>

    {{-- Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- 1. Script Single Delete ---
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const form = this.closest('form');
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
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            // --- 2. Script Bulk Delete ---
            const selectAllCheckbox = document.getElementById('select_all_ids');
            const allCheckboxes = document.querySelectorAll('.checkbox_ids');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

            if(selectAllCheckbox) { // Cek jika elemen ada (role admin)
                // Update Tombol Visibility
                function updateBulkButton() {
                    const checkedCount = document.querySelectorAll('.checkbox_ids:checked').length;
                    if (checkedCount > 0) {
                        bulkDeleteBtn.style.display = 'inline-flex';
                    } else {
                        bulkDeleteBtn.style.display = 'none';
                    }
                }

                // Select All Logic
                selectAllCheckbox.addEventListener('change', function() {
                    allCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkButton();
                });

                // Individual Checkbox Logic
                allCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (!this.checked) selectAllCheckbox.checked = false;
                        if(document.querySelectorAll('.checkbox_ids:checked').length === allCheckboxes.length){
                            selectAllCheckbox.checked = true;
                        }
                        updateBulkButton();
                    });
                });

                // Bulk Delete Action with SweetAlert
                bulkDeleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const allIds = [];
                    document.querySelectorAll('.checkbox_ids:checked').forEach(checkbox => {
                        allIds.push(checkbox.value);
                    });

                    if (allIds.length === 0) return;

                    Swal.fire({
                        title: 'Hapus pengguna terpilih?',
                        text: `Anda akan menghapus ${allIds.length} pengguna.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus Semua!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('bulk_delete_ids').value = allIds.join(',');
                            document.getElementById('form-bulk-delete').submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection