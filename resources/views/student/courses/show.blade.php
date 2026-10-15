@extends('layouts.app')

@section('title', $course->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Pelatihan')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="space-y-6">

    <!-- Tombol Kembali ke Katalog (Di atas Top Card) -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.courses.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-amber-600 transition bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Katalog Pelatihan</span>
        </a>

        <!-- Tombol Gradebook / Rekap Nilai Student -->
        <a href="javascript:void(0)" onclick="alert('Fitur Rekap Nilai Student segera hadir!')" class="inline-flex items-center space-x-2 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-4 py-2 rounded-xl transition shadow-sm">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Rekap Nilai Saya</span>
        </a>
    </div>

    <!-- Header Detail Kursus (Top Card) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 flex flex-col md:flex-row items-start justify-between gap-6">
        <div class="flex items-start space-x-5">
            @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="{{ $course->title }}" class="w-24 h-24 rounded-2xl object-cover border border-slate-200 flex-shrink-0 shadow-sm">
            @else
                <div class="w-24 h-24 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-2xl border border-amber-200 flex-shrink-0">
                    {{ substr($course->code, 0, 3) }}
                </div>
            @endif

            <div class="space-y-2">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-slate-100 text-slate-700 uppercase">
                        {{ $course->code }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-emerald-100 text-emerald-800 uppercase">
                        Terdaftar
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $course->title }}</h1>
                <p class="text-xs text-slate-500 max-w-3xl leading-relaxed">{{ $course->description ?? 'Tidak ada deskripsi.' }}</p>
                
                <!-- Pengajar/Instruktur Pelatihan -->
                <div class="pt-2 flex items-center space-x-2 text-xs text-slate-600">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-bold text-slate-700">Tim Pengajar:</span>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="px-2 py-0.5 bg-slate-100 rounded-md text-[11px] font-semibold text-slate-700">
                            {{ $course->creator->name ?? 'Instruktur Utama' }}
                        </span>
                        @if($course->teachers && $course->teachers->count() > 0)
                            @foreach($course->teachers as $teacher)
                                @if($teacher->id !== $course->created_by)
                                    <span class="px-2 py-0.5 bg-slate-100 rounded-md text-[11px] font-semibold text-slate-700">
                                        {{ $teacher->name }}
                                    </span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modul Pembelajaran / Pertemuan -->
    <div x-data="{ 
            openMeetings: [{{ $course->meetings->pluck('id')->implode(',') }}],
            toggleMeeting(id) {
                if (this.openMeetings.includes(id)) {
                    this.openMeetings = this.openMeetings.filter(m => m !== id);
                } else {
                    this.openMeetings.push(id);
                }
            },
            expandAll() {
                this.openMeetings = [{{ $course->meetings->pluck('id')->implode(',') }}];
            },
            collapseAll() {
                this.openMeetings = [];
            }
         }" class="space-y-4">

        <!-- Bar Kontrol Pertemuan & Tombol Expand / Collapse -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Modul Pembelajaran & Pertemuan</h2>
                <p class="text-xs text-slate-500">Akses seluruh materi, tugas, dan kuis sesuai jadwal pertemuan.</p>
            </div>

            <div class="flex items-center space-x-2">
                <button type="button" @click="expandAll()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7M19 15l-7 7-7-7"/></svg>
                    <span>Expand Semua</span>
                </button>
                <button type="button" @click="collapseAll()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7M5 9l7-7 7 7"/></svg>
                    <span>Collapse Semua</span>
                </button>
            </div>
        </div>

        @forelse($course->meetings->sortBy('meeting_number') as $meeting)
            @php
                $now = now();
                // Validasi waktu pembukaan & penutupan Pertemuan (jika ada)
                $isMeetingAccessible = true;
                $meetingTimeError = null;

                if ($meeting->available_from && $now->lessThan($meeting->available_from)) {
                    $isMeetingAccessible = false;
                    $meetingTimeError = 'Dapat diakses mulai ' . \Carbon\Carbon::parse($meeting->available_from)->format('d M Y, H:i');
                } elseif ($meeting->available_until && $now->greaterThan($meeting->available_until)) {
                    $isMeetingAccessible = false;
                    $meetingTimeError = 'Akses pertemuan telah ditutup pada ' . \Carbon\Carbon::parse($meeting->available_until)->format('d M Y, H:i');
                }
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- Header Pertemuan -->
                <div @click="toggleMeeting({{ $meeting->id }})" class="p-4 bg-slate-50/80 hover:bg-slate-100/80 border-b border-slate-100 flex items-center justify-between gap-4 cursor-pointer transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-8 h-8 rounded-xl bg-amber-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-sm">
                            {{ $meeting->meeting_number }}
                        </span>
                        <div class="min-w-0">
                            <div class="flex items-center space-x-2">
                                <h3 class="text-sm font-bold text-slate-800 truncate">{{ $meeting->title }}</h3>
                                @if(!$isMeetingAccessible)
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-md flex items-center space-x-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span>Terkunci</span>
                                    </span>
                                @endif
                            </div>
                            @if($meeting->description)
                                <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $meeting->description }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        @if($meetingTimeError)
                            <span class="text-[10px] font-semibold text-red-600 bg-red-50 border border-red-200 px-2.5 py-1 rounded-lg hidden sm:inline-block">
                                {{ $meetingTimeError }}
                            </span>
                        @endif
                        <button type="button" class="w-7 h-7 rounded-lg border border-slate-200 bg-white flex items-center justify-center text-slate-500">
                            <svg class="w-4 h-4 transform transition-transform" :class="openMeetings.includes({{ $meeting->id }}) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body / Konten Pertemuan (Sesuai Skema Guru: Materi, Tugas, Quiz) -->
                <div x-show="openMeetings.includes({{ $meeting->id }})" x-collapse class="p-5">
                    @if(!$isMeetingAccessible)
                        <!-- Tampilan Jika Pertemuan Terkunci Waktu -->
                        <div class="p-6 bg-red-50/50 border border-red-200/80 rounded-xl text-center space-y-1">
                            <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <h4 class="text-xs font-bold text-red-800">Modul Pertemuan Belum Bisa Diakses</h4>
                            <p class="text-xs text-red-600">{{ $meetingTimeError }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                            <!-- SECTION 1: MATERI PEMBELAJARAN (Warna Hijau / Emerald) -->
                            <section class="rounded-2xl border border-slate-200/80 bg-slate-50/40 p-4 space-y-3 min-w-0 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5 mb-3">
                                        <div class="flex items-center gap-2 text-xs font-bold text-emerald-800 uppercase tracking-wider">
                                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <span>Materi ({{ $meeting->materials->count() }})</span>
                                        </div>
                                    </div>

                                    @if($meeting->materials && $meeting->materials->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($meeting->materials as $material)
                                                <div class="p-3 bg-white hover:bg-emerald-50/40 rounded-xl border border-slate-200/80 flex items-center justify-between gap-2 shadow-2xs transition">
                                                    <div class="flex items-center gap-2.5 min-w-0">
                                                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 00-2-2V5a2 2 0 012-2h5.586a1 1 0 00.1.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                        </div>
                                                        <span class="text-xs text-slate-800 font-semibold truncate">{{ $material->title }}</span>
                                                    </div>
                                                    <a href="{{ route('student.materials.show', $material->id) }}" class="text-[11px] text-emerald-700 font-bold hover:underline flex-shrink-0">Pelajari →</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-[11px] text-slate-400 italic py-2">Belum ada materi dipublikasikan.</p>
                                    @endif
                                </div>
                            </section>

                            <!-- SECTION 2: TUGAS UPLOAD / SUBMISSION (Warna Biru / Blue) -->
                            <section class="rounded-2xl border border-slate-200/80 bg-slate-50/40 p-4 space-y-3 min-w-0 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5 mb-3">
                                        <div class="flex items-center gap-2 text-xs font-bold text-blue-800 uppercase tracking-wider">
                                            <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9 2 2 4-4"/>
                                            </svg>
                                            <span>Tugas ({{ $meeting->assignments->where('type', 'submission')->count() }})</span>
                                        </div>
                                    </div>

                                    @if($meeting->assignments && $meeting->assignments->where('type', 'submission')->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($meeting->assignments->where('type', 'submission') as $assignment)
                                                @php
                                                    // Pengecekan Waktu Akses Tugas
                                                    $isTaskOpen = true;
                                                    $taskTimeStatus = ''; // HTML badge status waktu
                                                    
                                                    if ($assignment->available_from && $now->lessThan($assignment->available_from)) {
                                                        $isTaskOpen = false;
                                                        $taskTimeStatus = '<span class="px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-bold rounded">Buka: ' . \Carbon\Carbon::parse($assignment->available_from)->format('d M H:i') . '</span>';
                                                    } elseif ($assignment->available_until) {
                                                        if ($now->greaterThan($assignment->available_until)) {
                                                            $taskTimeStatus = '<span class="px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-bold rounded">Tenggat Lewat: ' . \Carbon\Carbon::parse($assignment->available_until)->format('d M H:i') . '</span>';
                                                        } else {
                                                            $taskTimeStatus = '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[9px] font-bold rounded">Tenggat: ' . \Carbon\Carbon::parse($assignment->available_until)->format('d M H:i') . '</span>';
                                                        }
                                                    }

                                                    // Status Pengerjaan Siswa (Cek Submission & Nilai)
                                                    $userSubmissions = \App\Models\AssignmentSubmission::where('assignment_id', $assignment->id)
                                                        ->where('enrollment_id', $enrollment->id)
                                                        ->get();

                                                    $bestScore = $userSubmissions->where('status', 'graded')->max('score');
                                                    $isFinished = !is_null($bestScore) && $bestScore >= $assignment->passing_score;
                                                @endphp

                                                <div class="p-3 bg-white rounded-xl border border-slate-200/80 flex items-center justify-between gap-2 shadow-2xs">
                                                    <div class="flex items-center gap-2.5 min-w-0">
                                                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8-4-4m0 0L8 8m4-4v12"/>
                                                            </svg>
                                                        </div>
                                                        <div class="min-w-0 space-y-0.5">
                                                            <div class="text-xs text-slate-800 font-semibold truncate">{{ $assignment->title }}</div>
                                                            <div class="flex flex-wrap items-center gap-1.5">
                                                                {!! $taskTimeStatus !!}

                                                                <!-- Status Penyelesaian Student -->
                                                                @if($isFinished)
                                                                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-800 text-[9px] font-extrabold rounded">Selesai (Skor: {{ number_format($bestScore, 0) }})</span>
                                                                @elseif($userSubmissions->isNotEmpty())
                                                                    <span class="px-1.5 py-0.5 bg-amber-100 text-amber-800 text-[9px] font-extrabold rounded">Menunggu Penilaian</span>
                                                                @else
                                                                    <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[9px] font-bold rounded">Belum Selesai</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if($isTaskOpen)
                                                        <a href="{{ route('student.submissions.show', $assignment->id) }}" class="text-[11px] text-blue-700 font-bold hover:underline flex-shrink-0">Lihat →</a>
                                                    @else
                                                        <span class="text-[10px] text-slate-400 font-bold italic flex-shrink-0">Terkunci</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-[11px] text-slate-400 italic py-2">Belum ada tugas upload.</p>
                                    @endif
                                </div>
                            </section>

                            <!-- SECTION 3: KUIS (Warna Kuning/Orange / Amber) -->
                            <section class="rounded-2xl border border-slate-200/80 bg-slate-50/40 p-4 space-y-3 min-w-0 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5 mb-3">
                                        <div class="flex items-center gap-2 text-xs font-bold text-amber-800 uppercase tracking-wider">
                                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Kuis ({{ $meeting->assignments->where('type', 'quiz')->count() }})</span>
                                        </div>
                                    </div>

                                    @if($meeting->assignments && $meeting->assignments->where('type', 'quiz')->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($meeting->assignments->where('type', 'quiz') as $quizAssignment)
                                                @php
                                                    // Pengecekan Waktu Akses Kuis
                                                    $isQuizOpen = true;
                                                    $quizTimeStatus = '';

                                                    if ($quizAssignment->available_from && $now->lessThan($quizAssignment->available_from)) {
                                                        $isQuizOpen = false;
                                                        $quizTimeStatus = '<span class="px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-bold rounded">Buka: ' . \Carbon\Carbon::parse($quizAssignment->available_from)->format('d M H:i') . '</span>';
                                                    } elseif ($quizAssignment->available_until) {
                                                        if ($now->greaterThan($quizAssignment->available_until)) {
                                                            $quizTimeStatus = '<span class="px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-bold rounded">Tenggat Lewat: ' . \Carbon\Carbon::parse($quizAssignment->available_until)->format('d M H:i') . '</span>';
                                                        } else {
                                                            $quizTimeStatus = '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[9px] font-bold rounded">Tenggat: ' . \Carbon\Carbon::parse($quizAssignment->available_until)->format('d M H:i') . '</span>';
                                                        }
                                                    }

                                                    // Status Pengerjaan Quiz & Nilai Siswa
                                                    $quizAttempts = \App\Models\QuizAttempt::whereHas('quiz', function($q) use ($quizAssignment) {
                                                            $q->where('assignment_id', $quizAssignment->id);
                                                        })
                                                        ->where('enrollment_id', $enrollment->id)
                                                        ->whereIn('status', ['submitted', 'expired'])
                                                        ->get();

                                                    $bestQuizScore = $quizAttempts->max('score');
                                                    $isQuizFinished = !is_null($bestQuizScore) && $bestQuizScore >= $quizAssignment->passing_score;
                                                @endphp

                                                <div class="p-3 bg-white rounded-xl border border-slate-200/80 flex items-center justify-between gap-2 shadow-2xs">
                                                    <div class="flex items-center gap-2.5 min-w-0">
                                                        <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </div>
                                                        <div class="min-w-0 space-y-0.5">
                                                            <div class="text-xs text-slate-800 font-semibold truncate">{{ $quizAssignment->title }}</div>
                                                            <div class="flex flex-wrap items-center gap-1.5">
                                                                {!! $quizTimeStatus !!}

                                                                <!-- Status Penyelesaian Student -->
                                                                @if($isQuizFinished)
                                                                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-800 text-[9px] font-extrabold rounded">Selesai (Skor: {{ number_format($bestQuizScore, 0) }})</span>
                                                                @elseif($quizAttempts->isNotEmpty())
                                                                    <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[9px] font-extrabold rounded">Belum Lulus (Skor: {{ number_format($bestQuizScore, 0) }})</span>
                                                                @else
                                                                    <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[9px] font-bold rounded">Belum Selesai</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if($isQuizOpen)
                                                        <a href="{{ route('student.quizzes.show', $quizAssignment->id) }}" class="text-[11px] text-amber-700 font-bold hover:underline flex-shrink-0">Mulai →</a>
                                                    @else
                                                        <span class="text-[10px] text-slate-400 font-bold italic flex-shrink-0">Terkunci</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-[11px] text-slate-400 italic py-2">Belum ada kuis untuk pertemuan ini.</p>
                                    @endif
                                </div>
                            </section>

                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-xs text-slate-400">
                Belum ada pertemuan yang dibuat oleh instruktur untuk kelas ini.
            </div>
        @endforelse

    </div>

</div>
@endsection