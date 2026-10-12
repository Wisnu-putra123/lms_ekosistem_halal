@extends('layouts.app')

@section('title', 'Rekap Nilai - ' . $course->title)
@section('header_title', 'Rekap Nilai Kursus')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Rekap Nilai: {{ $course->title }}</h1>
            <p class="text-xs text-slate-500 mt-1">Pantau performa, progres pengerjaan, dan riwayat attempt seluruh siswa.</p>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Export CSV -->
            <a href="{{ route('teacher.courses.gradebook.export', $course->id) }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3.5 py-2 rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Export CSV / Excel</span>
            </a>

            <!-- Kembali -->
            <a href="{{ route('teacher.courses.show', $course->id) }}" class="inline-flex items-center space-x-1 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 px-3.5 py-2 rounded-xl transition shadow-sm">
                &larr; <span>Kembali ke Course</span>
            </a>
        </div>
    </div>

    <!-- Table Matriks Rekap Nilai -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider">
                    <th class="p-4 font-bold sticky left-0 bg-slate-50 min-w-[200px] border-r border-slate-200">Nama Siswa</th>
                    <th class="p-4 font-bold text-center min-w-[100px]">Progress</th>
                    @foreach($assignments as $assignment)
                        <th class="p-4 font-bold text-center min-w-[140px]" title="KKM: {{ $assignment->passing_score }}">
                            <span class="block text-slate-800 truncate max-w-[130px]">{{ $assignment->title }}</span>
                            <span class="text-[10px] text-slate-400 font-normal">
                                {{ strtoupper($assignment->type) }} | KKM: {{ $assignment->passing_score }}
                            </span>
                        </th>
                    @endforeach
                    <th class="p-4 font-bold text-center bg-amber-50 text-amber-900 border-l border-slate-200 min-w-[110px]">Rata-Rata</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($gradebookData as $data)
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Nama Siswa -->
                        <td class="p-4 font-semibold text-slate-800 sticky left-0 bg-white shadow-sm border-r border-slate-100">
                            {{ $data['user']->name }}
                            <span class="block text-[10px] font-normal text-slate-400">{{ $data['user']->email }}</span>
                        </td>

                        <!-- Progress (%) -->
                        <td class="p-4 text-center font-bold text-slate-700">
                            {{ $data['progress'] }}%
                        </td>

                        <!-- Columns Item Nilai -->
                        @foreach($assignments as $assignment)
                            @php 
                                $scoreData = $data['scores'][$assignment->id] ?? ['score' => null, 'is_passed' => false, 'attempt_count' => 0]; 
                            @endphp
                            <td class="p-3 text-center">
                                <a href="{{ route('teacher.courses.gradebook.student_detail', [$course->id, $assignment->id, $data['user']->id]) }}" 
                                   class="inline-block w-full p-2 rounded-xl transition border {{ $scoreData['attempt_count'] > 0 ? 'hover:scale-105 shadow-sm' : '' }} 
                                          {{ is_null($scoreData['score']) ? 'bg-slate-50 border-slate-200/60 text-slate-400' : ($scoreData['is_passed'] ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800') }}"
                                   title="Klik untuk melihat seluruh attempt siswa ini">
                                    
                                    @if(is_null($scoreData['score']))
                                        <span class="text-[11px] italic font-medium">Belum Ada</span>
                                    @else
                                        <div class="font-extrabold text-xs">{{ $scoreData['score'] }}</div>
                                        <div class="text-[9px] opacity-75 mt-0.5">({{ $scoreData['attempt_count'] }} Attempt)</div>
                                    @endif
                                </a>
                            </td>
                        @endforeach

                        <!-- Rata-rata Nilai -->
                        <td class="p-4 text-center font-extrabold text-amber-900 bg-amber-50/40 border-l border-slate-200 text-sm">
                            {{ $data['average_score'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($assignments) + 3 }}" class="p-12 text-center text-slate-400 text-xs">
                            Belum ada siswa yang terdaftar di kelas ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection