@extends('layouts.app')

@section('title', 'Dashboard UKBI')

@section('content')
  <div class="grid md:grid-cols-2 grid-cols-1 gap-4 my-4">
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji berdasarkan Wilayah per Tahun</h1>
      <div id="chart"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <h1 class="text-[24px] font-medium leading-tight">Jumlah Peuji berdasarkan Wilayah</h1>
      <div id="chartPeujiPredikat"></div>
    </div>
    <div id="map" class="rounded col-span-2" style="height: 400px; overflow: hidden;"></div>

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
        type: 'line',
        height: '300px',
        toolbar: {
          show: false
        }
      },
      series: [
        {
          name: 'Jambi',
          data: [120, 150, 180, 200]
        },
        {
          name: 'Muaro Jambi',
          data: [100, 130, 160, 190]
        },
        {
          name: 'Batanghari',
          data: [80, 110, 140, 170]
        }
      ],
      xaxis: {
        categories: ['2021', '2022', '2023', '2024']
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
        data: [280, 320, 300, 280, 50, 120]
      }],
      xaxis: {
        categories: ['Jambi', 'Muaro Jambi', 'Batanghari', 'Tanjung Jabung Barat', 'Tanjung Jabung Timur', 'Sarolangun'],
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

  </script>
@endsection