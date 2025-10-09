@extends('layouts.app')

@section('title', 'Dashboard UKBI')

@section('content')
<div class="relative w-full">
    <select
      class="w-full appearance-none border border-black rounded px-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1F2859] focus:border-[#1F2859]">
      <option>Tahun 2024</option>
      <option>Tahun 2023</option>
      <option>Tahun 2022</option>
      <option>Tahun 2021</option>
    </select>

    <!-- Icon kiri -->
    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-black">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
        class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </span>

    <!-- Icon panah kanan -->
    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
        class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9.75L12 13.5l3.75-3.75" />
      </svg>
    </span>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 my-4">
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji</h1>
      <p class="text-[32px] font-regular leading-tight">9.453</p>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji Pelajar</h1>
      <p class="text-[32px] font-regular leading-tight">7.207</p>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji Mahasiswa</h1>
      <p class="text-[32px] font-regular leading-tight">1.108</p>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji Umum</h1>
      <p class="text-[32px] font-regular leading-tight">1.228</p>
    </div>
  </div>

  {{-- Map --}}
  <div id="map" class="rounded" style="height: 400px; overflow: hidden;"></div>

  <div class="grid grid-cols-3 gap-4 my-4">
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji berdasarkan Predikat</h1>
      <div id="chart"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji berdasarkan Kategori</h1>
      <div id="chartKategori"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji berdasarkan Wilayah</h1>
      <div id="chartWilayah"></div>
    </div>
  </div>

  <script>
    // Inisialisasi peta di pusat Provinsi Jambi
    const map = L.map('map').setView([-1.6116, 103.6157], 8);

    // Tambahkan layer peta dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Daftar lokasi di Provinsi Jambi
    const locations = [
      { name: "Kota Jambi", coords: [-1.6101, 103.6131] },
      { name: "Muaro Jambi", coords: [-1.5565, 103.7264] },
      { name: "Sungai Penuh", coords: [-2.0631, 101.3843] },
      { name: "Kerinci", coords: [-2.1333, 101.6167] },
      { name: "Tebo", coords: [-1.4897, 102.3329] },
      { name: "Sarolangun", coords: [-2.3059, 102.6906] },
      { name: "Batanghari", coords: [-1.7089, 103.0826] },
      { name: "Bungo", coords: [-1.4867, 101.9014] },
      { name: "Tanjung Jabung Timur", coords: [-1.1352, 103.9322] },
      { name: "Tanjung Jabung Barat", coords: [-0.8119, 103.4613] },
    ];

    // Tambahkan marker untuk setiap lokasi
    locations.forEach(loc => {
      L.marker(loc.coords)
        .addTo(map);
    });

    var options = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: false // 🔹 Hilangkan tombol download / export
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: [280, 320, 300, 280, 50, 120]
      }],
      xaxis: {
        categories: ['Istimewa', 'Sangat Unggul', 'Unggul', 'Madya', 'Semenjana', 'Marginal']
      },
      colors: ['#1F2859'],
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();

    // === DONUT CHART ===
    var optionsKategori = {
      chart: {
        type: 'donut',
        toolbar: {
          show: false // 🔹 Hilangkan tombol download juga di donut
        }
      },
      series: [44, 55, 13],
      labels: ['Pelajar', 'Mahasiswa', 'Umum'],
      colors: ['#1F2859', '#547792', '#94B4C1'], // 🔹 Warna berbeda tiap data
      legend: {
        position: 'right'
      },
    };

    var chartKategori = new ApexCharts(document.querySelector("#chartKategori"), optionsKategori);
    chartKategori.render();

    var optionsWilayah = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: false // 🔹 Hilangkan tombol download / export
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: [280, 320, 300, 280, 50, 120]
      }],
      xaxis: {
        categories: ['Jambi', 'Kerinci', 'Merangin', 'Batanghari', 'Sarolangun', 'Tebo']
      },
      colors: ['#1F2859'],
    };

    var chartWilayah = new ApexCharts(document.querySelector("#chartWilayah"), optionsWilayah);
    chartWilayah.render();

  </script>
@endsection