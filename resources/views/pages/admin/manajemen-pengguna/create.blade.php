@extends('layouts.admin.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Input Data Manajemen Pengguna</h4>
      </div>

      <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf

        <div class="grid lg:grid-cols-2 gap-6">
          
          <div>
            <label for="name" class="text-default-800 text-sm font-medium inline-block mb-2">
              Nama
            </label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
            @error('name')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="email" class="text-default-800 text-sm font-medium inline-block mb-2">
              Email
            </label>
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required>
            @error('email')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label>NIP</label>
            <input type="text" name="nip" class="form-input" value="{{ old('nip') }}" required>
            @error('nip')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label>Password</label>

            <div style="position: relative; width: 100%;">
              <input type="password" name="password" id="passwordInput" class="form-input" required
                style="padding-right: 40px;">

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

          <div>
            <label>Role</label>
            <select name="role" class="form-input" required>
              <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
              @if(auth()->check() && auth()->user()->role === 'admin')
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
              @endif
            </select>
            @error('role')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="gambar" class="text-default-800 text-sm font-medium inline-block mb-2">
              Foto Profile <span class="text-gray-500 text-xs">(opsional)</span>
            </label>

            <input type="file" name="profile_pic" accept="image/*" id="gambar" class="form-input"
              onchange="previewImage(event)">

            @error('profile_pic')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror

            <div class="mt-4">
              <img id="preview" class="hidden w-60 h-auto rounded-lg shadow-md border" alt="Preview gambar">
            </div>
          </div>

        </div>

        <div class="mt-4">
          <button type="submit" class="btn border-primary text-primary hover:bg-primary hover:text-white">
            Tambah Data
          </button>
        </div>
    </form>
    </div>
  </div>

  <script>
    function previewImage(event) {
      const input = event.target;
      const preview = document.getElementById('preview');

      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
      } else {
        preview.src = '';
        preview.classList.add('hidden');
      }
    }

    function togglePassword() {
      const input = document.getElementById('passwordInput');
      const icon = document.getElementById('toggleIcon');

      if (input.type === "password") {
        input.type = "text";
        icon.setAttribute("icon", "mdi:eye"); // icon jadi 'lihat'
      } else {
        input.type = "password";
        icon.setAttribute("icon", "mdi:eye-off"); // icon jadi 'tidak lihat'
      }
    }
  </script>
@endsection