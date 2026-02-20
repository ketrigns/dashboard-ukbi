@extends('layouts.app')

@section('title', 'Dashboard UKBI')

@section('content')

  <div class=" mx-auto p-6 bg-white rounded-xl shadow-lg">

    {{-- <form method="GET">
      <div>
        <label for="rangePicker" class="block text-sm font-medium">
          Pilih Rentang Tanggal:
        </label>

        <div class="mt-1 flex items-start gap-2">

          <div class="relative flex-grow rounded-md shadow-sm">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
              <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                  d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002 2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1 1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                  clip-rule="evenodd" />
              </svg>
            </div>

            <input type="text" id="rangePicker" class="block w-full appearance-none rounded-md border border-gray-300 
                                                  px-3 py-2 pl-10 
                                                  text-gray-900 placeholder-gray-400 
                                                  focus:border-[#1F2859] focus:outline-none focus:ring-[#1F2859] 
                                                  sm:text-sm cursor-pointer" placeholder="Pilih rentang..."
              value="{{ $startDate }} - {{ $endDate }}">
          </div>

          <div class="flex-shrink-0">
            <button type="submit"
              class="rounded-md cursor-pointer border border-transparent 
                                                   bg-[#1F2859] px-4 py-2 
                                                   text-sm font-medium text-white shadow-sm 
                                                   hover:bg-[#3c4dac] 
                                                   focus:outline-none focus:ring-2 focus:ring-[#1F2859] focus:ring-offset-2">
              Terapkan
            </button>
          </div>

        </div>
      </div>

      <input type="hidden" name="tanggal_mulai" id="hidden_tanggal_mulai" value="{{ $startDate }}">
      <input type="hidden" name="tanggal_selesai" id="hidden_tanggal_selesai" value="{{ $endDate }}">
    </form> --}}

    <form method="GET" class="space-y-3">
      <label class="text-sm font-medium text-gray-700">
        Pilih Rentang Tanggal:
      </label>

      <div class="flex flex-col sm:flex-row gap-3 items-end">
        <div class="flex-1">
          <label for="tanggal_mulai" class="block text-xs text-gray-600 mb-1">Tanggal Mulai</label>
          <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ $startDate }}" class="block w-full rounded-md border border-gray-300 bg-white
                        px-3 py-2 shadow-sm focus:border-[#1F2859] focus:ring-[#1F2859] sm:text-sm">
        </div>

        <div class="flex-1">
          <label for="tanggal_selesai" class="block text-xs text-gray-600 mb-1">Tanggal Selesai</label>
          <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ $endDate }}" class="block w-full rounded-md border border-gray-300 bg-white
                        px-3 py-2 shadow-sm focus:border-[#1F2859] focus:ring-[#1F2859] sm:text-sm">
        </div>

        <div>
          <button type="submit" class="cursor-pointer rounded-md border border-transparent 
                         bg-[#1F2859] px-4 py-2 
                         text-sm font-medium text-white shadow-sm 
                         hover:bg-[#3c4dac] 
                         focus:outline-none focus:ring-2 focus:ring-[#1F2859] focus:ring-offset-2">
            Terapkan
          </button>
        </div>
      </div>
    </form>

  </div>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 my-4 print:grid-cols-4">
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

  <div class="grid grid-cols-2 gap-4 my-4">
    <div class="flex">
      <div class="bg-white p-4 rounded flex-1">
        <div id="chart"></div>
      </div>
    </div>
    <div class="flex">
      <div class="bg-white p-4 rounded flex-1">
        <div id="chartKategori"></div>
      </div>

    </div>
  </div>

  <div class="bg-white p-4 rounded">
    <div id="chartWilayah"></div>
  </div>

  <form action="{{ route('user.tahun.export') }}">
    <input type="hidden" name="tanggal_mulai" value="{{ $startDate }}">
    <input type="hidden" name="tanggal_selesai" value="{{ $endDate }}">
    <button type="submit" class="cursor-pointer mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded inline-block">
      Unduh Data
    </button>
  </form>

  <script>
    // 1. Definisikan urutan baku dan warnanya di awal agar bisa dipakai di Peta & Chart
    const urutanBakuPredikat = [
        'Istimewa',
        'Sangat Unggul',
        'Unggul',
        'Madya',
        'Semenjana',
        'Marginal',
        'Terbatas',
        'Tidak Berpredikat' // 🔹 Tambahan
    ];

    const warnaBaku = [
        '#1F77B4', '#FF7F0E', '#2CA02C', '#D62728', 
        '#9467BD', '#8C564B', '#E377C2', '#7F7F7F'
    ];

    // ==========================================
    // BAGIAN PETA (LEAFLET)
    // ==========================================
    const map = L.map('map').setView([-1.6116, 103.6157], 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const locations = @json($locations);

    locations.forEach(item => {
      if (item.titik_koordinat_peta) {
        const parts = item.titik_koordinat_peta.split(',').map(Number);

        if (parts.length === 2) {
          // 🔹 Looping berdasarkan urutan baku, bukan object bawaan
          let predikatList = '';
          if (item.predikat_detail) {
            urutanBakuPredikat.forEach(predikat => {
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

    // ==========================================
    // BAGIAN CHART (APEXCHARTS)
    // ==========================================
    // Ambil data mentah dari PHP
    const rawJmlPeuji = @json($jmlPeujiPredikat);

    // 🔹 Petakan data sesuai urutan baku (isi 0 jika kosong)
    const dataJmlPeuji = urutanBakuPredikat.map(predikat => {
        const found = rawJmlPeuji.find(item => item.predikat === predikat);
        return found ? found.total : 0;
    });

    var options = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: true
        }
      },
      title: {
        text: 'Jumlah Peuji berdasarkan Predikat',
        align: 'center',
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: dataJmlPeuji // 🔹 Gunakan data yang sudah urut
      }],
      xaxis: {
        categories: urutanBakuPredikat // 🔹 Gunakan kategori yang sudah urut
      },
      plotOptions: {
        bar: {
          distributed: true,
        }
      },
      colors: warnaBaku, // 🔹 Gunakan 8 warna yang sudah dirampingkan
      legend: {
        show: false 
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#000']
        }
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
      ],
      legend: {
        position: 'bottom'
      },
    };

    var chartKategori = new ApexCharts(document.querySelector("#chartKategori"), optionsKategori);
    chartKategori.render();

    var optionsWilayah = {
      chart: {
        type: 'bar',
        height: '500px',
        toolbar: {
          show: true // 🔹 Hilangkan tombol download / export
        }
      },
      title: {
        text: 'Jumlah Peuji berdasarkan Wilayah', // 🟢 Judul chart
        align: 'center', // bisa 'left', 'center', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: @json($jmlPeujiWilayah->pluck('total'))
      }],
      xaxis: {
        categories: @json($jmlPeujiWilayah->pluck('kota'))
      },
      plotOptions: {
        bar: {
          distributed: true,
        }
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
      grid: {
        padding: {
          bottom: 100,
          left: 20
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#000']
        }
      },
    };

    var chartWilayah = new ApexCharts(document.querySelector("#chartWilayah"), optionsWilayah);
    chartWilayah.render();

    // flatpickr("#rangePicker", {
    //   "locale": "id",

    //   mode: "range",
    //   dateFormat: "Y-m-d",
    //   altInput: true,
    //   altFormat: "j F Y",

    //   onClose: function (selectedDates) {
    //     if (selectedDates.length === 2) {
    //       const startDateInput = document.getElementById('hidden_tanggal_mulai');
    //       const endDateInput = document.getElementById('hidden_tanggal_selesai');

    //       startDateInput.value = flatpickr.formatDate(selectedDates[0], "Y-m-d");
    //       endDateInput.value = flatpickr.formatDate(selectedDates[1], "Y-m-d");
    //     }
    //   }
    // });

  </script>
@endsection