@extends('layouts.app')

@section('title', 'Rekap Nilai - ' . $course->title)
@section('header_title', 'Rekap Nilai Kursus')

@section('content')
<div class="space-y-6">

    <!-- Header Section & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Rekap Nilai: {{ $course->title }}</h1>
            <p class="text-xs text-slate-500 mt-1">Pantau performa, progres pengerjaan, dan riwayat attempt seluruh siswa.</p>
        </div>

        <div class="flex items-center space-x-2 flex-wrap gap-2">
            <!-- Export CSV -->
            <a href="{{ route('teacher.courses.gradebook.export', array_merge(['course' => $course->id], request()->query())) }}" 
               class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3.5 py-2 rounded-xl transition shadow-sm">
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

    <!-- Filter & Sorting Controls -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('teacher.courses.gradebook.show', $course->id) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <!-- Pencarian Nama / Email / ID Enrollment -->
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Cari Siswa / ID Enrollment</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau ID Enrollment..." 
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 pl-9 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Filter Status Enrollment -->
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Status Siswa</label>
                <select name="enrollment_status" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    <option value="all" {{ request('enrollment_status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('enrollment_status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="completed" {{ request('enrollment_status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="dropped" {{ request('enrollment_status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                </select>
            </div>

            <!-- Urutkan Berdasarkan (Sort) -->
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Urutkan Berdasarkan</label>
                <select name="sort" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    <option value="enrolled_at" {{ request('sort', 'enrolled_at') == 'enrolled_at' ? 'selected' : '' }}>Waktu Enrollment</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama Siswa</option>
                    <option value="progress" {{ request('sort') == 'progress' ? 'selected' : '' }}>Progress (%)</option>
                    <option value="average" {{ request('sort') == 'average' ? 'selected' : '' }}>Rata-Rata Nilai</option>
                </select>
            </div>

            <!-- Arah Urutan & Submit Button -->
            <div class="flex items-end space-x-2">
                <div class="w-1/2">
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Urutan</label>
                    <select name="direction" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru / High-Low</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Terlama / Low-High</option>
                    </select>
                </div>
                <div class="w-1/2 flex space-x-1">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2 rounded-xl transition shadow-sm">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'enrollment_status', 'sort', 'direction']))
                        <a href="{{ route('teacher.courses.gradebook.show', $course->id) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </div>
            </div>

        </form>
    </div>

    <!-- Table Matriks Rekap Nilai -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase tracking-wider">
                    <th class="p-4 font-bold sticky left-0 bg-slate-50 min-w-[240px] border-r border-slate-200 align-middle">Informasi Siswa & Enrollment</th>
                    <th class="p-4 font-bold text-center min-w-[100px] align-middle">Progress</th>
                    @foreach($assignments as $assignment)
                        <th class="p-4 font-bold text-center min-w-[140px] align-middle" title="KKM: {{ $assignment->passing_score }}">
                            <span class="block text-slate-800 truncate max-w-[130px] mx-auto">{{ $assignment->title }}</span>
                            <span class="text-[10px] text-slate-400 font-normal">
                                {{ strtoupper($assignment->type) }} | KKM: {{ $assignment->passing_score }}
                            </span>
                        </th>
                    @endforeach
                    <th class="p-4 font-bold text-center bg-amber-50 text-amber-900 border-l border-slate-200 min-w-[110px] align-middle">Rata-Rata</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($gradebookData as $data)
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Informasi Siswa & Enrollment -->
                        <td class="p-4 sticky left-0 bg-white shadow-sm border-r border-slate-100 align-middle space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-slate-800 text-xs">{{ $data['user']->name }}</span>
                                <span class="text-[10px] font-mono font-bold bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200">
                                    Id Enrollment: {{ $data['enrollment']->id }}
                                </span>
                            </div>
                            <div class="text-[10px] text-slate-400 truncate">{{ $data['user']->email }}</div>
                            
                            <div class="flex items-center justify-between gap-2 pt-1">
                                <!-- Status Enrollment Badge -->
                                @if($data['enrollment']->status === 'active')
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                                @elseif($data['enrollment']->status === 'completed')
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-800">Selesai</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-600">Dropped</span>
                                @endif

                                <!-- Waktu Enrollment -->
                                <span class="text-[9px] text-slate-400">
                                    {{ $data['enrollment']->enrolled_at ? \Carbon\Carbon::parse($data['enrollment']->enrolled_at)->format('d M Y H:i') : '-' }}
                                </span>
                            </div>
                        </td>

                        <!-- Progress (%) -->
                        <td class="p-4 text-center font-bold text-slate-700 align-middle">
                            {{ number_format($data['progress'], 2) }}%
                        </td>

                        <!-- Columns Item Nilai -->
                        @foreach($assignments as $assignment)
                            @php 
                                $scoreData = $data['scores'][$assignment->id] ?? ['score' => null, 'is_passed' => false, 'attempt_count' => 0]; 
                                $score = $scoreData['score'];
                                $kkm = $assignment->passing_score;

                                // Penentuan Warna Background & Teks Berdasarkan KKM
                                if (is_null($score)) {
                                    $bgClass = 'bg-slate-50 border-slate-200/60 text-slate-400';
                                } elseif ($score < $kkm) {
                                    $bgClass = 'bg-red-100 border-red-300 text-red-800 font-bold'; // Di bawah KKM
                                } else {
                                    $bgClass = 'bg-emerald-100 border-emerald-300 text-emerald-800 font-bold'; // Lulus KKM
                                }
                            @endphp

                            <td class="p-3 text-center align-middle">
                                <a href="{{ route('teacher.courses.gradebook.student_detail', [$course->id, $assignment->id, $data['enrollment']->id]) }}"
                                   class="inline-flex flex-col items-center justify-center w-full h-12 p-2 rounded-xl transition border {{ $bgClass }} {{ $scoreData['attempt_count'] > 0 ? 'hover:scale-105 shadow-sm' : '' }}"
                                   title="Klik untuk melihat seluruh attempt siswa ini">
                                    
                                    @if(is_null($score))
                                        <span class="text-[11px] italic font-medium">Belum Ada</span>
                                    @else
                                        <div class="font-extrabold text-xs leading-none">{{ number_format($score, 2) }}</div>
                                        <div class="text-[9px] opacity-75 mt-1">({{ $scoreData['attempt_count'] }} Attempt)</div>
                                    @endif
                                </a>
                            </td>
                        @endforeach

                        <!-- Rata-rata Nilai -->
                        <td class="p-4 text-center font-extrabold text-amber-900 bg-amber-50/40 border-l border-slate-200 text-sm align-middle">
                            {{ number_format($data['average_score'], 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($assignments) + 3 }}" class="p-12 text-center text-slate-400 text-xs">
                            Tidak ada data siswa yang cocok dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection