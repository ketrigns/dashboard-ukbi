@extends('layouts.app')

@section('title', 'Dashboard UKBI')

@section('content')
  <div>
    <h1 class="text-2xl text-[45px] font-bold">Apa itu UKBI Adaptif?</h1>
    <p class="text-[20px] font-regular">
      UKBI adalah sarana uji untuk mengukur tingkat kemahiran seseorang dalam berbahasa Indonesia, baik lisan maupun
      tulis. UKBI terdiri atas lima seksi, yaitu Seksi I Mendengarkan, Seksi II Merespons Kaidah, Seksi III Membaca,
      Seksi IV Menulis, dan Seksi V Berbicara yang dilaksanakan secara daring.
    </p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 my-4">
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji</h1>
      <p class="text-[32px] font-regular leading-tight">{{ number_format($total, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji Pelajar</h1>
      <p class="text-[32px] font-regular leading-tight">{{ number_format($pelajar, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji Mahasiswa</h1>
      <p class="text-[32px] font-regular leading-tight">{{ number_format($mahasiswa, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji Umum</h1>
      <p class="text-[32px] font-regular leading-tight">{{ number_format($umum, 0, ',', '.') }}</p>
    </div>
  </div>

  {{-- Map --}}
  <div id="map" class="rounded" style="height: 400px; overflow: hidden;"></div>

  {{-- <div class="grid grid-cols-3 gap-4 my-4">
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
  </div> --}}

  <div class="grid grid-cols-2 gap-4 my-4">
    <div class="bg-white p-4 rounded">
      <div id="chart"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <div id="chartKategori"></div>
    </div>

  </div>
  <div class="bg-white p-4 rounded">
    <div id="chartWilayah"></div>
  </div>


  <script>
    // Inisialisasi peta di pusat Provinsi Jambi
    const map = L.map('map').setView([-1.6116, 103.6157], 8);

    // Tambahkan layer peta
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Data dari PHP → JS
    const locations = @json($locations);

    locations.forEach(item => {
      if (item.titik_koordinat_peta) {
        const parts = item.titik_koordinat_peta.split(',').map(Number);

        if (parts.length === 2) {
          L.marker(parts)
            .addTo(map)
            .bindPopup(`
                          <b>${item.kota}</b><br>
                          Jumlah Peserta: ${item.total_peserta}
                        `);
        }
      }
    })

    const predikatData = @json($predikatCounts);
    const predikatCategories = Object.keys(predikatData);
    const predikatValues = Object.values(predikatData);

    var options = {
      chart: {
        type: 'bar',
      },
      title: {
        text: 'Jumlah Peuji Berdasarkan Predikat', // 🟢 Judul chart
        align: 'center', // bisa 'left', 'center', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          fontSize: '12px',
          colors: ['#000']
        },
      },
      series: [{
        name: 'Jumlah Peuji',
        data: predikatValues
      }],
      xaxis: {
        categories: predikatCategories
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
          show: true 
        }
      },
      title: {
        text: 'Jumlah Peuji Berdasarkan Kategori', // 🟢 Judul chart
        align: 'center', // bisa 'left', 'center', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      series: [{{ $pelajar }}, {{ $mahasiswa }}, {{ $umum }}],
      labels: ['Pelajar', 'Mahasiswa', 'Umum'],
      colors: ['#1F2859', '#547792', '#94B4C1'], // 🔹 Warna berbeda tiap data
      legend: {
        position: 'right'
      },
    };

    var chartKategori = new ApexCharts(document.querySelector("#chartKategori"), optionsKategori);
    chartKategori.render();

    const wilayahCounts = @json($wilayahCounts);

    const wilayahCategories = Object.keys(wilayahCounts);

    const wilayahValues = Object.values(wilayahCounts);

    var optionsWilayah = {
      chart: {
        type: 'bar',
        toolbar: {
          show: true
        },
        zoom: {
          enabled: true
        }
      },
      title: {
        text: 'Jumlah Peuji Berdasarkan Wilayah', // 🟢 Judul chart
        align: 'center', // bisa 'left', 'center', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          fontSize: '12px',
          colors: ['#000']
        },
      },

      series: [{
        name: 'Jumlah Peuji',
        data: wilayahValues
      }],

      grid: {
        padding: {
          bottom: 100,
          left: 20
        }
      },

      xaxis: {
        categories: wilayahCategories,
      },

      colors: ['#1F2859'],
    };

    var chartWilayah = new ApexCharts(
      document.querySelector("#chartWilayah"),
      optionsWilayah
    );
    chartWilayah.render();


  </script>
@endsection