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
            <button type="submit" class="inline-flex items-center space-x-1.5 bg-red-50 hover:bg-red-600 text-red-700 hover:text-white border border-red-300 px-3.5 py-2 rounded-xl text-xs font-bold shadow-2xs transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Hapus Attempt Ini</span>
            </button>
        </form>
    </div>

    <!-- Top Card Full Hijau Tua: Informasi Siswa & Kuis -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] rounded-2xl border border-[#28745B]/40 shadow-md p-5 sm:p-8 text-white space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-white/20 pb-5">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase backdrop-blur-xs">
                        Attempt #{{ $attempt->attempt_number }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase {{ $attempt->enrollment->status === 'active' ? 'bg-[#28745B] text-white border border-white/20' : 'bg-white/15 text-emerald-100 border border-white/20' }}">
                        Enrollment #{{ $attempt->enrollment_id }} ({{ $attempt->enrollment->status }})
                    </span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-white leading-snug">{{ $assignment->title }}</h1>
                <p class="text-xs text-emerald-100/85 mt-1">
                    Siswa: <strong class="text-white">{{ $attempt->enrollment->user->name ?? 'Siswa Tidak Ditemukan' }}</strong> ({{ $attempt->enrollment->user->email ?? '-' }})
                </p>
            </div>

            <div class="flex-shrink-0 self-start">
                @if($attempt->score >= $assignment->passing_score)
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] shadow-2xs">
                        ✓ LULUS &bull; Skor {{ number_format($attempt->score, 2) }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-red-500/20 text-red-200 border border-red-400/50 backdrop-blur-xs">
                        BELUM LULUS &bull; Skor {{ number_format($attempt->score, 2) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="block text-[10px] font-extrabold text-emerald-100/80 uppercase tracking-wider">Total Nilai</span>
                <div class="text-lg sm:text-xl font-black {{ $attempt->score >= $assignment->passing_score ? 'text-[#E7C982]' : 'text-red-200' }} mt-1">
                    {{ number_format($attempt->score, 2) }} <span class="text-xs font-normal text-emerald-100/75">/ {{ number_format($assignment->max_score, 0) }}</span>
                </div>
            </div>
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="block text-[10px] font-extrabold text-emerald-100/80 uppercase tracking-wider">Waktu Mulai</span>
                <div class="text-xs font-bold text-white mt-1.5">
                    {{ $attempt->started_at ? \Carbon\Carbon::parse($attempt->started_at)->format('d M Y, H:i') : '-' }}
                </div>
            </div>
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="block text-[10px] font-extrabold text-emerald-100/80 uppercase tracking-wider">Waktu Selesai</span>
                <div class="text-xs font-bold text-white mt-1.5">
                    {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y, H:i') : '-' }}
                </div>
            </div>
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="block text-[10px] font-extrabold text-emerald-100/80 uppercase tracking-wider">Status Pengerjaan</span>
                <div class="text-xs font-extrabold uppercase text-white mt-1.5">
                    {{ $attempt->status }}
                </div>
            </div>
        </div>
    </div>

    <!-- Lembar Jawaban Siswa -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <!-- Header Sub-Section -->
        <div class="bg-[#28745B] p-4 sm:px-6 border-b border-slate-300 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-white">Rincian Jawaban Soal ({{ $totalQuestions }} Butir)</h2>
                <p class="text-xs text-emerald-100/85">Tinjauan perbandingan pilihan jawaban siswa terhadap kunci jawaban.</p>
            </div>
            <div class="flex items-center space-x-2 text-[11px] font-bold self-start sm:self-auto">
                <span class="px-2.5 py-1 rounded-lg bg-white/15 text-white border border-white/25">
                    Benar: {{ $correctCount }}
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-red-500/25 text-red-100 border border-red-300/40">
                    Salah: {{ $incorrectCount }}
                </span>
            </div>
        </div>

        <div class="p-4 sm:p-8 space-y-5">
            @forelse($attemptQuestions as $index => $aq)
                <div class="rounded-2xl border border-slate-300 overflow-hidden shadow-2xs {{ $aq->is_correct ? 'bg-emerald-50/40' : 'bg-red-50/40' }}">
                    
                    <!-- Header Kartu Soal -->
                    <div class="mx-4 sm:mx-5 py-3.5 border-b border-slate-300 flex flex-wrap items-center justify-between gap-2">
                        <span class="px-2.5 py-1 bg-[#123F35] text-white rounded-lg font-bold text-xs">
                            Soal #{{ $attemptQuestions->firstItem() + $index }}
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
                                {!! nl2br(e($aq->question->question_text ?? 'Soal telah dihapus')) !!}
                            </div>

                            @if($aq->question && $aq->question->media)
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

                        <!-- Opsi-Opsi Jawaban -->
                        @if($aq->question && $aq->question->options)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($aq->question->options as $optIndex => $opt)
                                    @php
                                        $isSelected = ($aq->selected_option_id === $opt->id);
                                        $isCorrect = $opt->is_correct;
                                        
                                        $cardStyle = 'bg-white border-slate-300 text-slate-700';
                                        $badgeStyle = 'bg-[#F7F5EE] border border-slate-300 text-[#123F35]';

                                        if ($isSelected && $isCorrect) {
                                            $cardStyle = 'bg-emerald-100/90 border-[#28745B]/60 font-bold text-[#123F35]';
                                            $badgeStyle = 'bg-[#28745B] text-white';
                                        } elseif ($isSelected && !$isCorrect) {
                                            $cardStyle = 'bg-red-100/90 border-red-300 font-bold text-red-900';
                                            $badgeStyle = 'bg-red-100 border border-red-300 text-red-700';
                                        } elseif ($isCorrect) {
                                            $cardStyle = 'bg-emerald-50 border-[#28745B]/50 font-semibold text-[#123F35]';
                                            $badgeStyle = 'bg-[#28745B] text-white';
                                        }
                                    @endphp

                                    <div class="p-3 sm:p-3.5 rounded-xl border text-xs space-y-2.5 shadow-2xs {{ $cardStyle }}">
                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                                            <div class="flex items-start space-x-2.5 min-w-0">
                                                <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-extrabold flex-shrink-0 mt-0.5 {{ $badgeStyle }}">
                                                    {{ chr(65 + $optIndex) }}
                                                </span>
                                                @if(filled($opt->option_text))
                                                    <span class="break-words leading-relaxed">{!! nl2br(e($opt->option_text)) !!}</span>
                                                @else
                                                    <span class="italic text-slate-400 font-normal">(Opsi Media)</span>
                                                @endif
                                            </div>

                                            @if($isSelected || $isCorrect)
                                                <div class="flex flex-wrap items-center gap-1.5 flex-shrink-0 pl-7 sm:pl-0">
                                                    @if($isSelected && !$isCorrect)
                                                        <span class="text-[10px] bg-red-100 text-red-700 border border-red-300 px-2 py-0.5 rounded-md font-extrabold whitespace-nowrap">
                                                            Jawaban Siswa
                                                        </span>
                                                    @elseif($isSelected && $isCorrect)
                                                        <span class="text-[10px] bg-[#123F35] text-[#E7C982] px-2 py-0.5 rounded-md font-extrabold whitespace-nowrap">
                                                            Pilihan Siswa
                                                        </span>
                                                    @endif
                                                    @if($isCorrect)
                                                        <span class="text-[10px] bg-[#28745B] text-white px-2 py-0.5 rounded-md font-bold whitespace-nowrap">
                                                            Kunci Jawaban
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Media Opsi Jawaban -->
                                        @if($opt->media)
                                            @php
                                                $optMediaUrl  = asset('storage/' . $opt->media->file_path);
                                                $optMediaType = $opt->media->type ?? '';
                                                $optMimeType  = $opt->media->mime_type ?? '';
                                            @endphp
                                            <div class="pl-0 sm:pl-7">
                                                @if($optMediaType === 'image' || str_contains($optMimeType, 'image'))
                                                    <a href="{{ $optMediaUrl }}" target="_blank" rel="noopener noreferrer"
                                                       class="inline-block bg-white p-1.5 rounded-lg border border-slate-300 hover:border-[#28745B] transition max-w-full">
                                                        <img src="{{ $optMediaUrl }}" alt="{{ $opt->media->file_name }}"
                                                             class="w-full sm:w-auto max-h-36 rounded-md object-contain">
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
                        @endif

                        <!-- Pembahasan -->
                        @if($aq->question && $aq->question->explanation)
                            <div class="p-3.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-700 space-y-1">
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
            @empty
                <div class="p-10 text-center text-xs text-slate-500 bg-[#F7F5EE]/50 rounded-2xl border border-dashed border-slate-300">
                    Tidak ada rincian soal pada attempt ini.
                </div>
            @endforelse
        </div>

        <!-- Navigasi Paginasi Rincian Jawaban -->
        @if($attemptQuestions->hasPages())
            <div class="p-4 sm:px-6 border-t border-slate-300 bg-[#F7F5EE]/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div>
                    Menampilkan <span class="font-bold text-[#123F35]">{{ $attemptQuestions->firstItem() }}</span> - 
                    <span class="font-bold text-[#123F35]">{{ $attemptQuestions->lastItem() }}</span> dari 
                    <span class="font-bold text-[#123F35]">{{ $attemptQuestions->total() }}</span> butir soal
                </div>
                <div>
                    {{ $attemptQuestions->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection