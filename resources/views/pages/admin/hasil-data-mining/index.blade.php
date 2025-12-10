@extends('layouts.admin.app')

@section('content')
    <div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
        <h4 class="text-default-900 text-lg font-semibold">Hasil Data Mining</h4>
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
                </div>

                <div class="card-body overflow-auto" id="heatmap-card">
                    <form method="GET" class="mb-4 flex items-center gap-4">
                        <input type="hidden" name="tahunUsia" value="{{ $tahunUsia }}">
                        <input type="hidden" name="tahunJK" value="{{ $tahunJK }}">
                        <label>Pilih Tahun:</label>

                        <select name="tahun" onchange="
                                    const f = this.form;
                                    f.action = f.action.split('#')[0] + '#heatmap-card';
                                    f.requestSubmit ? f.requestSubmit() : f.submit();
                                " class="border p-2 rounded form-input max-w-[300px]">
                            @foreach($tahunList as $t)
                                <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <div id="heatmap-cluster" class="mt-6"></div>
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

                <div class="card-body overflow-auto" id="heatmap-usia-card">
                    <form method="GET" class="mb-4 flex items-center gap-4">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <input type="hidden" name="tahunJK" value="{{ $tahunJK }}">
                        <label>Pilih Tahun:</label>

                        <select name="tahunUsia" onchange="
                                    const f = this.form;
                                    f.action = f.action.split('#')[0] + '#heatmap-usia-card';
                                    f.requestSubmit ? f.requestSubmit() : f.submit();
                                " class="border p-2 rounded form-input max-w-[300px]">
                            @foreach($tahunList as $t)
                                <option value="{{ $t }}" {{ $t == $tahunUsia ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <div id="heatmap-usia-cluster" class="mt-6"></div>
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


                <div class="card-body overflow-auto" id="heatmap-jk-card">
                    <form method="GET" class="mb-4 flex items-center gap-4">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <input type="hidden" name="tahunUsia" value="{{ $tahunUsia }}">
                        <label>Pilih Tahun:</label>

                        <select name="tahunJK" onchange="
                                    const f = this.form;
                                    f.action = f.action.split('#')[0] + '#heatmap-jk-card';
                                    f.requestSubmit ? f.requestSubmit() : f.submit();
                                " class="border p-2 rounded form-input max-w-[300px]">
                            @foreach($tahunList as $t)
                                <option value="{{ $t }}" {{ $t == $tahunJK ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <div id="heatmap-jk-cluster" class="mt-6"></div>
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

@endsection

<script>
    let froala;

    document.addEventListener('DOMContentLoaded', function () {
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

        // Heatmap Kota/Kabupaten
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

            // INI YANG BIKIN LEGEND PINDAH KE SAMPING
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