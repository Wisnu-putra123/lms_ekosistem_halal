@extends('layouts.app')

@section('title', 'Rekap Nilai Saya - ' . $course->title)
@section('header_title', 'Rekap Nilai Pelatihan')

@section('content')
<div class="space-y-6">

    <!-- Top Card & Selector Enrollment -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-slate-100 text-slate-700 uppercase">
                    {{ $course->code }}
                </span>
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase {{ $currentEnrollment->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                    Enrollment: {{ $currentEnrollment->status }}
                </span>
            </div>
            <h1 class="text-xl font-bold text-slate-800 mt-1">Rekap Nilai: {{ $course->title }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau capaian nilai, batas KKM, dan riwayat pengerjaan Anda.</p>
        </div>

        <div class="flex items-center space-x-3">
            <!-- Dropdown Switcher Riwayat Enrollment -->
            @if($allEnrollments->count() > 0)
                <form method="GET" action="{{ route('student.courses.gradebook', $course->id) }}" class="flex items-center space-x-2">
                    <label class="text-xs font-bold text-slate-600 uppercase flex-shrink-0">Enrollment:</label>
                    <select name="enrollment_id" onchange="this.form.submit()" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        @foreach($allEnrollments as $en)
                            <option value="{{ $en->id }}" {{ $currentEnrollment->id === $en->id ? 'selected' : '' }}>
                                ID #{{ $en->id }} ({{ ucfirst($en->status) }} - {{ \Carbon\Carbon::parse($en->enrolled_at)->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <a href="{{ route('student.courses.show', $course->id) }}" class="inline-flex items-center space-x-1 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 px-3.5 py-2 rounded-xl transition shadow-sm">
                &larr; <span>Kembali ke Course</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400">Total Tugas & Quiz</span>
            <div class="text-lg font-bold text-slate-800">{{ $assignments->count() }} Item</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400">Item Tuntas (Lulus KKM)</span>
            <div class="text-lg font-bold text-emerald-700">{{ $totalPassed }} / {{ $assignments->count() }} Item</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400">Rata-Rata Nilai Saya</span>
            <div class="text-lg font-extrabold text-amber-700">{{ number_format($averageScore, 2) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400">Tanggal Mendaftar</span>
            <div class="text-xs font-semibold text-slate-700 mt-1">
                {{ \Carbon\Carbon::parse($currentEnrollment->enrolled_at)->format('d M Y, H:i') }}
            </div>
        </div>
    </div>

    <!-- Matriks Rekap Nilai Student -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider">
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
                            $bgClass = 'bg-slate-50 border-slate-200/60 text-slate-400';
                            $statusBadge = '<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">Belum Ada Attempt</span>';
                        } elseif ($score < $kkm) {
                            $bgClass = 'bg-red-100 border-red-300 text-red-800 font-bold';
                            $statusBadge = '<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Belum Lulus KKM</span>';
                        } else {
                            $bgClass = 'bg-emerald-100 border-emerald-300 text-emerald-800 font-bold';
                            $statusBadge = '<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">LULUS</span>';
                        }
                    @endphp

                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Judul Tugas/Kuis -->
                        <td class="p-4 font-bold text-slate-800">
                            <div>{{ $assignment->title }}</div>
                            <span class="text-[10px] text-slate-400 font-normal">Pertemuan {{ $assignment->meeting->meeting_number ?? '-' }}</span>
                        </td>

                        <!-- Tipe -->
                        <td class="p-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $assignment->type === 'quiz' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ strtoupper($assignment->type) }}
                            </span>
                        </td>

                        <!-- KKM -->
                        <td class="p-4 text-center font-semibold text-slate-600">
                            <span class="text-emerald-700 font-bold">{{ number_format($assignment->passing_score, 0) }}</span> / {{ number_format($assignment->max_score, 0) }}
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
                               class="inline-flex items-center space-x-1 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-bold px-3 py-1.5 rounded-xl transition shadow-sm"
                               title="Lihat riwayat attempt">
                                <span>Detail Attempt</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400 text-xs">
                            Belum ada tugas atau kuis yang dipublikasikan untuk course ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection