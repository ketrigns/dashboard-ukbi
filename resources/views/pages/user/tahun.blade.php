@extends('layouts.app')

@section('title', 'Dashboard UKBI')

@section('content')

  <div class="w-full mx-auto p-6 bg-white rounded-xl shadow-lg">

    <form method="GET">
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
            <button type="submit" class="rounded-md cursor-pointer border border-transparent 
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
    </form>
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

  <div class="grid grid-cols-2 gap-4 my-4">
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji berdasarkan Predikat</h1>
      <div id="chart"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji berdasarkan Kategori</h1>
      <div id="chartKategori"></div>
    </div>
  </div>

  <div class="bg-white p-4 rounded">
      <h1 class="text-[16px] font-medium leading-tight">Jumlah Peuji berdasarkan Wilayah</h1>
      <div id="chartWilayah"></div>
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
        data: @json($jmlPeujiPredikat->pluck('total'))
      }],
      xaxis: {
        categories: @json($jmlPeujiPredikat->pluck('predikat'))
      },
      colors: ['#1F2859'],
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
          show: false // 🔹 Hilangkan tombol download juga di donut
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

    var optionsWilayah = {
      chart: {
        type: 'bar',
        height: '500px',
        toolbar: {
          show: false // 🔹 Hilangkan tombol download / export
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: @json($jmlPeujiWilayah->pluck('total'))
      }],
      xaxis: {
        categories: @json($jmlPeujiWilayah->pluck('kota'))
      },
      colors: ['#1F2859'],
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

    flatpickr("#rangePicker", {
      "locale": "id", // "id" adalah kode untuk Bahasa Indonesia

      mode: "range",
      dateFormat: "Y-m-d",
      altInput: true,
      altFormat: "j F Y", // "F" sekarang akan menjadi "Oktober", "November", dll.

      onClose: function (selectedDates) {
        if (selectedDates.length === 2) {
          const startDateInput = document.getElementById('hidden_tanggal_mulai');
          const endDateInput = document.getElementById('hidden_tanggal_selesai');

          startDateInput.value = flatpickr.formatDate(selectedDates[0], "Y-m-d");
          endDateInput.value = flatpickr.formatDate(selectedDates[1], "Y-m-d");
        }
      }
    });

  </script>
@endsection