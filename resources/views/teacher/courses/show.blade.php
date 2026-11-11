@extends('layouts.app')

@section('title', $course->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Kursus & Pertemuan')

@section('content')
<!-- Script CDN Alpine.js untuk fitur Collapse/Expand & Modal -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div x-data="{ 
        addMeetingModal: {{ $errors->has('meeting_number') || $errors->has('title') ? 'true' : 'false' }}, 
        editMeetingModal: false, 
        activeEditMeeting: {}, 
        allExpanded: false 
    }" 
     class="space-y-5 sm:space-y-6">

    <!-- Flash Session Success & Error Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-2xl flex items-center justify-between text-xs font-medium shadow-2xs">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="p-4 bg-red-50 border border-red-300 text-red-800 rounded-2xl space-y-1 text-xs font-medium shadow-2xs">
            <div class="flex items-center space-x-2 font-bold text-red-900">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Gagal Memproses Pertemuan:</span>
            </div>
            <ul class="list-disc list-inside pl-6 space-y-0.5 text-red-700">
                @if(session('error'))
                    <li>{{ session('error') }}</li>
                @endif
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tombol Navigasi Atas (Kembali & Gradebook Student) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.courses.index') }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Katalog Pelatihan</span>
        </a>

        <!-- Tombol Rekap Nilai Student -->
        <a href="{{ route('teacher.courses.gradebook.show', $course->id) }}" class="inline-flex items-center justify-center space-x-2 text-xs font-bold bg-[#28745B] hover:bg-[#123F35] text-white border border-[#E7C982]/60 px-4 py-2 rounded-xl transition shadow-2xs">
            <svg class="w-4 h-4 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Rekap Nilai Student</span>
        </a>
    </div>

    <!-- TOP CARD: BANNER & DETAIL PELATIHAN -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-5 sm:p-8 rounded-2xl border border-[#28745B]/40 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-6 text-white relative overflow-hidden">
        
        <!-- Kiri: Thumbnail, Judul & Pengajar -->
        <div class="flex flex-col sm:flex-row items-start space-y-4 sm:space-y-0 sm:space-x-5 flex-1 min-w-0 w-full relative z-10">
            @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="{{ $course->title }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border border-white/30 flex-shrink-0 shadow-sm">
            @else
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/10 text-[#E7C982] flex items-center justify-center font-bold text-xl sm:text-2xl border border-white/30 flex-shrink-0 backdrop-blur-xs">
                    {{ substr($course->code, 0, 3) }}
                </div>
            @endif

            <div class="space-y-2 min-w-0 flex-1 w-full">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-white border border-white/20 uppercase backdrop-blur-xs">
                        {{ $course->category_label }}
                    </span>

                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-white border border-white/20 uppercase backdrop-blur-xs">
                        {{ $course->code }}
                    </span>

                    @if($course->status === 'published')
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-[#28745B] text-white border border-white/20 uppercase">
                            Published
                        </span>
                    @elseif($course->status === 'draft')
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-[#E7C982] text-[#123F35] uppercase">
                            Draft
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-slate-600 text-white border border-white/20 uppercase">
                            Archived
                        </span>
                    @endif

                    <!-- Tombol Aksi Edit & Delete Kursus -->
                    <div class="flex items-center space-x-1 pl-2 border-l border-white/20">
                        <a href="{{ route('teacher.courses.edit', $course->id) }}" class="p-1 text-emerald-100 hover:text-[#E7C982] transition rounded-lg hover:bg-white/10" title="Edit Kursus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('teacher.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini beserta seluruh pertemuannya?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 text-emerald-100 hover:text-red-300 transition rounded-lg hover:bg-white/10" title="Hapus Kursus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                
                <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug break-words sm:truncate">{{ $course->title }}</h1>
                <p class="text-xs text-emerald-100/80 max-w-3xl leading-relaxed line-clamp-3 sm:line-clamp-2">{{ $course->description ?? 'Tidak ada deskripsi.' }}</p>
                
                <div class="pt-2 flex flex-wrap items-center gap-1.5 text-xs text-emerald-100/90">
                    <div class="flex items-center space-x-1 flex-shrink-0">
                        <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="font-bold text-white">Tim Pengajar:</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="px-2 py-0.5 bg-white/10 border border-white/20 rounded-md text-[11px] font-semibold text-white">
                            {{ $course->creator->name ?? 'Instruktur Utama' }}
                        </span>
                        @if($course->instructors && $course->instructors->count() > 0)
                            @foreach($course->instructors as $instructor)
                                @if($instructor->id !== $course->created_by)
                                    <span class="px-2 py-0.5 bg-white/10 border border-white/20 rounded-md text-[11px] font-semibold text-white">
                                        {{ $instructor->name }}
                                    </span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanan: Box Information (Enrollment Key & Sertifikat Background) -->
        <div class="w-full md:w-auto flex-shrink-0 relative z-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 gap-2.5 w-full min-w-[260px]">
                
                <!-- Enrollment Key Box -->
                <div class="p-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl space-y-1">
                    <div class="text-[11px] font-medium text-emerald-100">Enrollment Key:</div>
                    <div class="flex items-center justify-between gap-2">
                        <code class="text-xs font-bold text-[#123F35] bg-[#E7C982] px-2.5 py-0.5 rounded-lg tracking-wider">{{ $course->enrollment_key }}</code>
                        <form action="{{ route('teacher.courses.regenerateKey', $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-1 text-emerald-100 hover:text-[#E7C982] transition" title="Regenerate Key" onclick="return confirm('Buat Enrollment Key baru?')">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Certificate Background Box -->
                <div class="p-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl space-y-1">
                    <div class="text-[11px] font-medium text-emerald-100">Latar Sertifikat:</div>
                    @if($course->certificateBackground)
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-[#E7C982] flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Custom Image
                            </span>
                            <a href="{{ asset('storage/' . $course->certificateBackground->file_path) }}" target="_blank" class="text-[10px] text-white hover:underline font-bold bg-white/10 px-2 py-0.5 rounded-md border border-white/20">Lihat</a>
                        </div>
                    @else
                        <div class="text-xs font-semibold text-emerald-100/70">
                            Default (HTML/CSS)
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- SECTION HEADER PERTEMUAN & BAR KONTROL -->
    @php
        $allMeetingIds = $course->meetings->pluck('id')->toArray();
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-300 pb-3">
        <div>
            <h2 class="text-base font-bold text-[#123F35]">Manajemen Pertemuan (Meetings)</h2>
            <p class="text-xs text-slate-500">Susun modul pembelajaran, materi, tugas, dan kuis berdasarkan urutan pertemuan.</p>
        </div>
        <div class="flex items-center justify-end space-x-2">
            <button @click="allExpanded = !allExpanded" class="px-3.5 py-1.5 bg-[#28745B] hover:bg-[#123F35] text-white text-xs font-semibold rounded-xl border border-slate-300 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                <template x-if="allExpanded">
                    <span class="flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        <span>Collapse Semua Pertemuan</span>
                    </span>
                </template>
                <template x-if="!allExpanded">
                    <span class="flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <span>Expand Semua Pertemuan</span>
                    </span>
                </template>
            </button>

            <!-- Tombol Tambah Pertemuan -->
            <button @click="addMeetingModal = true" class="inline-flex items-center space-x-2 bg-[#28745B] hover:bg-[#123F35] text-white border border-[#E7C982]/60 px-4 py-1.5 rounded-xl text-xs font-semibold shadow-2xs transition">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Pertemuan</span>
            </button>
        </div>
    </div>

    <!-- ACCORDION LIST PERTEMUAN -->
    <div class="space-y-4">
        @forelse($course->meetings->sortBy('meeting_number') as $meeting)
            <div x-data="{ expanded: false }" x-init="$watch('allExpanded', val => expanded = val)" class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden transition">
                
                <!-- Header Pertemuan (Warna Background Hijau bg-[#28745B]) -->
                <div class="p-4 bg-[#28745B] hover:bg-[#123F35] flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 cursor-pointer transition text-white" @click="expanded = !expanded">
                    
                    <!-- Kiri: Nomor, Judul & Deskripsi -->
                    <div class="flex items-start gap-3 min-w-0 flex-1">
                        <span class="w-8 h-8 rounded-xl bg-white/20 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 border border-white/30 shadow-2xs mt-0.5">
                            {{ $meeting->meeting_number }}
                        </span>
                        <div class="min-w-0 space-y-1">
                            <h3 class="text-sm font-bold text-white truncate">{{ $meeting->title }}</h3>
                            
                            @if($meeting->description)
                                <p class="text-xs text-emerald-100/80 line-clamp-2 leading-relaxed">{{ $meeting->description }}</p>
                            @else
                                <p class="text-xs text-emerald-100/60 italic">Belum ada pengantar/deskripsi pertemuan.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Kanan: Jadwal Akses & Tombol Aksi -->
                    <div class="flex items-center justify-between md:justify-end gap-3 flex-shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-white/20">
                        
                        <!-- Badge Informasi Jadwal -->
                        <div class="text-[11px] text-white bg-white/10 px-2.5 sm:px-3 py-1.5 rounded-xl border border-white/30 flex items-center space-x-1.5 shadow-2xs backdrop-blur-xs">
                            <svg class="w-3.5 h-3.5 text-[#E7C982] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            @if($meeting->available_from || $meeting->available_until)
                                <span class="truncate max-w-[200px] sm:max-w-none">
                                    Jadwal: 
                                    <strong class="text-white">{{ $meeting->available_from ? \Carbon\Carbon::parse($meeting->available_from)->format('d M H:i') : 'Sekarang' }}</strong>
                                    s/d 
                                    <strong class="text-white">{{ $meeting->available_until ? \Carbon\Carbon::parse($meeting->available_until)->format('d M H:i') : 'Tanpa Batas' }}</strong>
                                </span>
                            @else
                                <span class="text-[#E7C982] font-semibold">Akses Tanpa Batas Waktu</span>
                            @endif
                        </div>

                        <!-- Actions & Toggle Arrow -->
                        <div class="flex items-center space-x-1.5" @click.stop>
                            <!-- Edit Button -->
                            <button @click="activeEditMeeting = { 
                                        id: '{{ $meeting->id }}', 
                                        title: '{{ addslashes($meeting->title) }}', 
                                        meeting_number: '{{ $meeting->meeting_number }}', 
                                        description: '{{ addslashes($meeting->description) }}', 
                                        available_from: '{{ $meeting->available_from ? \Carbon\Carbon::parse($meeting->available_from)->format('Y-m-d\TH:i') : '' }}', 
                                        available_until: '{{ $meeting->available_until ? \Carbon\Carbon::parse($meeting->available_until)->format('Y-m-d\TH:i') : '' }}' 
                                    }; editMeetingModal = true" 
                                    class="p-1.5 text-white hover:text-[#E7C982] transition rounded-lg hover:bg-white/10" title="Edit Pertemuan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>

                            <!-- Delete Button -->
                            <form action="{{ route('teacher.meetings.destroy', $meeting->id) }}" method="POST" onsubmit="return confirm('Hapus pertemuan ini beserta seluruh isinya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-white hover:text-red-300 transition rounded-lg hover:bg-white/10" title="Hapus Pertemuan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>

                            <!-- Chevron Arrow -->
                            <button type="button" @click="expanded = !expanded" class="w-7 h-7 rounded-lg border border-white/30 bg-white/10 flex items-center justify-center text-white shadow-2xs backdrop-blur-xs">
                                <svg class="w-4 h-4 transform transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Body Accordion (Materi, Tugas, Kuis) -->
                <div x-show="expanded" x-collapse class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5 items-stretch">
                        
                        <!-- 1. SECTION MATERI PEMBELAJARAN -->
                        <section class="rounded-2xl border border-slate-300 bg-white min-w-0 flex flex-col justify-between overflow-hidden shadow-2xs h-full">
                            <div class="flex flex-col h-full justify-between">
                                <div>
                                    <!-- Header Seksi Materi -->
                                    <div class="bg-[#d1e4da] p-3 px-4 border-b border-slate-300 flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-xs font-bold text-[#123F35] uppercase tracking-wider">
                                            <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <span>Materi ({{ $meeting->materials->count() }})</span>
                                        </div>
                                        <a href="{{ route('teacher.meetings.materials.create', $meeting->id) }}" class="text-[11px] font-extrabold text-[#28745B] hover:text-[#123F35] hover:underline">
                                            + Tambah
                                        </a>
                                    </div>

                                    <div class="p-3.5 space-y-2.5 flex-1 flex flex-col">
                                        @forelse($meeting->materials as $material)
                                            <!-- Kartu Materi dengan flex-col justify-between & Badge Tersedia -->
                                            <div class="p-3 bg-[#F7F5EE]/40 rounded-xl border border-slate-300 space-y-2 shadow-2xs h-full flex flex-col justify-between">
                                                <!-- Baris Atas: Icon, Judul & Action Buttons -->
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <div class="w-6 h-6 rounded-lg bg-[#28745B]/10 text-[#28745B] flex items-center justify-center flex-shrink-0 border border-[#28745B]/20">
                                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 00-2-2V5a2 2 0 012-2h5.586a1 1 0 00.1.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                        </div>
                                                        <span class="text-xs text-[#123F35] font-bold truncate">{{ $material->title }}</span>
                                                    </div>

                                                    <div class="flex items-center space-x-1 flex-shrink-0">
                                                        <a href="{{ route('teacher.materials.show', $material->id) }}" class="p-1 text-slate-400 hover:text-[#123F35] transition" title="Lihat">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                        </a>
                                                        <a href="{{ route('teacher.materials.edit', $material->id) }}" class="p-1 text-slate-400 hover:text-amber-600 transition" title="Edit">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('teacher.materials.destroy', $material->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition" title="Hapus">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>

                                                <!-- Baris Bawah: Label "Tersedia" persis seperti milik Student -->
                                                <div class="flex flex-wrap items-center gap-1.5 pt-1.5 border-t border-slate-300">
                                                    <span class="px-2 py-0.5 bg-[#28745B]/10 text-[#123F35] border border-[#28745B]/40 text-[10px] font-bold rounded-md shadow-2xs">
                                                        Tersedia
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-[11px] text-slate-400 italic py-2">Belum ada materi dipublikasikan.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- 2. SECTION TUGAS (SUBMISSION) -->
                        <section class="rounded-2xl border border-slate-300 bg-white min-w-0 flex flex-col justify-between overflow-hidden shadow-2xs h-full">
                            <div class="flex flex-col h-full justify-between">
                                <div>
                                    <!-- Header Seksi Tugas -->
                                    <div class="bg-[#d1e4da] p-3 px-4 border-b border-slate-300 flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-xs font-bold text-[#123F35] uppercase tracking-wider">
                                            <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                            </svg>
                                            <span>Tugas ({{ $meeting->assignments ? $meeting->assignments->where('type', 'submission')->count() : 0 }})</span>
                                        </div>
                                        <a href="{{ route('teacher.meetings.assignments.create', $meeting->id) }}" class="text-[11px] font-extrabold text-[#28745B] hover:text-[#123F35] hover:underline">
                                            + Tambah
                                        </a>
                                    </div>

                                    <div class="p-3.5 space-y-2.5">
                                        @if(isset($meeting->assignments) && $meeting->assignments->where('type', 'submission')->count() > 0)
                                            @foreach($meeting->assignments->where('type', 'submission') as $assignment)
                                                <div class="p-3 bg-[#F7F5EE]/40 rounded-xl border border-slate-300 space-y-2 shadow-2xs">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <div class="flex items-center gap-2 min-w-0">
                                                            <div class="w-6 h-6 rounded-lg bg-[#28745B]/10 text-[#28745B] flex items-center justify-center flex-shrink-0 border border-[#28745B]/20">
                                                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                </svg>
                                                            </div>
                                                            <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="text-xs text-[#123F35] font-bold hover:underline truncate">
                                                                {{ $assignment->title }}
                                                            </a>
                                                        </div>

                                                        <div class="flex items-center space-x-1 flex-shrink-0">
                                                            <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="p-1 text-slate-400 hover:text-[#123F35] transition" title="Detail Tugas">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                </svg>
                                                            </a>
                                                            <a href="{{ route('teacher.assignments.edit', $assignment->id) }}" class="p-1 text-slate-400 hover:text-amber-600 transition" title="Edit Tugas">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </a>
                                                            <form action="{{ route('teacher.assignments.destroy', $assignment->id) }}" method="POST" onsubmit="return confirm('Hapus tempat submission tugas ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition" title="Hapus Tugas">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <div class="flex flex-wrap items-center gap-1.5 pt-1.5 border-t border-slate-300">
                                                        @if($assignment->status === 'published')
                                                            <span class="px-2 py-0.5 bg-[#28745B]/15 text-[#28745B] border border-[#28745B]/40 text-[9px] font-extrabold rounded-md shadow-2xs">Published</span>
                                                        @elseif($assignment->status === 'closed')
                                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 border border-red-300 text-[9px] font-extrabold rounded-md shadow-2xs">Closed</span>
                                                        @endif

                                                        @if($assignment->available_until)
                                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-300 text-[9px] font-bold rounded-md">Tenggat: {{ \Carbon\Carbon::parse($assignment->available_until)->format('d M H:i') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-[11px] text-slate-400 italic py-2">Belum ada tugas dibuat.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- 3. SECTION KUIS (QUIZ) -->
                        <section class="rounded-2xl border border-slate-300 bg-white min-w-0 flex flex-col justify-between overflow-hidden shadow-2xs h-full">
                            <div class="flex flex-col h-full justify-between">
                                <div>
                                    <!-- Header Seksi Kuis -->
                                    <div class="bg-[#d1e4da] p-3 px-4 border-b border-slate-300 flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-xs font-bold text-[#123F35] uppercase tracking-wider">
                                            <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Kuis ({{ $meeting->assignments ? $meeting->assignments->where('type', 'quiz')->count() : 0 }})</span>
                                        </div>
                                        <a href="{{ route('teacher.meetings.quizzes.create', $meeting->id) }}" class="text-[11px] font-extrabold text-[#28745B] hover:text-[#123F35] hover:underline">
                                            + Tambah
                                        </a>
                                    </div>

                                    <div class="p-3.5 space-y-2.5">
                                        @if(isset($meeting->assignments) && $meeting->assignments->where('type', 'quiz')->count() > 0)
                                            @foreach($meeting->assignments->where('type', 'quiz') as $quizAssignment)
                                                <div class="p-3 bg-[#F7F5EE]/40 rounded-xl border border-slate-300 space-y-2 shadow-2xs">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <div class="flex items-center gap-2 min-w-0">
                                                            <div class="w-6 h-6 rounded-lg bg-[#28745B]/10 text-[#28745B] flex items-center justify-center flex-shrink-0 border border-[#28745B]/20">
                                                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                </svg>
                                                            </div>
                                                            <a href="{{ route('teacher.quizzes.show', $quizAssignment->id) }}" class="text-xs text-[#123F35] font-bold hover:underline truncate">
                                                                {{ $quizAssignment->title }}
                                                            </a>
                                                        </div>

                                                        <div class="flex items-center space-x-1 flex-shrink-0">
                                                            <a href="{{ route('teacher.quizzes.show', $quizAssignment->id) }}" class="p-1 text-slate-400 hover:text-[#123F35] transition" title="Detail Kuis & Bank Soal">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                </svg>
                                                            </a>
                                                            <a href="{{ route('teacher.quizzes.edit', $quizAssignment->id) }}" class="p-1 text-slate-400 hover:text-amber-600 transition" title="Edit Konfigurasi Kuis">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </a>
                                                            <form action="{{ route('teacher.quizzes.destroy', $quizAssignment->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus kuis ini beserta seluruh bank soalnya?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition" title="Hapus Kuis">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <div class="flex flex-wrap items-center gap-1.5 pt-1.5 border-t border-slate-300">
                                                        @if($quizAssignment->status === 'published')
                                                            <span class="px-2 py-0.5 bg-[#28745B]/15 text-[#28745B] border border-[#28745B]/40 text-[9px] font-extrabold rounded-md shadow-2xs">Published</span>
                                                        @elseif($quizAssignment->status === 'closed')
                                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 border border-red-300 text-[9px] font-extrabold rounded-md shadow-2xs">Closed</span>
                                                        @endif

                                                        @if($quizAssignment->available_until)
                                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-300 text-[9px] font-bold rounded-md">Tenggat: {{ \Carbon\Carbon::parse($quizAssignment->available_until)->format('d M H:i') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-[11px] text-slate-400 italic py-2">Belum ada kuis dibuat.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-300 p-12 text-center shadow-2xs">
                <div class="w-12 h-12 bg-[#28745B]/10 text-[#28745B] rounded-full flex items-center justify-center mx-auto mb-3 border border-[#28745B]/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-[#123F35] text-sm">Belum Ada Pertemuan</h3>
                <p class="text-xs text-slate-500 mt-1">Buat pertemuan pertama untuk mulai mengunggah materi dan tugas.</p>
                <button @click="addMeetingModal = true" class="mt-4 bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-2xs">
                    + Tambah Pertemuan Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- MODAL 1: TAMBAH PERTEMUAN -->
    <div x-show="addMeetingModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-[#123F35]/40 backdrop-blur-xs transition-opacity" @click="addMeetingModal = false"></div>

            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-xl transform transition-all relative z-10 border border-slate-300">
                <h3 class="text-base font-bold text-[#123F35] border-b border-slate-200 pb-3 mb-4">Tambah Pertemuan Baru</h3>

                <form action="{{ route('teacher.courses.meetings.store', $course->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-[#123F35] mb-1">Nomor Pertemuan <span class="text-red-500">*</span></label>
                        <input type="number" name="meeting_number" value="{{ old('meeting_number', $course->meetings->count() + 1) }}" required min="1"
                               class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border @error('meeting_number') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-[#28745B] @enderror rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:bg-white transition">
                        @error('meeting_number')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#123F35] mb-1">Judul Pertemuan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Titik Kritis Halal Penyembelihan Unggas" required
                               class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border @error('title') border-red-500 focus:ring-red-500 @else border-slate-300 focus:ring-[#28745B] @enderror rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:bg-white transition">
                        @error('title')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#123F35] mb-1">Pengantar / Deskripsi Singkat</label>
                        <textarea name="description" rows="3" placeholder="Penjelasan singkat mengenai pertemuan ini..."
                                  class="w-full p-3 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#123F35] mb-1">Dibuka Mulai</label>
                            <input type="datetime-local" name="available_from" value="{{ old('available_from') }}"
                                   class="w-full px-2.5 py-2 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#123F35] mb-1">Ditutup Pada</label>
                            <input type="datetime-local" name="available_until" value="{{ old('available_until') }}"
                                   class="w-full px-2.5 py-2 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                    </div>

                    <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                        <button type="button" @click="addMeetingModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">Batal</button>
                        <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 text-xs font-bold rounded-xl shadow-2xs transition">Simpan Pertemuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: EDIT PERTEMUAN -->
    <div x-show="editMeetingModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-[#123F35]/40 backdrop-blur-xs transition-opacity" @click="editMeetingModal = false"></div>

            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-xl transform transition-all relative z-10 border border-slate-300">
                <h3 class="text-base font-bold text-[#123F35] border-b border-slate-200 pb-3 mb-4">Edit Pertemuan</h3>

                <form :action="'/teacher/meetings/' + activeEditMeeting.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-[#123F35] mb-1">Nomor Pertemuan <span class="text-red-500">*</span></label>
                        <input type="number" name="meeting_number" x-model="activeEditMeeting.meeting_number" required min="1"
                               class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#123F35] mb-1">Judul Pertemuan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="activeEditMeeting.title" required
                               class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#123F35] mb-1">Pengantar / Deskripsi Singkat</label>
                        <textarea name="description" rows="3" x-model="activeEditMeeting.description"
                                  class="w-full p-3 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#123F35] mb-1">Dibuka Mulai</label>
                            <input type="datetime-local" name="available_from" x-model="activeEditMeeting.available_from"
                                   class="w-full px-2.5 py-2 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#123F35] mb-1">Ditutup Pada</label>
                            <input type="datetime-local" name="available_until" x-model="activeEditMeeting.available_until"
                                   class="w-full px-2.5 py-2 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                    </div>

                    <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                        <button type="button" @click="editMeetingModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">Batal</button>
                        <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 text-xs font-bold rounded-xl shadow-2xs transition">Perbarui Pertemuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection