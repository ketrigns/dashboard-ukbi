<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
    <meta content="Myrathemes" name="author">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-ukbi.png') }}">

    <!-- Icons css  (Mandatory in All Pages) -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">

    <!-- App css  (Mandatory in All Pages) -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css' rel='stylesheet'
        type='text/css' />
    <script type='text/javascript'
        src='https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/heatmap.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    
    <style>
        .combobox-container {
            position: relative;
            margin: auto;
        }

        /* Stylilng untuk list dropdown */
        .options-list {
            /* Sembunyikan dropdown secara default */
            display: none;

            /* Posisi absolute agar "melayang" di bawah input */
            position: absolute;
            width: 100%;
            border: 1px solid #ddd;
            background: #fff;
            border-top: none;
            /* Hapus border atas krn sudah nempel input */
            max-height: 200px;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 4px 4px;
        }

        /* Class 'show' ini akan kita tambahkan/hapus via JavaScript */
        .options-list.show {
            display: block;
        }

        /* Styling untuk tiap item di dropdown */
        .option-item {
            padding: 10px;
            cursor: pointer;
        }

        /* Efek hover saat mouse di atas item */
        .option-item:hover {
            background-color: #e9e9e9;
        }

        ul {
            all: revert;
        }

        ol {
            all: revert;
        }
    </style>

</head>

<body>

    <div class="wrapper">

        <!-- Start Sidebar -->
        @include('layouts.admin.sidebar')
        <!-- End Sidebar -->

        <!-- Start Page Content here -->
        <div class="page-content">

            <!-- Topbar Start -->
            @include('layouts.admin.header')
            <!-- Topbar End -->

            <main>
                @yield('content')
            </main>

            <!-- Footer Start -->
            {{-- <footer class="footer bg-white flex items-center py-5">
                <div class="px-6 flex md:justify-between justify-center w-full gap-4">
                    <div>
                        <script>document.write(new Date().getFullYear())</script> © Drezoc
                    </div>
                    <div class="md:flex hidden gap-2 item-center md:justify-end">
                        Design &amp; Develop by<a href="#" class="text-primary">Myrathemes</a>
                    </div>
                </div>
            </footer> --}}
            <!-- Footer End -->

        </div>
        <!-- End Page content -->

    </div>

    <!-- Plugin Js (Mandatory in All Pages) -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/preline/preline.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/iconify-icon/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <!-- App Js (Mandatory in All Pages) -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

</body>

</html>