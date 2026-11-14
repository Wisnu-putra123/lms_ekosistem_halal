@extends('layouts.app')

@section('title', 'Detail Jawaban Kuis - ' . ($attempt->enrollment->user->name ?? 'Siswa'))
@section('header_title', 'Detail Attempt Kuis Siswa')

@section('content')
<div class="max-w-5xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Kuis</span>
        </a>

        <form action="{{ route('teacher.quizzes.attempts.destroy', $attempt->id) }}" method="POST" onsubmit="return confirm('Hapus attempt pengerjaan siswa ini?')" class="self-start sm:self-auto">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center space-x-1.5 bg-red-600 hover:bg-red-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-2xs transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Hapus Attempt Ini</span>
            </button>
        </form>
    </div>

    <!-- Top Card Full Hijau Tua: Informasi Siswa & Kuis -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] rounded-2xl border border-slate-300 shadow-2xs p-5 sm:p-8 text-white space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-white/15 pb-5">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase">
                        Attempt #{{ $attempt->attempt_number }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase {{ $attempt->enrollment->status === 'active' ? 'bg-[#28745B] text-white border border-white/20' : 'bg-white/15 text-emerald-100 border border-white/20' }}">
                        Enrollment #{{ $attempt->enrollment_id }} ({{ $attempt->enrollment->status }})
                    </span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-white">{{ $assignment->title }}</h1>
                <p class="text-xs text-emerald-100/85 mt-1">
                    Siswa: <strong class="text-white">{{ $attempt->enrollment->user->name ?? 'Siswa Tidak Ditemukan' }}</strong> ({{ $attempt->enrollment->user->email ?? '-' }})
                </p>
            </div>

            <div class="flex-shrink-0 self-start">
                @if($attempt->score >= $assignment->passing_score)
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] border border-white/20 shadow-2xs">
                        LULUS &bull; Skor {{ number_format($attempt->score, 2) }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-red-600 text-white border border-red-400 shadow-2xs">
                        BELUM LULUS &bull; Skor {{ number_format($attempt->score, 2) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="p-3.5 bg-white/10 border border-white/15 rounded-xl backdrop-blur-xs">
                <span class="block text-[10px] font-extrabold text-emerald-100/75 uppercase tracking-wider">Total Nilai</span>
                <div class="text-sm sm:text-base font-extrabold text-[#E7C982] mt-1">
                    {{ number_format($attempt->score, 2) }} <span class="text-xs font-semibold text-emerald-100/80">/ {{ number_format($assignment->max_score, 0) }}</span>
                </div>
            </div>
            <div class="p-3.5 bg-white/10 border border-white/15 rounded-xl backdrop-blur-xs">
                <span class="block text-[10px] font-extrabold text-emerald-100/75 uppercase tracking-wider">Waktu Mulai</span>
                <div class="text-xs font-bold text-white mt-1.5">
                    {{ $attempt->started_at ? \Carbon\Carbon::parse($attempt->started_at)->format('d M Y, H:i') : '-' }}
                </div>
            </div>
            <div class="p-3.5 bg-white/10 border border-white/15 rounded-xl backdrop-blur-xs">
                <span class="block text-[10px] font-extrabold text-emerald-100/75 uppercase tracking-wider">Waktu Selesai</span>
                <div class="text-xs font-bold text-white mt-1.5">
                    {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y, H:i') : '-' }}
                </div>
            </div>
            <div class="p-3.5 bg-white/10 border border-white/15 rounded-xl backdrop-blur-xs">
                <span class="block text-[10px] font-extrabold text-emerald-100/75 uppercase tracking-wider">Status Pengerjaan</span>
                <div class="text-xs font-extrabold uppercase text-[#E7C982] mt-1.5">
                    {{ $attempt->status }}
                </div>
            </div>
        </div>
    </div>

    <!-- Lembar Jawaban Siswa -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <!-- Header Sub-Section -->
        <div class="bg-[#28745B] p-4 sm:px-6 border-b border-slate-300 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-white">Rincian Jawaban Soal ({{ $attempt->attemptQuestions->count() }} Butir)</h2>
                <p class="text-[11px] text-emerald-100/85">Tinjauan perbandingan pilihan jawaban siswa terhadap kunci jawaban.</p>
            </div>
            <div class="flex items-center space-x-2 text-[11px] font-bold">
                <span class="px-2.5 py-1 rounded-lg bg-white/15 border border-white/20">
                    Benar: {{ $attempt->attemptQuestions->where('is_correct', true)->count() }}
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-red-600/80 border border-red-400/50">
                    Salah: {{ $attempt->attemptQuestions->where('is_correct', false)->count() }}
                </span>
            </div>
        </div>

        <div class="p-5 sm:p-6 space-y-5">
            @foreach($attempt->attemptQuestions as $index => $aq)
                <div class="p-4 sm:p-5 rounded-2xl border space-y-4 shadow-2xs {{ $aq->is_correct ? 'bg-emerald-50/40 border-[#28745B]/40' : 'bg-red-50/40 border-red-300' }}">
                    
                    <!-- Header Pertanyaan -->
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 border-b border-slate-300/80 pb-3.5">
                        <div class="flex items-start space-x-3">
                            <span class="w-7 h-7 rounded-xl text-white text-xs font-extrabold flex items-center justify-center flex-shrink-0 mt-0.5 shadow-2xs {{ $aq->is_correct ? 'bg-[#28745B]' : 'bg-red-600' }}">
                                {{ $index + 1 }}
                            </span>
                            <div class="space-y-2">
                                <div class="text-xs sm:text-sm font-bold text-[#123F35] leading-relaxed">
                                    {!! nl2br(e($aq->question->question_text ?? 'Soal telah dihapus')) !!}
                                </div>

                                @if($aq->question && $aq->question->media)
                                    <div class="pt-1">
                                        @if(Str::startsWith($aq->question->media->mime_type, 'image/'))
                                            <a href="{{ asset('storage/' . $aq->question->media->file_path) }}" target="_blank" rel="noopener noreferrer" class="inline-block">
                                                <img src="{{ asset('storage/' . $aq->question->media->file_path) }}" alt="Media Soal" class="max-h-44 rounded-xl border border-slate-300 shadow-2xs">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/' . $aq->question->media->file_path) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center space-x-1.5 px-3 py-1 bg-white border border-slate-300 rounded-lg text-[11px] font-bold text-[#123F35] hover:bg-[#123F35] hover:text-white transition shadow-2xs">
                                                <span>Lihat Lampiran Soal: {{ $aq->question->media->file_name }}</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 flex-shrink-0 self-start sm:self-auto">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold border {{ $aq->is_correct ? 'bg-[#28745B]/15 text-[#28745B] border-[#28745B]/40' : 'bg-red-100 text-red-700 border-red-300' }}">
                                {{ $aq->is_correct ? 'BENAR (+' . number_format($aq->points_earned, 2) . ' Poin)' : 'SALAH (0 Poin)' }}
                            </span>
                        </div>
                    </div>

                    <!-- Opsi-Opsi Jawaban (Simetris Kiri & Kanan) -->
                    @if($aq->question && $aq->question->options)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($aq->question->options as $optIndex => $opt)
                                @php
                                    $isSelected = $aq->selected_option_id === $opt->id;
                                    $isCorrect = $opt->is_correct;
                                    
                                    $cardStyle = 'bg-white border-slate-300 text-slate-700';
                                    $badgeStyle = 'bg-[#F7F5EE] border border-slate-300 text-[#123F35]';

                                    if ($isSelected && $isCorrect) {
                                        $cardStyle = 'bg-emerald-100/90 border-[#28745B] font-bold text-[#123F35]';
                                        $badgeStyle = 'bg-[#28745B] text-white';
                                    } elseif ($isSelected && !$isCorrect) {
                                        $cardStyle = 'bg-red-100/90 border-red-400 font-bold text-red-900';
                                        $badgeStyle = 'bg-red-600 text-white';
                                    } elseif ($isCorrect) {
                                        $cardStyle = 'bg-emerald-50 border-[#28745B]/60 font-semibold text-[#123F35]';
                                        $badgeStyle = 'bg-[#28745B] text-white';
                                    }
                                @endphp

                                <div class="p-3 rounded-xl border text-xs flex items-start justify-between gap-2 shadow-2xs {{ $cardStyle }}">
                                    <div class="flex items-start space-x-2.5 min-w-0">
                                        <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-extrabold flex-shrink-0 mt-0.5 {{ $badgeStyle }}">
                                            {{ chr(65 + $optIndex) }}
                                        </span>
                                        <div class="min-w-0 space-y-1.5">
                                            <span class="block break-words leading-relaxed">{!! e($opt->option_text) !!}</span>

                                            @if($opt->media)
                                                @if(Str::startsWith($opt->media->mime_type, 'image/'))
                                                    <a href="{{ asset('storage/' . $opt->media->file_path) }}" target="_blank" rel="noopener noreferrer" class="inline-block">
                                                        <img src="{{ asset('storage/' . $opt->media->file_path) }}" alt="Media Opsi" class="max-h-24 rounded-lg border border-slate-300">
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/' . $opt->media->file_path) }}" target="_blank" rel="noopener noreferrer" class="inline-block text-[10px] font-bold text-[#28745B] underline">
                                                        Lihat Lampiran Opsi
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                        @if($isSelected)
                                            <span class="text-[9px] bg-[#123F35] text-[#E7C982] px-2 py-0.5 rounded-md font-extrabold whitespace-nowrap">
                                                Pilihan Siswa
                                            </span>
                                        @endif
                                        @if($isCorrect)
                                            <span class="text-[9px] bg-[#28745B] text-white px-2 py-0.5 rounded-md font-extrabold whitespace-nowrap">
                                                Kunci
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Pembahasan (Simetris Kiri & Kanan) -->
                    @if($aq->question && $aq->question->explanation)
                        <div class="p-3.5 bg-[#F7F5EE] border border-slate-300 rounded-xl text-xs text-slate-700">
                            <span class="font-extrabold text-[#123F35] uppercase tracking-wider text-[10px] block mb-1">Pembahasan Soal:</span>
                            <p class="leading-relaxed">{{ $aq->question->explanation }}</p>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection 