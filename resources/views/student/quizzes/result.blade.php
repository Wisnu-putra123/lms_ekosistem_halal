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
                <div class="text-2xl sm:text-3xl font-black {{ $attempt->score >= $assignment->passing_score ? 'text-[#E7C982]' : 'text-red-300' }} mt-1">
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
        
        <!-- Heading Container dengan warna hijau yang lebih muda bg-[#28745B] -->
        <div class="bg-[#28745B] p-4 sm:px-6 border-b border-slate-300 text-white">
            <h2 class="text-sm sm:text-base font-bold text-white">Evaluasi Jawaban & Pembahasan</h2>
            <p class="text-xs text-emerald-100/85">Tinjauan hasil pengerjaan setiap butir soal beserta kunci jawaban dan pembahasannya.</p>
        </div>

        <div class="p-5 sm:p-8 space-y-5">
            @foreach($attempt->attemptQuestions as $index => $aq)
                <div class="rounded-2xl border border-slate-300 overflow-hidden shadow-2xs {{ $aq->is_correct ? 'bg-emerald-50/40' : 'bg-red-50/40' }}">
                    
                    <!-- Header Kartu Soal menyatu dengan body & garis bawah sejajar padding -->
                    <div class="mx-4 sm:mx-5 py-3.5 border-b border-slate-300 flex items-center justify-between gap-2">
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
                        <!-- Pertanyaan -->
                        <div class="text-xs sm:text-sm font-bold text-[#123F35] leading-relaxed">
                            {!! $aq->question->question_text !!}
                        </div>

                        <!-- Pilihan Jawaban -->
                        <div class="space-y-2">
                            @foreach($aq->question->options as $optIndex => $opt)
                                @php
                                    $isSelected = ($aq->selected_option_id === $opt->id);
                                    $isKey = $opt->is_correct;
                                @endphp
                                <div class="p-3 rounded-xl border text-xs flex items-center justify-between gap-2 transition
                                            {{ $isKey ? 'bg-emerald-100/90 border-[#28745B]/50 font-bold text-[#123F35]' : ($isSelected ? 'bg-red-100/90 border-red-300 font-bold text-red-900' : 'bg-white border-slate-300 text-slate-700') }}">
                                    <div class="flex items-start space-x-2">
                                        <span class="font-extrabold flex-shrink-0">{{ chr(65 + $optIndex) }}.</span>
                                        <span>{{ $opt->option_text }}</span>
                                    </div>

                                    <div class="flex items-center space-x-1.5 flex-shrink-0">
                                        @if($isKey)
                                            <span class="text-[10px] bg-[#28745B] text-white px-2.5 py-0.5 rounded-md font-bold shadow-2xs">Kunci Jawaban</span>
                                        @endif
                                        @if($isSelected && !$isKey)
                                            <span class="text-[10px] bg-red-600 text-white px-2.5 py-0.5 rounded-md font-bold shadow-2xs">Jawaban Anda</span>
                                        @endif
                                    </div>
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
                                <p class="leading-relaxed text-slate-600 pt-0.5">{{ $aq->question->explanation }}</p>
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection