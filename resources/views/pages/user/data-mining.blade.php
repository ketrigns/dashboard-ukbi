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
                <th class="px-3 py-2 border text-center font-semibold" colspan="5">
                  Centroid K-Mean
                </th>
              </tr>
              <tr>
                <th class="px-3 py-2 border">Seksi I</th>
                <th class="px-3 py-2 border">Seksi II</th>
                <th class="px-3 py-2 border">Seksi III</th>
                <th class="px-3 py-2 border">Cluster</th>
                <th class="px-3 py-2 border">Tahun</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($centroidKmeans as $row)
                <tr>
                  <td class="px-3 py-2 border text-center">{{ number_format($row->seksi_i, 6) }}</td>
                  <td class="px-3 py-2 border text-center">{{ number_format($row->seksi_ii, 6) }}</td>
                  <td class="px-3 py-2 border text-center">{{ number_format($row->seksi_iii, 6) }}</td>
                  <td class="px-3 py-2 border text-center">{{ $row->cluster }}</td>
                  <td class="px-3 py-2 border text-center">{{ $row->tahun }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <div class="overflow-auto">
              <h1 class="my-2 !text-xl font-bold">Peta Tematik Persebaran Cluster</h1>
              <div id="map-global" class="map-wrapper"></div>
          </div>
          <div class="overflow-auto">
              <h1 class="my-2 !text-xl font-bold">Small Multiples Map Persebaran Cluster</h1>
              <div class="grid sm:grid-cols-2 grid-cols-1 gap-1">
                  @foreach($dataPerTahun as $tahun => $dataKota)
                      <div class="card mb-4 shadow-sm">
                          <div class="card-header bg-[#3B82F6] text-white px-4 py-2">
                              <h5 class="m-0">Tahun Ujian: {{ $tahun }}</h5>
                          </div>
                          <div class=" p-0">
                              {{-- ID Unik per tahun: map-2024, map-2025 --}}
                              <div id="map-{{ $tahun }}" class="map-wrapper"></div>
                          </div>
                      </div>
                  @endforeach
              </div>
          </div>

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
      // 1. Data dari Controller
        const globalData = @json($mappedData);   // Data Lama
        const yearData = @json($dataPerTahun);   // Data Baru

        // Config Warna
        function getColor(cluster) {
            cluster = parseInt(cluster);
            switch(cluster) {
                case 1: return '#8B0000'; case 2: return '#006400'; 
                case 3: return '#0000FF'; case 4: return '#FFA500'; 
                case 5: return '#FFFF00'; default: return '#DDDDDD'; 
            }
        }

        // LOAD GEOJSON (Sekali Saja)
        fetch("{{ asset('geojson/provinsi jambi.geojson') }}")
            .then(res => res.json())
            .then(geoJsonData => {

                // 1. RENDER PETA GLOBAL (Logic Lama)
                createMap('map-global', globalData, geoJsonData, 'Global');

                // 2. RENDER PETA PER TAHUN (Logic Baru)
                Object.keys(yearData).forEach(tahun => {
                    let dataTahunIni = yearData[tahun];
                    createMap('map-' + tahun, dataTahunIni, geoJsonData, tahun);
                });

            })
            .catch(err => console.error("Error GeoJSON:", err));


        // --- FUNGSI GENERATOR PETA (Reusable) ---
        function createMap(elementId, cityData, geoData, labelTahun) {
            
            // Init Map
            let map = L.map(elementId, {
                zoomControl: true, scrollWheelZoom: false, doubleClickZoom: false, 
                touchZoom: false, attributionControl: false,
                zoomSnap: 0.1, zoomDelta: 0.5
            });

            // Layer GeoJSON
            let layer = L.geoJson(geoData, {
                style: function(feature) {
                    let dbKey = feature.properties.NAME_2.toUpperCase();
                    let cluster = (cityData[dbKey] && cityData[dbKey]['cluster']) ? cityData[dbKey]['cluster'] : 0;
                    return {
                        fillColor: getColor(cluster),
                        weight: 1, opacity: 1, color: 'white', fillOpacity: 1
                    };
                },
                onEachFeature: function(feature, layer) {
                    let dbKey = feature.properties.NAME_2.toUpperCase();
                    let data = cityData[dbKey] || null;
                    let cluster = data ? data['cluster'] : '-';
                    let total = data ? data['total'] : 0;

                    // Tooltip
                    layer.bindTooltip(`
                        <div style="text-align:left;">
                            <strong>${dbKey}</strong><br>
                            Cluster Dominan: <b>${cluster}</b><br>
                            Total Data: <b>${total}</b>
                        </div>
                    `, { permanent: false, direction: 'top', sticky: true });

                    // Label Nama Kota
                    L.marker(layer.getBounds().getCenter(), {
                        icon: L.divIcon({
                            className: 'region-label', 
                            html: dbKey, iconSize: [120, 20] 
                        })
                    }).addTo(map);
                }
            }).addTo(map);

            // Zoom Pas
            map.fitBounds(layer.getBounds(), { padding: [20, 20] });

            // Legenda
            let legend = L.control({position: 'topleft'});
            legend.onAdd = function (map) {
                let div = L.DomUtil.create('div', 'info legend'), grades = [1, 2, 3, 4, 5];
                div.innerHTML += '<strong>Keterangan</strong><br>';
                for (let i = 0; i < grades.length; i++) {
                    div.innerHTML += '<div style="margin-bottom:3px;"><i style="background:' + getColor(grades[i]) + '; width:15px; height:15px; float:left; margin-right:5px;"></i> Cluster ' + grades[i] + '</div>';
                }
                return div;
            };
            legend.addTo(map);
        }

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