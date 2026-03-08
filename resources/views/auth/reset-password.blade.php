<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Buat Password Baru - Admin</title>
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
            <h1 class="text-3xl font-bold text-gray-800">Buat Password Baru</h1>
            <p class="text-gray-500">Silakan ketikkan password baru untuk akun Anda.</p>
        </div>
        
        <div class="card">
            <div class="p-6">
                
                <form class="sm:min-w-[500px] w-[350px]" method="POST" action="{{ route('password.update') }}">
                    @csrf
                    
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-4">
                        <label for="emailinput" class="text-default-800 text-sm font-medium inline-block mb-2">Email</label>
                        <input type="email" class="form-input bg-gray-100 cursor-not-allowed" id="emailinput" name="email"
                            value="{{ $email ?? old('email') }}" required readonly>
                        
                        @error('email')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="passwordInput" class="text-default-800 text-sm font-medium inline-block mb-2">Password Baru</label>
                        <div class="relative">
                            <input type="password" class="form-input pr-10" id="passwordInput" placeholder="Masukkan Password"
                                name="password" required>

                            <span class="absolute inset-y-0 right-3 flex items-center cursor-pointer" onclick="togglePassword()">
                                <iconify-icon id="toggleIcon" icon="mdi:eye-off" width="22" class="text-gray-600"></iconify-icon>
                            </span>
                        </div>
                        @error('password')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="passwordConfirmInput" class="text-default-800 text-sm font-medium inline-block mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" class="form-input pr-10" id="passwordConfirmInput" placeholder="Ketik ulang password baru"
                                name="password_confirmation" required>

                            <span class="absolute inset-y-0 right-3 flex items-center cursor-pointer" onclick="togglePasswordConfirm()">
                                <iconify-icon id="toggleConfirmIcon" icon="mdi:eye-off" width="22" class="text-gray-600"></iconify-icon>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn bg-primary text-white">
                        Simpan Password Baru
                    </button>
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
                icon.setAttribute("icon", "mdi:eye");
            } else {
                input.type = "password";
                icon.setAttribute("icon", "mdi:eye-off");
            }
        }

        function togglePasswordConfirm() {
            const input = document.getElementById('passwordConfirmInput');
            const icon = document.getElementById('toggleConfirmIcon');

            if (input.type === "password") {
                input.type = "text";
                icon.setAttribute("icon", "mdi:eye");
            } else {
                input.type = "password";
                icon.setAttribute("icon", "mdi:eye-off");
            }
        }
    </script>

    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/preline/preline.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/iconify-icon/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <script src="{{ asset('assets/js/app.js') }}"></script>

</body>

</html>