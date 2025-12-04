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
                                    <td class="px-3 py-2 border text-center">{{ $row->cluster_usia }}</td>
                                    <td class="px-3 py-2 border text-center">{{ number_format(num: $row->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h1 class="my-2 !text-xl font-bold">Deskripsi</h1>
                    <form action="{{ route('deskripsi.save') }}" method="POST">
                        @csrf
                        <textarea class="prosess" id="froala" name="bar_chart_jml_data_per_cluster_usia">
                                                                                                                    {!! $deskripsi->bar_chart_jml_data_per_cluster_usia ?? '' !!}
                                                                                                                </textarea>
                        <div class="flex flex-col gap-2 mt-4">
                            <button type="submit" class="btn w-full bg-primary text-white">Simpan Deskripsi</button>
                        </div>
                    </form>
                </div>
                
                @php
                    $kategoriUsia = [
                        1 => 'Pelajar',
                        2 => 'Mahasiswa',
                        3 => 'Umum',
                    ];
                @endphp
                
                <div id="radar-ukbi" class="mt-6"></div>

                <div class="card-body overflow-auto">
                    <table class="table-auto w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 border text-center font-semibold" colspan="4">
                                    Centroid Nilai UKBI (Per Cluster Usia)
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
                            @foreach ($tableCentroidNilaiPerClusterUsia as $row)
                                <tr>
                                    <td class="px-3 py-2 border text-center">C{{ $loop->iteration }}
                                        ({{ $kategoriUsia[$loop->iteration] }})</td>
                                    <td class="px-3 py-2 border text-center">{{ $row->seksi_i }}</td>
                                    <td class="px-3 py-2 border text-center">{{ $row->seksi_ii }}</td>
                                    <td class="px-3 py-2 border text-center">{{ $row->seksi_iii }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                    <h1 class="my-2 !text-xl font-bold">Deskripsi</h1>
                    <form action="{{ route('deskripsi.save') }}" method="POST">
                        @csrf
                        <textarea class="prosess" id="froala" name="spider_nilai_ukbi_per_cluster_usia">
                                                                                                                    {!! $deskripsi->spider_nilai_ukbi_per_cluster_usia ?? '' !!}
                                                                                                                </textarea>
                        <div class="flex flex-col gap-2 mt-4">
                            <button type="submit" class="btn w-full bg-primary text-white">Simpan Deskripsi</button>
                        </div>
                    </form>
                </div>

                <div id="heatmap-usia" class="mt-6"></div>
                <div class="card-body overflow-auto">
                    <table class="table-auto w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 border text-center font-semibold" colspan="4">
                                    Centroid Nilai UKBI (Per Cluster Usia)
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
                            @foreach ($tableCentroidNilaiPerClusterUsia as $row)
                                <tr>
                                    <td class="px-3 py-2 border text-center">C{{ $loop->iteration }}
                                        ({{ $kategoriUsia[$loop->iteration] }})</td>
                                    <td class="px-3 py-2 border text-center">{{ $row->seksi_i }}</td>
                                    <td class="px-3 py-2 border text-center">{{ $row->seksi_ii }}</td>
                                    <td class="px-3 py-2 border text-center">{{ $row->seksi_iii }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                    <h1 class="my-2 !text-xl font-bold">Deskripsi</h1>
                    <form action="{{ route('deskripsi.save') }}" method="POST">
                        @csrf
                        <textarea class="prosess" id="froala" name="heatmap_nilai_ukbi_per_cluster_usia">
                                                                                                                    {!! $deskripsi->heatmap_nilai_ukbi_per_cluster_usia ?? '' !!}
                                                                                                                </textarea>
                        <div class="flex flex-col gap-2 mt-4">
                            <button type="submit" class="btn w-full bg-primary text-white">Simpan Deskripsi</button>
                        </div>
                    </form>
                </div>

                <div id="barchart-cluster-jk" class="p-4"></div>
                <div class="card-body overflow-auto">
                    <table class="table-auto w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 border text-center font-semibold" colspan="2">
                                    Jumlah Data Tiap Cluster Berdasarkan Jenis Kelamin
                                </th>
                            </tr>
                            <tr>
                                <th class="px-3 py-2 border">Jenis Kelamin</th>
                                <th class="px-3 py-2 border">Jumlah Data</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($jmlJKTiapCluster as $row)
                                <tr>
                                    <td class="px-3 py-2 border text-center">{{ $row->jenis_kelamin }}</td>
                                    <td class="px-3 py-2 border text-center">{{ number_format(num: $row->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h1 class="my-2 !text-xl font-bold">Deskripsi</h1>
                    <form action="{{ route('deskripsi.save') }}" method="POST">
                        @csrf
                        <textarea class="prosess" id="froala" name="bar_chart_jml_data_per_jk">
                                                                                                                    {!! $deskripsi->bar_chart_jml_data_per_jk ?? '' !!}
                                                                                                                </textarea>
                        <div class="flex flex-col gap-2 mt-4">
                            <button type="submit" class="btn w-full bg-primary text-white">Simpan Deskripsi</button>
                        </div>
                    </form>
                </div>


                <div id="radar-ukbi-jk" class="mt-6"></div>
                <div class="card-body overflow-auto">
                    <table class="table-auto w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 border text-center font-semibold" colspan="4">
                                    Centroid Berdasarkan Jenis Kelamin (P & L)
                                </th>
                            </tr>
                            <tr>
                                <th class="px-3 py-2 border">Jenis Kelamin</th>
                                <th class="px-3 py-2 border">Seksi I</th>
                                <th class="px-3 py-2 border">Seksi II</th>
                                <th class="px-3 py-2 border">Seksi III</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($centroidJenisKelamin as $row)
                                <tr>
                                    <td class="px-3 py-2 border text-center">{{ $row->jenis_kelamin }}</td>
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
                        <textarea class="prosess" id="froala" name="spider_nilai_ukbi_per_jk">
                                                                                                                    {!! $deskripsi->spider_nilai_ukbi_per_jk ?? '' !!}
                                                                                                                </textarea>
                        <div class="flex flex-col gap-2 mt-4">
                            <button type="submit" class="btn w-full bg-primary text-white">Simpan Deskripsi</button>
                        </div>
                    </form>
                </div>

                <div id="heatmap-jk" class="mt-6"></div>
                <div class="card-body overflow-auto">
                    <table class="table-auto w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 border text-center font-semibold" colspan="4">
                                    Centroid Berdasarkan Jenis Kelamin (P & L)
                                </th>
                            </tr>
                            <tr>
                                <th class="px-3 py-2 border">Jenis Kelamin</th>
                                <th class="px-3 py-2 border">Seksi I</th>
                                <th class="px-3 py-2 border">Seksi II</th>
                                <th class="px-3 py-2 border">Seksi III</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($centroidJenisKelamin as $row)
                                <tr>
                                    <td class="px-3 py-2 border text-center">{{ $row->jenis_kelamin }}</td>
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
                        <textarea class="prosess" id="froala" name="heatmap_nilai_ukbi_per_jk">
                                                                                                                    {!! $deskripsi->heatmap_nilai_ukbi_per_jk ?? '' !!}
                                                                                                                </textarea>
                        <div class="flex flex-col gap-2 mt-4">
                            <button type="submit" class="btn w-full bg-primary text-white">Simpan Deskripsi</button>
                        </div>
                    </form>
                </div>

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

                <div class="card-body overflow-auto">
                    <table class="table-auto w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 border text-center font-semibold" colspan="4">
                                    Rata-Rata Usia
                                </th>
                            </tr>
                            <tr>
                                <th class="px-3 py-2 border"></th>
                                <th class="px-3 py-2 border">Usia</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($rataUsia as $row)
                                <tr>
                                    <td class="px-3 py-2 border text-center">C{{ $loop->iteration }}
                                        ({{ $kategoriUsia[$loop->iteration] }})</td>
                                    <td class="px-3 py-2 border text-center">{{ number_format($row->usia, 6) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h1 class="my-2 !text-xl font-bold">Deskripsi</h1>
                    <form action="{{ route('deskripsi.save') }}" method="POST">
                        @csrf
                        <textarea class="prosess" id="froala" name="rata_usia">
                                                                                                                    {!! $deskripsi->rata_usia ?? '' !!}
                                                                                                                </textarea>
                        <div class="flex flex-col gap-2 mt-4">
                            <button type="submit" class="btn w-full bg-primary text-white">Simpan Deskripsi</button>
                        </div>
                    </form>
                </div>

                <div class="card-body overflow-auto">
                    <form method="GET" class="mb-4 flex items-center gap-4">
                        <label>Pilih Tahun:</label>
                        <select name="tahun" onchange="this.form.submit()"
                            class="border p-2 rounded form-input max-w-[300px]">
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
                                    Cluster KMeans
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

                    <h1 class="my-2 !text-xl font-bold">Deskripsi</h1>
                    <form action="{{ route('deskripsi.save') }}" method="POST">
                        @csrf
                        <textarea class="prosess" id="froala" name="cluster_kmeans_pertahun">
                                                                                                                    {!! $deskripsi->cluster_kmeans_pertahun ?? '' !!}
                                                                                                                </textarea>
                        <div class="flex flex-col gap-2 mt-4">
                            <button type="submit" class="btn w-full bg-primary text-white">Simpan Deskripsi</button>
                        </div>
                    </form>



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


        const series = [
            {
                name: 'Cluster Usia 1',
                data: @json($radarSeries['cluster1'])
            },
            {
                name: 'Cluster Usia 2',
                data: @json($radarSeries['cluster2'])
            },
            {
                name: 'Cluster Usia 3',
                data: @json($radarSeries['cluster3'])
            }
        ];

        const optionsSpiderChart = {
            chart: {
                type: 'radar',
                height: 380
            },
            title: {
                text: 'Spider Chart Nilai UKBI Berdasarkan Cluster Usia',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold',
                    color: '#333'
                }
            },
            xaxis: {
                categories: ['Seksi I', 'Seksi II', 'Seksi III']
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#000']
                }
            },
            stroke: {
                width: 2
            },
            series: series
        };

        const chartSpider = new ApexCharts(document.querySelector("#radar-ukbi"), optionsSpiderChart);
        chartSpider.render();

        // Heatmap nilai rata-rata UKBI Berdasarkan Cluster Usia
        const heatmapNilaiUkbiBerdasarkanCluster = @json($heatmapNilaiUkbiBerdasarkanCluster);
        const seksiKeys = ["seksi_i", "seksi_ii", "seksi_iii"];

        // generate data heatmap + round 2 decimal
        const dataHeatmapNilaiUkbiBerdasarkanCluster = heatmapNilaiUkbiBerdasarkanCluster.flatMap((row, rowIndex) =>
            seksiKeys.map((key, colIndex) => [
                colIndex,                      // X = kolom seksi
                rowIndex,                      // Y = baris cluster
                parseFloat(parseFloat(row[key]).toFixed(2))
            ])
        );

        // Substring template helper for the responsive labels
        Highcharts.Templating.helpers.substr = (s, from, length) =>
            s.substr(from, length);

        // Create the chart
        Highcharts.chart('heatmap-usia', {

            chart: {
                type: 'heatmap',
                marginTop: 40,
                marginBottom: 80,
                plotBorderWidth: 1
            },

            title: {
                text: 'Heatmap Nilai Rata-Rata UKBI Berdasarkan Cluster Usia',
                style: {
                    fontSize: '1em'
                }
            },

            xAxis: {
                title: {
                    text: "Seksi Ujian UKBI",
                    style: {
                        color: "#000",       // warna
                        fontWeight: "bold"   // opsional
                    }
                },
                categories: ['Seksi I', 'Seksi II', 'Seksi III']
            },

            yAxis: {
                title: {
                    text: "Cluster Usia", style: {
                        color: "#000",       // warna
                        fontWeight: "bold"   // opsional
                    }
                },    // <= LABEL Y
                categories: ['C1 (Pelajar)', 'C1 (Mahasiswa)', 'C1 (Umum)'],
                reversed: true
            },

            colorAxis: {
                min: 440,
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
                title: { text: "Nilai Rata-Rata Seksi" }
            },

            tooltip: {
                format:
                    '<b>{series.xAxis.categories.(point.x)}</b><br>' +
                    'Day: <b>{series.yAxis.categories.(point.y)}</b><br>' +
                    'Nilai Rata-rata: <b>{point.value}</b>'
            },

            series: [{
                borderColor: '#000',
                borderWidth: 1,
                data: dataHeatmapNilaiUkbiBerdasarkanCluster,
                dataLabels: {
                    enabled: true,
                    format: '{point.value:.2f}',
                    color: '#000',      // warna teks
                    style: {
                        fontWeight: 'bold'
                    }
                }
            }]
        });

        // BarChart Nilai UKBI Per Jenis Kelamin
        const clusterLaki = @json($clusterLaki);
        const clusterPerempuan = @json($clusterPerempuan);

        // Konversi pluck keyed (tahun => jumlah) menjadi array sesuai urutan tahun
        const dataclusterLaki = years.map(year => clusterLaki[year] ?? 0);
        const dataclusterPerempuan = years.map(year => clusterPerempuan[year] ?? 0);

        const optionsJmlJk = {
            chart: {
                type: 'bar',
                height: 380
            },
            title: {
                text: 'Jumlah Peserta UKBI per Tahun Ujian berdasarkan Jenis Kelamin',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold',
                    color: '#333'
                }
            },
            series: [
                {
                    name: "Laki-laki",
                    data: dataclusterLaki
                },
                {
                    name: "Perempuan",
                    data: dataclusterPerempuan
                },
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
            colors: ['#1E88E5', '#F29AAE']
        };

        const chartJmlJk = new ApexCharts(
            document.querySelector("#barchart-cluster-jk"),
            optionsJmlJk
        );

        chartJmlJk.render();

        const optionsSpiderJK = {
            chart: {
                type: 'radar',
                height: 380
            },
            title: {
                text: 'Spider Chart Nilai UKBI Berdasarkan Jenis Kelamin',
                align: 'center',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold',
                    color: '#333'
                }
            },
            xaxis: {
                categories: ['Seksi I', 'Seksi II', 'Seksi III']
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#000']
                }
            },
            stroke: {
                width: 2
            },
            colors: ['#1E88E5', '#F29AAE'],
            series: @json($nilaiCentroidJK)
        };

        const chartSpiderJK = new ApexCharts(document.querySelector("#radar-ukbi-jk"), optionsSpiderJK);
        chartSpiderJK.render();


        // Heatmap nilai rata-rata UKBI Berdasarkan Cluster Usia
        const centroidJenisKelamin = @json($centroidJenisKelamin);

        // generate data heatmap + round 2 decimal
        const dataCentroidJenisKelamin = centroidJenisKelamin.flatMap((row, rowIndex) =>
            seksiKeys.map((key, colIndex) => [
                colIndex,                      // X = kolom seksi
                rowIndex,                      // Y = baris cluster
                parseFloat(parseFloat(row[key]).toFixed(2))
            ])
        );

        // Substring template helper for the responsive labels
        Highcharts.Templating.helpers.substr = (s, from, length) =>
            s.substr(from, length);

        // Create the chart
        Highcharts.chart('heatmap-jk', {

            chart: {
                type: 'heatmap',
                marginTop: 40,
                marginBottom: 80,
                plotBorderWidth: 1
            },

            title: {
                text: 'Heatmap Nilai Rata-Rata UKBI Berdasarkan Jenis Kelamin',
                style: {
                    fontSize: '1em'
                }
            },

            xAxis: {
                title: {
                    text: "Seksi Ujian UKBI",
                    style: {
                        color: "#000",       // warna
                        fontWeight: "bold"   // opsional
                    }
                },
                categories: ['Seksi I', 'Seksi II', 'Seksi III']
            },

            yAxis: {
                title: {
                    text: "Jenis Kelamin", style: {
                        color: "#000",       // warna
                        fontWeight: "bold"   // opsional
                    }
                },    // <= LABEL Y
                categories: ['Laki-laki', 'Perempuan'],
                reversed: true
            },

            colorAxis: {
                min: 440,
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
                title: { text: "Nilai Rata-Rata Seksi" }
            },

            tooltip: {
                format:
                    '<b>{series.xAxis.categories.(point.x)}</b><br>' +
                    'Day: <b>{series.yAxis.categories.(point.y)}</b><br>' +
                    'Nilai Rata-rata: <b>{point.value}</b>'
            },

            series: [{
                borderColor: '#000',
                borderWidth: 1,
                data: dataCentroidJenisKelamin,
                dataLabels: {
                    enabled: true,
                    format: '{point.value:.2f}',
                    color: '#000',      // warna teks
                    style: {
                        fontWeight: 'bold'
                    }
                }
            }]
        });

        Highcharts.chart('heatmap-cluster', {

            chart: {
                type: 'heatmap',
                marginTop: 40,
                marginBottom: 80,
                plotBorderWidth: 1
            },

            title: {
                text: 'Heatmap Total Peserta per Kota per Cluster K-Means ({{ $tahun }})',
                style: {
                    fontSize: '1em'
                }
            },

            xAxis: {
                title: {
                    text: "Cluster K-Means",
                    style: {
                        color: "#000",
                        fontWeight: "bold"
                    }
                },
                categories: @json($clusters)   // <-- daftar cluster
            },

            yAxis: {
                title: {
                    text: "Kota",
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