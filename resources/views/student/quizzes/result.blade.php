@extends('layouts.app')

@section('title', 'Hasil Quiz - ' . $assignment->title)
@section('header_title', 'Hasil Evaluasi Quiz')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('student.quizzes.show', $assignment->id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-emerald-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Detail Quiz</span>
        </a>
        <span class="px-3 py-1 bg-slate-800 text-white rounded-md font-bold text-xs">
            Attempt #{{ $attempt->attempt_number }}
        </span>
    </div>

    <!-- Ringkasan Hasil Score Banner -->
    <div class="p-6 sm:p-8 rounded-2xl border shadow-sm space-y-4 {{ $attempt->score >= $assignment->passing_score ? 'bg-emerald-50/80 border-emerald-200' : 'bg-red-50/80 border-red-200' }}">
        <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
            <div>
                <span class="text-[10px] font-bold text-slate-500 uppercase">Hasil Evaluasi Quiz:</span>
                <h1 class="text-lg font-bold text-slate-800 mt-0.5">{{ $assignment->title }}</h1>
            </div>

            @if($attempt->score >= $assignment->passing_score)
                <span class="px-3.5 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-sm">LULUS</span>
            @else
                <span class="px-3.5 py-1 rounded-full text-xs font-bold bg-red-600 text-white shadow-sm">BELUM LULUS</span>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <span class="text-[11px] font-semibold text-slate-500 uppercase">Nilai Akhir:</span>
                <div class="text-3xl font-black {{ $attempt->score >= $assignment->passing_score ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ number_format($attempt->score, 2) }} <span class="text-xs text-slate-400 font-normal">/ {{ number_format($assignment->max_score, 0) }}</span>
                </div>
            </div>

            <div>
                <span class="text-[11px] font-semibold text-slate-500 uppercase">Passing Grade (KKM):</span>
                <div class="text-base font-bold text-slate-800 mt-1">{{ number_format($assignment->passing_score, 0) }}</div>
            </div>

            <div>
                <span class="text-[11px] font-semibold text-slate-500 uppercase">Waktu Dikumpulkan:</span>
                <div class="text-xs font-semibold text-slate-700 mt-1">
                    {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y H:i') : '-' }}
                </div>
            </div>

            <div>
                <span class="text-[11px] font-semibold text-slate-500 uppercase">Status Pengerjaan:</span>
                <div class="text-xs font-bold text-slate-800 mt-1 uppercase">{{ $attempt->status }}</div>
            </div>
        </div>
    </div>

    <!-- EVALUASI SOAL & PEMBAHASAN -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <h2 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3">Evaluasi Jawaban & Pembahasan</h2>

        <div class="space-y-6">
            @foreach($attempt->attemptQuestions as $index => $aq)
                <div class="p-5 rounded-2xl border space-y-4 {{ $aq->is_correct ? 'bg-emerald-50/30 border-emerald-200' : 'bg-red-50/30 border-red-200' }}">
                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                        <span class="font-bold text-xs text-slate-800">Soal #{{ $index + 1 }}</span>
                        @if($aq->is_correct)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">+{{ number_format($aq->points_earned, 2) }} Poin (Benar)</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">0 Poin (Salah / Tidak Dijawab)</span>
                        @endif
                    </div>

                    <!-- Pertanyaan -->
                    <div class="text-xs font-semibold text-slate-800 leading-relaxed">{!! $aq->question->question_text !!}</div>

                    <!-- Pilihan Jawaban -->
                    <div class="space-y-2">
                        @foreach($aq->question->options as $optIndex => $opt)
                            @php
                                $isSelected = ($aq->selected_option_id === $opt->id);
                                $isKey = $opt->is_correct;
                            @endphp
                            <div class="p-3 rounded-xl border text-xs flex items-center justify-between 
                                        {{ $isKey ? 'bg-emerald-100 border-emerald-300 font-semibold text-emerald-900' : ($isSelected ? 'bg-red-100 border-red-300 font-semibold text-red-900' : 'bg-white border-slate-200 text-slate-700') }}">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold">{{ chr(65 + $optIndex) }}.</span>
                                    <span>{{ $opt->option_text }}</span>
                                </div>

                                <div>
                                    @if($isKey)
                                        <span class="text-[10px] bg-emerald-700 text-white px-2 py-0.5 rounded font-bold">Kunci Jawaban</span>
                                    @endif
                                    @if($isSelected && !$isKey)
                                        <span class="text-[10px] bg-red-600 text-white px-2 py-0.5 rounded font-bold">Jawaban Anda</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Penjelasan Pembahasan -->
                    @if($aq->question->explanation)
                        <div class="p-3.5 bg-white/80 rounded-xl border border-slate-200 text-xs text-slate-600 space-y-1">
                            <span class="font-bold text-slate-800 uppercase text-[10px]">Pembahasan:</span>
                            <p class="leading-relaxed">{{ $aq->question->explanation }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection