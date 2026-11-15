<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Ekosistem Halal LMS</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#123F35] min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Dekorasi Radial Latar Belakang Halus -->
    <div class="absolute -left-20 -top-20 -z-0 h-96 w-96 rounded-full bg-[#28745B]/10 blur-3xl"></div>
    <div class="absolute -right-20 -bottom-20 -z-0 h-96 w-96 rounded-full bg-[#E7C982]/20 blur-3xl"></div>

    <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-xl border border-[#28745B]/15 p-6 sm:p-8">
        
        <!-- Header & Logo Brand -->
        <div class="flex flex-col items-center text-center mb-8">
            <a href="{{ url('/') }}" class="group flex items-center space-x-3 mb-4">
                <div class="w-14 h-14 rounded-2xl text-[#E7C982] flex items-center justify-center shadow-md border border-[#E7C982]/30 transition group-hover:scale-105">
                    <img src="{{ asset('images/logo_telkom_transparan2.png') }}" alt="Logo Telkom" class="w-10 h-10 object-contain">
                </div>
                <div class="w-14 h-14 rounded-2xl text-[#E7C982] flex items-center justify-center shadow-md border border-[#E7C982]/30 transition group-hover:scale-105">
                    <img src="{{ asset('images/logo_lms_lppjph_transparan2.png') }}" alt="Logo LPP-JPH" class="w-10 h-10 object-contain">
                </div>
            </a>

            <h2 class="text-2xl font-bold text-[#123F35]">Masuk Pengguna</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Silakan masukkan akun Anda untuk melanjutkan ke LMS</p>
        </div>

        <!-- Flash Message (misal setelah logout) -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Login -->
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Field Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Alamat Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus
                           class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                </div>
                @error('email')
                    <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field Password (Dengan Tombol Visibility) -->
            <div>
                <label for="password" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="••••••••" required
                           class="w-full pl-11 pr-11 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                    <button type="button" onclick="togglePasswordVisibility('password', this)" aria-label="Tampilkan kata sandi"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-[#123F35] transition cursor-pointer">
                        <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Checkbox Remember Me -->
            <div class="flex items-center justify-between text-sm pt-1">
                <label class="flex items-center text-slate-600 cursor-pointer select-none">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                           class="w-4 h-4 text-[#28745B] border-slate-300 rounded focus:ring-[#28745B]">
                    <span class="ml-2 text-xs font-medium text-slate-700">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 px-4 bg-[#123F35] hover:bg-[#28745B] text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition duration-200 mt-2 cursor-pointer">
                Masuk Sekarang
            </button>

            <!-- Footer Register Link -->
            <div class="text-center pt-3 border-t border-slate-100">
                <p class="text-xs sm:text-sm text-slate-600">
                    Belum memiliki akun? <a href="{{ route('register') }}" class="font-bold text-[#28745B] hover:text-[#123F35] underline underline-offset-2 transition">Daftar Akun Baru</a>
                </p>
            </div>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const eyeOpen = btn.querySelector('.eye-open');
            const eyeClosed = btn.querySelector('.eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</body>
</html>