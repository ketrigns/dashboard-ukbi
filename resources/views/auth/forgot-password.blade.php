<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Lupa Password - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
    <meta content="Myrathemes" name="author">

    <link rel="shortcut icon" href="{{ asset('assets/images/logo-ukbi.png') }}">

    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">

    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>

<body>

    <div class="flex flex-col items-center justify-center min-h-screen px-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Lupa Password</h1>
            <p class="text-gray-500">Masukkan email Anda. Kami akan mengirimkan link untuk mereset password.</p>
        </div>
        
        <div class="card">
            <div class="p-6">
                
                @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-green-600 bg-green-100 p-3 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                <form class="sm:min-w-[500px] w-[350px]" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="emailinput"
                            class="text-default-800 text-sm font-medium inline-block mb-2">Email</label>
                        <input type="email" class="form-input" id="emailinput" placeholder="Masukkan Email Terdaftar" name="email"
                            value="{{ old('email') }}" required autofocus>
                        
                        @error('email')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="w-full btn bg-primary text-white mb-4">
                        Kirim Link Reset Password
                    </button>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                            <iconify-icon icon="mdi:arrow-left" class="inline-block align-middle"></iconify-icon> Kembali ke halaman Login
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/preline/preline.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/iconify-icon/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <script src="{{ asset('assets/js/app.js') }}"></script>

</body>

</html>