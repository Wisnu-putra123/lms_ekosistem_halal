@extends('layouts.app')

@section('title', 'Detail Jawaban Kuis - ' . ($attempt->enrollment->user->name ?? 'Siswa'))
@section('header_title', 'Detail Attempt Kuis Siswa')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-amber-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Kuis</span>
        </a>

        <form action="{{ route('teacher.quizzes.attempts.destroy', $attempt->id) }}" method="POST" onsubmit="return confirm('Hapus attempt pengerjaan siswa ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition">
                Hapus Attempt Ini
            </button>
        </form>
    </div>

    <!-- Informasi Siswa & Kuis -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-amber-100 text-amber-800 uppercase">
                        Attempt #{{ $attempt->attempt_number }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $attempt->enrollment->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                        Enrollment #{{ $attempt->enrollment_id }} ({{ $attempt->enrollment->status }})
                    </span>
                </div>
                <h1 class="text-xl font-bold text-slate-800 mt-1">{{ $assignment->title }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Siswa: <strong class="text-slate-800">{{ $attempt->enrollment->user->name ?? 'Siswa Tidak Ditemukan' }}</strong> ({{ $attempt->enrollment->user->email ?? '-' }})</p>
            </div>

            <div>
                @if($attempt->score >= $assignment->passing_score)
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        LULUS (Skor: {{ number_format($attempt->score, 2) }})
                    </span>
                @else
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                        BELUM LULUS (Skor: {{ number_format($attempt->score, 2) }})
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Total Nilai</span>
                <div class="text-sm font-bold text-slate-800 mt-1">
                    {{ number_format($attempt->score, 2) }} / {{ number_format($assignment->max_score, 0) }}
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Waktu Mulai</span>
                <div class="text-xs font-semibold text-slate-700 mt-1">
                    {{ $attempt->started_at ? \Carbon\Carbon::parse($attempt->started_at)->format('d M Y H:i') : '-' }}
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Waktu Selesai</span>
                <div class="text-xs font-semibold text-slate-700 mt-1">
                    {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y H:i') : '-' }}
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Status Pengerjaan</span>
                <div class="text-xs font-bold uppercase text-slate-800 mt-1">
                    {{ $attempt->status }}
                </div>
            </div>
        </div>
    </div>

    <!-- Lembar Jawaban Siswa -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <h2 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3">Rincian Jawaban Soal ({{ $attempt->attemptQuestions->count() }})</h2>

        <div class="space-y-6">
            @foreach($attempt->attemptQuestions as $index => $aq)
                <div class="p-5 rounded-2xl border space-y-3 {{ $aq->is_correct ? 'bg-emerald-50/30 border-emerald-200' : 'bg-red-50/30 border-red-200' }}">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200/60 pb-3">
                        <div class="flex items-start space-x-3">
                            <span class="w-6 h-6 rounded-lg text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5 {{ $aq->is_correct ? 'bg-emerald-600' : 'bg-red-500' }}">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <div class="text-xs font-bold text-slate-800 leading-relaxed">
                                    {!! nl2br(e($aq->question->question_text ?? 'Soal telah dihapus')) !!}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $aq->is_correct ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $aq->is_correct ? 'BENAR (+ ' . number_format($aq->points_earned, 2) . ' Poin)' : 'SALAH (0 Poin)' }}
                            </span>
                        </div>
                    </div>

                    <!-- Opsi-Opsi Jawaban -->
                    @if($aq->question && $aq->question->options)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pl-9">
                            @foreach($aq->question->options as $optIndex => $opt)
                                @php
                                    $isSelected = $aq->selected_option_id === $opt->id;
                                    $isCorrect = $opt->is_correct;
                                    
                                    $cardStyle = 'bg-white border-slate-200 text-slate-700';
                                    if ($isSelected && $isCorrect) {
                                        $cardStyle = 'bg-emerald-100 border-emerald-400 font-bold text-emerald-900';
                                    } elseif ($isSelected && !$isCorrect) {
                                        $cardStyle = 'bg-red-100 border-red-400 font-bold text-red-900';
                                    } elseif ($isCorrect) {
                                        $cardStyle = 'bg-emerald-50 border-emerald-300 font-semibold text-emerald-800';
                                    }
                                @endphp

                                <div class="p-2.5 rounded-xl border text-xs flex items-center justify-between {{ $cardStyle }}">
                                    <div class="flex items-center space-x-2 overflow-hidden pr-2">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold {{ $isSelected ? 'bg-slate-800 text-white' : 'bg-slate-200 text-slate-600' }}">
                                            {{ chr(65 + $optIndex) }}
                                        </span>
                                        <span class="truncate">{!! e($opt->option_text) !!}</span>
                                    </div>

                                    <div class="flex items-center space-x-1 flex-shrink-0">
                                        @if($isSelected)
                                            <span class="text-[9px] bg-slate-800 text-white px-2 py-0.5 rounded-md font-bold">Pilihan Siswa</span>
                                        @endif
                                        @if($isCorrect)
                                            <span class="text-[9px] bg-emerald-600 text-white px-2 py-0.5 rounded-md font-bold">Kunci</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection