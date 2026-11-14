@extends('layouts.app')

@section('title', 'Dashboard Pengajar - Ekosistem Halal')
@section('header_title', 'Dashboard Pengajar')

@section('content')
<div class="space-y-6">

    <!-- Welcome Card Banner (Deep Forest Green dengan Aksen Muted Gold) -->
    <div class="relative overflow-hidden rounded-2xl bg-[#123F35] p-6 sm:p-8 text-white shadow-md border border-[#E7C982]/40">
        <!-- Background Radial Gradient & Glow Effect -->
        <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
        <div class="absolute -right-10 -bottom-10 -z-0 h-48 w-48 rounded-full border border-[#E7C982]/20 blur-xl"></div>

        <div class="relative z-10 space-y-2 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wider bg-[#E7C982]/20 text-[#E7C982] border border-[#E7C982]/40 uppercase">
                Panel Pengajar
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Selamat Datang, {{ Auth::user()->name }}</h1>
            <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
                Kelola materi pelatihan Sistem Jaminan Halal, evaluasi pengumpulan tugas siswa, dan pantau progres pengerjaan kuis siswa.
            </p>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Stats 1: Total Pelatihan Dikelola -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 shadow-2xs flex items-center justify-between hover:border-[#28745B]/60 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#123F35]/70">Total Pelatihan Dikelola</span>
                <div class="text-2xl font-black text-[#123F35]">{{ $myCourses->count() }} Kelas</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold border border-[#28745B]/30">
                <svg class="w-6 h-6 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>

        <!-- Stats 2: Tugas Belum Dinilai -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 shadow-2xs flex items-center justify-between hover:border-[#28745B]/60 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#123F35]/70">Tugas Belum Dinilai</span>
                <div class="text-2xl font-black {{ $pendingGradingCount > 0 ? 'text-amber-600' : 'text-[#28745B]' }}">
                    {{ $pendingGradingCount }} Submission
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold border border-[#28745B]/30">
                <svg class="w-6 h-6 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
        </div>

        <!-- Stats 3: Total Siswa Aktif -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 shadow-2xs flex items-center justify-between hover:border-[#28745B]/60 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#123F35]/70">Total Siswa Aktif</span>
                <div class="text-2xl font-black text-[#28745B]">{{ $totalActiveStudents }} Siswa</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold border border-[#28745B]/30">
                <svg class="w-6 h-6 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Main Layout 2 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KIRI (2 Kolom): Daftar Submission yang Membutuhkan Penilaian (Action Needed) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-300 p-6 shadow-2xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-300 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-[#123F35] flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tugas Butuh Penilaian (Action Needed)</span>
                        </h2>
                        <p class="text-xs text-slate-500">Daftar pengumpulan tugas siswa yang belum diperiksa.</p>
                    </div>
                    <span class="px-3 py-1 bg-[#F7F5EE] text-[#123F35] rounded-xl text-xs font-bold border border-[#28745B]/30 self-start sm:self-auto">
                        {{ $pendingGradingCount }} Meminta Penilaian
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($pendingSubmissions as $submission)
                        <div class="p-4 rounded-xl border border-slate-300 bg-[#F7F5EE]/40 hover:bg-[#F7F5EE]/80 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-[#E7C982]/30 text-[#123F35] border border-[#E7C982]">
                                        Attempt #{{ $submission->attempt_number }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-semibold truncate">
                                        {{ $submission->assignment->course->title ?? '-' }}
                                    </span>
                                </div>
                                <h3 class="text-xs font-bold text-[#123F35] truncate">{{ $submission->assignment->title }}</h3>
                                
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] text-slate-600 pt-0.5">
                                    <span>Siswa: <strong class="text-[#123F35]">{{ $submission->enrollment->user->name ?? 'Siswa' }}</strong></span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-500">Dikumpulkan: {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}</span>
                                </div>
                            </div>

                            <a href="{{ route('teacher.submissions.grade', $submission->id) }}" class="px-4 py-2 bg-[#123F35] hover:bg-[#28745B] text-white rounded-xl text-xs font-bold transition shadow-xs flex-shrink-0 text-center">
                                Periksa & Beri Nilai →
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-500 bg-[#F7F5EE]/40 rounded-xl border border-dashed border-slate-300">
                            Tidak ada tugas siswa untuk dinilai saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- KANAN (1 Kolom): Daftar Kelas Saya & Aktivitas Kuis Terbaru -->
        <div class="space-y-6">

            <!-- Card Akses Cepat Pelatihan Dikelola -->
            <div class="bg-white rounded-2xl border border-slate-300 p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-300 pb-2.5">
                    <h2 class="text-sm font-bold text-[#123F35]">Pelatihan Dikelola</h2>
                    <a href="{{ route('teacher.courses.create') }}" class="text-[11px] text-[#28745B] hover:text-[#123F35] font-extrabold hover:underline">+ Buat Kelas</a>
                </div>

                <div class="space-y-3">
                    @forelse($myCourses as $course)
                        <div class="p-3 bg-[#F7F5EE]/50 hover:bg-[#F7F5EE] rounded-xl border border-slate-300 flex items-center justify-between gap-3 transition">
                            <div class="flex items-center space-x-3 min-w-0">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 border border-slate-300">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-[#28745B]/15 text-[#123F35] flex items-center justify-center font-bold text-xs flex-shrink-0 border border-[#28745B]/30">
                                        {{ substr($course->code, 0, 3) }}
                                    </div>
                                @endif
                                <div class="min-w-0 space-y-0.5">
                                    <div class="text-xs font-bold text-[#123F35] truncate">{{ $course->title }}</div>
                                    <div class="text-[10px] text-slate-500 font-medium">
                                        {{ $course->enrollments->where('status', 'active')->count() }} Siswa Aktif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-1.5 flex-shrink-0">
                                <a href="{{ route('teacher.courses.show', $course->id) }}" class="p-1.5 bg-white border border-slate-300 hover:bg-slate-100 rounded-lg text-[#123F35] transition shadow-2xs" title="Kelola Course">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('teacher.courses.gradebook.show', $course->id) }}" class="p-1.5 bg-[#28745B] hover:bg-[#123F35] text-white border border-[#28745B] rounded-lg transition shadow-2xs" title="Rekap Nilai">
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
            <div class="bg-white rounded-2xl border border-slate-300 p-6 shadow-2xs space-y-4">
                <h2 class="text-sm font-bold text-[#123F35] border-b border-slate-300 pb-2.5">Pengerjaan Kuis Terbaru</h2>

                <div class="space-y-3">
                    @forelse($recentQuizAttempts as $attempt)
                        <div class="p-3 bg-[#F7F5EE]/50 rounded-xl border border-slate-300 flex items-center justify-between gap-2">
                            <div class="min-w-0 space-y-0.5">
                                <div class="text-xs font-bold text-[#123F35] truncate">{{ $attempt->enrollment->user->name ?? 'Siswa' }}</div>
                                <div class="text-[10px] text-slate-500 truncate">{{ $attempt->quiz->assignment->title ?? 'Kuis' }}</div>
                            </div>
                            <div class="text-right flex-shrink-0 space-y-0.5">
                                <span class="inline-block text-[10px] font-extrabold px-2 py-0.5 rounded border {{ $attempt->score >= ($attempt->quiz->assignment->passing_score ?? 0) ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-red-100 text-red-800 border-red-300' }}">
                                    Nilai: {{ number_format($attempt->score, 1) }}
                                </span>
                                <div class="text-[9px] text-slate-500 font-medium">Attempt #{{ $attempt->attempt_number }}</div>
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