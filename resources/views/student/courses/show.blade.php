@extends('layouts.app')

@section('title', $course->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Pelatihan')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="space-y-5 sm:space-y-6">

    <!-- Tombol Navigasi Atas (Kembali & Gradebook) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('student.courses.index') }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Katalog Pelatihan</span>
        </a>

        <!-- Tombol Gradebook / Rekap Nilai Student -->
        <a href="{{ route('student.courses.gradebook', $course->id) }}" class="inline-flex items-center justify-center space-x-2 text-xs font-bold bg-[#28745B] hover:bg-[#123F35] text-white border border-[#E7C982]/60 px-4 py-2 rounded-xl transition shadow-2xs">
            <svg class="w-4 h-4 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Rekap Nilai Saya</span>
        </a>
    </div>

    <!-- NOTIFIKASI KELULUSAN & MASA BERLAKU AKSES (JIKA COMPLETED) -->
    @if($enrollment->status === 'completed')
        <div class="p-4 sm:p-5 bg-gradient-to-r from-[#F7F5EE] via-emerald-50 to-emerald-100/60 border border-[#28745B]/40 rounded-2xl shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start space-x-3 sm:space-x-3.5">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#28745B] text-white flex items-center justify-center flex-shrink-0 shadow-2xs mt-0.5">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-xs sm:text-sm font-bold text-[#123F35]">Selamat! Anda telah menyelesaikan pelatihan ini</h3>
                    <p class="text-[11px] sm:text-xs text-slate-600 leading-relaxed">
                        @if($enrollment->access_expires_at)
                            Akses materi pelatihan ini berlaku sampai <span class="font-bold text-[#123F35]">{{ \Carbon\Carbon::parse($enrollment->access_expires_at)->format('d F Y') }}</span>.
                        @else
                            Akses materi pelatihan ini berlaku tanpa batas waktu.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Tombol Lihat/Unduh Sertifikat jika tersedia -->
            @if($enrollment->certificate)
                <a href="{{ route('student.certificates.show', $enrollment->certificate->id) }}" class="w-full md:w-auto inline-flex items-center justify-center space-x-2 bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-2xs flex-shrink-0">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 00.1.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Unduh Sertifikat</span>
                </a>
            @endif
        </div>
    @endif

    <!-- TOP CARD -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-5 sm:p-8 rounded-2xl border border-[#28745B]/40 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-6 text-white relative overflow-hidden">
        <!-- Kiri: Thumbnail & Detail Pelatihan -->
        <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-5 flex-1 min-w-0 w-full relative z-10">
            @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="{{ $course->title }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border border-white/30 flex-shrink-0 shadow-sm">
            @else
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/10 text-[#E7C982] flex items-center justify-center font-bold text-xl sm:text-2xl border border-white/30 flex-shrink-0 backdrop-blur-xs">
                    {{ substr($course->code, 0, 3) }}
                </div>
            @endif

            <div class="space-y-2 min-w-0 flex-1 w-full">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-white border border-white/20 uppercase backdrop-blur-xs">
                        {{ $course->category_label }}
                    </span>

                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-white border border-white/20 uppercase backdrop-blur-xs">
                        {{ $course->code }}
                    </span>

                    @if($enrollment->status === 'completed')
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-[#E7C982] text-[#123F35] uppercase">
                            ✓ Selesai (Lulus)
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-[#28745B] text-white border border-white/20 uppercase">
                            Terdaftar
                        </span>
                    @endif
                </div>
                
                <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug break-words sm:truncate">{{ $course->title }}</h1>
                <p class="text-xs text-emerald-100/80 max-w-3xl leading-relaxed line-clamp-3 sm:line-clamp-2">{{ $course->description ?? 'Tidak ada deskripsi.' }}</p>
                
                <div class="pt-2 flex flex-wrap items-center gap-1.5 text-xs text-emerald-100/90">
                    <div class="flex items-center space-x-1 flex-shrink-0">
                        <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="font-bold text-white">Tim Pengajar:</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="px-2 py-0.5 bg-white/10 border border-white/20 rounded-md text-[11px] font-semibold text-white">
                            {{ $course->creator->name ?? 'Instruktur Utama' }}
                        </span>
                        @if($course->instructors && $course->instructors->count() > 0)
                            @foreach($course->instructors as $instructor)
                                @if($instructor->id !== $course->created_by)
                                    <span class="px-2 py-0.5 bg-white/10 border border-white/20 rounded-md text-[11px] font-semibold text-white">
                                        {{ $instructor->name }}
                                    </span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @php
            $progressPercent = $enrollment->progress->progress_percentage 
                ?? $enrollment->progress_percentage 
                ?? ($enrollment->status === 'completed' ? 100 : 0);
            
            $progress = round($progressPercent);
        @endphp

        <div class="w-full md:w-64 bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl space-y-2 flex-shrink-0 relative z-10">
            <div class="flex items-center justify-between text-xs font-bold text-white">
                <span>Progres Pelatihan</span>
                <span class="text-[#E7C982]">{{ $progress }}%</span>
            </div>
            
            <div class="w-full h-3 bg-white/20 rounded-full overflow-hidden p-0.5">
                <div class="h-full rounded-full transition-all duration-500 bg-[#E7C982]" 
                     style="width: {{ $progress }}%;"></div>
            </div>
        </div>
    </div>

    <!-- Modul Pembelajaran / Pertemuan -->
    @php
        $allMeetingIds = $course->meetings->pluck('id')->toArray();
    @endphp

    <div x-data="{ 
            allIds: {{ json_encode($allMeetingIds) }},
            openMeetings: {{ json_encode($allMeetingIds) }},
            toggleMeeting(id) {
                if (this.openMeetings.includes(id)) {
                    this.openMeetings = this.openMeetings.filter(m => m !== id);
                } else {
                    this.openMeetings.push(id);
                }
            },
            toggleAll() {
                if (this.openMeetings.length === this.allIds.length) {
                    this.openMeetings = [];
                } else {
                    this.openMeetings = [...this.allIds];
                }
            }
         }" class="space-y-4">

        <!-- Bar Kontrol Pertemuan & Tombol Toggle Expand/Collapse -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-300 pb-3">
            <div>
                <h2 class="text-base font-bold text-[#123F35]">Modul Pembelajaran & Pertemuan</h2>
                <p class="text-xs text-slate-500">Akses seluruh materi, tugas, dan kuis sesuai jadwal pertemuan.</p>
            </div>

            <div class="flex items-center justify-end">
                <button type="button" @click="toggleAll()" class="w-full sm:w-auto px-3.5 py-1.5 bg-[#28745B] hover:bg-[#123F35] text-white text-xs font-semibold rounded-xl border border-slate-300 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <template x-if="openMeetings.length === allIds.length">
                        <span class="flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            <span>Collapse Semua Pertemuan</span>
                        </span>
                    </template>
                    <template x-if="openMeetings.length !== allIds.length">
                        <span class="flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            <span>Expand Semua Pertemuan</span>
                        </span>
                    </template>
                </button>
            </div>
        </div>

        @forelse($course->meetings->sortBy('meeting_number') as $meeting)
            @php
                $now = now();
                $isMeetingAccessible = true;
                $meetingTimeError = null;

                if ($meeting->available_from && $now->lessThan($meeting->available_from)) {
                    $isMeetingAccessible = false;
                    $meetingTimeError = 'Dapat diakses mulai ' . \Carbon\Carbon::parse($meeting->available_from)->format('d M Y, H:i');
                } elseif ($meeting->available_until && $now->greaterThan($meeting->available_until)) {
                    $isMeetingAccessible = false;
                    $meetingTimeError = 'Akses pertemuan telah ditutup pada ' . \Carbon\Carbon::parse($meeting->available_until)->format('d M Y, H:i');
                }

                $activeSubmissions = $meeting->assignments
                    ->where('type', 'submission')
                    ->whereNotIn('status', ['closed', 'draft']);

                $activeQuizzes = $meeting->assignments
                    ->where('type', 'quiz')
                    ->whereNotIn('status', ['closed', 'draft']);
            @endphp

            <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
                <!-- Header Pertemuan -->
                <div @click="toggleMeeting({{ $meeting->id }})" class="p-4 bg-[#28745B] hover:bg-[#123F35] flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 cursor-pointer transition text-white">
                    
                    <!-- Kiri: Nomor, Judul & Deskripsi -->
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        <span class="w-8 h-8 rounded-xl bg-white/20 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 border border-white/30 shadow-2xs mt-0.5">
                            {{ $meeting->meeting_number }}
                        </span>
                        <div class="min-w-0 space-y-1">
                            <div class="flex items-center space-x-2">
                                <h3 class="text-sm font-bold text-white truncate">{{ $meeting->title }}</h3>
                                @if(!$isMeetingAccessible)
                                    <span class="px-2 py-0.5 bg-red-500/20 text-red-200 border border-red-400/50 text-[10px] font-bold rounded-md flex items-center space-x-1 flex-shrink-0 backdrop-blur-xs">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span>Terkunci</span>
                                    </span>
                                @endif
                            </div>

                            @if($meeting->description)
                                <p class="text-xs text-emerald-100/80 line-clamp-2 leading-relaxed">{{ $meeting->description }}</p>
                            @else
                                <p class="text-xs text-emerald-100/60 italic">Belum ada pengantar/deskripsi pertemuan.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Kanan: Informasi Jadwal Akses & Tombol Arrow Toggle -->
                    <div class="flex items-center justify-between md:justify-end gap-3 flex-shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-white/20">
                        
                        <!-- Badge Informasi Tanggal Buka dan Tutup -->
                        <div class="text-[11px] text-white bg-white/10 px-2.5 sm:px-3 py-1.5 rounded-xl border border-white/30 flex items-center space-x-1.5 shadow-2xs backdrop-blur-xs">
                            <svg class="w-3.5 h-3.5 text-[#E7C982] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            @if($meeting->available_from || $meeting->available_until)
                                <span class="truncate max-w-[200px] sm:max-w-none">
                                    Jadwal: 
                                    <strong class="text-white">{{ $meeting->available_from ? \Carbon\Carbon::parse($meeting->available_from)->format('d M H:i') : 'Sekarang' }}</strong>
                                    s/d 
                                    <strong class="text-white">{{ $meeting->available_until ? \Carbon\Carbon::parse($meeting->available_until)->format('d M H:i') : 'Tanpa Batas' }}</strong>
                                </span>
                            @else
                                <span class="text-[#E7C982] font-semibold">Akses Tanpa Batas Waktu</span>
                            @endif
                        </div>

                        <!-- Chevron Arrow Toggle -->
                        <button type="button" class="w-7 h-7 rounded-lg border border-white/30 bg-white/10 flex items-center justify-center text-white shadow-2xs flex-shrink-0 backdrop-blur-xs">
                            <svg class="w-4 h-4 transform transition-transform" :class="openMeetings.includes({{ $meeting->id }}) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body / Konten Pertemuan -->
                <div x-show="openMeetings.includes({{ $meeting->id }})" x-collapse class="p-4 sm:p-5">
                    @if(!$isMeetingAccessible)
                        <div class="p-5 sm:p-6 bg-red-50/50 border border-red-300 rounded-xl text-center space-y-1">
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-2 border border-red-200">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <h4 class="text-xs font-bold text-red-800">Modul Pertemuan Belum Bisa Diakses</h4>
                            <p class="text-xs text-red-600">{{ $meetingTimeError }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">

                            <!-- SECTION 1: MATERI PEMBELAJARAN -->
                            <section class="rounded-2xl border border-slate-300 bg-white min-w-0 flex flex-col justify-between overflow-hidden shadow-2xs">
                                <div>
                                    <!-- Header Cream untuk Seksi Materi -->
                                    <div class="bg-[#d1e4da] p-3 px-4 border-b border-slate-300">
                                        <div class="flex items-center gap-2 text-xs font-bold text-[#123F35] uppercase tracking-wider">
                                            <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <span>Materi ({{ $meeting->materials->count() }})</span>
                                        </div>
                                    </div>

                                    <div class="p-3.5">
                                        @if($meeting->materials && $meeting->materials->count() > 0)
                                            <div class="space-y-2.5">
                                                @foreach($meeting->materials as $material)
                                                    <div class="p-3 bg-[#F7F5EE]/40 rounded-xl border border-slate-300 space-y-2">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <div class="flex items-center gap-2 min-w-0">
                                                                <div class="w-6 h-6 rounded-lg bg-[#28745B]/10 text-[#28745B] flex items-center justify-center flex-shrink-0 border border-[#28745B]/20">
                                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 00-2-2V5a2 2 0 012-2h5.586a1 1 0 00.1.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                    </svg>
                                                                </div>
                                                                <span class="text-xs text-[#123F35] font-bold truncate">{{ $material->title }}</span>
                                                            </div>

                                                            <a href="{{ route('student.materials.show', $material->id) }}" class="text-[11px] text-[#28745B] font-bold hover:underline flex-shrink-0">Pelajari →</a>
                                                        </div>

                                                        <div class="flex flex-wrap items-center gap-1.5 pt-1.5 border-t border-slate-300">
                                                            <span class="px-2 py-0.5 bg-[#28745B]/10 text-[#123F35] border border-[#28745B]/40 text-[10px] font-bold rounded-md shadow-2xs">
                                                                Tersedia
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-[11px] text-slate-400 italic py-2">Belum ada materi dipublikasikan.</p>
                                        @endif
                                    </div>
                                </div>
                            </section>

                            <!-- SECTION 2: TUGAS UPLOAD / SUBMISSION -->
                            <section class="rounded-2xl border border-slate-300 bg-white min-w-0 flex flex-col justify-between overflow-hidden shadow-2xs">
                                <div>
                                    <!-- Header Cream untuk Seksi Tugas -->
                                    <div class="bg-[#d1e4da] p-3 px-4 border-b border-slate-300">
                                        <div class="flex items-center gap-2 text-xs font-bold text-[#123F35] uppercase tracking-wider">
                                            <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                            </svg>
                                            <span>Tugas ({{ $activeSubmissions->count() }})</span>
                                        </div>
                                    </div>

                                    <div class="p-3.5">
                                        @if($activeSubmissions->count() > 0)
                                            <div class="space-y-2.5">
                                                @foreach($activeSubmissions as $assignment)
                                                    @php
                                                        $taskTimeStatus = ''; 
                                                        
                                                        if ($assignment->available_from && $now->lessThan($assignment->available_from)) {
                                                            $taskTimeStatus = '<span class="px-2 py-0.5 bg-amber-100 text-amber-800 border border-amber-300 text-[9px] font-bold rounded-md">Buka: ' . \Carbon\Carbon::parse($assignment->available_from)->format('d M H:i') . '</span>';
                                                        } elseif ($assignment->available_until) {
                                                            if ($now->greaterThan($assignment->available_until)) {
                                                                $taskTimeStatus = '<span class="px-2 py-0.5 bg-red-100 text-red-700 border border-red-300 text-[9px] font-bold rounded-md">Tenggat Lewat: ' . \Carbon\Carbon::parse($assignment->available_until)->format('d M H:i') . '</span>';
                                                            } else {
                                                                $taskTimeStatus = '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-300 text-[9px] font-bold rounded-md">Tenggat: ' . \Carbon\Carbon::parse($assignment->available_until)->format('d M H:i') . '</span>';
                                                            }
                                                        }

                                                        $userSubmissions = \App\Models\AssignmentSubmission::where('assignment_id', $assignment->id)
                                                            ->where('enrollment_id', $enrollment->id)
                                                            ->get();

                                                        $bestScore = $userSubmissions->where('status', 'graded')->max('score');
                                                        $isFinished = !is_null($bestScore) && $bestScore >= $assignment->passing_score;
                                                    @endphp

                                                    <div class="p-3 bg-[#F7F5EE]/40 rounded-xl border border-slate-300 space-y-2">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <div class="flex items-center gap-2 min-w-0">
                                                                <div class="w-6 h-6 rounded-lg bg-[#28745B]/10 text-[#28745B] flex items-center justify-center flex-shrink-0 border border-[#28745B]/20">
                                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                    </svg>
                                                                </div>
                                                                <span class="text-xs text-[#123F35] font-bold truncate">{{ $assignment->title }}</span>
                                                            </div>

                                                            <a href="{{ route('student.submissions.show', $assignment->id) }}" class="text-[11px] text-[#28745B] font-bold hover:underline flex-shrink-0">Kerjakan →</a>
                                                        </div>

                                                        <div class="flex flex-wrap items-center gap-1.5 pt-1.5 border-t border-slate-300">
                                                            {!! $taskTimeStatus !!}

                                                            @if($isFinished)
                                                                <span class="px-2 py-0.5 bg-[#28745B]/15 text-[#28745B] border border-[#28745B]/40 text-[9px] font-extrabold rounded-md shadow-2xs">Selesai (Skor: {{ number_format($bestScore, 0) }})</span>
                                                            @elseif($userSubmissions->isNotEmpty())
                                                                <span class="px-2 py-0.5 bg-[#E7C982]/40 text-[#123F35] border border-[#E7C982] text-[9px] font-extrabold rounded-md shadow-2xs">Menunggu Penilaian</span>
                                                            @else
                                                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 border border-slate-300 text-[9px] font-bold rounded-md shadow-2xs">Belum Selesai</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-[11px] text-slate-400 italic py-2">Belum ada tugas dipublikasikan.</p>
                                        @endif
                                    </div>
                                </div>
                            </section>

                            <!-- SECTION 3: KUIS -->
                            <section class="rounded-2xl border border-slate-300 bg-white min-w-0 flex flex-col justify-between overflow-hidden shadow-2xs">
                                <div>
                                    <!-- Header Cream untuk Seksi Kuis -->
                                    <div class="bg-[#d1e4da] p-3 px-4 border-b border-slate-300">
                                        <div class="flex items-center gap-2 text-xs font-bold text-[#123F35] uppercase tracking-wider">
                                            <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Kuis ({{ $activeQuizzes->count() }})</span>
                                        </div>
                                    </div>

                                    <div class="p-3.5">
                                        @if($activeQuizzes->count() > 0)
                                            <div class="space-y-2.5">
                                                @foreach($activeQuizzes as $quizAssignment)
                                                    @php
                                                        $quizTimeStatus = '';

                                                        if ($quizAssignment->available_from && $now->lessThan($quizAssignment->available_from)) {
                                                            $quizTimeStatus = '<span class="px-2 py-0.5 bg-amber-100 text-amber-800 border border-amber-300 text-[9px] font-bold rounded-md">Buka: ' . \Carbon\Carbon::parse($quizAssignment->available_from)->format('d M H:i') . '</span>';
                                                        } elseif ($quizAssignment->available_until) {
                                                            if ($now->greaterThan($quizAssignment->available_until)) {
                                                                $quizTimeStatus = '<span class="px-2 py-0.5 bg-red-100 text-red-700 border border-red-300 text-[9px] font-bold rounded-md">Tenggat Lewat: ' . \Carbon\Carbon::parse($quizAssignment->available_until)->format('d M H:i') . '</span>';
                                                            } else {
                                                                $quizTimeStatus = '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-300 text-[9px] font-bold rounded-md">Tenggat: ' . \Carbon\Carbon::parse($quizAssignment->available_until)->format('d M H:i') . '</span>';
                                                            }
                                                        }

                                                        $quizAttempts = \App\Models\QuizAttempt::whereHas('quiz', function($q) use ($quizAssignment) {
                                                                $q->where('assignment_id', $quizAssignment->id);
                                                            })
                                                            ->where('enrollment_id', $enrollment->id)
                                                            ->whereIn('status', ['submitted', 'expired'])
                                                            ->get();

                                                        $bestQuizScore = $quizAttempts->max('score');
                                                        $isQuizFinished = !is_null($bestQuizScore) && $bestQuizScore >= $quizAssignment->passing_score;
                                                    @endphp

                                                    <div class="p-3 bg-[#F7F5EE]/40 rounded-xl border border-slate-300 space-y-2">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <div class="flex items-center gap-2 min-w-0">
                                                                <div class="w-6 h-6 rounded-lg bg-[#28745B]/10 text-[#28745B] flex items-center justify-center flex-shrink-0 border border-[#28745B]/20">
                                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                    </svg>
                                                                </div>
                                                                <span class="text-xs text-[#123F35] font-bold truncate">{{ $quizAssignment->title }}</span>
                                                            </div>

                                                            <a href="{{ route('student.quizzes.show', $quizAssignment->id) }}" class="text-[11px] text-[#28745B] font-bold hover:underline flex-shrink-0">Mulai →</a>
                                                        </div>

                                                        <div class="flex flex-wrap items-center gap-1.5 pt-1.5 border-t border-slate-300">
                                                            {!! $quizTimeStatus !!}

                                                            @if($isQuizFinished)
                                                                <span class="px-2 py-0.5 bg-[#28745B]/15 text-[#28745B] border border-[#28745B]/40 text-[9px] font-extrabold rounded-md shadow-2xs">Selesai (Skor: {{ number_format($bestQuizScore, 0) }})</span>
                                                            @elseif($quizAttempts->isNotEmpty())
                                                                <span class="px-2 py-0.5 bg-red-100 text-red-700 border border-red-300 text-[9px] font-extrabold rounded-md shadow-2xs">Belum Lulus (Skor: {{ number_format($bestQuizScore, 0) }})</span>
                                                            @else
                                                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 border border-slate-300 text-[9px] font-bold rounded-md shadow-2xs">Belum Selesai</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-[11px] text-slate-400 italic py-2">Belum ada kuis untuk pertemuan ini.</p>
                                        @endif
                                    </div>
                                </div>
                            </section>

                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-300 p-8 text-center text-xs text-slate-400">
                Belum ada pertemuan yang dibuat oleh instruktur untuk kelas ini.
            </div>
        @endforelse
    </div>
</div>
@endsection