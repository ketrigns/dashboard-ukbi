@extends('layouts.app')

@section('title', 'Dashboard UKBI')

@section('content')
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

  <div class="bg-white p-4 rounded mt-4">
    <div id="chart"></div>
  </div>

  <div class="flex gap-4 mt-4">
    <div class="bg-white p-4 rounded flex-1">
      <div id="chartPeujiPredikat"></div>
    </div>
    <div class="bg-white p-4 rounded flex-1">
      <div id="chartSkorPredikat"></div>
    </div>
  </div>

  <div class="flex gap-4 mt-4">
    <div class="bg-white p-4 rounded flex-1">
      <div id="chartMahasiswaPredikat"></div>
    </div>
    <div class="bg-white p-4 rounded flex-1">
      <div id="chartUmumPredikat"></div>
    </div>
  </div>

  <div class="grid md:grid-cols-2 grid-cols-1 gap-4 my-4 print:grid-cols-2">
    <div class="flex gap-4 mt-4">
      <div class="bg-white p-4 rounded flex-1">
        <div id="chartPelajarPredikat"></div>
      </div>
    </div>
    @foreach($groupedData as $jenisPelajar => $dataPelajar)
      @php
        $chartId = 'chart-' . \Illuminate\Support\Str::slug($jenisPelajar);
      @endphp
      <div class="flex">
        <div class="bg-white p-4 rounded flex-1">
          <div id="{{ $chartId }}" class="chart-container" data-chart-data="{{ json_encode($dataPelajar) }}"
            data-chart-id="{{ $chartId }}" data-title="Jumlah Peuji Kategori {{ $jenisPelajar }} berdasarkan Predikat">
          </div>
        </div>
      </div>
    @endforeach

  </div>

  <div class="block">
    <a href="{{ route('predikat.export', ['wilayah' => request('wilayah')]) }}" class="cursor-pointer mt-4 px-4 py-2 bg-blue-600 text-white rounded">
      Unduh Data
    </a>
  </div>

  <script>
    const allCharts = [];

    const rawPredikat = @json($predikatPerTahun);
    
    // Ambil tahun yang tersedia secara dinamis dan urutkan
    const uniqueYears = [...new Set(rawPredikat.map(item => item.tahun))].sort((a, b) => a - b);

    // 1. Definisikan urutan predikat secara manual (Statis)
    // Dengan cara ini, meskipun tidak ada data di database, predikat ini akan tetap dirender dengan nilai 0
    const uniquePredikats = [
        'Istimewa',
        'Sangat Unggul',
        'Unggul',
        'Madya',
        'Semenjana',
        'Marginal',
        'Terbatas',
        'Tidak Berpredikat'
    ];

    // 2. Buat struktur data 'series' yang dibutuhkan ApexCharts
    const seriesData = uniquePredikats.map(predikat => {
      // Untuk setiap predikat, cari totalnya di setiap tahun
      const data = uniqueYears.map(tahun => {
        // Cari data yang cocok berdasarkan tahun dan predikat
        const entry = rawPredikat.find(item => item.tahun === tahun && item.predikat === predikat);
        // Jika ditemukan, kembalikan totalnya. Jika tidak ada, kembalikan 0.
        return entry ? entry.total : 0;
      });

      return {
        name: predikat,
        data: data
      };
    });

    var options = {
      chart: {
        type: 'line',
        height: '300px',
        toolbar: {
          show: true
        }
      },
      title: {
        text: 'Jumlah Peuji berdasarkan Predikat per Tahun',
        align: 'center',
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      series: seriesData,
      xaxis: {
        categories: uniqueYears
      },
      yaxis: {
        title: {
          text: 'Peuji',
          style: {
            color: '#1F2859',
            fontSize: '14px',
            fontWeight: 'bold'
          }
        }
      },
      // Sesuaikan jumlah warna dengan jumlah predikat (7 warna pertama akan dipakai berurutan)
      colors: [
        '#1F77B4', // Istimewa
        '#FF7F0E', // Sangat Unggul
        '#2CA02C', // Unggul
        '#D62728', // Madya
        '#9467BD', // Semenjana
        '#8C564B', // Marginal
        '#E377C2', // Terbatas
      ],
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

    const rawPeujiPredikat = @json($jmlPeujiPredikat);
    const dataPeuji = uniquePredikats.map(predikat => {
      const found = rawPeujiPredikat.find(item => item.predikat === predikat);
      return found ? found.total : 0;
    });

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();

    var optionsPeujiPredikat = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: true // 🔹 Hilangkan tombol download / export
        }
      },
      title: {
        text: 'Jumlah Peuji berdasarkan Predikat', // 🟢 Judul chart
        align: 'center', // bisa 'center', 'center', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: dataPeuji
      }],
      xaxis: {
        categories: uniquePredikats,
        title: {
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            color: '#1F2859'
          }
        }
      },
      yaxis: {
        title: {
          text: 'Peuji',
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            color: '#1F2859'
          }
        },
        labels: {
          style: {
            colors: '#374151'
          }
        }
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
        borderColor: '#e5e7eb',
        strokeDashArray: 4
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#000000']
        }
      }
    };

    var chartPeujiPredikat = new ApexCharts(document.querySelector("#chartPeujiPredikat"), optionsPeujiPredikat);
    chartPeujiPredikat.render();

    const rawSkorPredikat = @json($rerataSkorPredikat);

    const dataSkor = uniquePredikats.map(predikat => {
      const found = rawSkorPredikat.find(item => item.predikat === predikat);
      // Perhatikan di sini kita mengambil .rerata (sesuai query DB::raw Anda)
      return found ? parseFloat(found.rerata) : 0; 
    });

    var optionsSkorPredikat = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: true // 🔹 Hilangkan tombol download / export
        }
      },
      title: {
        text: 'Rata-Rata Skor berdasarkan Predikat',
        align: 'center', // bisa 'center', 'center', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      series: [{
        name: 'Rata-rata Skor',
        data: dataSkor
      }],
      xaxis: {
        categories: uniquePredikats,
        title: {
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            color: '#1F2859'
          }
        }
      },
      yaxis: {
        title: {
          text: 'Skor',
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            color: '#1F2859'
          }
        },
        labels: {
          style: {
            colors: '#374151'
          }
        }
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
        borderColor: '#e5e7eb',
        strokeDashArray: 4
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#000000']
        }
      }
    };

    var chartSkorPredikat = new ApexCharts(document.querySelector("#chartSkorPredikat"), optionsSkorPredikat);
    chartSkorPredikat.render();

    const rawMhs = @json($jmlPeujiMhs);
    const dataMhs = uniquePredikats.map(predikat => {
      const found = rawMhs.find(item => item.predikat === predikat);
      return found ? found.total : 0;
    });

    var optionsMahasiswaPredikat = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: true // 🔹 Hilangkan tombol download / export
        }
      },
      title: {
        text: 'Jumlah Peuji Kategori Mahasiswa berdasarkan Predikat',
        align: 'center',
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },

      series: [{
        name: 'Jumlah Peuji',
        data: dataMhs
      }],
      xaxis: {
        categories: uniquePredikats,
        title: {
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            color: '#1F2859'
          }
        }
      },
      yaxis: {
        title: {
          text: 'Skor',
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            color: '#1F2859'
          }
        },
        labels: {
          style: {
            colors: '#374151'
          }
        }
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
        borderColor: '#e5e7eb',
        strokeDashArray: 4
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#000000']
        }
      }
    };

    var chartMahasiswaPredikat = new ApexCharts(document.querySelector("#chartMahasiswaPredikat"), optionsMahasiswaPredikat);
    chartMahasiswaPredikat.render();

    const rawUmum = @json($jmlPeujiUmum);
    const dataUmum = uniquePredikats.map(predikat => {
      const found = rawUmum.find(item => item.predikat === predikat);
      return found ? found.total : 0;
    });

    var optionsUmumPredikat = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: true // 🔹 Hilangkan tombol download / export
        }
      },
      title: {
        text: 'Jumlah Peuji Kategori Umum berdasarkan Predikat', // 🟢 Judul chart
        align: 'center', // bisa 'center', 'center', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: dataUmum
      }],
      xaxis: {
        categories: uniquePredikats,
        title: {
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            color: '#1F2859'
          }
        }
      },
      yaxis: {
        title: {
          text: 'Skor',
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            color: '#1F2859'
          }
        },
        labels: {
          style: {
            colors: '#374151'
          }
        }
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
        borderColor: '#e5e7eb',
        strokeDashArray: 4
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#000000']
        }
      }
    };

    var chartUmumPredikat = new ApexCharts(document.querySelector("#chartUmumPredikat"), optionsUmumPredikat);
    chartUmumPredikat.render();

    const rawPelajar = @json($jmlPeujiPelajar);
    const dataPelajar = uniquePredikats.map(predikat => {
      const found = rawPelajar.find(item => item.predikat === predikat);
      return found ? found.total : 0;
    });

    var optionsPelajarPredikat = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: true // 🔹 Hilangkan tombol download / export
        }
      },
      title: {
        text: 'Jumlah Peuji Kategori Pelajar berdasarkan Predikat', // 🟢 Judul chart
        align: 'center', // bisa 'center', 'center', atau 'right'
        style: {
          fontSize: '16px',
          fontWeight: 'bold',
          color: '#000'
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: dataPelajar
      }],
      xaxis: {
        categories: uniquePredikats,
        title: {
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            color: '#1F2859'
          }
        }
      },
      yaxis: {
        title: {
          text: 'Skor',
          style: {
            fontSize: '14px',
            fontWeight: 'bold',
            color: '#1F2859'
          }
        },
        labels: {
          style: {
            colors: '#374151'
          }
        }
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
        borderColor: '#e5e7eb',
        strokeDashArray: 4
      },
      dataLabels: {
        enabled: true,
        style: {
          colors: ['#000']
        }
      }
    };

    var chartPelajarPredikat = new ApexCharts(document.querySelector("#chartPelajarPredikat"), optionsPelajarPredikat);
    chartPelajarPredikat.render();


    // 1. Definisikan urutan dan warna baku DI LUAR loop agar efisien
