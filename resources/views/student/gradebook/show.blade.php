@extends('layouts.app')

@section('title', 'Rekap Nilai Saya - ' . $course->title)
@section('header_title', 'Rekap Nilai Pelatihan')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('student.courses.show', $course->id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-600 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-2xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Course</span>
        </a>
    </div>
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-4 sm:p-6 md:p-8 rounded-2xl border border-[#28745B]/30 shadow-md flex flex-col justify-between gap-4 sm:gap-6 text-white relative overflow-hidden">
        
        <!-- Detail Kursus & Selector Enrollment -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 sm:gap-6 relative z-10 w-full min-w-0">
            
            <!-- Informasi Kursus -->
            <div class="space-y-2 min-w-0 flex-1 w-full">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-white border border-white/20 uppercase backdrop-blur-xs">
                        {{ $course->code }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase border {{ $currentEnrollment->status === 'active' ? 'bg-[#28745B] text-white border-white/20' : 'bg-[#E7C982] text-[#123F35] border-[#E7C982]' }}">
                        Enrollment: {{ $currentEnrollment->status }}
                    </span>
                </div>
                <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-white leading-snug break-words">Rekap Nilai: {{ $course->title }}</h1>
                <p class="text-xs text-emerald-100/80 max-w-3xl leading-relaxed">Pantau capaian nilai, batas KKM, dan riwayat pengerjaan Anda.</p>
            </div>

            <!-- Dropdown Switcher Riwayat Enrollment -->
            @if($allEnrollments->count() > 0)
                <div class="w-full md:w-auto flex-shrink-0 pt-1 md:pt-0">
                    <form method="GET" action="{{ route('student.courses.gradebook', $course->id) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 bg-white/10 p-2 sm:p-1.5 sm:px-3 rounded-xl border border-white/20 backdrop-blur-xs w-full sm:w-auto">
                        <label class="text-[10px] sm:text-[11px] font-bold text-emerald-100 uppercase flex-shrink-0">Enrollment:</label>
                        <select name="enrollment_id" onchange="this.form.submit()" class="text-xs bg-[#123F35] border border-white/20 rounded-lg px-2.5 py-1.5 font-semibold text-white focus:outline-none focus:ring-2 focus:ring-[#E7C982] transition cursor-pointer w-full sm:w-auto min-w-0 truncate">
                            @foreach($allEnrollments as $en)
                                <option value="{{ $en->id }}" {{ $currentEnrollment->id === $en->id ? 'selected' : '' }}>
                                    ID #{{ $en->id }} ({{ ucfirst($en->status) }} - {{ \Carbon\Carbon::parse($en->enrolled_at)->format('d M Y') }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif

        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400">Total Tugas & Quiz</span>
            <div class="text-lg font-bold text-[#123F35]">{{ $assignments->count() }} Item</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400">Item Tuntas (Lulus KKM)</span>
            <div class="text-lg font-bold text-[#28745B]">{{ $totalPassed }} / {{ $assignments->count() }} Item</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400">Rata-Rata Nilai Saya</span>
            <div class="text-lg font-extrabold text-[#123F35]">{{ number_format($averageScore, 2) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400">Tanggal Mendaftar</span>
            <div class="text-xs font-semibold text-slate-700 mt-1">
                {{ \Carbon\Carbon::parse($currentEnrollment->enrolled_at)->format('d M Y, H:i') }}
            </div>
        </div>
    </div>

    <!-- Matriks Rekap Nilai Student -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                
                {{-- 
                ===================================================================
                VERSI HEADING 1: BACKGROUND CREAM (#F7F5EE)
                ===================================================================
                <thead>
                    <tr class="bg-[#F7F5EE] border-b border-slate-200/80 text-[#123F35] uppercase tracking-wider">
                        <th class="p-4 font-bold min-w-[200px]">Modul / Nama Tugas & Kuis</th>
                        <th class="p-4 font-bold text-center min-w-[100px]">Tipe Item</th>
                        <th class="p-4 font-bold text-center min-w-[100px]">KKM / Max</th>
                        <th class="p-4 font-bold text-center min-w-[120px]">Nilai Terbaik Saya</th>
                        <th class="p-4 font-bold text-center min-w-[120px]">Status Kelulusan</th>
                        <th class="p-4 font-bold text-center min-w-[100px]">Aksi</th>
                    </tr>
                </thead>
                --}}

                <!-- VERSI HEADING 2: BACKGROUND HIJAU (#28745B) - ACTIVE DEFAULT -->
                <thead>
                    <tr class="bg-[#28745B] text-white uppercase tracking-wider">
                        <th class="p-4 font-bold min-w-[200px]">Modul / Nama Tugas & Kuis</th>
                        <th class="p-4 font-bold text-center min-w-[100px]">Tipe Item</th>
                        <th class="p-4 font-bold text-center min-w-[100px]">KKM / Max</th>
                        <th class="p-4 font-bold text-center min-w-[120px]">Nilai Terbaik Saya</th>
                        <th class="p-4 font-bold text-center min-w-[120px]">Status Kelulusan</th>
                        <th class="p-4 font-bold text-center min-w-[100px]">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($assignments as $assignment)
                        @php
                            $scoreData = $scoresData[$assignment->id] ?? ['score' => null, 'is_passed' => false, 'attempt_count' => 0];
                            $score = $scoreData['score'];
                            $kkm = $assignment->passing_score;

                            if (is_null($score)) {
                                $bgClass = 'bg-slate-50 border-slate-200/80 text-slate-400';
                                $statusBadge = '<span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs">Belum Ada Attempt</span>';
                            } elseif ($score < $kkm) {
                                $bgClass = 'bg-red-50 border-red-200 text-red-800 font-bold';
                                $statusBadge = '<span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-red-100 text-red-700 border border-red-200/80 shadow-2xs">Belum Lulus KKM</span>';
                            } else {
                                $bgClass = 'bg-[#28745B]/10 border-[#28745B]/30 text-[#123F35] font-bold';
                                $statusBadge = '<span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-[#28745B]/15 text-[#28745B] border border-[#28745B]/30 shadow-2xs">✓ LULUS</span>';
                            }
                        @endphp

                        <tr class="hover:bg-[#F7F5EE]/40 transition">
                            <!-- Judul Tugas/Kuis -->
                            <td class="p-4 font-bold text-[#123F35]">
                                <div>{{ $assignment->title }}</div>
                                <span class="text-[10px] text-slate-500 font-normal">Pertemuan {{ $assignment->meeting->meeting_number ?? '-' }}</span>
                            </td>

                            <!-- Tipe -->
                            <td class="p-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $assignment->type === 'quiz' ? 'bg-[#E7C982]/30 text-[#123F35] border-[#E7C982]' : 'bg-[#28745B]/10 text-[#28745B] border-[#28745B]/20' }}">
                                    {{ strtoupper($assignment->type) }}
                                </span>
                            </td>

                            <!-- KKM -->
                            <td class="p-4 text-center font-semibold text-slate-600">
                                <span class="text-[#28745B] font-bold">{{ number_format($assignment->passing_score, 0) }}</span> / {{ number_format($assignment->max_score, 0) }}
                            </td>

                            <!-- Nilai Terbaik -->
                            <td class="p-3 text-center align-middle">
                                <div class="inline-flex flex-col items-center justify-center min-w-[90px] p-2 rounded-xl border {{ $bgClass }}">
                                    @if(is_null($score))
                                        <span class="text-[11px] italic font-medium">Belum Ada</span>
                                    @else
                                        <div class="font-extrabold text-xs leading-none">{{ number_format($score, 2) }}</div>
                                        <div class="text-[9px] opacity-75 mt-1">({{ $scoreData['attempt_count'] }} Attempt)</div>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Kelulusan -->
                            <td class="p-4 text-center align-middle">
                                {!! $statusBadge !!}
                            </td>

                            <!-- Tombol Lihat Detail -->
                            <td class="p-4 text-center align-middle">
                                <a href="{{ route('student.courses.gradebook.detail', [$course->id, $assignment->id, 'enrollment_id' => $currentEnrollment->id]) }}" 
                                   class="inline-flex items-center space-x-1 bg-[#28745B] hover:bg-[#123F35] text-white text-[11px] font-bold px-3 py-1.5 rounded-xl transition shadow-2xs"
                                   title="Lihat riwayat attempt">
                                    <span>Detail Attempt</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400 text-xs italic">
                                Belum ada tugas atau kuis yang dipublikasikan untuk course ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection