@php
    // View Composer Inline untuk memuat daftar Course sesuai Role tanpa perlu mengubah Controller
    $sidebarCourses = collect();

    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'student') {
            // Ambil course yang statusnya 'active' ATAU sudah 'completed' namun masa aksesnya masih berlaku
            $sidebarCourses = \App\Models\Enrollment::with('course')
                ->where('user_id', $user->id)
                ->where(function ($query) {
                    $query->where('status', 'active')
                        ->orWhere(function ($q) {
                            $q->where('status', 'completed')
                                ->where(function ($subQuery) {
                                    $subQuery->whereNull('access_expires_at')
                                            ->orWhere('access_expires_at', '>', now());
                                });
                        });
                })
                ->get()
                ->pluck('course')
                ->filter();
        } elseif ($user->role === 'teacher') {
            // Ambil course yang diajar oleh teacher (baik sebagai pembuat/created_by maupun pengajar tambahan)
            $sidebarCourses = \App\Models\Course::where('created_by', $user->id)
                ->orWhereHas('instructors', function($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->where('status', 'published')
                ->get();
        }
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LPP-JPH LMS')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN (Untuk Toggle Sidebar Mobile & Dropdown Menu) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br bg-white text-slate-800 antialiased min-h-screen flex flex-col" x-data="{ sidebarOpen: false }">

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
             class="fixed inset-0 bg-[#123F35]/70 backdrop-blur-sm z-40 lg:hidden" 
             style="display: none;"></div>

        <!-- Sidebar Warna Dasar Hijau Deep Forest Green (#123F35) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#123F35] text-slate-100 flex flex-col flex-shrink-0 shadow-2xl transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-[#28745B]/30">
            
            <!-- Brand Logo & Mobile Close Button -->
            <div class="h-16 flex items-center justify-center px-6 border-b border-[#28745B]/40 bg-[#123F35]">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo_telkom_transparan2.png') }}" alt="Logo Telkom" class="w-10 h-10 object-contain">
                    <img src="{{ asset('images/logo_lms_lppjph_transparan_putih - Copy.png') }}" alt="Logo LPP-JPH" class="w-10 h-10 object-contain">
                </a>

                <!-- Tombol Close (Khusus Layar HP) -->
                <button @click="sidebarOpen = false" class="lg:hidden text-emerald-100/70 hover:text-white p-1 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                @auth
                    @if(Auth::user()->role === 'student')
                        <div class="px-3 pb-2 text-[10px] font-extrabold uppercase tracking-widest text-[#E7C982]">
                            Menu Siswa
                        </div>

                        <!-- Dashboard Student -->
                        <a href="{{ route('student.dashboard') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('student.dashboard') ? 'bg-[#28745B] text-white shadow-md' : 'text-emerald-100/80 hover:bg-[#28745B]/50 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <!-- Dropdown Katalog Pelatihan Student -->
                        <div x-data="{ openKatalog: {{ request()->routeIs('student.courses.*') ? 'true' : 'false' }} }">
                            <button @click="openKatalog = !openKatalog" 
                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition text-emerald-100/80 hover:bg-[#28745B]/50 hover:text-white {{ request()->routeIs('student.courses.*') ? 'bg-[#28745B]/30 text-white' : '' }}">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <span>Katalog Pelatihan</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="openKatalog ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Submenu Sub-Kategori Student -->
                            <div x-show="openKatalog" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                                <a href="{{ route('student.courses.index') }}" 
                                   class="block px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('student.courses.index') && !request('category') ? 'bg-[#28745B] text-white' : 'text-emerald-100/70 hover:bg-[#28745B]/40 hover:text-white' }}">
                                    • Semua Pelatihan
                                </a>
                                <a href="{{ route('student.courses.index', ['category' => 'penyelia_halal']) }}" 
                                   class="block px-3 py-2 rounded-lg text-xs font-semibold transition {{ request('category') === 'penyelia_halal' ? 'bg-[#28745B] text-white' : 'text-emerald-100/70 hover:bg-[#28745B]/40 hover:text-white' }}">
                                    • Penyelia Halal
                                </a>
                                <a href="{{ route('student.courses.index', ['category' => 'juleha']) }}" 
                                   class="block px-3 py-2 rounded-lg text-xs font-semibold transition {{ request('category') === 'juleha' ? 'bg-[#28745B] text-white' : 'text-emerald-100/70 hover:bg-[#28745B]/40 hover:text-white' }}">
                                    • Juleha (Juru Sembelih)
                                </a>
                                <a href="{{ route('student.courses.index', ['category' => 'auditor_halal']) }}" 
                                   class="block px-3 py-2 rounded-lg text-xs font-semibold transition {{ request('category') === 'auditor_halal' ? 'bg-[#28745B] text-white' : 'text-emerald-100/70 hover:bg-[#28745B]/40 hover:text-white' }}">
                                    • Auditor Halal
                                </a>
                            </div>
                        </div>

                        <!-- Sertifikat Student -->
                        <a href="{{ route('student.certificates.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('student.certificates.index') ? 'bg-[#28745B] text-white shadow-md' : 'text-emerald-100/80 hover:bg-[#28745B]/50 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            <span>Sertifikat Saya</span>
                        </a>

                        <!-- Daftar Course Aktif Student -->
                        @if($sidebarCourses->isNotEmpty())
                            <div class="pt-4 pb-1.5 px-3 text-[10px] font-extrabold uppercase tracking-widest text-[#E7C982]">
                                Pelatihan Saya
                            </div>
                            <div class="space-y-1 pl-1">
                                @foreach($sidebarCourses as $course)
                                    <a href="{{ route('student.courses.show', $course->id) }}" 
                                       class="flex items-center space-x-2.5 px-2.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->is('student/courses/' . $course->id . '*') ? 'bg-[#28745B] text-white font-bold shadow-sm' : 'text-emerald-100/75 hover:bg-[#28745B]/40 hover:text-white' }}"
                                       title="{{ $course->title }}">
                                        <svg class="w-4 h-4 text-emerald-100/75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/>
                                        </svg>
                                        <span class="truncate">{{ $course->title }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @endif

                    @if(Auth::user()->role === 'teacher')
                        <div class="px-3 pb-2 text-[10px] font-extrabold uppercase tracking-widest text-[#E7C982]">
                            Menu Pengajar
                        </div>

                        <!-- Dashboard Teacher -->
                        <a href="{{ route('teacher.dashboard') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('teacher.dashboard') ? 'bg-[#28745B] text-white shadow-md' : 'text-emerald-100/80 hover:bg-[#28745B]/50 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <!-- Dropdown Kelola Course Teacher -->
                        <div x-data="{ openTeacherCourse: {{ request()->routeIs('teacher.courses.*') ? 'true' : 'false' }} }">
                            <button @click="openTeacherCourse = !openTeacherCourse" 
                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition text-emerald-100/80 hover:bg-[#28745B]/50 hover:text-white {{ request()->routeIs('teacher.courses.*') ? 'bg-[#28745B]/30 text-white' : '' }}">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    <span>Kelola Course</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="openTeacherCourse ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Submenu Sub-Kategori Teacher -->
                            <div x-show="openTeacherCourse" x-collapse class="pl-9 pr-2 py-1 space-y-1">
                                <a href="{{ route('teacher.courses.index') }}" 
                                   class="block px-3 py-2 rounded-lg text-xs font-semibold transition {{ request()->routeIs('teacher.courses.index') && !request('category') ? 'bg-[#28745B] text-white' : 'text-emerald-100/70 hover:bg-[#28745B]/40 hover:text-white' }}">
                                    • Semua Kelas
                                </a>
                                <a href="{{ route('teacher.courses.index', ['category' => 'penyelia_halal']) }}" 
                                   class="block px-3 py-2 rounded-lg text-xs font-semibold transition {{ request('category') === 'penyelia_halal' ? 'bg-[#28745B] text-white' : 'text-emerald-100/70 hover:bg-[#28745B]/40 hover:text-white' }}">
                                    • Penyelia Halal
                                </a>
                                <a href="{{ route('teacher.courses.index', ['category' => 'juleha']) }}" 
                                   class="block px-3 py-2 rounded-lg text-xs font-semibold transition {{ request('category') === 'juleha' ? 'bg-[#28745B] text-white' : 'text-emerald-100/70 hover:bg-[#28745B]/40 hover:text-white' }}">
                                    • Juleha (Juru Sembelih)
                                </a>
                                <a href="{{ route('teacher.courses.index', ['category' => 'auditor_halal']) }}" 
                                   class="block px-3 py-2 rounded-lg text-xs font-semibold transition {{ request('category') === 'auditor_halal' ? 'bg-[#28745B] text-white' : 'text-emerald-100/70 hover:bg-[#28745B]/40 hover:text-white' }}">
                                    • Auditor Halal
                                </a>
                            </div>
                        </div>

                        <!-- Daftar Course Yang Diajar Teacher -->
                        @if($sidebarCourses->isNotEmpty())
                            <div class="pt-4 pb-1.5 px-3 text-[10px] font-extrabold uppercase tracking-widest text-[#E7C982]">
                                Kelas Yang Diajar
                            </div>
                            <div class="space-y-1 pl-1">
                                @foreach($sidebarCourses as $course)
                                    <a href="{{ route('teacher.courses.show', $course->id) }}" 
                                       class="flex items-center space-x-2.5 px-2.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->is('teacher/courses/' . $course->id . '*') ? 'bg-[#28745B] font-bold shadow-sm' : 'text-emerald-100/75 hover:bg-[#28745B]/40 hover:text-white' }}"
                                       title="{{ $course->title }}">
                                        <svg class="w-4 h-4 text-emerald-100/75 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/>
                                        </svg>
                                        <span class="truncate">{{ $course->title }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @endif

                    @if(Auth::user()->role === 'admin')
                        <div class="px-3 pb-2 text-[10px] font-extrabold uppercase tracking-widest text-[#E7C982]">
                            Menu Administrator
                        </div>

                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#28745B] text-white shadow-md' : 'text-emerald-100/80 hover:bg-[#28745B]/50 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-[#28745B] text-white shadow-md' : 'text-emerald-100/80 hover:bg-[#28745B]/50 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Kelola User</span>
                        </a>

                        <a href="{{ route('admin.certificates.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.certificates.*') ? 'bg-[#28745B] text-white shadow-md' : 'text-emerald-100/80 hover:bg-[#28745B]/50 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            <span>Kelola Sertifikat</span>
                        </a>
                    @endif

                    <div class="pt-4 pb-1 px-3 text-[10px] font-extrabold uppercase tracking-widest text-[#E7C982]">
                        Pengaturan
                    </div>

                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('profile.*') ? 'bg-[#28745B] text-white font-bold shadow-md' : 'text-emerald-100/80 hover:bg-[#28745B]/50 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <header class="h-16 bg-white border-b border-[#28745B]/15 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-10 shadow-2xs">
                
                <div class="flex items-center space-x-3">
                    <!-- Hamburger Button (Khusus Layar HP/Tablet) -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-[#123F35] hover:bg-[#F7F5EE] transition focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Title Header -->
                    <div class="font-bold text-[#123F35] text-sm sm:text-base truncate">
                        @yield('header_title', 'Dashboard')
                    </div>
                </div>

                <!-- User Dropdown & Profile -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    @auth
                        <div class="flex items-center space-x-2 sm:space-x-3 border-r border-slate-200 pr-2 sm:pr-4">
                            <!-- Link Profile (Foto/Icon + Nama & Email) -->
                            <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 sm:space-x-3 group p-1 sm:p-1.5 rounded-xl hover:bg-[#F7F5EE] transition">
                                <!-- User Avatar/Icon -->
                                <div class="relative w-8 h-8 sm:w-9 sm:h-9 flex-shrink-0">
                                    @if(Auth::user()->profile_photo ?? false)
                                        <img src="{{ Auth::user()->profile_photo_url }}" 
                                        alt="{{ Auth::user()->name }}" 
                                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover border border-[#28745B]/30">
                                    @else
                                        <!-- Default User Icon Fallback -->
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold text-xs sm:text-sm border border-[#28745B]/30 group-hover:bg-[#28745B] group-hover:text-white transition">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- User Name & Email -->
                                <div class="text-right hidden md:block">
                                    <div class="text-sm font-bold text-[#123F35] group-hover:text-[#28745B] transition truncate max-w-[150px]">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-[150px]">{{ Auth::user()->email }}</div>
                                </div>
                            </a>

                            <!-- Role Badge -->
                            <div class="hidden xs:block">
                                @if(Auth::user()->role === 'admin')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-bold bg-red-100 text-red-800 border border-red-200">Admin</span>
                                @elseif(Auth::user()->role === 'teacher')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-bold bg-[#E7C982]/30 text-[#123F35] border border-[#E7C982]">Teacher</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-bold bg-[#28745B]/15 text-[#123F35] border border-[#28745B]/30">Student</span>
                                @endif
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="p-1.5 sm:p-2 text-slate-400 hover:text-red-600 transition rounded-xl hover:bg-red-50" title="Logout">
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
                    <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-[#123F35] text-sm flex items-center justify-between shadow-2xs">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-[#28745B] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between shadow-2xs">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-[#28745B]/15 py-3.5 px-4 sm:px-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} LPP-JPH LMS - Lembaga Pelaksana Pelatihan Jaminan Produk Halal.
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>