const urutanBakuPredikat = [
  'Istimewa',
  'Sangat Unggul',
  'Unggul',
  'Madya',
  'Semenjana',
  'Marginal',
  'Terbatas'
];

const warnaBaku = [
  '#1F77B4', '#FF7F0E', '#2CA02C', '#D62728', 
  '#9467BD', '#8C564B', '#E377C2'
];

const chartContainers = document.querySelectorAll('.chart-container');

// Loop setiap div container
chartContainers.forEach(container => {

  // Ambil ID dan data dari data-attribute
  const chartId = container.dataset.chartId;
  const chartData = JSON.parse(container.dataset.chartData);
  const chartTitle = container.dataset.title;

  // 2. Petakan data berdasarkan urutan baku (isi 0 jika data tidak ditemukan)
  const seriesData = urutanBakuPredikat.map(predikat => {
    const found = chartData.find(item => item.predikat === predikat);
    return found ? found.total : 0;
  });

  // --- Konfigurasi ApexCharts ---
  var options = {
    chart: {
      type: 'bar',
      height: '300px',
      toolbar: {
        show: true
      }
    },
    title: {
      text: chartTitle,
      align: 'center',
      style: {
        fontSize: '16px',
        fontWeight: 'bold',
        color: '#000'
      }
    },
    series: [{
      name: 'Jumlah Peuji',
      data: seriesData // 🔹 Gunakan data yang sudah dipetakan
    }],
    xaxis: {
      categories: urutanBakuPredikat, // 🔹 Gunakan urutan statis
      title: {
        style: {
          fontSize: '14px',
          fontWeight: 'bold',
          color: '#1F2859'
        }
      }
    },
    yaxis: {
      title: {
        text: 'Peuji', // 🔹 Diubah dari 'Skor' menjadi 'Peuji'
        style: {
          fontSize: '14px',
          fontWeight: 'bold',
          color: '#1F2859'
        }
      },
      labels: {
        style: {
          colors: '#374151'
        }
      }
    },
    plotOptions: {
      bar: {
        distributed: true,
      }
    },
    colors: warnaBaku, // 🔹 Gunakan warna baku yang sudah dirampingkan
    legend: {
      show: false
    },
    grid: {
      borderColor: '#e5e7eb',
      strokeDashArray: 4
    },
    dataLabels: {
      enabled: true,
      style: {
        colors: ['#000']
      }
    }
  };

  // --- Render Chart ---
  var chart = new ApexCharts(document.querySelector("#" + chartId), options);
  chart.render();
  allCharts.push(chart);
});


  </script>
@endsection