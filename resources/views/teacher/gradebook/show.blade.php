@extends('layouts.app')

@section('title', 'Rekap Nilai - ' . $course->title)
@section('header_title', 'Rekap Nilai Kursus')

@section('content')
<div class="space-y-6">

    <!-- 1. Tombol Kembali di Atas Kiri -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.courses.show', $course->id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Course</span>
        </a>
    </div>

    <!-- 2. Top Card Hijau Tua dengan Gradasi -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-4 sm:p-6 md:p-8 rounded-2xl border border-[#28745B]/50 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4 sm:gap-6 text-white relative overflow-hidden">
        <div class="space-y-2 min-w-0 flex-1 w-full relative z-10">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-white border border-white/20 uppercase backdrop-blur-xs">
                    {{ $course->code }}
                </span>
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase bg-[#E7C982] text-[#123F35] border border-[#E7C982]">
                    Panel Pengajar
                </span>
            </div>
            <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-white leading-snug break-words">Rekap Nilai: {{ $course->title }}</h1>
            <p class="text-xs text-emerald-100/80 max-w-3xl leading-relaxed">Pantau performa, progres pengerjaan, dan riwayat attempt seluruh siswa.</p>
        </div>

        <!-- Action Export CSV -->
        <div class="w-full md:w-auto flex-shrink-0 pt-1 md:pt-0 relative z-10">
            <a href="{{ route('teacher.courses.gradebook.export', array_merge(['course' => $course->id], request()->query())) }}" 
               class="w-full md:w-auto inline-flex items-center justify-center space-x-2 text-xs font-extrabold text-[#123F35] bg-[#E7C982] hover:bg-[#d8b76e] px-4 py-2.5 rounded-xl transition shadow-2xs">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Export CSV / Excel</span>
            </a>
        </div>
    </div>

    <!-- 3. Container Sortir dan Filter Warna Cream (#F7F5EE) -->
    <div class="bg-[#F7F5EE] rounded-2xl border border-slate-300 p-4 sm:p-5 shadow-2xs">
        <form method="GET" action="{{ route('teacher.courses.gradebook.show', $course->id) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <!-- Pencarian Nama / Email / ID Enrollment -->
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Cari Siswa / ID Enrollment</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau ID Enrollment..." 
                           class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 pl-9 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#28745B]/20 focus:border-[#28745B] transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Filter Status Enrollment -->
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Status Siswa</label>
                <select name="enrollment_status" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#28745B]/20 focus:border-[#28745B] transition cursor-pointer">
                    <option value="all" {{ request('enrollment_status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('enrollment_status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="completed" {{ request('enrollment_status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="dropped" {{ request('enrollment_status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                </select>
            </div>

            <!-- Urutkan Berdasarkan (Sort) -->
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Urutkan Berdasarkan</label>
                <select name="sort" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#28745B]/20 focus:border-[#28745B] transition cursor-pointer">
                    <option value="enrolled_at" {{ request('sort', 'enrolled_at') == 'enrolled_at' ? 'selected' : '' }}>Waktu Enrollment</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama Siswa</option>
                    <option value="progress" {{ request('sort') == 'progress' ? 'selected' : '' }}>Progress (%)</option>
                    <option value="average" {{ request('sort') == 'average' ? 'selected' : '' }}>Rata-Rata Nilai</option>
                </select>
            </div>

            <!-- Arah Urutan & Submit Button -->
            <div class="flex flex-col sm:flex-row sm:items-end gap-3 sm:gap-2">
                <div class="w-full sm:w-1/2">
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Urutan</label>
                    <select name="direction" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#28745B]/20 focus:border-[#28745B] transition cursor-pointer">
                        <option value="desc" {{ request('direction', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru / High-Low</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Terlama / Low-High</option>
                    </select>
                </div>
                <div class="w-full sm:w-1/2 flex space-x-1.5">
                    <button type="submit" class="w-full bg-[#28745B] hover:bg-[#123F35] text-white font-bold text-xs py-2 rounded-xl transition shadow-2xs">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'enrollment_status', 'sort', 'direction']))
                        <a href="{{ route('teacher.courses.gradebook.show', $course->id) }}" class="p-2 bg-white hover:bg-slate-100 text-slate-600 border border-slate-300 rounded-xl transition flex items-center justify-center shadow-2xs" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </div>
            </div>

        </form>
    </div>

    <!-- 4. Table Matriks Rekap Nilai dengan Heading Hijau (#28745B) -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-[#28745B] text-white uppercase tracking-wider">
                        <th class="p-4 font-bold sticky left-0 z-20 bg-[#28745B] min-w-[240px] border-r border-white/15 align-middle">Informasi Siswa & Enrollment</th>
                        <th class="p-4 font-bold text-center min-w-[100px] align-middle">Progress</th>
                        @foreach($assignments as $assignment)
                            <th class="p-4 font-bold text-center min-w-[140px] align-middle" title="KKM: {{ $assignment->passing_score }}">
                                <span class="block text-white truncate max-w-[130px] mx-auto">{{ $assignment->title }}</span>
                                <span class="text-[10px] text-emerald-100/80 font-normal">
                                    {{ strtoupper($assignment->type) }} | KKM: {{ $assignment->passing_score }}
                                </span>
                            </th>
                        @endforeach
                        <th class="p-4 font-bold text-center bg-[#28745B] border-l border-white/15 min-w-[110px] align-middle">Rata-Rata</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($gradebookData as $data)
                        <tr class="hover:bg-[#F7F5EE]/40 transition group">
                            <!-- Informasi Siswa & Enrollment -->
                            <td class="p-4 sticky left-0 z-10 bg-white group-hover:bg-[#F7F5EE] border-r border-slate-300 align-middle space-y-1 transition">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold text-[#123F35] text-xs">{{ $data['user']->name }}</span>
                                    <span class="text-[10px] font-mono font-bold bg-[#F7F5EE] text-[#123F35] px-1.5 py-0.5 rounded border border-slate-300">
                                        ID #{{ $data['enrollment']->id }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-500 truncate">{{ $data['user']->email }}</div>
                                
                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <!-- Status Enrollment Badge -->
                                    @if($data['enrollment']->status === 'active')
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-[#28745B]/15 text-[#28745B] border border-[#28745B]/30">Aktif</span>
                                    @elseif($data['enrollment']->status === 'completed')
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-[#E7C982]/30 text-[#123F35] border border-[#E7C982]">Selesai</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-300">Dropped</span>
                                    @endif

                                    <!-- Waktu Enrollment -->
                                    <span class="text-[9px] text-slate-500">
                                        {{ $data['enrollment']->enrolled_at ? \Carbon\Carbon::parse($data['enrollment']->enrolled_at)->format('d M Y H:i') : '-' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Progress (%) -->
                            <td class="p-4 text-center font-bold text-[#123F35] align-middle">
                                {{ number_format($data['progress'], 2) }}%
                            </td>

                            <!-- Columns Item Nilai -->
                            @foreach($assignments as $assignment)
                                @php 
                                    $scoreData = $data['scores'][$assignment->id] ?? ['score' => null, 'is_passed' => false, 'attempt_count' => 0]; 
                                    $score = $scoreData['score'];
                                    $kkm = $assignment->passing_score;

                                    if (is_null($score)) {
                                        $bgClass = 'bg-slate-50 border-slate-300 text-slate-500';
                                    } elseif ($score < $kkm) {
                                        $bgClass = 'bg-red-50 border-red-300 text-red-800 font-bold';
                                    } else {
                                        $bgClass = 'bg-[#28745B]/10 border-[#28745B]/40 text-[#123F35] font-bold';
                                    }
                                @endphp

                                <td class="p-3 text-center align-middle">
                                    <a href="{{ route('teacher.courses.gradebook.student_detail', [$course->id, $assignment->id, $data['enrollment']->id]) }}"
                                       class="inline-flex flex-col items-center justify-center w-full h-12 p-2 rounded-xl transition border {{ $bgClass }} {{ $scoreData['attempt_count'] > 0 ? 'hover:scale-[1.03] shadow-2xs' : '' }}"
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
                            <td class="p-4 text-center font-extrabold text-[#123F35] bg-[#E7C982]/20 border-l border-slate-300 text-sm align-middle">
                                {{ number_format($data['average_score'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($assignments) + 3 }}" class="p-12 text-center text-slate-400 text-xs italic">
                                Tidak ada data siswa yang cocok dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($gradebookData->hasPages())
        <div class="bg-white rounded-2xl border border-slate-300 p-4 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-slate-500">
                Menampilkan <span class="font-bold text-[#123F35]">{{ $gradebookData->firstItem() }}</span> - 
                <span class="font-bold text-[#123F35]">{{ $gradebookData->lastItem() }}</span> dari 
                <span class="font-bold text-[#123F35]">{{ $gradebookData->total() }}</span> siswa
            </div>
            <div>
                {{ $gradebookData->links() }}
            </div>
        </div>
    @endif

</div>
@endsection