@extends('layouts.app')

@section('title', 'Hasil Quiz - ' . $assignment->title)
@section('header_title', 'Hasil Evaluasi Quiz')

@section('content')
<div class="max-w-5xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header Navigation: Tombol Kembali dengan Border Konsisten -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('student.quizzes.show', $assignment->id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Quiz</span>
        </a>

        <span class="px-3 py-1.5 bg-[#123F35] text-white rounded-xl font-bold text-xs self-start sm:self-auto shadow-2xs">
            Attempt #{{ $attempt->attempt_number }}
        </span>
    </div>

    <!-- 1. TOP CARD RINGKASAN HASIL SCORE (HIJAU TUA GRADASI) -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] rounded-2xl border border-[#28745B]/40 shadow-md p-5 sm:p-8 text-white space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-white/20 pb-4">
            <div class="space-y-1.5">
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase backdrop-blur-xs">
                    Hasil Evaluasi Quiz
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug">{{ $assignment->title }}</h1>
            </div>

            <div class="flex-shrink-0">
                @if($attempt->score >= $assignment->passing_score)
                    <span class="inline-block px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] shadow-2xs">
                        ✓ LULUS
                    </span>
                @else
                    <span class="inline-block px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-red-500/20 text-red-200 border border-red-400/50 backdrop-blur-xs">
                        BELUM LULUS
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Cards Ringkasan -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Nilai Akhir</span>
                <div class="text-xl sm:text-3xl font-black {{ $attempt->score >= $assignment->passing_score ? 'text-[#E7C982]' : 'text-red-300' }} mt-1">
                    {{ number_format($attempt->score, 2) }} <span class="text-xs text-emerald-100/70 font-normal">/ {{ number_format($assignment->max_score, 0) }}</span>
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Passing Grade (KKM)</span>
                <div class="text-base font-extrabold text-white mt-1">{{ number_format($assignment->passing_score, 0) }}</div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Waktu Dikumpulkan</span>
                <div class="text-xs font-semibold text-white mt-1">
                    {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y H:i') : '-' }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs flex flex-col justify-between">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Status Pengerjaan</span>
                <div class="text-xs font-bold text-[#E7C982] mt-1 uppercase">{{ $attempt->status }}</div>
            </div>
        </div>
    </div>

    <!-- 2. EVALUASI SOAL & PEMBAHASAN -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        
        <!-- Heading Container dengan warna hijau #28745B -->
        <div class="bg-[#28745B] p-4 sm:px-6 border-b border-slate-300 text-white">
            <h2 class="text-sm sm:text-base font-bold text-white">Evaluasi Jawaban & Pembahasan</h2>
            <p class="text-xs text-emerald-100/85">Tinjauan hasil pengerjaan setiap butir soal beserta kunci jawaban dan pembahasannya.</p>
        </div>

        <div class="p-4 sm:p-8 space-y-5">
            @foreach($attempt->attemptQuestions as $index => $aq)
                <div class="rounded-2xl border border-slate-300 overflow-hidden shadow-2xs {{ $aq->is_correct ? 'bg-emerald-50/40' : 'bg-red-50/40' }}">
                    
                    <!-- Header Kartu Soal -->
                    <div class="mx-4 sm:mx-5 py-3.5 border-b border-slate-300 flex flex-wrap items-center justify-between gap-2">
                        <span class="px-2.5 py-1 bg-[#123F35] text-white rounded-lg font-bold text-xs">
                            Soal #{{ $index + 1 }}
                        </span>
                        @if($aq->is_correct)
                            <span class="px-3 py-1 rounded-lg border border-[#28745B]/40 bg-[#28745B]/15 text-[#28745B] text-[11px] font-extrabold">
                                +{{ number_format($aq->points_earned, 2) }} Poin (Benar)
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-lg border border-red-300 bg-red-100 text-red-700 text-[11px] font-extrabold">
                                0 Poin (Salah / Tidak Dijawab)
                            </span>
                        @endif
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        <!-- Pertanyaan & Media Soal -->
                        <div class="space-y-3">
                            <div class="text-xs sm:text-sm font-bold text-[#123F35] leading-relaxed break-words">
                                {!! nl2br(e($aq->question->question_text)) !!}
                            </div>

                            @if($aq->question->media)
                                @php
                                    $qMediaUrl  = asset('storage/' . $aq->question->media->file_path);
                                    $qMediaType = $aq->question->media->type ?? '';
                                    $qMimeType  = $aq->question->media->mime_type ?? '';
                                @endphp
                                <div class="pt-1">
                                    @if($qMediaType === 'image' || str_contains($qMimeType, 'image'))
                                        <div class="p-2.5 bg-white rounded-xl border border-slate-300 inline-block max-w-full">
                                            <a href="{{ $qMediaUrl }}" target="_blank" rel="noopener noreferrer">
                                                <img src="{{ $qMediaUrl }}" alt="{{ $aq->question->media->file_name }}"
                                                     class="w-full sm:w-auto max-h-64 rounded-lg object-contain">
                                            </a>
                                        </div>
                                    @elseif($qMediaType === 'audio' || str_contains($qMimeType, 'audio'))
                                        <div class="p-3 bg-white rounded-xl border border-slate-300 max-w-lg space-y-1.5">
                                            <div class="flex items-center space-x-1.5 text-[11px] font-bold text-[#123F35]">
                                                <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                                </svg>
                                                <span class="truncate">{{ $aq->question->media->file_name }}</span>
                                            </div>
                                            <audio controls preload="metadata" class="w-full h-9">
                                                <source src="{{ $qMediaUrl }}" type="{{ $qMimeType }}">
                                                Browser Anda tidak mendukung pemutar audio.
                                            </audio>
                                        </div>
                                    @elseif($qMediaType === 'video' || str_contains($qMimeType, 'video'))
                                        <div class="p-2.5 bg-white rounded-xl border border-slate-300 max-w-xl space-y-1.5">
                                            <video controls preload="metadata" class="w-full max-h-64 rounded-lg bg-black">
                                                <source src="{{ $qMediaUrl }}" type="{{ $qMimeType }}">
                                                Browser Anda tidak mendukung pemutar video.
                                            </video>
                                            <p class="text-[10px] text-slate-500 truncate">{{ $aq->question->media->file_name }}</p>
                                        </div>
                                    @else
                                        <a href="{{ $qMediaUrl }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center space-x-2 px-3.5 py-2 bg-[#d1e4da] border border-slate-300 rounded-xl text-xs font-bold text-[#123F35] hover:bg-[#28745B] hover:text-white transition max-w-full">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="truncate">Lihat / Unduh Lampiran ({{ $aq->question->media->file_name }})</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Pilihan Jawaban (Teks + Gambar / Audio / Video) -->
                        <div class="space-y-2.5">
                            @foreach($aq->question->options as $optIndex => $opt)
                                @php
                                    $isSelected = ($aq->selected_option_id === $opt->id);
                                    $isKey = $opt->is_correct;
                                @endphp
                                <div class="p-3 sm:p-3.5 rounded-xl border text-xs space-y-2.5 transition
                                            {{ $isKey ? 'bg-emerald-100/90 border-[#28745B]/50 font-bold text-[#123F35]' : ($isSelected ? 'bg-red-100/90 border-red-300 font-bold text-red-900' : 'bg-white border-slate-300 text-slate-700') }}">
                                    
                                    <!-- Baris Teks Opsi & Badge Status -->
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                                        <div class="flex items-start space-x-2 min-w-0">
                                            <span class="font-extrabold flex-shrink-0">{{ chr(65 + $optIndex) }}.</span>
                                            @if(filled($opt->option_text))
                                                <span class="leading-relaxed break-words">{!! nl2br(e($opt->option_text)) !!}</span>
                                            @else
                                                <span class="italic text-slate-400 font-normal">(Opsi Media)</span>
                                            @endif
                                        </div>

                                        @if($isKey || ($isSelected && !$isKey))
                                            <div class="flex flex-wrap items-center gap-1.5 flex-shrink-0 pl-5 sm:pl-0">
                                                @if($isKey)
                                                    <span class="text-[10px] bg-[#28745B] text-white px-2.5 py-0.5 rounded-md font-bold shadow-2xs">Kunci Jawaban</span>
                                                @endif
                                                @if($isSelected && !$isKey)
                                                    <span class="text-[10px] bg-red-600 text-white px-2.5 py-0.5 rounded-md font-bold shadow-2xs">Jawaban Anda</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Media Opsi Jawaban (Jika Ada) -->
                                    @if($opt->media)
                                        @php
                                            $optMediaUrl  = asset('storage/' . $opt->media->file_path);
                                            $optMediaType = $opt->media->type ?? '';
                                            $optMimeType  = $opt->media->mime_type ?? '';
                                        @endphp
                                        <div class="pl-0 sm:pl-5">
                                            @if($optMediaType === 'image' || str_contains($optMimeType, 'image'))
                                                <a href="{{ $optMediaUrl }}" target="_blank" rel="noopener noreferrer"
                                                   class="inline-block bg-white p-1.5 rounded-lg border border-slate-300 hover:border-[#28745B] transition max-w-full">
                                                    <img src="{{ $optMediaUrl }}" alt="{{ $opt->media->file_name }}"
                                                         class="w-full sm:w-auto max-h-40 rounded-md object-contain">
                                                </a>
                                            @elseif($optMediaType === 'audio' || str_contains($optMimeType, 'audio'))
                                                <div class="p-2.5 bg-white rounded-lg border border-slate-300 max-w-md space-y-1">
                                                    <p class="text-[10px] font-semibold text-slate-500 truncate">{{ $opt->media->file_name }}</p>
                                                    <audio controls preload="metadata" class="w-full h-8">
                                                        <source src="{{ $optMediaUrl }}" type="{{ $optMimeType }}">
                                                        Browser Anda tidak mendukung pemutar audio.
                                                    </audio>
                                                </div>
                                            @elseif($optMediaType === 'video' || str_contains($optMimeType, 'video'))
                                                <div class="p-2 bg-white rounded-lg border border-slate-300 max-w-md space-y-1">
                                                    <video controls preload="metadata" class="w-full max-h-48 rounded-md bg-black">
                                                        <source src="{{ $optMediaUrl }}" type="{{ $optMimeType }}">
                                                        Browser Anda tidak mendukung pemutar video.
                                                    </video>
                                                    <p class="text-[10px] font-normal text-slate-500 truncate">{{ $opt->media->file_name }}</p>
                                                </div>
                                            @else
                                                <a href="{{ $optMediaUrl }}" target="_blank" rel="noopener noreferrer"
                                                   class="inline-flex items-center space-x-1 text-[11px] text-[#28745B] hover:underline font-bold">
                                                    <span>Lihat Media ({{ $opt->media->file_name }})</span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Penjelasan Pembahasan -->
                        @if($aq->question->explanation)
                            <div class="p-3.5 bg-white rounded-xl border border-slate-300 text-xs text-slate-700 space-y-1">
                                <span class="font-bold text-[#123F35] uppercase text-[10px] tracking-wider flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Pembahasan:</span>
                                </span>
                                <p class="leading-relaxed text-slate-600 pt-0.5 whitespace-pre-line break-words">{{ $aq->question->explanation }}</p>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection