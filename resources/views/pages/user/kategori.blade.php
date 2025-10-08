@extends('layouts.app')

@section('title', 'Dashboard UKBI')

@section('content')
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
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji berdasarkan Kategori</h1>
      <h1 class="text-[40px] font-regular leading-tight text-[#1F2859]">9.453</h1>
      <div id="chart"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji berdasarkan Kategori Pelajar</h1>
      <h1 class="text-[40px] font-regular leading-tight text-[#1F2859]">7.207</h1>
      <div id="chartPelajar"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji berdasarkan Kategori Mahasiswa</h1>
      <h1 class="text-[40px] font-regular leading-tight text-[#1F2859]">1.108</h1>
      <div id="chartMahasiswa"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji berdasarkan Kategori Umum</h1>
      <h1 class="text-[40px] font-regular leading-tight text-[#1F2859]">1.228</h1>
      <div id="chartUmum"></div>
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
          name: 'Pelajar',
          data: [120, 150, 180, 200]
        },
        {
          name: 'Mahasiswa',
          data: [100, 130, 160, 190]
        },
        {
          name: 'Umum',
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

    var optionsPelajar = {
      chart: {
        type: 'pie',
        height: '300px',
        toolbar: {
          show: false
        }
      },
      series: [200, 190, 170],
      labels: ['Pelajar SMA', 'Pelajar SMK', 'Pelajar SMP'],
      colors: ['#1F2859', '#547792', '#94B4C1'],
      legend: {
        position: 'right',
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

    var optionsMahasiswa = {
      chart: {
        type: 'pie',
        height: '300px',
        toolbar: {
          show: false
        }
      },
      series: [200, 190],
      labels: ['Mahasiswa WNI', 'Mahasiswa WNA'],
      colors: ['#1F2859', '#547792'],
      legend: {
        position: 'right',
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

    var chartMahasiswa = new ApexCharts(document.querySelector("#chartMahasiswa"), optionsMahasiswa);
    chartMahasiswa.render();

    var optionsUmum = {
      chart: {
        type: 'pie',
        height: '300px',
        toolbar: {
          show: false
        }
      },
      series: [200, 190, 170],
      labels: ['ASN', 'Guru', 'Dosen'],
      colors: ['#1F2859', '#547792', '#94B4C1'],
      legend: {
        position: 'right',
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

    var chartUmum = new ApexCharts(document.querySelector("#chartUmum"), optionsUmum);
    chartUmum.render();
  </script>
@endsection