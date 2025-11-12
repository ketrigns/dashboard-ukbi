<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login Admin</title>
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
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>

<body>

    <div class="flex flex-col items-center justify-center min-h-screen px-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Login Administrator</h1>
            <p class="text-gray-500">Dashboard UKBI</p>
        </div>
        <div class="card">
            <div class="p-6">
                <h4 class="card-title mb-4">Login</h4>
                <form class="sm:min-w-[500px] w-[350px]" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="nameinput"
                            class="text-default-800 text-sm font-medium inline-block mb-2">Username</label>
                        <input type="text" class="form-input" id="nameinput" placeholder="Masukkan Username"
                            name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span style="color: red;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1"
                            class="text-default-800 text-sm font-medium inline-block mb-2">Password</label>
                        <input type="password" class="form-input" id="exampleInputPassword1" placeholder="Password"
                            name="password" required>
                    </div>
                    <button type="submit" class="w-full btn bg-primary text-white">Login</button>
                </form>

            </div>
        </div>
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