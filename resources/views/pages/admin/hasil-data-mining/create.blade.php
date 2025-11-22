@extends('layouts.admin.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Input Data UKBI</h4>
      </div>

      <form id="formDataMining" action="{{ route('hasil-data-mining.store') }}" method="POST" class="p-6"
        enctype="multipart/form-data">
        @csrf

        <div class="grid lg:grid-cols-1 gap-6">
          <div>
            <label for="gambar" class="text-default-800 text-sm font-medium inline-block mb-2">
              Gambar Data Mining
            </label>
            <input type="file" name="gambar" accept="image/*" id="gambar" class="form-input"
              onchange="previewImage(event)" required>
            @error('gambar')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror

            <!-- Tempat preview -->
            <div class="mt-4">
              <img id="preview" class="hidden w-60 h-auto rounded-lg shadow-md border" alt="Preview gambar">
            </div>
          </div>
          <textarea class="prosess" id="example" name="deskripsi"></textarea>
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
    // Simpan instance Froala agar bisa dipanggil
    let froala;

    document.addEventListener("DOMContentLoaded", function () {
      froala = new FroalaEditor('#example', {
        toolbarButtons: [
          ['fontSize'],
          ['bold', 'italic', 'underline', 'strikeThrough'],
          ['formatOL', 'formatUL'],
          ['align', 'undo', 'redo', 'clearFormatting'],
          ['insertLink']
        ],
        quickInsertEnabled: false,
        imageUpload: false,
        videoUpload: false,
        fileUpload: false,
        toolbarSticky: false,
        height: 300,
        fontSizeSelection: true,
        fontSizeDefaultSelection: '14',
        fontSize: ['10', '12', '14', '16', '18', '20', '24', '28', '32'],
      });
    });


    // VALIDASI SWEETALERT SEBELUM SUBMIT
    document.getElementById("formDataMining").addEventListener("submit", function (e) {
      const content = froala.html.get().trim().replace(/<[^>]*>/g, '').trim(); // hilangkan tag HTML

      if (content.length === 0) {
        e.preventDefault();

        Swal.fire({
          icon: 'warning',
          title: 'Deskripsi kosong!',
          text: 'Silakan isi deskripsi terlebih dahulu.',
          confirmButtonColor: '#3085d6',
        });

        return false;
      }
    });

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
  </script>
@endsection