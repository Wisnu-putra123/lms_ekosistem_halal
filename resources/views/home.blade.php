<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekosistem Halal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl font-bold text-emerald-600">Ekosistem Halal</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-emerald-600 font-medium px-3 py-2">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 font-medium px-3 py-2">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg transition">Daftar</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-emerald-50 via-white to-gray-50 py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-6xl font-extrabold text-gray-900 tracking-tight leading-tight mb-6">
                Platform Terintegrasi <br class="hidden sm:inline" />
                <span class="text-emerald-600">Ekosistem Halal</span>
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto mb-8">
                Layanan digital terpadu untuk pendampingan, pencatatan, dan pengelolaan sertifikasi halal secara cepat, transparan, dan terpercaya.
            </p>
            <div class="flex justify-center items-center space-x-4">
                @auth
                    <a href="{{ route('home') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-lg font-semibold text-lg shadow-md transition">
                        Masuk ke Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-lg font-semibold text-lg shadow-md transition">
                        Mulai Sekarang
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-8 py-3 rounded-lg font-semibold text-lg shadow-sm transition">
                            Buat Akun
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} Ekosistem Halal. All rights reserved.
    </footer>
</body>
</html>