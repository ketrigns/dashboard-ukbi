@extends('layouts.app')

@section('title', 'Hasil Data Mining')

@section('content')
  <div>
    <h1 class="text-2xl text-[45px] font-bold">Hasil Data Mining</h1>

    <div class="mt-8">
      <div>
        <div class="card-body overflow-auto">
          <table class="table-auto w-full text-sm border">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-3 py-2 border text-center font-semibold" colspan="4">
                  Centroid K-Mean
                </th>
              </tr>
              <tr>
                <th class="px-3 py-2 border"></th>
                <th class="px-3 py-2 border">Seksi I</th>
                <th class="px-3 py-2 border">Seksi II</th>
                <th class="px-3 py-2 border">Seksi III</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($centroidKmeans as $row)
                <tr>
                  <td class="px-3 py-2 border text-center">C{{ $loop->iteration }}</td>
                  <td class="px-3 py-2 border text-center">{{ number_format($row->seksi_i, 6) }}</td>
                  <td class="px-3 py-2 border text-center">{{ number_format($row->seksi_ii, 6) }}</td>
                  <td class="px-3 py-2 border text-center">{{ number_format($row->seksi_iii, 6) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <h1 class="my-2 !text-xl font-bold">Deskripsi</h1>
          <div>
            {!! $deskripsi->centroid_kmeans ?? '' !!}
          </div>

        </div>

      </div>
    </div>

    <div class="mt-8">
      <div id="heatmap-card">
        <form method="GET" class="mb-4 flex items-center gap-4">
          <input type="hidden" name="tahunUsia" value="{{ $tahunUsia }}">
          <input type="hidden" name="tahunJK" value="{{ $tahunJK }}">

          <label>Pilih Tahun:</label>
          <select name="tahun" onchange="
                                                  const f = this.form;
                                                  f.action = f.action.split('#')[0] + '#heatmap-card';
                                                  f.requestSubmit ? f.requestSubmit() : f.submit();
                                              " class="border p-2 rounded form-input flex-1">
            @foreach($tahunList as $t)
              <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>
                {{ $t }}
              </option>
            @endforeach
          </select>
        </form>
        <div id="heatmap-cluster" class="p-4"></div>
        <div class="card-body overflow-auto">
          <table class="table-auto w-full text-sm border">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-3 py-2 border" rowspan="2">Kota</th>

                <th class="px-3 py-2 border text-center font-semibold" colspan="5">
                  Cluster
                </th>
                <th class="px-3 py-2 border" rowspan="2">Total Peserta</th>

              </tr>
              <tr>
                @foreach ($clusters as $cluster)
                  <th class="px-3 py-2 border">{{ $cluster }}</th>
                @endforeach
              </tr>
            </thead>

            <tbody>
              @foreach ($result as $kota => $row)
                <tr>
                  <td class="px-3 py-2 border text-center">{{ $kota }}</td>
                  @foreach ($clusters as $cluster)
                    <td class="px-3 py-2 border text-center">{{ $row[$cluster] }}</td>
                  @endforeach
                  <td class="px-3 py-2 border text-center">{{ $row['total_peserta'] }}</td>


                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>
    </div>

    <div class="mt-8">
      <div id="heatmap-usia-card">
        <form method="GET" class="mb-4 flex items-center gap-4">
          <input type="hidden" name="tahun" value="{{ $tahun }}">
          <input type="hidden" name="tahunJK" value="{{ $tahunJK }}">

          <label>Pilih Tahun:</label>
          <select name="tahunUsia" onchange="
                                                  const f = this.form;
                                                  f.action = f.action.split('#')[0] + '#heatmap-usia-card';
                                                  f.requestSubmit ? f.requestSubmit() : f.submit();
                                              " class="border p-2 rounded form-input flex-1">
            @foreach($tahunList as $t)
              <option value="{{ $t }}" {{ $t == $tahunUsia ? 'selected' : '' }}>
                {{ $t }}
              </option>
            @endforeach
          </select>
        </form>
        <div id="heatmap-usia-cluster" class="p-4"></div>
        <div class="card-body overflow-auto">
          <table class="table-auto w-full text-sm border">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-3 py-2 border" rowspan="2">Kategori Usia</th>

                <th class="px-3 py-2 border text-center font-semibold" colspan="{{ count($clustersUsia) }}">
                  Cluster
                </th>

                <th class="px-3 py-2 border" rowspan="2">Total Penguji</th>
              </tr>

              <tr>
                @foreach ($clustersUsia as $cluster)
                  <th class="px-3 py-2 border">{{ $cluster }}</th>
                @endforeach
              </tr>
            </thead>

            <tbody>
              @foreach ($resultUsia as $usia => $row)
                <tr>
                  <td class="px-3 py-2 border text-center">{{ $usia }}</td>

                  @foreach ($clustersUsia as $cluster)
                    <td class="px-3 py-2 border text-center">{{ $row[$cluster] }}</td>
                  @endforeach

                  <td class="px-3 py-2 border text-center">{{ $row['total_peserta'] }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>
    </div>

    <div class="mt-8">
      <div id="heatmap-jk-card">
        <form method="GET" class="mb-4 flex items-center gap-4">
          <input type="hidden" name="tahun" value="{{ $tahun }}">
          <input type="hidden" name="tahunUsia" value="{{ $tahunUsia }}">

          <label>Pilih Tahun:</label>
          <select name="tahunJK" onchange="
                                                  const f = this.form;
                                                  f.action = f.action.split('#')[0] + '#heatmap-jk-card';
                                                  f.requestSubmit ? f.requestSubmit() : f.submit();
                                              " class="border p-2 rounded form-input flex-1">
            @foreach($tahunList as $t)
              <option value="{{ $t }}" {{ $t == $tahunJK ? 'selected' : '' }}>
                {{ $t }}
              </option>
            @endforeach
          </select>
        </form>
        <div id="heatmap-jk-cluster" class="p-4"></div>
        <div class="card-body overflow-auto">
          <table class="table-auto w-full text-sm border">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-3 py-2 border" rowspan="2">Jenis Kelamin</th>

                <th class="px-3 py-2 border text-center font-semibold" colspan="{{ count($clustersJK) }}">
                  Cluster
                </th>

                <th class="px-3 py-2 border" rowspan="2">Total Penguji</th>
              </tr>

              <tr>
                @foreach ($clustersJK as $cluster)
                  <th class="px-3 py-2 border">{{ $cluster }}</th>
                @endforeach
              </tr>
            </thead>

            <tbody>
              @foreach ($resultJK as $jk => $row)
                <tr>
                  <td class="px-3 py-2 border text-center">{{ $jk }}</td>

                  @foreach ($clustersJK as $cluster)
                    <td class="px-3 py-2 border text-center">{{ $row[$cluster] }}</td>
                  @endforeach

                  <td class="px-3 py-2 border text-center">{{ $row['total_peserta'] }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>
    </div>



  </div>
  <button onclick="printChart()" class="cursor-pointer mt-4 px-4 py-2 bg-blue-600 text-white rounded">
    Print Halaman
  </button>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      Highcharts.chart('heatmap-cluster', {

        chart: {
          type: 'heatmap',
          marginTop: 40,
          marginBottom: 80,
          plotBorderWidth: 1
        },

        title: {
          text: 'Peta Sebaran Cluster per Kabupaten/Kota ({{ $tahun }})',
          style: {
            fontSize: '1em'
          }
        },

        xAxis: {
          title: {
            text: "Cluster",
            style: {
              color: "#000",
              fontWeight: "bold"
            }
          },
          categories: @json($clusters)   // <-- daftar cluster
        },

        yAxis: {
          title: {
            text: "Kota/Kabupaten",
            style: {
              color: "#000",
              fontWeight: "bold"
            }
          },
          categories: @json($kotaList), // <-- daftar kota
          reversed: true
        },

        colorAxis: {
          min: 0,
          minColor: '#F7E396',
          maxColor: '#004E89'
        },

        legend: {
          align: 'right',
          layout: 'vertical',
          margin: 0,
          verticalAlign: 'top',
          y: 25,
          symbolHeight: 280,
          title: { text: "Jumlah Peserta" }
        },

        tooltip: {
          formatter: function () {
            return `
                                                      <b>Kota:</b> ${this.series.yAxis.categories[this.point.y]}<br>
                                                      <b>Cluster:</b> ${this.series.xAxis.categories[this.point.x]}<br>
                                                      <b>Total Peserta:</b> ${this.point.value}
                                                  `;
          }
        },

        series: [{
          borderColor: '#000',
          borderWidth: 1,
          data: @json($heatmapData),   // <-- data heatmap
          dataLabels: {
            enabled: true,
            format: '{point.value}',
            color: '#000',
            style: {
              fontWeight: 'bold'
            }
          }
        }]
      });


    });

    // Heatmap Usia
    Highcharts.chart('heatmap-usia-cluster', {
      chart: { type: 'heatmap', marginTop: 40, marginBottom: 80, plotBorderWidth: 1 },

      title: {
        text: 'Peta Sebaran Cluster Berdasarkan Usia ({{ $tahunUsia }})',
        style: { fontSize: '1em' }
      },

      xAxis: {
        title: { text: "Cluster", style: { color: "#000", fontWeight: "bold" } },
        categories: @json($clustersUsia)
      },

      yAxis: {
        title: { text: "Kategori Usia", style: { color: "#000", fontWeight: "bold" } },
        categories: @json($usiaGroups),
        reversed: true
      },

      colorAxis: { min: 0, minColor: '#F7E396', maxColor: '#004E89' },

      legend: {
        align: 'right', layout: 'vertical', margin: 0,
        verticalAlign: 'top', y: 25, symbolHeight: 280,
        title: { text: "Jumlah Penguji" }
      },

      tooltip: {
        formatter: function () {
          return `
                <b>Kategori Usia:</b> ${this.series.yAxis.categories[this.point.y]}<br>
                <b>Cluster:</b> ${this.series.xAxis.categories[this.point.x]}<br>
                <b>Total Penguji:</b> ${this.point.value}
              `;
        }
      },

      series: [{
        borderColor: '#000',
        borderWidth: 1,
        data: @json($heatmapUsiaData),
        dataLabels: {
          enabled: true,
          format: '{point.value}',
          color: '#000',
          style: { fontWeight: 'bold' }
        }
      }]
    });

    // Heatmap Jenis Kelamin
    Highcharts.chart('heatmap-jk-cluster', {
      chart: { type: 'heatmap', marginTop: 40, marginBottom: 80, plotBorderWidth: 1 },

      title: { text: 'Peta Sebaran Cluster Berdasarkan Jenis Kelamin ({{ $tahunJK }})' },

      xAxis: { title: { text: 'Cluster', style: { fontWeight: 'bold', color: '#000', } }, categories: @json($clustersJK) },
      yAxis: { title: { text: 'Jenis Kelamin', style: { fontWeight: 'bold', color: '#000', } }, categories: @json($jkGroups), reversed: true },

      colorAxis: { min: 0, minColor: '#F7E396', maxColor: '#004E89' },

      legend: {
        align: 'right',
        layout: 'vertical',
        verticalAlign: 'middle',
        symbolHeight: 220,
        title: { text: 'Jumlah Penguji' }
      },

      tooltip: {
        formatter: function () {
          return `<b>Jenis Kelamin:</b> ${this.series.yAxis.categories[this.point.y]}<br>
                <b>Cluster:</b> ${this.series.xAxis.categories[this.point.x]}<br>
                <b>Total Penguji:</b> ${this.point.value}`;
        }
      },

      series: [{
        borderColor: '#000',
        borderWidth: 1,
        data: @json($heatmapJKData),
        dataLabels: { enabled: true, format: '{point.value}' }
      }]
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