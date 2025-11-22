@extends('layouts.admin.app')

@section('content')
  <div class="max-w-3xl mx-auto">

    <h2 class="text-2xl font-bold mb-6 text-default-900">Profil Akun</h2>

    @if(session('success'))
      <div class="p-3 mb-4 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
      </div>
    @endif

    <div class="card p-6">
      <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Foto Profil --}}
        <div class="flex items-center gap-6 mb-6">
          <img id="profile-preview"
            src="{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : asset('assets/images/gbr-admin.jpeg') }}"
            class="rounded-full object-cover border shadow"
            style="width: 70px; height: 70px;">

          <div>
            <label class="font-medium">Ganti Foto Profil</label>
            <input type="file" name="profile_pic" accept="image/*" class="mt-2 block" onchange="previewImage(event)">
            @error('profile_pic')
              <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Input --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <div>
            <label class="text-sm font-medium">Nama</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}">
            @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}">
            @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="text-sm font-medium">NIP</label>
            <input type="text" name="nip" class="form-input" value="{{ old('nip', $user->nip) }}">
            @error('nip') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>

          {{-- Password --}}
          <div>
            <label>Password Baru</label>

            <div style="position: relative; width: 100%;">
              <input type="password" name="password" id="passwordInput"
                class="form-input" style="padding-right: 40px;">

              <span onclick="togglePassword('passwordInput','iconPassword')" 
                style="
                  position: absolute;
                  right: 10px;
                  top: 50%;
                  transform: translateY(-50%);
                  cursor: pointer;
                  display: flex;
                  align-items: center;
                ">
                <iconify-icon id="iconPassword" icon="mdi:eye-off" width="20"></iconify-icon>
              </span>
            </div>

            @error('password') 
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
            @enderror
          </div>

          {{-- Konfirmasi Password --}}
          <div>
            <label>Konfirmasi Password Baru</label>

            <div style="position: relative; width: 100%;">
              <input type="password" name="password_confirmation" id="passwordConfirm"
                class="form-input" style="padding-right: 40px;">

              <span onclick="togglePassword('passwordConfirm','iconConfirm')" 
                style="
                  position: absolute;
                  right: 10px;
                  top: 50%;
                  transform: translateY(-50%);
                  cursor: pointer;
                  display: flex;
                  align-items: center;
                ">
                <iconify-icon id="iconConfirm" icon="mdi:eye-off" width="20"></iconify-icon>
              </span>
            </div>

            @error('password_confirmation') 
              <p class="text-red-600 text-sm">{{ $message }}</p> 
            @enderror
          </div>

        </div>

        <div class="mt-6">
          <button class="btn bg-blue-600 text-white hover:bg-blue-700 px-6 py-2 rounded">
            Simpan Perubahan
          </button>
        </div>

      </form>
    </div>
  </div>

  <script>
    function previewImage(event) {
      const file = event.target.files[0];
      const preview = document.getElementById('profile-preview');

      if (file) preview.src = URL.createObjectURL(file);
    }

    function togglePassword(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);

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
