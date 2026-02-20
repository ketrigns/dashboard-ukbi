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
        </div>

      </div>
    </div>
  </div>
  <a href="{{ route('user.mining.export') }}" class="cursor-pointer mt-4 px-4 py-2 bg-blue-600 text-white rounded">
    Unduh Data
  </a>

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