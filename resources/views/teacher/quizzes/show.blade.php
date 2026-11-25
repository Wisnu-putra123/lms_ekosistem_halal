@extends('layouts.app')

@section('title', $assignment->title . ' - Ekosistem Halal')
@section('header_title', 'Detail & Hasil Quiz')

@section('content')
<div class="max-w-5xl mx-auto space-y-5 sm:space-y-6" x-data="{ activeTab: '{{ request()->has('questions_page') ? 'questions' : 'attempts' }}' }">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.courses.show', $assignment->course_id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>

        <div class="flex items-center space-x-2 self-start sm:self-auto">
            <a href="{{ route('teacher.quizzes.edit', $assignment->id) }}" class="inline-flex items-center space-x-1.5 bg-[#E7C982]/40 text-[#123F35] border border-[#E7C982] hover:bg-[#123F35] hover:text-white px-3.5 py-2 rounded-xl text-xs font-extrabold shadow-2xs transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Quiz</span>
            </a>

            <form action="{{ route('teacher.quizzes.destroy', $assignment->id) }}" method="POST" onsubmit="return confirm('Hapus Quiz ini beserta seluruh bank soalnya?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center space-x-1.5 bg-red-50 hover:bg-red-600 text-red-700 hover:text-white border border-red-300 px-3.5 py-2 rounded-xl text-xs font-bold shadow-2xs transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Hapus Quiz</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Flash Notification -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center space-x-2 shadow-2xs">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-300 text-red-800 rounded-2xl text-xs font-semibold flex items-center space-x-2 shadow-2xs">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 1. TOP CARD KONFIGURASI RINGKAS QUIZ -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] rounded-2xl border border-[#28745B]/40 shadow-md p-5 sm:p-8 text-white space-y-6">
        <div class="border-b border-white/20 pb-4 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-1.5">
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase backdrop-blur-xs">
                    Pertemuan {{ $assignment->meeting->meeting_number }} &bull; Aktivitas Quiz
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug">{{ $assignment->title }}</h1>
            </div>

            <div class="flex-shrink-0">
                @if($assignment->status === 'published')
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] shadow-2xs">
                        ✓ Published
                    </span>
                @else
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-white/15 text-emerald-100 border border-white/30 backdrop-blur-xs">
                        Closed
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5 sm:gap-3">
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Durasi Timer</span>
                <div class="text-xs font-bold text-white mt-1">
                    {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' Menit' : 'Tanpa Batas' }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Soal Diuji / Bank</span>
                <div class="text-xs font-extrabold text-white mt-1">
                    {{ $quiz->question_count }} / {{ $quiz->questions_count }} Soal
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Poin / Soal</span>
                <div class="text-xs font-bold text-white mt-1">
                    &plusmn; {{ $pointPerQuestion }} Poin
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Passing Grade</span>
                <div class="text-xs font-extrabold text-white mt-1">
                    {{ number_format($assignment->passing_score, 0) }} / {{ number_format($assignment->max_score, 0) }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Batas Attempt</span>
                <div class="text-xs font-bold text-white mt-1">
                    {{ $assignment->max_attempts ? $assignment->max_attempts . 'x Attempt' : 'Tanpa Batas' }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Waktu Ditutup</span>
                <div class="text-xs font-semibold text-white mt-1">
                    {{ $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('d M Y H:i') : 'Tanpa Batas' }}
                </div>
            </div>
        </div>

        @if($quiz->questions_count < $quiz->question_count)
            <div class="p-3.5 bg-[#E7C982]/20 border border-[#E7C982]/50 rounded-xl text-xs text-[#E7C982] flex items-center space-x-2 backdrop-blur-xs">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Jumlah soal di bank soal (<strong class="text-white">{{ $quiz->questions_count }}</strong>) masih kurang dari target soal yang diuji (<strong class="text-white">{{ $quiz->question_count }}</strong>). Mohon tambahkan soal baru.</span>
            </div>
        @endif
    </div>

    <!-- 2. TAB HEADER SWITCHER -->
    <div class="flex items-center space-x-2 border-b border-slate-300 pb-2 overflow-x-auto">
        <button type="button" @click="activeTab = 'attempts'" 
                :class="activeTab === 'attempts' ? 'bg-[#28745B] text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border-slate-300 hover:bg-[#F7F5EE] font-semibold'"
                class="px-5 py-2.5 rounded-xl border text-xs transition flex items-center space-x-2 flex-shrink-0 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span>Hasil & Nilai Siswa ({{ $quizAttempts->total() }})</span>
        </button>

        <button type="button" @click="activeTab = 'questions'" 
                :class="activeTab === 'questions' ? 'bg-[#28745B] text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border-slate-300 hover:bg-[#F7F5EE] font-semibold'"
                class="px-5 py-2.5 rounded-xl border text-xs transition flex items-center space-x-2 flex-shrink-0 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Bank Soal Quiz ({{ $quiz->questions_count }})</span>
        </button>
    </div>

    <!-- 3. TAB DAFTAR BANK SOAL -->
    <div x-show="activeTab === 'questions'" x-cloak class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <!-- Heading Container bg-[#28745B] -->
        <div class="bg-[#28745B] p-4 sm:px-6 border-b border-slate-300 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-white">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-white">Daftar Bank Soal ({{ $quiz->questions_count }})</h2>
                <p class="text-xs text-emerald-100/85">Kelola soal, media lampiran, kunci jawaban, dan pembahasan yang digunakan untuk quiz ini.</p>
            </div>

            <a href="{{ route('teacher.quizzes.questions.create', $assignment->id) }}" class="bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] px-4 py-2 rounded-xl text-xs font-extrabold shadow-2xs transition flex items-center space-x-1.5 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Soal Baru</span>
            </a>
        </div>

        <div class="p-5 sm:p-8 space-y-5">
            @forelse($questions as $index => $q)
                <div class="rounded-2xl border border-slate-300 bg-[#F7F5EE]/50 overflow-hidden shadow-2xs">
                    <!-- Header Kartu Soal -->
                    <div class="mx-4 sm:mx-5 py-3.5 border-b border-slate-300 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="px-2.5 py-1 bg-[#123F35] text-white rounded-lg font-bold text-xs">
                                Soal #{{ $questions->firstItem() + $index }}
                            </span>
                            <span class="px-2.5 py-1 bg-white border border-slate-300 text-[#123F35] rounded-lg font-bold text-[11px]">
                                {{ $q->question_type === 'true_false' ? 'Benar / Salah' : 'Pilihan Ganda' }}
                            </span>
                            @if($q->points > 0)
                                <span class="px-2.5 py-1 bg-[#28745B]/15 text-[#28745B] rounded-lg font-extrabold text-[11px]">
                                    {{ number_format($q->points, 1) }} Poin
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <a href="{{ route('teacher.quizzes.questions.edit', [$assignment->id, $q->id]) }}" class="p-1.5 bg-[#E7C982]/40 text-[#123F35] border border-[#E7C982] hover:bg-[#123F35] hover:text-white rounded-lg transition shadow-2xs" title="Edit Soal">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('teacher.quizzes.questions.destroy', [$assignment->id, $q->id]) }}" method="POST" onsubmit="return confirm('Hapus soal ini dari bank soal?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 border border-red-300 hover:bg-red-600 hover:text-white rounded-lg transition shadow-2xs cursor-pointer" title="Hapus Soal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Body Soal, Opsi, & Pembahasan -->
                    <div class="p-4 sm:p-5 space-y-4">
                        <div>
                            <div class="text-xs sm:text-sm font-bold text-[#123F35] leading-relaxed">{!! nl2br(e($q->question_text)) !!}</div>

                            <!-- Media Lampiran Soal (Gambar / Audio / Video / PDF) -->
                            @if($q->media)
                                @php
                                    $qMediaUrl  = asset('storage/' . $q->media->file_path);
                                    $qMediaType = $q->media->type ?? '';
                                    $qMimeType  = $q->media->mime_type ?? '';
                                @endphp
                                <div class="mt-3">
                                    @if($qMediaType === 'image' || str_contains($qMimeType, 'image'))
                                        <div class="p-2.5 bg-white rounded-xl border border-slate-300 inline-block">
                                            <a href="{{ $qMediaUrl }}" target="_blank" rel="noopener noreferrer">
                                                <img src="{{ $qMediaUrl }}" alt="{{ $q->media->file_name }}" class="max-h-56 rounded-lg object-contain">
                                            </a>
                                            <p class="text-[10px] text-slate-500 mt-1.5 truncate max-w-xs">{{ $q->media->file_name }}</p>
                                        </div>
                                    @elseif($qMediaType === 'audio' || str_contains($qMimeType, 'audio'))
                                        <div class="p-3 bg-white rounded-xl border border-slate-300 max-w-md space-y-1.5">
                                            <div class="flex items-center space-x-1.5 text-[11px] font-bold text-[#123F35]">
                                                <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                                </svg>
                                                <span class="truncate">{{ $q->media->file_name }}</span>
                                            </div>
                                            <audio controls preload="metadata" class="w-full h-9">
                                                <source src="{{ $qMediaUrl }}" type="{{ $qMimeType }}">
                                                Browser Anda tidak mendukung pemutar audio.
                                            </audio>
                                        </div>
                                    @elseif($qMediaType === 'video' || str_contains($qMimeType, 'video'))
                                        <div class="p-2.5 bg-white rounded-xl border border-slate-300 max-w-lg space-y-1.5">
                                            <video controls preload="metadata" class="w-full max-h-64 rounded-lg bg-black">
                                                <source src="{{ $qMediaUrl }}" type="{{ $qMimeType }}">
                                                Browser Anda tidak mendukung pemutar video.
                                            </video>
                                            <p class="text-[10px] text-slate-500 truncate">{{ $q->media->file_name }}</p>
                                        </div>
                                    @else
                                        <a href="{{ $qMediaUrl }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center space-x-2 px-3.5 py-2 bg-[#d1e4da] border border-slate-300 rounded-xl text-xs font-bold text-[#123F35] hover:bg-[#28745B] hover:text-white transition">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>Lihat / Unduh Lampiran ({{ $q->media->file_name }})</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Opsi Jawaban (Teks + Gambar / Audio / Video) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach($q->options as $optIndex => $opt)
                                <div class="p-3 rounded-xl border text-xs flex flex-col justify-between gap-2.5 {{ $opt->is_correct ? 'bg-emerald-100/90 border-[#28745B]/50 font-bold text-[#123F35]' : 'bg-white border-slate-300 text-slate-700' }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-start space-x-2 min-w-0 pr-1">
                                            <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-extrabold flex-shrink-0 mt-0.5 {{ $opt->is_correct ? 'bg-[#28745B] text-white' : 'bg-[#F7F5EE] border border-slate-300 text-slate-700' }}">
                                                {{ chr(65 + $optIndex) }}
                                            </span>
                                            @if(filled($opt->option_text))
                                                <span class="break-words leading-relaxed">{!! nl2br(e($opt->option_text)) !!}</span>
                                            @else
                                                <span class="italic text-slate-400 font-normal">(Opsi Media)</span>
                                            @endif
                                        </div>

                                        @if($opt->is_correct)
                                            <span class="text-[10px] bg-[#28745B] text-white px-2.5 py-0.5 rounded-md font-bold flex-shrink-0 shadow-2xs">Kunci Jawaban</span>
                                        @endif
                                    </div>

                                    <!-- Media Opsi Jawaban -->
                                    @if($opt->media)
                                        @php
                                            $optMediaUrl  = asset('storage/' . $opt->media->file_path);
                                            $optMediaType = $opt->media->type ?? '';
                                            $optMimeType  = $opt->media->mime_type ?? '';
                                        @endphp
                                        <div class="pl-7">
                                            @if($optMediaType === 'image' || str_contains($optMimeType, 'image'))
                                                <a href="{{ $optMediaUrl }}" target="_blank" rel="noopener noreferrer" class="inline-block bg-white p-1.5 rounded-lg border border-slate-300 hover:border-[#28745B] transition">
                                                    <img src="{{ $optMediaUrl }}" alt="{{ $opt->media->file_name }}" class="max-h-36 rounded-md object-contain">
                                                </a>
                                            @elseif($optMediaType === 'audio' || str_contains($optMimeType, 'audio'))
                                                <div class="p-2 bg-white rounded-lg border border-slate-300 space-y-1">
                                                    <p class="text-[10px] font-semibold text-slate-500 truncate">{{ $opt->media->file_name }}</p>
                                                    <audio controls preload="metadata" class="w-full h-8">
                                                        <source src="{{ $optMediaUrl }}" type="{{ $optMimeType }}">
                                                        Browser Anda tidak mendukung pemutar audio.
                                                    </audio>
                                                </div>
                                            @elseif($optMediaType === 'video' || str_contains($optMimeType, 'video'))
                                                <div class="p-1.5 bg-white rounded-lg border border-slate-300 space-y-1">
                                                    <video controls preload="metadata" class="w-full max-h-44 rounded-md bg-black">
                                                        <source src="{{ $optMediaUrl }}" type="{{ $optMimeType }}">
                                                        Browser Anda tidak mendukung pemutar video.
                                                    </video>
                                                    <p class="text-[10px] font-normal text-slate-500 truncate">{{ $opt->media->file_name }}</p>
                                                </div>
                                            @else
                                                <a href="{{ $optMediaUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center space-x-1 text-[11px] text-[#28745B] hover:underline font-bold">
                                                    <span>Lihat Media ({{ $opt->media->file_name }})</span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Pembahasan Soal -->
                        @if($q->explanation)
                            <div class="p-3.5 bg-white rounded-xl border border-slate-300 text-xs text-slate-700 space-y-1">
                                <span class="font-bold text-[#123F35] uppercase text-[10px] tracking-wider flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Pembahasan:</span>
                                </span>
                                <p class="leading-relaxed text-slate-600 pt-0.5 whitespace-pre-line">{{ $q->explanation }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-xs text-slate-500 bg-[#F7F5EE]/50 rounded-2xl border border-dashed border-slate-300">
                    Belum ada soal yang ditambahkan pada bank soal Quiz ini. Silakan klik tombol "+ Tambah Soal Baru".
                </div>
            @endforelse
        </div>

        <!-- Navigasi Paginasi Bank Soal -->
        @if($questions->hasPages())
            <div class="p-4 sm:px-6 border-t border-slate-300 bg-[#F7F5EE]/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div>
                    Menampilkan <span class="font-bold text-[#123F35]">{{ $questions->firstItem() }}</span> - 
                    <span class="font-bold text-[#123F35]">{{ $questions->lastItem() }}</span> dari 
                    <span class="font-bold text-[#123F35]">{{ $questions->total() }}</span> soal
                </div>
                <div>
                    {{ $questions->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- 4. TAB TABEL PENILAIAN / ATTEMPTS SISWA -->
    <div x-show="activeTab === 'attempts'" class="space-y-5">
        
        <!-- Filter & Sort Form dengan Warna Cream (#F7F5EE) -->
        <div class="bg-[#F7F5EE] rounded-2xl border border-slate-300 p-4 sm:p-5 shadow-2xs">
            <form method="GET" action="{{ route('teacher.quizzes.show', $assignment->id) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Cari Siswa</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..." 
                           class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 bg-white outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                </div>
                
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Status Enrollment</label>
                    <select name="enrollment_status" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 bg-white outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('enrollment_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('enrollment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="dropped" {{ request('enrollment_status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Status Attempt</label>
                    <select name="status" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 bg-white outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted (Selesai)</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress (Mengerjakan)</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired (Waktu Habis)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Urutkan</label>
                    <select name="sort" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 bg-white outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition cursor-pointer">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru Dimulai</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama Dimulai</option>
                        <option value="score_high" {{ request('sort') == 'score_high' ? 'selected' : '' }}>Nilai Tertinggi</option>
                        <option value="score_low" {{ request('sort') == 'score_low' ? 'selected' : '' }}>Nilai Terendah</option>
                    </select>
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                        Filter
                    </button>
                    <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="w-full bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-100 transition flex items-center justify-center shadow-2xs">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Attempt Siswa dengan Heading Hijau (#28745B) -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#28745B] text-white border-b border-slate-300">
                        <tr class="text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                            <th class="py-3.5 px-4 sm:px-6 min-w-[180px]">Siswa & Enrollment</th>
                            <th class="py-3.5 px-4 min-w-[120px]">Attempt Ke-</th>
                            <th class="py-3.5 px-4 min-w-[140px]">Waktu Selesai</th>
                            <th class="py-3.5 px-4 min-w-[140px]">Status</th>
                            <th class="py-3.5 px-4 min-w-[80px]">Nilai</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right min-w-[100px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-300 text-xs text-slate-700">
                        @forelse($quizAttempts as $attempt)
                            <tr class="hover:bg-[#F7F5EE]/40 transition">
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="font-bold text-[#123F35]">{{ $attempt->enrollment->user->name ?? 'Siswa Tidak Ditemukan' }}</div>
                                    <div class="text-[10px] mt-0.5 flex items-center space-x-1">
                                        <span class="text-slate-500">ID #{{ $attempt->enrollment_id }}:</span>
                                        <span class="uppercase font-extrabold {{ $attempt->enrollment->status === 'active' ? 'text-[#28745B]' : 'text-slate-600' }}">
                                            {{ $attempt->enrollment->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-[#123F35] text-white rounded-lg font-bold text-[11px] whitespace-nowrap">
                                        Attempt #{{ $attempt->attempt_number }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($attempt->status === 'submitted' || $attempt->status === 'expired')
                                        @if($attempt->score >= $assignment->passing_score)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-[#28745B]/40 bg-[#28745B]/15 text-[#28745B] text-[10px] font-extrabold whitespace-nowrap">
                                                Lulus
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-red-300 bg-red-100 text-red-700 text-[10px] font-extrabold whitespace-nowrap">
                                                Belum Lulus
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-[#E7C982] bg-[#E7C982]/40 text-[#123F35] text-[10px] font-extrabold whitespace-nowrap">
                                            Sedang Mengerjakan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-extrabold text-[#123F35] whitespace-nowrap">
                                    {{ $attempt->score !== null ? number_format($attempt->score, 2) : '-' }}
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- Tombol Lihat Detail Jawaban Siswa -->
                                        <a href="{{ route('teacher.quizzes.attempts.show', $attempt->id) }}" 
                                           class="p-1.5 bg-[#28745B]/15 text-[#123F35] border border-[#28745B]/40 hover:bg-[#123F35] hover:text-white rounded-lg transition shadow-2xs" 
                                           title="Lihat Detail Jawaban Siswa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        <!-- Tombol Hapus Attempt -->
                                        <form action="{{ route('teacher.quizzes.attempts.destroy', $attempt->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus attempt pengerjaan kuis siswa ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-red-50 text-red-600 border border-red-300 hover:bg-red-600 hover:text-white rounded-lg transition shadow-2xs" title="Hapus Attempt">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-xs text-slate-400 italic">
                                    Tidak ada data pengerjaan kuis siswa yang cocok dengan filter tersebut.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Navigasi Paginasi -->
            @if($quizAttempts->hasPages())
                <div class="p-4 sm:px-6 border-t border-slate-300 bg-[#F7F5EE]/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <div>
                        Menampilkan <span class="font-bold text-[#123F35]">{{ $quizAttempts->firstItem() }}</span> - 
                        <span class="font-bold text-[#123F35]">{{ $quizAttempts->lastItem() }}</span> dari 
                        <span class="font-bold text-[#123F35]">{{ $quizAttempts->total() }}</span> attempt siswa
                    </div>
                    <div>
                        {{ $quizAttempts->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection