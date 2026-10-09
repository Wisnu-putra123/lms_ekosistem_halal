@extends('layouts.app')

@section('title', $assignment->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Quiz')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.courses.show', $assignment->course_id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-emerald-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
            Pertemuan {{ $assignment->meeting->meeting_number }}: {{ $assignment->meeting->title }}
        </span>
    </div>

    <!-- Alert Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Informasi Utama Quiz -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-amber-100 text-amber-800 uppercase">
                    Aktivitas Quiz
                </span>
                <h1 class="text-xl font-bold text-slate-800 mt-1">{{ $assignment->title }}</h1>
            </div>

            <!-- Status Kelulusan Berdasarkan Best Score -->
            <div>
                @if($attempts->isEmpty())
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Belum Mengerjakan</span>
                @elseif(!is_null($bestScore) && $bestScore >= $assignment->passing_score)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">LULUS (Skor Terbaik: {{ number_format($bestScore, 0) }})</span>
                @elseif(!is_null($bestScore))
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">BELUM LULUS (Skor Terbaik: {{ number_format($bestScore, 0) }})</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Sedang Berlangsung</span>
                @endif
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Durasi Timer</span>
                <div class="text-xs font-bold text-slate-800 mt-1">
                    {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' Menit' : 'Tanpa Batas' }}
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Jumlah Soal</span>
                <div class="text-xs font-bold text-slate-800 mt-1">{{ $quiz->question_count }} Soal</div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Passing Grade</span>
                <div class="text-xs font-bold text-emerald-700 mt-1">{{ number_format($assignment->passing_score, 0) }} / {{ number_format($assignment->max_score, 0) }}</div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Batas Attempt</span>
                <div class="text-xs font-bold text-slate-800 mt-1">
                    {{ $assignment->max_attempts ? $attempts->count() . ' / ' . $assignment->max_attempts . 'x Attempt' : $attempts->count() . 'x Attempt' }}
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Tenggat Waktu</span>
                <div class="text-xs font-semibold text-slate-700 mt-1">
                    {{ $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('d M Y H:i') : 'Tanpa Batas' }}
                </div>
            </div>
        </div>

        <!-- Petunjuk Pengerjaan -->
        @if($assignment->description)
            <div class="space-y-1">
                <h3 class="text-xs font-bold text-slate-700 uppercase">Petunjuk Pengerjaan:</h3>
                <div class="text-xs text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100 leading-relaxed whitespace-pre-line">
                    {{ $assignment->description }}
                </div>
            </div>
        @endif

        <!-- Action Start / Continue Button -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
            @if($activeAttempt)
                <a href="{{ route('student.quizzes.attempt', [$assignment->id, $activeAttempt->id]) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition flex items-center space-x-2">
                    <span>Lanjutkan Attempt #{{ $activeAttempt->attempt_number }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            @elseif($canStartNewAttempt)
                <form action="{{ route('student.quizzes.start', $assignment->id) }}" method="POST" onsubmit="return confirm('Mulai pengerjaan Quiz sekarang?')">
                    @csrf
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition flex items-center space-x-2">
                        <span>Mulai Mengerjakan Quiz</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>
            @else
                <button disabled class="bg-slate-200 text-slate-500 px-6 py-2.5 rounded-xl text-xs font-bold cursor-not-allowed">
                    Batas Attempt Telah Habis
                </button>
            @endif
        </div>
    </div>

    <!-- Riwayat Attempt Siswa -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
        <h2 class="text-base font-bold text-slate-800">Riwayat Percobaan ({{ $attempts->count() }})</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase">
                        <th class="py-3 px-4">Attempt</th>
                        <th class="py-3 px-4">Waktu Mulai</th>
                        <th class="py-3 px-4">Waktu Selesai</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Nilai</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($attempts as $att)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-800">Attempt #{{ $att->attempt_number }}</td>
                            <td class="py-3.5 px-4">{{ \Carbon\Carbon::parse($att->started_at)->format('d M Y H:i') }}</td>
                            <td class="py-3.5 px-4">{{ $att->submitted_at ? \Carbon\Carbon::parse($att->submitted_at)->format('d M Y H:i') : '-' }}</td>
                            <td class="py-3.5 px-4">
                                @if($att->status === 'in_progress')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Sedang Dikerjakan</span>
                                @elseif($att->score >= $assignment->passing_score)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Lulus</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Belum Lulus</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold">
                                {{ $att->score !== null ? number_format($att->score, 2) : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                @if($att->status !== 'in_progress')
                                    <a href="{{ route('student.quizzes.result', [$assignment->id, $att->id]) }}" class="text-xs font-bold text-emerald-600 hover:underline">
                                        Lihat Hasil & Pembahasan
                                    </a>
                                @else
                                    <a href="{{ route('student.quizzes.attempt', [$assignment->id, $att->id]) }}" class="text-xs font-bold text-amber-600 hover:underline">
                                        Lanjutkan
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-slate-400">Belum ada riwayat pengerjaan quiz.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection