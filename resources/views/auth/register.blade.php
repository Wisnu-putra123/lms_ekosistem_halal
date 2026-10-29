<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Ekosistem Halal LMS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F7F5EE] min-h-screen flex items-center justify-center p-4 py-8 relative overflow-hidden">

    <!-- Dekorasi Radial Latar Belakang Halus -->
    <div class="absolute -left-20 -top-20 -z-0 h-96 w-96 rounded-full bg-[#28745B]/10 blur-3xl"></div>
    <div class="absolute -right-20 -bottom-20 -z-0 h-96 w-96 rounded-full bg-[#E7C982]/20 blur-3xl"></div>

    <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-xl border border-[#28745B]/15 p-6 sm:p-8">
        
        <!-- Header & Logo Brand (Konsisten dengan Halaman Login) -->
        <div class="flex flex-col items-center text-center mb-6">
            <a href="{{ url('/') }}" class="group flex items-center space-x-3 mb-4">
                <!-- Logo Shield Icon SVG (Bisa diganti dengan <img> logo asli nanti) -->
                <div class="w-12 h-12 rounded-2xl bg-[#123F35] text-[#E7C982] flex items-center justify-center shadow-md border border-[#E7C982]/30 transition group-hover:scale-105">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <!-- <img src="{{ asset('images/logo-lpp-jph.png') }}" alt="Logo LPP-JPH" class="w-10 h-10 object-contain"> -->
                </div>
                
            </a>

            <h2 class="text-2xl font-bold text-[#123F35]">Daftar Akun Baru</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Registrasi untuk mulai mengakses platform LMS</p>
        </div>

        <!-- Form Register -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Field Nama Lengkap -->
            <div>
                <label for="name" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </span>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required autofocus
                           class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                </div>
                @error('name')
                    <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Alamat Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required
                           class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                </div>
                @error('email')
                    <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field Password -->
            <div>
                <label for="password" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required
                           class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                </div>
                @error('password')
                    <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </span>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi" required
                           class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 px-4 bg-[#123F35] hover:bg-[#28745B] text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition duration-200 mt-2">
                Daftar Sekarang
            </button>

            <!-- Footer Login Link -->
            <div class="text-center pt-3 border-t border-slate-100">
                <p class="text-xs sm:text-sm text-slate-600">
                    Sudah memiliki akun? <a href="{{ route('login') }}" class="font-bold text-[#28745B] hover:text-[#123F35] underline underline-offset-2 transition">Login ke Akun Anda</a>
                </p>
            </div>
        </form>
    </div>

</body>
</html>