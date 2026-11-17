@extends('layouts.app')

@section('title', 'Dashboard Admin - LPP-JPH LMS')
@section('header_title', 'Dashboard Administrasi')

@section('content')
<div class="space-y-6">

    <!-- Welcome Card Banner (Deep Forest Green dengan Aksen Muted Gold) -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-6 sm:p-8 text-white shadow-md border border-[#E7C982]/40">
        <!-- Background Radial Gradient & Glow Effect -->
        <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
        <div class="absolute -right-10 -bottom-10 -z-0 h-48 w-48 rounded-full border border-[#E7C982]/20 blur-xl"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="space-y-2 max-w-2xl">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wider bg-[#E7C982]/20 text-[#E7C982] border border-[#E7C982]/40 uppercase">
                    Panel Kendali Administrator
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Halo, {{ Auth::user()->name }}</h1>
                <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
                    Pantau statistik pengguna, progres pelatihan aktif, kelengkapan berkas persyaratan, serta antrean penerbitan dan publikasi sertifikat Sistem Jaminan Halal.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 flex-shrink-0">
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center space-x-1.5 bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] px-4 py-2.5 rounded-xl text-xs font-extrabold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Tambah User</span>
                </a>
                <!-- route('admin.courses.create') -->
                <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center space-x-1.5 bg-white/10 hover:bg-white/20 text-white border border-white/25 px-4 py-2.5 rounded-xl text-xs font-bold backdrop-blur-xs transition">
                    <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Buat Pelatihan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid (4 KPI Utama) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stats 1: Total Pengguna -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 shadow-2xs flex items-center justify-between hover:border-[#28745B]/60 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#123F35]/70">Total Pengguna</span>
                <div class="text-2xl font-black text-[#123F35]">{{ $userStats['total'] }} Akun</div>
                <p class="text-[11px] text-slate-500">
                    {{ $userStats['students'] }} Peserta &bull; {{ $userStats['teachers'] }} Pengajar
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold border border-[#28745B]/30 flex-shrink-0">
                <svg class="w-6 h-6 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>

        <!-- Stats 2: Pelatihan Terbit -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 shadow-2xs flex items-center justify-between hover:border-[#28745B]/60 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#123F35]/70">Pelatihan Terbit</span>
                <div class="text-2xl font-black text-[#123F35]">{{ $courseStats['published'] }} Kelas</div>
                <p class="text-[11px] text-slate-500">
                    Total {{ $courseStats['total'] }} ({{ $courseStats['draft'] }} Draft)
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold border border-[#28745B]/30 flex-shrink-0">
                <svg class="w-6 h-6 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>

        <!-- Stats 3: Enrollment Peserta -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 shadow-2xs flex items-center justify-between hover:border-[#28745B]/60 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#123F35]/70">Enrollment Peserta</span>
                <div class="text-2xl font-black text-[#28745B]">{{ $enrollmentStats['active'] }} Aktif</div>
                <p class="text-[11px] text-slate-500">
                    {{ $enrollmentStats['completed'] }} Lulus dari {{ $enrollmentStats['total'] }} Total
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold border border-[#28745B]/30 flex-shrink-0">
                <svg class="w-6 h-6 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Stats 4: Status Sertifikat -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 shadow-2xs flex items-center justify-between hover:border-[#28745B]/60 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#123F35]/70">Sertifikat Terbit</span>
                <div class="text-2xl font-black {{ $certificateStats['draft'] > 0 ? 'text-amber-600' : 'text-[#28745B]' }}">
                    {{ $certificateStats['published'] }} Terbit
                </div>
                <p class="text-[11px] text-slate-500">
                    {{ $certificateStats['draft'] }} Draft &bull; {{ $certificateStats['signed'] }} TTD BPJPH
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold border border-[#28745B]/30 flex-shrink-0">
                <svg class="w-6 h-6 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Banner Peringatan Kelengkapan Berkas Identitas Peserta -->
    @if($incompleteProfilesCount > 0)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-start sm:items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-amber-100 border border-amber-300 text-amber-800 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs font-extrabold text-amber-900">Perhatian Kelengkapan Data Kependudukan &amp; Berkas</h3>
                    <p class="text-xs text-amber-800/90 mt-0.5">
                        Terdapat <span class="font-extrabold">{{ $incompleteProfilesCount }} peserta</span> yang belum melengkapi NIK, Nomor KK, Nomor Ijazah, atau unggahan dokumen persyaratan sertifikasi.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.users.index', ['role' => 'student']) }}" class="px-4 py-2 bg-amber-700 hover:bg-amber-800 text-white rounded-xl text-xs font-bold transition flex-shrink-0 self-start sm:self-auto">
                Periksa Data Peserta &rarr;
            </a>
        </div>
    @endif

    <!-- Layout Grid 2 Kolom Utama -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KIRI (2 Kolom): Antrean Prioritas Sertifikat & Performa Pelatihan -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. Antrean Penerbitan & Publikasi Sertifikat -->
            <div class="bg-white rounded-2xl border border-slate-300 p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-300 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-[#123F35] flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            <span>Antrean Administrasi Sertifikat</span>
                        </h2>
                        <p class="text-xs text-slate-500">Peserta lulus yang menunggu penerbitan record sertifikat atau publikasi draft.</p>
                    </div>
                    <span class="px-3 py-1 bg-[#F7F5EE] text-[#123F35] rounded-xl text-xs font-bold border border-[#28745B]/30">
                        {{ $pendingCertificates->count() + $draftCertificates->count() }} Antrean
                    </span>
                </div>

                <div class="space-y-3">
                    <!-- Item Belum Dibuatkan Sertifikat -->
                    @foreach($pendingCertificates as $en)
                        <div class="p-4 rounded-xl border border-amber-300 bg-amber-50/50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-300">
                                        Belum Dibuatkan Sertifikat
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-semibold truncate">
                                        Lulus {{ $en->completed_at ? \Carbon\Carbon::parse($en->completed_at)->diffForHumans() : 'baru saja' }}
                                    </span>
                                </div>
                                <h3 class="text-xs font-bold text-[#123F35] truncate">{{ $en->user->name }}</h3>
                                <p class="text-[11px] text-slate-600 truncate">Pelatihan: <span class="font-semibold">{{ $en->course->title }}</span></p>
                            </div>
                            <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2 bg-[#123F35] hover:bg-[#28745B] text-white rounded-xl text-xs font-bold transition shadow-xs flex-shrink-0 self-start sm:self-auto">
                                Proses Sertifikat &rarr;
                            </a>
                        </div>
                    @endforeach

                    <!-- Item Berstatus Draft (Belum Dipublikasikan) -->
                    @foreach($draftCertificates as $cert)
                        <div class="p-4 rounded-xl border border-slate-300 bg-[#F7F5EE]/50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-[#E7C982]/40 text-[#123F35] border border-[#E7C982]">
                                        Draft Sertifikat
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-semibold">No: {{ $cert->certificate_number }}</span>
                                    @if($cert->signed_media_id)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">PDF TTD BPJPH Siap</span>
                                    @endif
                                </div>
                                <h3 class="text-xs font-bold text-[#123F35] truncate">{{ $cert->enrollment->user->name ?? '-' }}</h3>
                                <p class="text-[11px] text-slate-600 truncate">Pelatihan: <span class="font-semibold">{{ $cert->enrollment->course->title ?? '-' }}</span></p>
                            </div>
                            <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2 bg-[#28745B] hover:bg-[#123F35] text-white rounded-xl text-xs font-bold transition shadow-xs flex-shrink-0 self-start sm:self-auto">
                                Publikasikan &rarr;
                            </a>
                        </div>
                    @endforeach

                    @if($pendingCertificates->isEmpty() && $draftCertificates->isEmpty())
                        <div class="p-8 text-center text-xs text-slate-500 bg-[#F7F5EE]/40 rounded-xl border border-dashed border-slate-300">
                            Seluruh sertifikat peserta lulus telah diterbitkan dan dipublikasikan.
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. Ringkasan Performa Pelatihan (Tabel dengan Header #28745B) -->
            <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-slate-300 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-[#123F35]">Ringkasan Performa Pelatihan</h2>
                        <p class="text-xs text-slate-500">Statistik peserta aktif, kelulusan, dan rata-rata progres per kelas.</p>
                    </div>
                    <!-- route('admin.courses.index') -->
                    <a href="{{ route('admin.courses.index') }}" class="text-xs font-bold text-[#28745B] hover:text-[#123F35] transition">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-[#28745B] text-white border-b border-slate-300">
                            <tr class="text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                                <th class="py-3 px-4">Pelatihan</th>
                                <th class="py-3 px-4">Pengajar</th>
                                <th class="py-3 px-4 text-center">Aktif / Lulus</th>
                                <th class="py-3 px-4">Rata-Rata Progres</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-300 text-xs text-slate-700">
                            @forelse($courseOverviews as $course)
                                <tr class="hover:bg-[#F7F5EE]/40 transition">
                                    <td class="py-3.5 px-4">
                                        <!-- route('admin.courses.show', $course) -->
                                        <a href="" class="font-bold text-[#123F35] hover:text-[#28745B] transition line-clamp-1">
                                            {{ $course->title }}
                                        </a>
                                        <span class="text-[10px] text-slate-500">{{ $course->code ?? 'Tanpa Kode' }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($course->instructors->isNotEmpty())
                                            <span class="font-semibold text-slate-700">{{ $course->instructors->pluck('name')->join(', ') }}</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-bold border border-red-300">Belum Ada</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="font-extrabold text-[#123F35]">{{ $course->active_students_count }}</span>
                                        <span class="text-slate-400">/</span>
                                        <span class="font-extrabold text-[#28745B]">{{ $course->completed_students_count }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @php $progress = round($course->avg_progress ?? 0); @endphp
                                        <div class="flex items-center space-x-2">
                                            <div class="w-24 bg-slate-200 h-1.5 rounded-full overflow-hidden border border-slate-300/60">
                                                <div class="bg-[#28745B] h-full rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-[10px] font-extrabold text-[#28745B]">{{ $progress }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-xs text-slate-400 italic">
                                        Belum ada data pelatihan yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- KANAN (1 Kolom): Pengguna Baru, Pelatihan Tanpa Pengajar & Aktivitas Terbaru -->
        <div class="space-y-6">

            <!-- 1. Pengguna Baru Terdaftar -->
            <div class="bg-white rounded-2xl border border-slate-300 p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-300 pb-2.5">
                    <h2 class="text-sm font-bold text-[#123F35]">Pengguna Baru Terdaftar</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-[11px] font-bold text-[#28745B] hover:text-[#123F35]">Kelola &rarr;</a>
                </div>

                <div class="space-y-3">
                    @forelse($recentUsers as $recentUser)
                        <a href="{{ route('admin.users.edit', $recentUser) }}" class="p-3 bg-[#F7F5EE]/50 hover:bg-[#F7F5EE] rounded-xl border border-slate-300 flex items-center justify-between gap-3 transition group">
                            <div class="flex items-center space-x-3 min-w-0">
                                <img src="{{ $recentUser->profile_photo_url }}" alt="{{ $recentUser->name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0 border border-[#28745B]/40">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-[#123F35] group-hover:text-[#28745B] truncate transition">
                                        {{ $recentUser->name }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 truncate">{{ $recentUser->email }}</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase flex-shrink-0 border {{ $recentUser->role === 'admin' ? 'bg-red-100 text-red-700 border-red-300' : ($recentUser->role === 'teacher' ? 'bg-[#E7C982]/40 text-[#123F35] border-[#E7C982]' : 'bg-[#28745B]/15 text-[#28745B] border-[#28745B]/30') }}">
                                {{ $recentUser->role }}
                            </span>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada pengguna baru terdaftar.</p>
                    @endforelse
                </div>
            </div>

            <!-- 2. Pelatihan Tanpa Pengajar -->
            @if($coursesWithoutInstructor->isNotEmpty())
                <div class="bg-white rounded-2xl border border-red-300 p-6 shadow-2xs space-y-3">
                    <h2 class="text-sm font-bold text-red-800 border-b border-red-200 pb-2">Pelatihan Tanpa Pengajar</h2>
                    <div class="space-y-2.5">
                        @foreach($coursesWithoutInstructor as $emptyCourse)
                           <!-- route('admin.courses.edit', $emptyCourse) -->
                            <a href="" class="p-3 bg-red-50/60 hover:bg-red-50 rounded-xl border border-red-200 flex items-center justify-between gap-2 transition">
                                <span class="text-xs font-bold text-slate-800 truncate">{{ $emptyCourse->title }}</span>
                                <span class="text-[10px] font-extrabold text-red-700 flex-shrink-0">Atur &rarr;</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 3. Aktivitas Pengumpulan Tugas & Kuis Terbaru -->
            <div class="bg-white rounded-2xl border border-slate-300 p-6 shadow-2xs space-y-4">
                <h2 class="text-sm font-bold text-[#123F35] border-b border-slate-300 pb-2.5">Aktivitas Akademik Terbaru</h2>

                <div class="space-y-3">
                    @foreach($recentSubmissions->take(3) as $sub)
                        <div class="p-3 bg-[#F7F5EE]/50 rounded-xl border border-slate-300 space-y-1">
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="px-1.5 py-0.5 rounded font-bold bg-[#28745B]/15 text-[#123F35] border border-[#28745B]/30">TUGAS</span>
                                <span class="text-slate-500">{{ \Carbon\Carbon::parse($sub->submitted_at)->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs font-bold text-[#123F35] truncate">{{ $sub->enrollment->user->name ?? 'Peserta' }}</p>
                            <p class="text-[11px] text-slate-600 truncate">{{ $sub->assignment->title ?? '-' }}</p>
                        </div>
                    @endforeach

                    @foreach($recentQuizAttempts->take(3) as $attempt)
                        <div class="p-3 bg-[#F7F5EE]/50 rounded-xl border border-slate-300 space-y-1">
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="px-1.5 py-0.5 rounded font-bold bg-[#E7C982]/40 text-[#123F35] border border-[#E7C982]">KUIS</span>
                                <span class="font-extrabold text-[#28745B]">Skor: {{ number_format($attempt->score ?? 0, 0) }}</span>
                            </div>
                            <p class="text-xs font-bold text-[#123F35] truncate">{{ $attempt->enrollment->user->name ?? 'Peserta' }}</p>
                            <p class="text-[11px] text-slate-600 truncate">{{ $attempt->quiz->assignment->title ?? 'Kuis Pelatihan' }}</p>
                        </div>
                    @endforeach

                    @if($recentSubmissions->isEmpty() && $recentQuizAttempts->isEmpty())
                        <p class="text-xs text-slate-400 italic">Belum ada aktivitas pengerjaan tugas atau kuis terbaru.</p>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection