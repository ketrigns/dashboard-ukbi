@extends('layouts.app')

@section('title', 'Dashboard UKBI')

@section('content')
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

  <div class="relative w-full">
    <form action="" method="GET">
      <select name="wilayah" onchange="this.form.submit()"
        class="w-full appearance-none border border-black rounded px-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1F2859] focus:border-[#1F2859]">

        <option value="" {{ request('wilayah') == "" ? 'selected' : '' }}>Semua Wilayah</option>

        @foreach ($allWilayah as $item)
          <option value="{{ $item }}" {{ request('wilayah') == $item ? 'selected' : '' }}>
            {{ strtoupper($item) }}
          </option>
        @endforeach

      </select>
    </form>

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

  <div class="bg-white p-4 rounded">
    <div id="chart"></div>
  </div>
  <div class="grid md:grid-cols-2 grid-cols-1 gap-4 my-4">
    <div class="bg-white p-4 rounded">
      <div id="chartPelajar"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <div id="chartUmum"></div>
    </div>
  </div>

  <script>
    const rawKategori = @json($kategoriPerTahun);
    const uniqueYears = [...new Set(rawKategori.map(item => item.tahun))].sort((a, b) => a - b);
    const uniquePeuji = [...new Set(rawKategori.map(item => item.kategori))].sort();

    const seriesDataKategoriPerTahun = uniquePeuji.map(peuji => {
      // Untuk setiap peuji, cari totalnya di setiap tahun
      const data = uniqueYears.map(tahun => {
        // Cari data yang cocok
        const entry = rawKategori.find(item => item.tahun === tahun && item.kategori === peuji);
        // Jika ditemukan, kembalikan totalnya. Jika tidak, kembalikan 0.
        return entry ? entry.total : 0;
      });

      return {
        name: peuji,
        data: data
      };
    });

    var options = {
      chart: {
        type: 'line',
        height: '400px',
        toolbar: {
          show: true
        }
      },
      title: {
        text: 'Jumlah Peuji berdasarkan Kategori', // 🟢 Judul chart
        align: 'left', // bisa 'left', 'left', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      subtitle: {
        text: {{ number_format($total, 0, ',', '.') }}, // 🟢 tampil di bawah title
        align: 'left',
        style: {
          fontSize: '20px',
          color: '#000'
        }
      },
      series: seriesDataKategoriPerTahun,
      xaxis: {
        categories: uniqueYears
      },
      colors: ['#94B4C1', '#547792', '#1F2859'], // warna unik tiap legend
      stroke: {
        curve: 'smooth',
        width: 3
      },
      markers: {
        size: 5,
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: {
          size: 7
        }
      },
      grid: {
        borderColor: '#e5e7eb',
        strokeDashArray: 4
      },
      legend: {
        position: 'bottom',
        horizontalAlign: 'start',
        fontSize: '14px',
        labels: {
          colors: '#374151'
        },
        markers: {
          radius: 12
        }
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return val + " peserta";
          }
        }
      }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();

    const pelajarData = @json($pelajarCounts);
    const pelajarCategories = Object.keys(pelajarData);
    const pelajarValues = Object.values(pelajarData);
    var optionsPelajar = {
      chart: {
        type: 'pie',
        toolbar: {
          show: true
        }
      },
      title: {
        text: 'Jumlah Peuji berdasarkan Kategori Pelajar', // 🟢 Judul chart
        align: 'left', // bisa 'left', 'left', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      subtitle: {
        text: {{ number_format($pelajar, 0, ',', '.') }}, // 🟢 tampil di bawah title
        align: 'left',
        style: {
          fontSize: '20px',
          color: '#000'
        }
      },
      series: pelajarValues,
      labels: pelajarCategories,
      colors: ['#1F2859', '#547792', '#94B4C1'],
      legend: {
        position: 'bottom',
        fontSize: '14px',
        labels: {
          colors: '#1f2937'
        },
        markers: {
          radius: 12
        }
      },
      plotOptions: {
        pie: {
          dataLabels: {
            offset: -20, // 🔹 geser ke tengah
            minAngleToShowLabel: 10 // biar gak numpuk di slice kecil
          }
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          fontSize: '13px',
          fontWeight: 'bold',
          colors: ['#fff']
        },
        dropShadow: {
          enabled: true,
          top: 1,
          left: 1,
          blur: 2,
          opacity: 0.8
        },
        formatter: function (val) {
          return val.toFixed(1) + '%';
        }
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return val + " peserta";
          }
        }
      },
      stroke: {
        colors: ['#fff']
      }
    };

    var chartPelajar = new ApexCharts(document.querySelector("#chartPelajar"), optionsPelajar);
    chartPelajar.render();

    const umumData = @json($umumCounts);
    const umumCategories = Object.keys(umumData);
    const umumValues = Object.values(umumData);

    var optionsUmum = {
      chart: {
        type: 'pie',
        toolbar: {
          show: true
        }
      },
      title: {
        text: 'Jumlah Peuji berdasarkan Kategori Umum', // 🟢 Judul chart
        align: 'left', // bisa 'left', 'left', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      subtitle: {
        text: {{ number_format($umum, 0, ',', '.') }}, // 🟢 tampil di bawah title
        align: 'left',
        style: {
          fontSize: '20px',
          color: '#000'
        }
      },
      series: umumValues,
      labels: umumCategories,
      colors: ['#1F2859', '#547792', '#94B4C1'],
      legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        fontSize: '14px',
        markers: { radius: 12 }
      },
      plotOptions: {
        pie: {
          dataLabels: {
            offset: -20, // 🔹 geser ke tengah
            minAngleToShowLabel: 10 // biar gak numpuk di slice kecil
          }
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          fontSize: '13px',
          fontWeight: 'bold',
          colors: ['#fff']
        },
        dropShadow: {
          enabled: true,
          top: 1,
          left: 1,
          blur: 2,
          opacity: 0.8
        },
        formatter: function (val) {
          return val.toFixed(1) + '%';
        }
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return val + " peserta";
          }
        }
      },
      stroke: {
        colors: ['#fff']
      }
    };

    var chartUmum = new ApexCharts(document.querySelector("#chartUmum"), optionsUmum);
    chartUmum.render();
  </script>
@endsection