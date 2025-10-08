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
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
    {{-- Tambahkan CSS Leaflet --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  {{-- Tambahkan JS Leaflet --}}
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body style='font-family: "Roboto", sans-serif; background-color: rgba(148, 180, 193, 0.08);' class="bg-gray-50">

  {{-- Navbar --}}
  @include('layouts.navbar')

  {{-- Konten Halaman --}}
  <main class="px-5 py-3">
    @yield('content')
  </main>

</body>
</html>
