@extends('layouts.app')

@section('title', $assignment->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Tempat Submission Tugas')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-5xl mx-auto space-y-5 sm:space-y-6" x-data="{ activeTab: 'submissions' }">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.courses.show', $assignment->course_id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>

        <div class="flex items-center space-x-2 self-start sm:self-auto">
            <a href="{{ route('teacher.assignments.edit', $assignment->id) }}" class="inline-flex items-center space-x-1.5 bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] border border-[#123F35]/20 px-3.5 py-2 rounded-xl text-xs font-extrabold shadow-2xs transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Tugas</span>
            </a>

            <form action="{{ route('teacher.assignments.destroy', $assignment->id) }}" method="POST" onsubmit="return confirm('Hapus tempat submission tugas ini beserta seluruh lampirannya?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center space-x-1.5 bg-red-600 hover:bg-red-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-2xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
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

    <!-- 1. TOP CARD FULL GRADASI HIJAU TUA -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] rounded-2xl border border-[#28745B]/40 shadow-md p-5 sm:p-8 text-white space-y-6">
        <div class="border-b border-white/20 pb-4 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-1.5">
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase backdrop-blur-xs">
                    Pertemuan {{ $assignment->meeting->meeting_number }} &bull; Tempat Submission
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug">{{ $assignment->title }}</h1>
            </div>

            <div class="flex items-center space-x-2 flex-shrink-0">
                @if($assignment->status === 'published')
                    <span class="px-3 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] shadow-2xs">
                        ✓ Published
                    </span>
                @else
                    <span class="px-3 py-1.5 rounded-xl text-xs font-extrabold bg-white/15 text-emerald-100 border border-white/30 backdrop-blur-xs">
                        Closed
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5 sm:gap-3">
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Metode</span>
                <div class="text-xs font-bold text-white mt-1 uppercase">
                    @if($assignment->submission_method === 'file')
                        Unggah Berkas
                    @elseif($assignment->submission_method === 'text')
                        Teks Editor
                    @else
                        Berkas & Teks
                    @endif
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Batas Attempt</span>
                <div class="text-xs font-bold text-white mt-1">
                    {{ $assignment->max_attempts ? $assignment->max_attempts . 'x Attempt' : 'Tanpa Batas' }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Nilai Maksimal</span>
                <div class="text-xs font-bold text-white mt-1">{{ number_format($assignment->max_score, 0) }}</div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Passing Grade</span>
                <div class="text-xs font-extrabold text-[#E7C982] mt-1">{{ number_format($assignment->passing_score, 0) }}</div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Waktu Dibuka</span>
                <div class="text-xs font-semibold text-white mt-1">
                    {{ $assignment->available_from ? \Carbon\Carbon::parse($assignment->available_from)->format('d M Y H:i') : 'Langsung' }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Waktu Ditutup</span>
                <div class="text-xs font-semibold text-[#E7C982] mt-1">
                    {{ $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('d M Y H:i') : 'Tanpa Batas' }}
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TAB HEADER SWITCHER -->
    <div class="flex items-center space-x-2 border-b border-slate-300 pb-2 overflow-x-auto">
        <button type="button" @click="activeTab = 'submissions'" 
                :class="activeTab === 'submissions' ? 'bg-[#123F35] text-white font-bold border-[#123F35] shadow-2xs' : 'bg-white text-slate-700 border-slate-300 hover:bg-[#F7F5EE] font-semibold'"
                class="px-5 py-2.5 rounded-xl border text-xs transition flex items-center space-x-2 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Hasil & Pengumpulan Siswa ({{ $submissions->total() }})</span>
        </button>

        <button type="button" @click="activeTab = 'instructions'" 
                :class="activeTab === 'instructions' ? 'bg-[#123F35] text-white font-bold border-[#123F35] shadow-2xs' : 'bg-white text-slate-700 border-slate-300 hover:bg-[#F7F5EE] font-semibold'"
                class="px-5 py-2.5 rounded-xl border text-xs transition flex items-center space-x-2 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Instruksi & Berkas Soal</span>
        </button>
    </div>

    <!-- 3. TAB DESKRIPSI & BERKAS SOAL/LAMPIRAN -->
    <div x-show="activeTab === 'instructions'" x-cloak class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <!-- Heading Container bg-[#28745B] -->
        <div class="bg-[#28745B] p-4 sm:px-6 border-b border-slate-300 text-white">
            <h2 class="text-sm sm:text-base font-bold text-white">Instruksi & Berkas Soal Tugas</h2>
            <p class="text-xs text-emerald-100/85">Detail petunjuk pengerjaan dan berkas lampiran yang dibagikan kepada peserta.</p>
        </div>

        <div class="p-5 sm:p-8 space-y-6">
            <!-- Deskripsi / Instruksi -->
            <div class="space-y-2">
                <h3 class="text-xs font-extrabold text-[#123F35] uppercase tracking-wider">Instruksi / Petunjuk Pengerjaan:</h3>
                @if($assignment->description)
                    <div class="text-xs sm:text-sm text-slate-700 bg-[#F7F5EE]/60 p-4 rounded-xl border border-slate-300 leading-relaxed whitespace-pre-line">{{ $assignment->description }}</div>
                @else
                    <p class="text-xs text-slate-400 italic bg-[#F7F5EE]/60 p-4 rounded-xl border border-slate-300">Tidak ada deskripsi atau instruksi pengerjaan.</p>
                @endif
            </div>

            <!-- Berkas Lampiran Pengajar -->
            <div class="space-y-2.5 pt-4 border-t border-slate-300">
                <h3 class="text-xs font-extrabold text-[#123F35] uppercase tracking-wider">Berkas Lampiran / Soal Pengajar:</h3>
                @if($assignment->attachments && $assignment->attachments->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($assignment->attachments as $attachment)
                            @if($attachment->media)
                                <div class="p-3 bg-[#d1e4da]/60 rounded-xl border border-slate-300 flex items-center justify-between gap-2">
                                    <div class="flex items-center space-x-2 overflow-hidden pr-2">
                                        <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-xs text-[#123F35] font-bold truncate">{{ $attachment->media->file_name }}</span>
                                    </div>
                                    <a href="{{ asset('storage/' . $attachment->media->file_path) }}" download="{{ $attachment->media->file_name }}" class="px-3 py-1 bg-[#28745B] hover:bg-[#123F35] text-white rounded-lg text-[11px] font-bold transition shadow-2xs flex-shrink-0">
                                        Unduh
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic bg-[#F7F5EE]/60 p-4 rounded-xl border border-slate-300">Tidak ada berkas lampiran yang diunggah.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- 4. TAB TABEL PENGUMPULAN SISWA (SUBMISSIONS) -->
    <div x-show="activeTab === 'submissions'" class="space-y-5">
        
        <!-- Filter & Sort Container dengan Warna Cream (#F7F5EE) -->
        <div class="bg-[#F7F5EE] rounded-2xl border border-slate-300 p-4 sm:p-5 shadow-2xs">
            <form method="GET" action="{{ route('teacher.assignments.show', $assignment->id) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                
                <!-- Input Pencarian -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Cari Siswa</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..." 
                            class="w-full border border-slate-300 rounded-xl px-3 py-2 pl-9 text-xs text-slate-700 outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 bg-white transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Filter Status Enrollment -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Status Enrollment</label>
                    <select name="enrollment_status" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 bg-white transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('enrollment_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('enrollment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- Filter Status Tugas -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Status Penilaian</label>
                    <select name="status" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 bg-white transition cursor-pointer">
                        <option value="">Semua Status Tugas</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted (Belum Dinilai)</option>
                        <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Graded (Sudah Dinilai)</option>
                    </select>
                </div>

                <!-- Filter Penilai -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Penilai</label>
                    <select name="graded_by" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 bg-white transition cursor-pointer">
                        <option value="">Semua Penilai</option>
                        @if(isset($assignment->course->instructors))
                            @foreach($assignment->course->instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ request('graded_by') == $instructor->id ? 'selected' : '' }}>
                                    Penilai: {{ $instructor->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Urutan (Sort) -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Urutkan</label>
                    <select name="sort" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 bg-white transition cursor-pointer">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru Dikumpulkan</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama Dikumpulkan</option>
                        <option value="score_high" {{ request('sort') == 'score_high' ? 'selected' : '' }}>Nilai Tertinggi</option>
                        <option value="score_low" {{ request('sort') == 'score_low' ? 'selected' : '' }}>Nilai Terendah</option>
                    </select>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-2xs">
                        Filter
                    </button>
                    <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="w-full bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-100 transition flex items-center justify-center shadow-2xs">
                        Reset
                    </a>
                </div>

            </form>
        </div>

        <!-- Tabel Pengumpulan Siswa dengan Heading Hijau (#28745B) -->
        <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#28745B] text-white border-b border-slate-300">
                        <tr class="text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                            <th class="py-3.5 px-4 sm:px-6 min-w-[180px]">Siswa & Enrollment</th>
                            <th class="py-3.5 px-4 min-w-[120px]">Attempt Ke-</th>
                            <th class="py-3.5 px-4 min-w-[150px]">Waktu Mengumpulkan</th>
                            <th class="py-3.5 px-4 min-w-[150px]">Status Tugas</th>
                            <th class="py-3.5 px-4 min-w-[80px]">Nilai</th>
                            <th class="py-3.5 px-4 min-w-[130px]">Penilai</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right min-w-[100px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-300 text-xs text-slate-700">
                        @forelse($submissions as $sub)
                            <tr class="hover:bg-[#F7F5EE]/40 transition">
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="font-bold text-[#123F35]">{{ $sub->enrollment->user->name ?? 'Siswa Tidak Ditemukan' }}</div>
                                    <div class="text-[10px] mt-0.5 flex items-center space-x-1">
                                        <span class="text-slate-500">ID #{{ $sub->enrollment_id }}:</span>
                                        <span class="uppercase font-extrabold {{ $sub->enrollment->status === 'active' ? 'text-[#28745B]' : 'text-slate-600' }}">
                                            {{ $sub->enrollment->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-[#123F35] text-white rounded-lg font-bold text-[11px] whitespace-nowrap">
                                        Attempt #{{ $sub->attempt_number }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                    {{ $sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($sub->status === 'graded')
                                        @if($sub->score >= $assignment->passing_score)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-[#28745B]/40 bg-[#28745B]/15 text-[#28745B] text-[10px] font-extrabold whitespace-nowrap">
                                                Lulus
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-red-300 bg-red-100 text-red-700 text-[10px] font-extrabold whitespace-nowrap">
                                                Belum Lulus
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-[#E7C982] bg-[#E7C982]/40 text-[#123F35] text-[10px] font-extrabold whitespace-nowrap">
                                            Menunggu Penilaian
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-extrabold text-[#123F35] whitespace-nowrap">
                                    {{ $sub->score !== null ? number_format($sub->score, 2) : '-' }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($sub->status === 'graded' && $sub->grader)
                                        <span class="font-bold text-[#123F35]">{{ $sub->grader->name }}</span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('teacher.submissions.grade', $sub->id) }}" 
                                        class="p-1.5 bg-[#E7C982]/40 text-[#123F35] border border-[#E7C982] hover:bg-[#123F35] hover:text-white rounded-lg transition shadow-2xs" 
                                        title="Review & Beri Nilai">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>

                                        <form action="{{ route('teacher.submissions.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus attempt submission siswa ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-red-50 text-red-600 border border-red-300 hover:bg-red-600 hover:text-white rounded-lg transition shadow-2xs" title="Hapus Submission Attempt">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-xs text-slate-400 italic">
                                    Tidak ada data pengumpulan siswa yang cocok dengan filter tersebut.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($submissions->hasPages())
                <div class="p-4 sm:px-6 border-t border-slate-300 bg-[#F7F5EE]/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <div>
                        Menampilkan <span class="font-bold text-[#123F35]">{{ $submissions->firstItem() }}</span> - 
                        <span class="font-bold text-[#123F35]">{{ $submissions->lastItem() }}</span> dari 
                        <span class="font-bold text-[#123F35]">{{ $submissions->total() }}</span> pengumpulan tugas
                    </div>
                    <div>
                        {{ $submissions->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

            @if($submissions->hasPages())
                <div class="p-4 sm:px-6 border-t border-slate-300 bg-[#F7F5EE]/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <div>
                        Menampilkan <span class="font-bold text-[#123F35]">{{ $submissions->firstItem() }}</span> - 
                        <span class="font-bold text-[#123F35]">{{ $submissions->lastItem() }}</span> dari 
                        <span class="font-bold text-[#123F35]">{{ $submissions->total() }}</span> pengumpulan tugas
                    </div>
                    <div>
                        {{ $submissions->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection