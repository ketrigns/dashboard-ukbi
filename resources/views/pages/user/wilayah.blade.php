@extends('layouts.app')

@section('title', 'Dashboard UKBI')

@section('content')
  <div class="grid grid-cols-1 gap-4 my-4">
    <div class="bg-white p-4 rounded">
      <div id="chartWilayahPerTahun"></div>
    </div>
    <div class="bg-white p-4 rounded">
      <div id="chartPeujiWilayah"></div>
    </div>
    <div id="map" class="rounded" style="height: 400px; overflow: hidden;"></div>
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

    const rawWilayah = @json($wilayahPerTahun);
    const uniqueYears = [...new Set(rawWilayah.map(item => item.tahun))].sort((a, b) => a - b);

    // 2. Dapatkan semua predikat unik dan urutkan
    const uniqueWilayahs = [...new Set(rawWilayah.map(item => item.kota))].sort();

    // 3. Buat struktur data 'series' yang dibutuhkan ApexCharts
    const seriesData = uniqueWilayahs.map(kota => {
      // Untuk setiap kota, cari totalnya di setiap tahun
      const data = uniqueYears.map(tahun => {
        // Cari data yang cocok
        const entry = rawWilayah.find(item => item.tahun === tahun && item.kota === kota);
        // Jika ditemukan, kembalikan totalnya. Jika tidak, kembalikan 0.
        return entry ? entry.total : 0;
      });

      return {
        name: kota,
        data: data
      };
    });

    console.log(seriesData)

    var optionsWilayahPerTahun = {
      chart: {
        type: 'line',
        height: '500px',
        toolbar: {
          show: true
        }
      },
      title: {
        text: 'Jumlah Peuji berdasarkan Predikat per Tahun', // 🟢 Judul chart
        align: 'center', // bisa 'center', 'center', atau 'right'
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
          text: 'Peuji', // 🔹 Judul Y-axis
          style: {
            color: '#1F2859',
            fontSize: '14px',
            fontWeight: 'bold'
          }
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

    var chartWilayahPerTahun = new ApexCharts(document.querySelector("#chartWilayahPerTahun"), optionsWilayahPerTahun);
    chartWilayahPerTahun.render();

    var optionsPeujiWilayah = {
      chart: {
        type: 'bar',
        height: '500px',
        toolbar: {
          show: true // 🔹 Hilangkan tombol download / export
        }
      },
      title: {
        text: 'Jumlah Peuji berdasarkan Wilayah', // 🟢 Judul chart
        align: 'center', // bisa 'center', 'center', atau 'right'
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
        categories: @json($jmlPeujiWilayah->pluck('kota')),
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
          colors: ['#000']
        }
      },
      grid: {
        padding: {
          bottom: 100,
          left: 20
        }
      },
    };

    var chartPeujiWilayah = new ApexCharts(document.querySelector("#chartPeujiWilayah"), optionsPeujiWilayah);
    chartPeujiWilayah.render();

  </script>
@endsection