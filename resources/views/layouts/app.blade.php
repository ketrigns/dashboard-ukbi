<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="shortcut icon" href="{{ asset('assets/images/logo-ukbi.png') }}">
  <title>@yield('title', 'Dashboard UKBI')</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
  {{-- Tambahkan CSS Leaflet --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  {{-- Tambahkan JS Leaflet --}}
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/heatmap.js"></script>
  <script src="https://code.highcharts.com/modules/exporting.js"></script>
  <script src="https://code.highcharts.com/modules/export-data.js"></script>
  <script src="https://code.highcharts.com/modules/accessibility.js"></script>
  <script src="https://code.highcharts.com/themes/adaptive.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    ul {
      all: revert;
    }

    ol {
      all: revert;
    }

    /* Background peta (Canvas) */
    .map-wrapper {
      height: 80vh;
      width: 100%;
      background-color: #f4f4f4;
    }

    /* Style Label Nama Wilayah */
    .region-label {
      font-size: 10px;
      font-weight: bold;
      color: #fff;
      text-align: center;
      /* Outline hitam agar tulisan putih terbaca jelas */
      text-shadow: 1px 1px 2px black, -1px -1px 2px black, 1px -1px 2px black, -1px 1px 2px black;
      white-space: normal;
      line-height: 1.2;
      margin-top: -10px;
    }

    /* Style Legenda */
    .legend {
      background: white;
      padding: 10px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      font-size: 12px;
      font-family: sans-serif;
    }

    @media print {
      .print-grid-4 {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 8px !important;
        /* optional */
      }

      * {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      #print-loader {
        display: none !important;
      }

    }
  </style>
</head>

<body style='font-family: "Roboto", sans-serif; background-color: rgba(148, 180, 193, 0.08);' class="bg-gray-50">

  {{-- Navbar --}}
  @include('layouts.navbar')
  <div id="print-loader" class="fixed inset-0 bg-white/80 flex items-center justify-center z-[9999] hidden">
    <div class="flex flex-col items-center gap-2">
      <div class="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
      <p class="text-gray-700 font-semibold">Menyiapkan tampilan cetak...</p>
    </div>
  </div>


  {{-- Konten Halaman --}}
  <main class="px-5 py-3">
    @yield('content')
  </main>

  @include('layouts.footer')

</body>

</html>