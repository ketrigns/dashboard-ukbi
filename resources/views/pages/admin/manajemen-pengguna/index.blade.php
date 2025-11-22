@extends('layouts.admin.app')

@section('content')
    <div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
        <h4 class="text-default-900 text-lg font-semibold">Manajemen Pengguna</h4>
        <a href="{{ route('users.create') }}" class="btn bg-primary text-white">+ Tambah Pengguna</a>
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
        <div class="card overflow-hidden">
            <div class="card-header">
                <h4 class="card-title">Daftar Pengguna</h4>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-default-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-start text-sm text-default-500">Foto</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500">Nama</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500">Email</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500">NIP</th>
                            <th class="px-6 py-3 text-start text-sm text-default-500">Role</th>
                            <th class="px-6 py-3 text-end text-sm text-default-500">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            <tr class="odd:bg-white even:bg-default-100 hover:bg-default-200">
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
                                    <span
                                        class="px-3 py-1 rounded-full text-xs
                                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-purple-100 text-blue-700' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-end">
                                    <div class="flex justify-end gap-3">

                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('users.edit', $user->id) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
                                                <g fill="none" stroke="#4f46e5" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2">
                                                    <path d="M7 7H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1" />
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0-2.97-2.97L9 12v3h3zM16 5l3 3" />
                                                </g>
                                            </svg>
                                        </a>

                                        {{-- Tombol Delete --}}
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                            class="delete-form inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="delete-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                                    viewBox="0 0 24 24">
                                                    <path fill="#e11d48"
                                                        d="M7 21q-.825 0-1.412-.587T5 19V6H4V4h5V3h6v1h5v2h-1v13q0 .825-.587 1.413T17 21zm2-4h2V8H9zm4 0h2V8h-2z" />
                                                </svg>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada pengguna.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

{{-- Script Delete Confirm --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
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
    });
</script>