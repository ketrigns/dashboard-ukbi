@extends('layouts.admin.app')

@section('content')
<div class="flex items-center gap-3 text-sm font-semibold mb-5">
        <a href="{{ route('users.index') }}" class="text-sm font-medium text-default-700">Manajemen Pengguna</a>
        <i class="i-tabler-chevron-right text-lg flex-shrink-0 text-default-500 rtl:rotate-180"></i>
        <p class="text-sm font-bold text-default-900">Edit Data Pengguna</p>
    </div>
  <div class="flex flex-col gap-6">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Data Pengguna</h4>
      </div>

      <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="p-6">

        @csrf
        @method('PUT')

        <div class="grid lg:grid-cols-2 gap-6">

          {{-- Nama --}}
          <div>
            <label for="name" class="text-default-800 text-sm font-medium inline-block mb-2">Nama</label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}" required>
            @error('name')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Email --}}
          <div>
            <label for="email" class="text-default-800 text-sm font-medium inline-block mb-2">Email</label>
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}"
              required>
            @error('email')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- NIP --}}
          <div>
            <label>NIP</label>
            <input type="text" name="nip" class="form-input" value="{{ old('nip', $user->nip) }}" required>
            @error('nip')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Password (opsional) --}}
          {{-- Password (opsional) --}}
          <div>
            <label>Password <span class="text-gray-500 text-xs">(kosongkan jika tidak ganti)</span></label>
            <div style="position: relative; width: 100%;">
              <input type="password" name="password" id="passwordInput" class="form-input"
                style="padding-right: 40px;">

              <!-- Icon Mata -->
              <span onclick="togglePassword()" style="
                  position: absolute;
                  right: 10px;
                  top: 50%;
                  transform: translateY(-50%);
                  cursor: pointer;
                  display: flex;
                  align-items: center;
              ">
                <iconify-icon id="toggleIcon" icon="mdi:eye-off" width="20"></iconify-icon>
              </span>
            </div>



            @error('password')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>


        {{-- Role --}}
        <div>
          <label>Role</label>
          <select name="role" class="form-input" required>
            <option value="petugas" {{ $user->role == 'petugas' ? 'selected' : '' }}>Petugas</option>
            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
          </select>
          @error('role')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Foto Profile --}}
        <div>
          <label class="text-default-800 text-sm font-medium inline-block mb-2">
            Foto Profile <span class="text-gray-500 text-xs">(opsional)</span>
          </label>

          <input type="file" name="profile_pic" accept="image/*" class="form-input" onchange="previewImage(event)">

          @error('profile_pic')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror

          {{-- Foto lama --}}
          <div class="mt-4">
            @if ($user->profile_pic)
              <p class="text-xs text-gray-600 mb-1">Foto saat ini:</p>
              <img src="{{ asset('storage/' . $user->profile_pic) }}" id="previewOld"
                class="w-40 h-auto rounded-lg shadow border">
            @endif
          </div>

          {{-- Preview foto baru --}}
          <div class="mt-4">
            <img id="preview" class="hidden w-40 h-auto rounded-lg shadow border" alt="Preview gambar">
          </div>
        </div>

    </div>

    <div class="mt-4">
      <button type="submit" class="btn border-primary text-primary hover:bg-primary hover:text-white">
        Update Data
      </button>
    </div>

    </form>
  </div>
  </div>

  <script>
    function previewImage(event) {
      const input = event.target;
      const preview = document.getElementById('preview');
      const previewOld = document.getElementById('previewOld');

      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.classList.remove('hidden');

          // Sembunyikan foto lama
          if (previewOld) previewOld.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
      } else {
        preview.src = '';
        preview.classList.add('hidden');

        if (previewOld) previewOld.classList.remove('hidden');
      }
    }

    function togglePassword() {
      const input = document.getElementById('passwordInput');
      const icon = document.getElementById('toggleIcon');

      if (input.type === "password") {
        input.type = "text";
        icon.setAttribute("icon", "mdi:eye");
      } else {
        input.type = "password";
        icon.setAttribute("icon", "mdi:eye-off");
      }
    }
  </script>

@endsection