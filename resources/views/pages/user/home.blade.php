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

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 my-4 print-grid-4">
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

  <div class="grid sm:grid-cols-2 grid-cols-1 gap-4 my-4">
    <div class="bg-white p-4 rounded">
      <div id="chart"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <div id="chartKategori"></div>
    </div>

  </div>
  <div class="bg-white p-4 rounded">
    <div id="chartWilayah" class="chartWilayah"></div>
  </div>

  <div class="mt-5">
    <a href="{{ route('dashboard.export_excel') }}" class="cursor-pointer mt-4 px-4 py-2 bg-blue-600 text-white rounded">
      Unduh Data
    </a>
  </div>



  <script>
    // Inisialisasi peta di pusat Provinsi Jambi
    const map = L.map('map').setView([-1.6116, 103.6157], 8);

    // Tambahkan layer peta
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // 1. Definisikan urutan baku predikat (termasuk 'Tidak Berpredikat')
    const urutanBakuPredikat = [
        'Istimewa',
        'Sangat Unggul',
        'Unggul',
        'Madya',
        'Semenjana',
        'Marginal',
        'Terbatas',
        'Tidak Berpredikat'
    ];

    // Data dari PHP → JS
    const locations = @json($locations);

    locations.forEach(item => {
      if (item.titik_koordinat_peta) {
        const parts = item.titik_koordinat_peta.split(',').map(Number);

        if (parts.length === 2) {
          // Format data predikat per kota sesuai urutan baku
          let predikatList = '';
          
          if (item.predikat_detail) {
            // 2. Looping menggunakan array baku, BUKAN dari object item.predikat_detail
            urutanBakuPredikat.forEach(predikat => {
              // Ambil nilai predikat jika ada, jika tidak ada set menjadi 0
              const val = item.predikat_detail[predikat] || 0;
              predikatList += `${predikat}: ${val}<br>`;
            });
          }

          L.marker(parts)
            .addTo(map)
            .bindPopup(`
                  <b>${item.kota}</b><br>
                  Jumlah Peserta: ${item.total_peserta}<br><br>
                  <b>Predikat:</b><br>
                  ${predikatList}
            `);
        }
      }
    });


    const predikatData = @json($predikatCounts);
    const predikatCategories = Object.keys(predikatData);
    const predikatValues = Object.values(predikatData);

    var options = {
      chart: {
        type: 'bar',
      },
      title: {
        text: 'Jumlah Peuji Berdasarkan Predikat',
        align: 'center',
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      plotOptions: {
        bar: {
          distributed: true,
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
      colors: ['#8CE4FF', '#FEEE91', '#B7A3E3', '#ECEE81', '#B7E0FF', '#FC4100', '#A1EEBD', '#5272F2'],
      legend: {
        show: false // 🔹 Sembunyikan legend warna
      },

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
      series: @json($kategoriCounts->pluck('total')),
      labels: @json($kategoriCounts->pluck('terdaftar_sbg')),
      colors: [
        '#1F77B4', // biru klasik
        '#FF7F0E', // oranye terang
        '#2CA02C', // hijau cerah
        '#D62728', // merah tua
        '#9467BD', // ungu lembut
        '#8C564B', // cokelat muda
        '#E377C2', // pink lembut
        '#7F7F7F', // abu-abu netral
        '#BCBD22', // kuning zaitun
        '#17BECF', // biru toska

        '#FF6F61', // coral
        '#6B5B95', // ungu royal
        '#88B04B', // hijau zaitun
        '#F7CAC9', // pink pastel
        '#92A8D1', // biru pastel
        '#955251', // maroon muda
        '#B565A7', // ungu muda
        '#009B77', // hijau zamrud
        '#DD4124', // merah oranye
        '#45B8AC', // turquoise

        '#EFC050', // emas terang
        '#5B5EA6', // biru keunguan
        '#9B2335', // merah anggur
        '#DFCFBE', // krem muda
        '#55B4B0', // hijau kebiruan
        '#E15D44', // merah bata
        '#7FCDCD', // cyan lembut
        '#BC243C', // merah crimson
        '#C3447A', // magenta
        '#98B4D4'  // biru muda
      ]
      , // 🔹 Warna berbeda tiap data
      legend: {
        position: 'bottom'
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

      plotOptions: {
        bar: {
          distributed: true,
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
          left: 30
        }
      },

      xaxis: {
        categories: wilayahCategories,
      },

      colors: [
        '#1F77B4', // biru klasik
        '#FF7F0E', // oranye terang
        '#2CA02C', // hijau cerah
        '#D62728', // merah tua
        '#9467BD', // ungu lembut
        '#8C564B', // cokelat muda
        '#E377C2', // pink lembut
        '#7F7F7F', // abu-abu netral
        '#BCBD22', // kuning zaitun
        '#17BECF', // biru toska

        '#FF6F61', // coral
        '#6B5B95', // ungu royal
        '#88B04B', // hijau zaitun
        '#F7CAC9', // pink pastel
        '#92A8D1', // biru pastel
        '#955251', // maroon muda
        '#B565A7', // ungu muda
        '#009B77', // hijau zamrud
        '#DD4124', // merah oranye
        '#45B8AC', // turquoise

        '#EFC050', // emas terang
        '#5B5EA6', // biru keunguan
        '#9B2335', // merah anggur
        '#DFCFBE', // krem muda
        '#55B4B0', // hijau kebiruan
        '#E15D44', // merah bata
        '#7FCDCD', // cyan lembut
        '#BC243C', // merah crimson
        '#C3447A', // magenta
        '#98B4D4'  // biru muda
      ],
      legend: {
        show: false // 🔹 Sembunyikan legend warna
      },
    };

    var chartWilayah = new ApexCharts(
      document.querySelector("#chartWilayah"),
      optionsWilayah
    );
    chartWilayah.render();

  </script>
@endsection