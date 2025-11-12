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

  <div class="grid md:grid-cols-2 grid-cols-1 gap-4 my-4">
    <div class="bg-white p-4 rounded col-span-2">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji berdasarkan Predikat per Tahun</h1>
      <div id="chart"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji berdasarkan Predikat</h1>
      <div id="chartPeujiPredikat"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Rata-Rata Skor berdasarkan Predikat</h1>
      <div id="chartSkorPredikat"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji Kategori Mahasiswa berdasarkan Predikat</h1>
      <div id="chartMahasiswaPredikat"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji Kategori Umum berdasarkan Predikat</h1>
      <div id="chartUmumPredikat"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji Kategori Pelajar berdasarkan Predikat</h1>
      <div id="chartPelajarPredikat"></div>
    </div>

    @foreach($groupedData as $jenisPelajar => $dataPelajar)
      @php
        $chartId = 'chart-' . \Illuminate\Support\Str::slug($jenisPelajar);
      @endphp

      <div class="bg-white p-4 rounded">
        <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji Kategori {{ $jenisPelajar }} berdasarkan Predikat
        </h1>
        <div id="{{ $chartId }}" class="chart-container" data-chart-data="{{ json_encode($dataPelajar) }}"
          data-chart-id="{{ $chartId }}">
        </div>
      </div>
    @endforeach
  </div>

  <script>
    const rawPredikat = @json($predikatPerTahun);
    const uniqueYears = [...new Set(rawPredikat.map(item => item.tahun))].sort((a, b) => a - b);

    // 2. Dapatkan semua predikat unik dan urutkan
    const uniquePredikats = [...new Set(rawPredikat.map(item => item.predikat))].sort();

    // 3. Buat struktur data 'series' yang dibutuhkan ApexCharts
    const seriesData = uniquePredikats.map(predikat => {
      // Untuk setiap predikat, cari totalnya di setiap tahun
      const data = uniqueYears.map(tahun => {
        // Cari data yang cocok
        const entry = rawPredikat.find(item => item.tahun === tahun && item.predikat === predikat);
        // Jika ditemukan, kembalikan totalnya. Jika tidak, kembalikan 0.
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
          show: false
        }
      },
      series: seriesData,
      xaxis: {
        categories: uniqueYears
      },
      yaxis: {
        title: {
          text: 'Peuji', // 🔹 Judul Y-axis
          style: {
            color: '#1F2859',
            fontSize: '14px',
            fontWeight: 'bold'
          }
        }
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

    var optionsPeujiPredikat = {
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
        categories: @json($jmlPeujiPredikat->pluck('predikat')),
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
      colors: ['#1F2859'],
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

    var optionsSkorPredikat = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: false // 🔹 Hilangkan tombol download / export
        }
      },
      series: [{
        name: 'Rata-rata Skor',
        data: @json($rerataSkorPredikat->pluck('rerata'))
      }],
      xaxis: {
        categories: @json($rerataSkorPredikat->pluck('predikat')),
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
      colors: ['#1F2859'],
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

    var optionsMahasiswaPredikat = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: false // 🔹 Hilangkan tombol download / export
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: @json($jmlPeujiMhs->pluck('total'))
      }],
      xaxis: {
        categories: @json($jmlPeujiMhs->pluck('predikat')),
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
      colors: ['#1F2859'],
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

    var optionsUmumPredikat = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: false // 🔹 Hilangkan tombol download / export
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: @json($jmlPeujiUmum->pluck('total'))
      }],
      xaxis: {
        categories: @json($jmlPeujiUmum->pluck('predikat')),
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
      colors: ['#1F2859'],
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

    var optionsPelajarPredikat = {
      chart: {
        type: 'bar',
        height: '300px',
        toolbar: {
          show: false // 🔹 Hilangkan tombol download / export
        }
      },
      series: [{
        name: 'Jumlah Peuji',
        data: @json($jmlPeujiPelajar->pluck('total'))
      }],
      xaxis: {
        categories: @json($jmlPeujiPelajar->pluck('predikat')),
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
      colors: ['#1F2859'],
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

    const chartContainers = document.querySelectorAll('.chart-container');

    // Loop setiap div container
    chartContainers.forEach(container => {

      // Ambil ID dan data dari data-attribute
      const chartId = container.dataset.chartId;
      const chartData = JSON.parse(container.dataset.chartData);

      // --- Transformasi Data (untuk chart ini saja) ---
      // Kita pisahkan antara 'predikat' (untuk label) dan 'total' (untuk data)

      // Urutkan data berdasarkan predikat (opsional, tapi rapi)
      chartData.sort((a, b) => a.predikat.localeCompare(b.predikat));

      const categories = chartData.map(item => item.predikat);
      const seriesData = chartData.map(item => item.total);

      // --- Konfigurasi ApexCharts ---
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
          data: seriesData
        }],
        xaxis: {
          categories: categories,
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
        colors: ['#1F2859'],
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
      // Buat instance chart baru dan render ke div yang sesuai
      var chart = new ApexCharts(document.querySelector("#" + chartId), options);
      chart.render();
    });

  </script>
@endsection