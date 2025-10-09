@extends('layouts.app')

@section('title', 'Dashboard UKBI')

@section('content')
  <div class="relative w-full">
    <select
      class="w-full appearance-none border border-black rounded px-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1F2859] focus:border-[#1F2859]">
      <option>Semua Wilayah</option>
      <option>Jambi</option>
      <option>Muaro Jambi</option>
      <option>Kota Sungai Penuh</option>
      <option>Batanghari</option>
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

  <div class="grid md:grid-cols-2 grid-cols-1 gap-4 my-4">
    <div class="bg-white p-4 rounded">
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
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji Kategori Pelajar berdasarkan Predikat</h1>
      <div id="chartPelajarPredikat"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji Kategori Mahasiswa berdasarkan Predikat</h1>
      <div id="chartMahasiswaPredikat"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji Kategori Umum berdasarkan Predikat</h1>
      <div id="chartUmumPredikat"></div>
    </div>
    
  </div>

  <script>
    var options = {
      chart: {
        type: 'line',
        height: '300px',
        toolbar: {
          show: false
        }
      },
      series: [
        {
          name: 'Istimewa',
          data: [120, 150, 180, 200]
        },
        {
          name: 'Sangat Unggul',
          data: [100, 130, 160, 190]
        },
        {
          name: 'Unggul',
          data: [80, 110, 140, 170]
        }
      ],
      xaxis: {
        categories: ['2021', '2022', '2023', '2024']
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
        data: [280, 320, 300, 280, 50, 120]
      }],
      xaxis: {
        categories: ['Istimewa', 'Sangat Unggul', 'Unggul', 'Madya', 'Semenjana', 'Marginal'],
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
        enabled: true // 🔹 Tampilkan angka di atas setiap batang (opsional)
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
        name: 'Jumlah Peuji',
        data: [280, 320, 300, 280, 50, 120]
      }],
      xaxis: {
        categories: ['Istimewa', 'Sangat Unggul', 'Unggul', 'Madya', 'Semenjana', 'Marginal'],
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
        enabled: true // 🔹 Tampilkan angka di atas setiap batang (opsional)
      }
    };

    var chartSkorPredikat = new ApexCharts(document.querySelector("#chartSkorPredikat"), optionsSkorPredikat);
    chartSkorPredikat.render();

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
        data: [280, 320, 300, 280, 50, 120]
      }],
      xaxis: {
        categories: ['Istimewa', 'Sangat Unggul', 'Unggul', 'Madya', 'Semenjana', 'Marginal'],
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
        enabled: true // 🔹 Tampilkan angka di atas setiap batang (opsional)
      }
    };

    var chartPelajarPredikat = new ApexCharts(document.querySelector("#chartPelajarPredikat"), optionsPelajarPredikat);
    chartPelajarPredikat.render();

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
        data: [280, 320, 300, 280, 50, 120]
      }],
      xaxis: {
        categories: ['Istimewa', 'Sangat Unggul', 'Unggul', 'Madya', 'Semenjana', 'Marginal'],
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
        enabled: true // 🔹 Tampilkan angka di atas setiap batang (opsional)
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
        data: [280, 320, 300, 280, 50, 120]
      }],
      xaxis: {
        categories: ['Istimewa', 'Sangat Unggul', 'Unggul', 'Madya', 'Semenjana', 'Marginal'],
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
        enabled: true // 🔹 Tampilkan angka di atas setiap batang (opsional)
      }
    };

    var chartUmumPredikat = new ApexCharts(document.querySelector("#chartUmumPredikat"), optionsUmumPredikat);
    chartUmumPredikat.render();

    
  </script>
@endsection