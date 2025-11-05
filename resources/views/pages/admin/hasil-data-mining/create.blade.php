@extends('layouts.admin.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Input Data UKBI</h4>
      </div>

      <form action="{{ route('hasil-data-mining.store') }}" method="POST" class="p-6" enctype="multipart/form-data">
        @csrf
        
        <div class="grid lg:grid-cols-2 gap-6">
          <div>
            <label for="gambar" class="text-default-800 text-sm font-medium inline-block mb-2">
              Gambar Data Mining
            </label>
            <input type="file" name="gambar" accept="image/*" id="gambar" class="form-input" onchange="previewImage(event)">
            @error('gambar')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror

            <!-- Tempat preview -->
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

  {{-- Script Preview Gambar --}}
  <script>
    function previewImage(event) {
      const input = event.target;
      const preview = document.getElementById('preview');

      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
      } else {
        preview.src = '';
        preview.classList.add('hidden');
      }
    }
  </script>
@endsection
