@extends('layouts.admin.app')

@section('content')
    <div class="flex items-center gap-3 text-sm font-semibold mb-5">
        <p class="text-sm font-bold text-default-900">Hasil Data Mining</p>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-2">
        @if (session('success'))
            <div id="toast-success"
                class="flex items-center w-72 p-4 text-sm text-green-700 bg-green-100 border border-green-400 rounded-lg shadow-lg animate-fade-in">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-green-600" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 111.414-1.414L8.414 12.172l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div id="toast-error"
                class="flex items-center w-72 p-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg shadow-lg animate-fade-in">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-red-600" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-4h2v2H9v-2zm0-8h2v6H9V6z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('import_errors'))
            <div id="toast-import"
                class="w-80 p-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded-lg shadow-lg animate-fade-in">
                <p class="font-semibold mb-2">Ditemukan beberapa error pada file Anda:</p>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Animasi Fade Out -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toasts = document.querySelectorAll('#toast-container > div');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-x-5');
                    setTimeout(() => toast.remove(), 700);
                }, 5000); // tampil 3 detik
            });
        });
    </script>

    <!-- Tambahkan animasi sederhana Tailwind -->
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        .transition-opacity {
            transition: opacity 0.7s, transform 0.7s;
        }
    </style>

    <div class="mt-8">
        <div class="card overflow-auto">
            <div class="card-header flex sm:!flex-row !flex-col justify-between gap-4">
                <div>
                    <h4 class="card-title">Hasil Data Mining</h4>

                </div>
                <form action="{{ route('data-mining.import.handle') }}" method="POST" enctype="multipart/form-data"
                    class="flex sm:!flex-row !flex-col items-center gap-2">
                    @csrf
                    <label for="file" class="cursor-pointer">Upload File Excel</label>
                    <input type="file" name="file" id="file" class="border rounded px-2 py-1 text-sm cursor-pointer"
                        required>
                    <button type="submit" class="btn bg-primary text-white text-sm px-4 py-2">Import File</button>
                </form>
            </div>
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
                    <form action="{{ route('deskripsi.save') }}" method="POST">
                        @csrf
                        <textarea class="prosess" id="froala" name="centroid_kmeans">
                                                                                                                                                    {!! $deskripsi->centroid_kmeans ?? '' !!}
                                                                                                                                                </textarea>
                        <div class="flex flex-col gap-2 mt-4">
                            <button type="submit" class="btn w-full bg-primary text-white">Simpan Deskripsi</button>
                        </div>
                    </form>

                    <div class="overflow-auto">
                        <h1 class="my-2 !text-xl font-bold">Peta Tematik Persebaran Cluster</h1>
                        <div id="map-global" class="map-wrapper"></div>
                    </div>
                    <div class="overflow-auto">
                        <h1 class="my-2 !text-xl font-bold">Small Multiples Map Persebaran Cluster</h1>
                        <div class="grid sm:grid-cols-2 grid-cols-1">
                            @foreach($dataPerTahun as $tahun => $dataKota)
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-primary text-white">
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

@endsection

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let froala;

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

        froala = new FroalaEditor('#froala', {
            toolbarButtons: [
                ['fontSize'],
                ['bold', 'italic', 'underline', 'strikeThrough'],
                ['formatOL', 'formatUL'],
                ['align', 'undo', 'redo', 'clearFormatting'],
                ['insertLink']
            ],
            quickInsertEnabled: false,
            imageUpload: false,
            videoUpload: false,
            fileUpload: false,
            toolbarSticky: false,
            height: 300,
            fontSizeSelection: true,
            fontSizeDefaultSelection: '14',
            fontSize: ['10', '12', '14', '16', '18', '20', '24', '28', '32'],
        });

        // --- LOADING SAAT IMPORT FILE ---
        const importForm = document.querySelector('form[action="{{ route('data-mining.import.handle') }}"]');

        if (importForm) {
            importForm.addEventListener('submit', function (e) {
                // Tampilkan SweetAlert Loading
                Swal.fire({
                    title: 'Mengupload...',
                    text: 'Mohon tunggu, file sedang diproses.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        }
    });
</script>