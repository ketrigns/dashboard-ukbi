@extends('layouts.app')

@section('title', 'Hasil Data Mining')

@section('content')
  <div>
    <h1 class="text-2xl text-[45px] font-bold">Hasil Data Mining</h1>

    <div class="mt-8">
      @if($data)
        <img src="{{ asset('storage/' . $data->gambar) }}" alt="Gambar Hasil Data Mining"
          class="w-full h-auto rounded-lg shadow-md">
        <div class="mt-5">
          {!! $data->deskripsi !!}
        </div>
      @else
        <div class="p-4 text-center text-gray-500 bg-gray-100 rounded-lg">
          <p>Gambar hasil data mining Belum diupload.</p>
        </div>
      @endif

    </div>

  </div>
  <button onclick="printChart()" class="cursor-pointer mt-4 px-4 py-2 bg-blue-600 text-white rounded">
    Print Halaman
  </button>

  <script>
    function printChart() {
      const navMenu = document.getElementById('nav-menu');
      const menuToggle = document.getElementById('menu-toggle');
      const loader = document.getElementById('print-loader');

      // Simpan inline-style asli
      const originalStyle = navMenu.getAttribute('style') || '';
      const originalToggleStyle = menuToggle.getAttribute('style') || '';
      menuToggle.style.display = "none";

      // Inject style print mode
      navMenu.style.display = "flex";
      navMenu.style.flexDirection = "row";
      navMenu.style.maxHeight = "none";
      navMenu.style.opacity = "1";

      loader.classList.remove("hidden");

      setTimeout(() => {
        loader.classList.add("hidden");
        window.print();
      }, 1000);

      window.addEventListener('afterprint', () => {
        // Kembalikan style asli
        navMenu.setAttribute('style', originalStyle);
        menuToggle.setAttribute('style', originalToggleStyle);

      }, { once: true });
    }
  </script>
@endsection