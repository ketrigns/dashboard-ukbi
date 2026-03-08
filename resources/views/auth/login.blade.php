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
                        <label for="emailinput"
                            class="text-default-800 text-sm font-medium inline-block mb-2">Email</label>
                        <input type="email" class="form-input" id="emailinput" placeholder="Masukkan Email" name="email"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <span style="color: red;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <label for="exampleInputPassword1" class="text-default-800 text-sm font-medium inline-block">Password</label>
                            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">Lupa Password?</a>
                        </div>

                        <div class="relative">
                            <input type="password" class="form-input pr-10" id="passwordInput" placeholder="Password"
                                name="password" required>

                            <!-- Icon Mata -->
                            <span class="absolute inset-y-0 right-3 flex items-center cursor-pointer"
                                onclick="togglePassword()">
                                <iconify-icon id="toggleIcon" icon="mdi:eye-off" width="22"
                                    class="text-gray-600"></iconify-icon>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn bg-primary text-white">Login</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('toggleIcon');

            if (input.type === "password") {
                input.type = "text";
                icon.setAttribute("icon", "mdi:eye"); // icon lihat
            } else {
                input.type = "password";
                icon.setAttribute("icon", "mdi:eye-off"); // icon tutup
            }
        }
    </script>


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