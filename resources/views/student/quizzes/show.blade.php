@extends('layouts.app')

@section('title', $assignment->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Quiz')

@section('content')

<div class="max-w-5xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('student.courses.show', $assignment->course_id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>

        <span class="px-3 py-1.5 rounded-xl text-xs font-bold tracking-wider bg-[#28745B]/10 text-[#123F35] border border-[#28745B]/30 self-start sm:self-auto shadow-2xs">
            Pertemuan {{ $assignment->meeting->meeting_number }}: {{ $assignment->meeting->title }}
        </span>
    </div>

    <!-- Alert Flash Messages -->
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

    <!-- Alert jika Enrollment Sudah Complete atau Tenggat Waktu Berakhir -->
    @if($isEnrollmentCompleted)
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-[#123F35] rounded-2xl text-xs font-semibold flex items-center space-x-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Status pelatihan Anda telah <strong>Selesai (Completed)</strong>. Pengerjaan attempt kuis baru telah dikunci.</span>
        </div>
    @elseif($isPastDeadline)
        <div class="p-4 bg-red-50 border border-red-300 text-red-800 rounded-2xl text-xs font-semibold flex items-center space-x-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Tenggat waktu pengerjaan kuis ini telah berakhir. Anda tidak dapat lagi memulai atau melanjutkan attempt baru.</span>
        </div>
    @endif

    <!-- 1. TOP CARD FULL GRADASI HIJAU -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] rounded-2xl border border-[#28745B]/40 shadow-md p-5 sm:p-8 text-white space-y-6">
        
        <!-- Judul & Status Kelulusan -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-1.5">
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase backdrop-blur-xs">
                    Aktivitas Quiz
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug">{{ $assignment->title }}</h1>
            </div>

            <!-- Status Kelulusan Berdasarkan Best Score & Tenggat Waktu -->
            <div class="flex-shrink-0">
                @if($isPastDeadline && $attempts->isEmpty())
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-red-500/20 text-red-200 border border-red-400/50 backdrop-blur-xs">
                        Tenggat Waktu Berakhir
                    </span>
                @elseif($attempts->isEmpty())
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] shadow-2xs">
                        Belum Mengerjakan
                    </span>
                @elseif(!is_null($bestScore) && $bestScore >= $assignment->passing_score)
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] shadow-2xs">
                        ✓ LULUS (Skor Terbaik: {{ number_format($bestScore, 0) }})
                    </span>
                @elseif(!is_null($bestScore))
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-red-500/20 text-red-200 border border-red-400/50 backdrop-blur-xs">
                        BELUM LULUS (Skor Terbaik: {{ number_format($bestScore, 0) }})
                    </span>
                @else
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-white/15 text-white border border-white/30 backdrop-blur-xs">
                        Sedang Berlangsung
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 sm:gap-3">
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Durasi Timer</span>
                <div class="text-xs font-bold text-white mt-1">
                    {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' Menit' : 'Tanpa Batas' }}
                </div>
            </div>
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Jumlah Soal</span>
                <div class="text-xs font-bold text-white mt-1">{{ $quiz->question_count }} Soal</div>
            </div>
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Passing Grade</span>
                <div class="text-xs font-extrabold text-[#E7C982] mt-1">
                    {{ number_format($assignment->passing_score, 0) }} / {{ number_format($assignment->max_score, 0) }}
                </div>
            </div>
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Batas Attempt</span>
                <div class="text-xs font-bold text-white mt-1">
                    {{ $assignment->max_attempts ? $attempts->count() . ' / ' . $assignment->max_attempts . 'x Attempt' : $attempts->count() . 'x (Tanpa Batas)' }}
                </div>
            </div>
            <div class="col-span-2 sm:col-span-1 p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Tenggat Waktu</span>
                <div class="text-xs font-bold {{ $isPastDeadline ? 'text-red-300' : 'text-[#E7C982]' }} mt-1">
                    {{ $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('d M Y H:i') : 'Tanpa Batas' }}
                </div>
            </div>
        </div>

        <!-- Petunjuk Pengerjaan -->
        @if($assignment->description)
            <div class="pt-4 border-t border-white/20 space-y-2">
                <h3 class="text-xs font-bold text-[#E7C982] uppercase tracking-wider flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Petunjuk Pengerjaan</span>
                </h3>
                <div class="text-xs sm:text-sm text-emerald-50 bg-white/10 p-4 rounded-xl border border-white/20 leading-relaxed whitespace-pre-line backdrop-blur-xs">
                    {{ $assignment->description }}
                </div>
            </div>
        @endif

        <!-- Action Start / Continue Button -->
        <div class="pt-4 border-t border-white/20 flex items-center justify-end">
            @if($isEnrollmentCompleted)
                <!-- Kondisi ketika status enrollment sudah completed -->
                <button disabled class="bg-white/15 text-emerald-100/70 border border-white/20 px-6 py-2.5 rounded-xl text-xs font-bold cursor-not-allowed flex items-center space-x-2">
                    <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Pelatihan Selesai</span>
                </button>
            @elseif($isPastDeadline)
                <!-- Kondisi ketika tenggat waktu kuis sudah lewat -->
                <button disabled class="bg-white/15 text-emerald-100/70 border border-white/20 px-6 py-2.5 rounded-xl text-xs font-bold cursor-not-allowed flex items-center space-x-2">
                    <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Waktu Pengerjaan Kuis Telah Berakhir</span>
                </button>
            @elseif(($quiz->questions_count ?? $quiz->questions->count()) === 0)
                <!-- Kondisi ketika kuis belum memiliki soal -->
                <button disabled class="bg-white/15 text-emerald-100/70 border border-white/20 px-6 py-2.5 rounded-xl text-xs font-bold cursor-not-allowed flex items-center space-x-2">
                    <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Soal Kuis Belum Tersedia</span>
                </button>
            @elseif($activeAttempt)
                <!-- Kondisi ketika ada attempt aktif yang belum selesai -->
                <a href="{{ route('student.quizzes.attempt', [$assignment->id, $activeAttempt->id]) }}" class="bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] px-6 py-2.5 rounded-xl text-xs font-extrabold shadow-2xs transition flex items-center space-x-2">
                    <span>Lanjutkan Attempt #{{ $activeAttempt->attempt_number }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            @elseif($canStartNewAttempt)
                <!-- Kondisi ketika siswa masih bisa memulai attempt baru -->
                <form action="{{ route('student.quizzes.start', $assignment->id) }}" method="POST" onsubmit="return confirm('Mulai pengerjaan Quiz sekarang?')">
                    @csrf
                    <button type="submit" class="bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] px-6 py-2.5 rounded-xl text-xs font-extrabold shadow-2xs transition flex items-center space-x-2">
                        <span>Mulai Mengerjakan Quiz</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>
            @else
                <!-- Kondisi ketika kuota attempt siswa sudah habis -->
                <button disabled class="bg-white/15 text-emerald-100/70 border border-white/20 px-6 py-2.5 rounded-xl text-xs font-bold cursor-not-allowed">
                    Batas Attempt Telah Habis
                </button>
            @endif
        </div>
    </div>

    <!-- 2. RIWAYAT ATTEMPT SISWA -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        
        <!-- Header Container Riwayat -->
        <div class="p-4 sm:px-6 border-b border-slate-300 bg-[#F7F5EE]/50 flex items-center justify-between">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-[#123F35]">Riwayat Percobaan ({{ $attempts->count() }})</h2>
                <p class="text-xs text-slate-500">Nilai tertinggi dari seluruh percobaan akan digunakan sebagai nilai akhir kuis.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <!-- Ketentuan 2: Warna bg-[#28745B] diterapkan pada heading tabel saja -->
                <thead class="bg-[#28745B] text-white border-b border-slate-300">
                    <tr class="text-[11px] font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-6">Attempt</th>
                        <th class="py-3.5 px-4">Waktu Mulai</th>
                        <th class="py-3.5 px-4">Waktu Selesai</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Nilai</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300 text-xs text-slate-700">
                    @forelse($attempts as $att)
                        <tr class="hover:bg-[#F7F5EE]/40 transition">
                            <td class="py-3.5 px-4 sm:px-6 font-bold text-[#123F35]">
                                <span class="px-2.5 py-1 bg-[#123F35] text-white rounded-lg text-xs">
                                    Attempt #{{ $att->attempt_number }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ \Carbon\Carbon::parse($att->started_at)->format('d M Y H:i') }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $att->submitted_at ? \Carbon\Carbon::parse($att->submitted_at)->format('d M Y H:i') : '-' }}</td>
                            <td class="py-3.5 px-4">
                                @if($att->status === 'in_progress')
                                    <span class="px-2.5 py-0.5 rounded-lg border border-[#E7C982] bg-[#E7C982]/40 text-[#123F35] text-[10px] font-extrabold">Sedang Dikerjakan</span>
                                @elseif($att->score >= $assignment->passing_score)
                                    <span class="px-2.5 py-0.5 rounded-lg border border-[#28745B]/40 bg-[#28745B]/15 text-[#28745B] text-[10px] font-extrabold">Lulus</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-lg border border-red-300 bg-red-100 text-red-700 text-[10px] font-extrabold">Belum Lulus</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-[#123F35]">
                                {{ $att->score !== null ? number_format($att->score, 2) : '-' }}
                            </td>
                            <td class="py-3.5 px-4 sm:px-6 text-right">
                                @if($att->status !== 'in_progress')
                                    <a href="{{ route('student.quizzes.result', [$assignment->id, $att->id]) }}" class="text-xs font-extrabold text-[#28745B] hover:text-[#123F35] hover:underline">
                                        Lihat Hasil & Pembahasan →
                                    </a>
                                @elseif(!$isQuizLocked)
                                    <a href="{{ route('student.quizzes.attempt', [$assignment->id, $att->id]) }}" class="text-xs font-extrabold text-amber-600 hover:underline">
                                        Lanjutkan →
                                    </a>
                                @else
                                    <span class="text-xs font-semibold text-slate-400 cursor-not-allowed">
                                        {{ $isEnrollmentCompleted ? 'Pelatihan Selesai' : 'Waktu Habis' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-xs text-slate-400 italic">Belum ada riwayat pengerjaan quiz.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection