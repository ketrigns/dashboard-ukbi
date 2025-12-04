@extends('layouts.app')

@section('title', 'Hasil Data Mining')

@section('content')
  <div>
    <h1 class="text-2xl text-[45px] font-bold">Hasil Data Mining</h1>

    <div class="mt-8">
      <div>
        <div id="chart-cluster-usia" class="p-4"></div>

        <div class="card-body overflow-auto">
          <table class="table-auto w-full text-sm border">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-3 py-2 border text-center font-semibold" colspan="2">
                  Jumlah Data Tiap Cluster Berdasarkan Usia
                </th>
              </tr>
              <tr>
                <th class="px-3 py-2 border">Cluster Usia</th>
                <th class="px-3 py-2 border">Jumlah Data</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($jmlUsiaTiapCluster as $row)
                <tr>
                  <td class="px-3 py-2 border text-center font-semibold">{{ $row->cluster_usia }}</td>
                  <td class="px-3 py-2 border text-center">{{ number_format(num: $row->total) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <h1 class="my-2 !text-xl font-bold">Deskripsi</h1>
          <div>
            {!! $deskripsi->bar_chart_jml_data_per_cluster_usia ?? '' !!}
          </div>

        </div>

      </div>
    </div>

  </div>
  <button onclick="printChart()" class="cursor-pointer mt-4 px-4 py-2 bg-blue-600 text-white rounded">
    Print Halaman
  </button>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const years = @json($years);
      const cluster1 = @json($cluster1);
      const cluster2 = @json($cluster2);
      const cluster3 = @json($cluster3);

      // Konversi pluck keyed (tahun => jumlah) menjadi array sesuai urutan tahun
      const dataCluster1 = years.map(year => cluster1[year] ?? 0);
      const dataCluster2 = years.map(year => cluster2[year] ?? 0);
      const dataCluster3 = years.map(year => cluster3[year] ?? 0);

      const options = {
        chart: {
          type: 'bar',
          height: 380
        },
        title: {
          text: 'Jumlah Data per Cluster Usia (Pelajar, Mahasiswa, Umum)',
          align: 'center',
          style: {
            fontSize: '16px',
            fontWeight: 'bold',
            color: '#333'
          }
        },
        series: [
          {
            name: "Cluster Usia 1",
            data: dataCluster1
          },
          {
            name: "Cluster Usia 2",
            data: dataCluster2
          },
          {
            name: "Cluster Usia 3",
            data: dataCluster3
          }
        ],
        xaxis: {
          categories: years,
          title: { text: "Tahun Ujian" }
        },
        yaxis: {
          title: { text: "Jumlah Peserta" }
        },
        dataLabels: {
          enabled: true,
          style: {
            colors: ['#000'] // <-- angka di dalam bar jadi hitam
          }
        },
        plotOptions: {
          bar: {
            horizontal: false,
            borderRadius: 4,
            columnWidth: "45%"
          }
        },
        colors: ['#1E88E5', '#43A047', '#FB8C00']
      };

      const chart = new ApexCharts(
        document.querySelector("#chart-cluster-usia"),
        options
      );

      chart.render();

      
    });

    function printChart() {
      const navMenu = document.getElementById('nav-menu');
      const menuToggle = document.getElementById('menu-toggle');
      const loader = document.getElementById('print-loader');

      // Simpan inline-style asli
      const originalStyle = navMenu.getAttribute('style') || '';
      const originalToggleStyle = menuToggle.getAttribute('style') || '';
      menuToggle.style.display = "none";

      // Inject style print mode
      navMenu.style.display = "flex";
      navMenu.style.flexDirection = "row";
      navMenu.style.maxHeight = "none";
      navMenu.style.opacity = "1";

      loader.classList.remove("hidden");

      setTimeout(() => {
        loader.classList.add("hidden");
        window.print();
      }, 1000);

      window.addEventListener('afterprint', () => {
        // Kembalikan style asli
        navMenu.setAttribute('style', originalStyle);
        menuToggle.setAttribute('style', originalToggleStyle);

      }, { once: true });
    }
  </script>
@endsection