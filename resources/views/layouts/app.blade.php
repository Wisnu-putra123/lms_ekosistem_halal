<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ekosistem Halal LMS')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN (Untuk Toggle Sidebar Mobile) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Overlay / Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden" 
             style="display: none;"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-200 flex flex-col flex-shrink-0 shadow-xl transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">
            
            <!-- Brand Logo & Mobile Close Button -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <svg class="w-7 h-7 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-bold text-lg text-white tracking-tight">Ekosistem Halal</span>
                </a>

                <!-- Tombol Close (Khusus Layar HP) -->
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                @auth
                    @if(Auth::user()->role === 'student')
                        <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            Menu Siswa
                        </div>

                        <a href="{{ route('student.dashboard') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('student.dashboard') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('student.courses.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-slate-400 hover:bg-slate-800 hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span>Pelatihan Saya</span>
                        </a>
                    @endif

                    @if(Auth::user()->role === 'teacher')
                        <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-amber-500/80">
                            Menu Pengajar
                        </div>

                        <a href="{{ route('teacher.dashboard') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('teacher.dashboard') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('teacher.courses.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-slate-400 hover:bg-slate-800 hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Course</span>
                        </a>
                    @endif

                    @if(Auth::user()->role === 'admin')
                        <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-red-400/80">
                            Menu Administrator
                        </div>

                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Kelola User</span>
                        </a>
                    @endif

                    <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        Pengaturan
                    </div>

                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('profile.*') ? 'bg-slate-800 text-emerald-400 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Pengaturan Profil</span>
                    </a>
                @endauth
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-10">
                
                <div class="flex items-center space-x-3">
                    <!-- Hamburger Button (Khusus Layar HP/Tablet) -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Title Header -->
                    <div class="font-semibold text-slate-800 text-sm sm:text-base truncate">
                        @yield('header_title', 'Dashboard')
                    </div>
                </div>

                <!-- User Dropdown & Profile -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    @auth
                        <div class="flex items-center space-x-2 sm:space-x-3 border-r border-slate-200 pr-2 sm:pr-4">
                            <!-- Link Profile (Foto/Icon + Nama & Email) -->
                            <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 sm:space-x-3 group p-1 sm:p-1.5 rounded-lg hover:bg-slate-100 transition">
                                <!-- User Avatar/Icon -->
                                <div class="relative w-8 h-8 sm:w-9 sm:h-9 flex-shrink-0">
                                    @if(Auth::user()->avatar_url ?? false)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar_url) }}" alt="Profile" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover border border-slate-200">
                                    @else
                                        <!-- Default User Icon Fallback -->
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs sm:text-sm border border-emerald-200 group-hover:bg-emerald-200 transition">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- User Name & Email (Disembunyikan di layar HP kecil) -->
                                <div class="text-right hidden md:block">
                                    <div class="text-sm font-semibold text-slate-800 group-hover:text-emerald-600 transition truncate max-w-[150px]">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-[150px]">{{ Auth::user()->email }}</div>
                                </div>
                            </a>

                            <!-- Role Badge -->
                            <div class="hidden xs:block">
                                @if(Auth::user()->role === 'admin')
                                    <span class="px-2 sm:px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold bg-red-100 text-red-700">Admin</span>
                                @elseif(Auth::user()->role === 'teacher')
                                    <span class="px-2 sm:px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold bg-amber-100 text-amber-700">Teacher</span>
                                @else
                                    <span class="px-2 sm:px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold bg-emerald-100 text-emerald-700">Student</span>
                                @endif
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="p-1.5 sm:p-2 text-slate-400 hover:text-red-600 transition rounded-lg hover:bg-slate-100" title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                <!-- Notifications -->
                @if(session('success'))
                    <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 py-3 px-4 sm:px-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Ekosistem Halal LMS - Sistem Manajemen Autentikasi & Pengguna.
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>