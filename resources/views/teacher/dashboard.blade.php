@extends('layouts.app')

@section('title', 'Dashboard Pengajar - Ekosistem Halal')
@section('header_title', 'Dashboard Pengajar')

@section('content')
<div class="space-y-6">

    <!-- Welcome Card Banner -->
    <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-amber-700 text-white rounded-2xl p-6 sm:p-8 shadow-md relative overflow-hidden">
        <div class="relative z-10 space-y-2 max-w-2xl">
            <span class="px-3 py-1 rounded-full text-[10px] font-bold tracking-wider bg-white/20 text-white border border-white/30 uppercase">
                Panel Pengajar
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ Auth::user()->name }} 👨‍🏫</h1>
            <p class="text-xs sm:text-sm text-amber-100 leading-relaxed">
                Kelola materi pelatihan Sistem Jaminan Halal, evaluasi pengumpulan tugas siswa, dan pantau progres pengerjaan kuis siswa.
            </p>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Course -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-bold uppercase text-slate-400">Total Pelatihan Dikelola</span>
                <div class="text-2xl font-black text-slate-800">{{ $myCourses->count() }} Kelas</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>

        <!-- Tugas Butuh Penilaian -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-bold uppercase text-slate-400">Tugas Belum Dinilai</span>
                <div class="text-2xl font-black {{ $pendingGradingCount > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                    {{ $pendingGradingCount }} Submission
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
        </div>

        <!-- Total Siswa Aktif -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] font-bold uppercase text-slate-400">Total Siswa Aktif</span>
                <div class="text-2xl font-black text-blue-600">{{ $totalActiveStudents }} Siswa</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Main Layout 2 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KIRI (2 Kolom): Daftar Submission yang Membutuhkan Penilaian (Action Needed) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tugas Butuh Penilaian (Action Needed)</span>
                        </h2>
                        <p class="text-xs text-slate-400">Daftar pengumpulan tugas siswa yang belum diperiksa.</p>
                    </div>
                    <span class="px-2.5 py-1 bg-red-100 text-red-800 rounded-lg text-xs font-bold">
                        {{ $pendingGradingCount }} Meminta Penilaian
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($pendingSubmissions as $submission)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/80 hover:bg-slate-100/60 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-blue-100 text-blue-800">
                                        Attempt #{{ $submission->attempt_number }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-semibold truncate">
                                        {{ $submission->assignment->course->title ?? '-' }}
                                    </span>
                                </div>
                                <h3 class="text-xs font-bold text-slate-800 truncate">{{ $submission->assignment->title }}</h3>
                                
                                <div class="flex items-center space-x-2 text-[11px] text-slate-600 pt-0.5">
                                    <span>Siswa: <strong class="text-slate-800">{{ $submission->enrollment->user->name ?? 'Siswa' }}</strong></span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-400">Dikumpulkan: {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}</span>
                                </div>
                            </div>

                            <a href="{{ route('teacher.submissions.grade', $submission->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition shadow-sm flex-shrink-0 text-center">
                                Periksa & Beri Nilai →
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            Tidak ada tugas siswa untuk dinilai saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- KANAN (1 Kolom): Daftar Kelas Saya & Aktivitas Kuis Terbaru -->
        <div class="space-y-6">

            <!-- Card Akses Cepat Pelatihan Dikelola -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h2 class="text-sm font-bold text-slate-800">Pelatihan Dikelola</h2>
                    <a href="{{ route('teacher.courses.create') }}" class="text-[11px] text-amber-600 font-bold hover:underline">+ Buat Kelas</a>
                </div>

                <div class="space-y-3">
                    @forelse($myCourses as $course)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ substr($course->code, 0, 3) }}
                                    </div>
                                @endif
                                <div class="min-w-0 space-y-0.5">
                                    <div class="text-xs font-bold text-slate-800 truncate">{{ $course->title }}</div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $course->enrollments->where('status', 'active')->count() }} Siswa Aktif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-1 flex-shrink-0">
                                <a href="{{ route('teacher.courses.show', $course->id) }}" class="p-1.5 bg-white border border-slate-200 hover:bg-slate-100 rounded-lg text-slate-600 transition" title="Kelola Course">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('teacher.courses.gradebook.show', $course->id) }}" class="p-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition" title="Rekap Nilai">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada kelas yang Anda buat.</p>
                    @endforelse
                </div>
            </div>

            <!-- Card Pengerjaan Kuis Terbaru Siswa -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <h2 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2.5">Pengerjaan Kuis Terbaru</h2>

                <div class="space-y-3">
                    @forelse($recentQuizAttempts as $attempt)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between gap-2">
                            <div class="min-w-0 space-y-0.5">
                                <div class="text-xs font-bold text-slate-800 truncate">{{ $attempt->enrollment->user->name ?? 'Siswa' }}</div>
                                <div class="text-[10px] text-slate-400 truncate">{{ $attempt->quiz->assignment->title ?? 'Kuis' }}</div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="text-xs font-extrabold {{ $attempt->score >= ($attempt->quiz->assignment->passing_score ?? 0) ? 'text-emerald-600' : 'text-red-600' }}">
                                    Nilai: {{ number_format($attempt->score, 1) }}
                                </div>
                                <div class="text-[9px] text-slate-400">Attempt #{{ $attempt->attempt_number }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada aktivitas pengerjaan kuis siswa.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection