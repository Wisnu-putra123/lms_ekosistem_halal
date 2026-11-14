@extends('layouts.app')

@section('title', 'Detail Attempt - ' . $user->name)
@section('header_title', 'Riwayat Attempt Siswa')

@section('content')
<div class="max-w-5xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header Navigation: Tombol Kembali di Sebelah Kiri Atas -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.courses.gradebook.show', $course->id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Matriks Rekap</span>
        </a>

        <span class="px-3 py-1.5 rounded-xl text-xs font-bold tracking-wider bg-[#28745B]/10 text-[#123F35] border border-[#28745B]/30 self-start sm:self-auto shadow-2xs">
            {{ $course->code }} &bull; Panel Pengajar
        </span>
    </div>

    <!-- 1. TOP CARD FULL GRADASI HIJAU -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] rounded-2xl border border-[#28745B]/40 shadow-md p-5 sm:p-8 text-white space-y-6">
        
        <!-- Badge Tipe & Judul -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-1.5">
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase backdrop-blur-xs">
                    {{ $assignment->type === 'quiz' ? 'Aktivitas Quiz' : 'Tugas Upload / Submission' }}
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug">{{ $assignment->title }}</h1>
            </div>

            <!-- Badge Info Siswa -->
            <div class="flex-shrink-0">
                <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] shadow-2xs">
                    {{ $user->name }}
                </span>
            </div>
        </div>

        <!-- Metric Cards Ringkasan -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Peserta / Siswa</span>
                <div class="text-xs font-bold text-white mt-1 truncate" title="{{ $user->name }} ({{ $user->email }})">
                    {{ $user->name }}
                </div>
                <div class="text-[10px] text-emerald-100/75 truncate mt-0.5">{{ $user->email }}</div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Tipe Aktivitas</span>
                <div class="text-xs font-bold text-white mt-1 uppercase">
                    {{ strtoupper($assignment->type) }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Passing Grade (KKM)</span>
                <div class="text-xs font-extrabold text-[#E7C982] mt-1">
                    {{ number_format($assignment->passing_score, 0) }} / {{ number_format($assignment->max_score, 0) }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Total Percobaan</span>
                <div class="text-xs font-bold text-white mt-1">
                    {{ $attempts->count() }}x Attempt
                </div>
            </div>
        </div>
    </div>

    <!-- 2. LIST TABEL ATTEMPT -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        
        <!-- Header Container Riwayat -->
        <div class="p-4 sm:px-6 border-b border-slate-300 bg-[#F7F5EE]/50 flex items-center justify-between">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-[#123F35]">Daftar Percobaan Pengerjaan ({{ $attempts->count() }})</h2>
                <p class="text-xs text-slate-500">Tinjau setiap riwayat pengerjaan, status pengumpulan, dan berikan penilaian.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <!-- Heading Tabel menggunakan bg-[#28745B] -->
                <thead class="bg-[#28745B] text-white border-b border-slate-300">
                    <tr class="text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                        <th class="py-3.5 px-4 sm:px-6 text-center min-w-[100px]">Attempt</th>
                        <th class="py-3.5 px-4 min-w-[180px]">Waktu Submit / Selesai</th>
                        <th class="py-3.5 px-4 text-center min-w-[140px]">Status</th>
                        <th class="py-3.5 px-4 text-center min-w-[110px]">Skor Akhir</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right min-w-[180px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300 text-xs text-slate-700">
                    @forelse($attempts as $attempt)
                        <tr class="hover:bg-[#F7F5EE]/40 transition">
                            <!-- Percobaan Ke- -->
                            <td class="py-3.5 px-4 sm:px-6 text-center font-bold text-[#123F35] whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 bg-[#123F35] text-white rounded-lg text-xs whitespace-nowrap">
                                    #{{ $attempt->attempt_number }}
                                </span>
                            </td>

                            <!-- Tanggal Submit -->
                            <td class="py-3.5 px-4 text-slate-600 font-medium whitespace-nowrap">
                                {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y, H:i:s WIB') : '-' }}
                            </td>

                            <!-- Status Attempt -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($attempt->status === 'graded' || $attempt->status === 'submitted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-[#28745B]/40 bg-[#28745B]/15 text-[#28745B] text-[10px] font-extrabold whitespace-nowrap">
                                        Selesai / Dinilai
                                    </span>
                                @elseif($attempt->status === 'expired')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-red-300 bg-red-100 text-red-700 text-[10px] font-extrabold whitespace-nowrap">
                                        Waktu Habis
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-[#E7C982] bg-[#E7C982]/40 text-[#123F35] text-[10px] font-extrabold whitespace-nowrap">
                                        In Progress
                                    </span>
                                @endif
                            </td>

                            <!-- Nilai -->
                            <td class="py-3.5 px-4 text-center font-extrabold text-sm whitespace-nowrap">
                                @if(is_null($attempt->score))
                                    <span class="text-slate-400 text-xs font-normal italic whitespace-nowrap">Belum Dinilai</span>
                                @else
                                    <span class="{{ $attempt->score >= $assignment->passing_score ? 'text-[#28745B]' : 'text-red-600' }} whitespace-nowrap">
                                        {{ number_format($attempt->score, 2) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Tombol Aksi Penilaian -->
                            <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                @if($assignment->type === 'submission')
                                    <a href="{{ route('teacher.submissions.grade', $attempt->id) }}" class="inline-flex items-center space-x-1 bg-[#123F35] hover:bg-[#28745B] text-white text-[11px] font-bold px-3.5 py-1.5 rounded-xl transition shadow-2xs whitespace-nowrap">
                                        <span>Beri Nilai / Feedback &rarr;</span>
                                    </a>
                                @else
                                    <a href="{{ route('teacher.quizzes.attempts.show', $attempt->id) }}" class="inline-flex items-center space-x-1 bg-[#28745B] hover:bg-[#123F35] text-white text-[11px] font-bold px-3.5 py-1.5 rounded-xl transition shadow-2xs whitespace-nowrap">
                                        <span>Lihat Jawaban Siswa &rarr;</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 text-xs italic">
                                Siswa belum pernah melakukan pengerjaan untuk item ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection