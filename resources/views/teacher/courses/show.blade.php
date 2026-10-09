@extends('layouts.app')

@section('title', $course->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Kursus & Pertemuan')

@section('content')
<!-- Script CDN Alpine.js untuk fitur Collapse/Expand & Modal -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div x-data="{ addMeetingModal: false, editMeetingModal: false, activeEditMeeting: {} }" class="space-y-6">

    <!-- Top Card: Banner & Detail Pelatihan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start space-x-4">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="{{ $course->title }}" class="w-20 h-20 rounded-xl object-cover border border-slate-200">
                @else
                    <div class="w-20 h-20 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xl border border-amber-200">
                        {{ substr($course->code, 0, 3) }}
                    </div>
                @endif

                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-slate-100 text-slate-700 uppercase">
                            {{ $course->code }}
                        </span>
                        @if($course->status === 'published')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Published</span>
                        @elseif($course->status === 'draft')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Draft</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">Archived</span>
                        @endif
                    </div>
                    <h1 class="text-xl font-bold text-slate-800">{{ $course->title }}</h1>
                    <p class="text-xs text-slate-500 line-clamp-2 max-w-2xl">{{ $course->description ?? 'Belum ada deskripsi kursus.' }}</p>
                </div>
            </div>

            <!-- Enrollment Key Box -->
            <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl space-y-1 min-w-[200px]">
                <div class="text-[11px] font-medium text-amber-800">Enrollment Key:</div>
                <div class="flex items-center justify-between">
                    <code class="text-sm font-bold text-amber-700 bg-amber-200/60 px-2 py-1 rounded">{{ $course->enrollment_key }}</code>
                    <form action="{{ route('teacher.courses.regenerateKey', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="p-1 text-amber-600 hover:text-amber-800 transition" title="Regenerate Key" onclick="return confirm('Buat Enrollment Key baru?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Header Pertemuan & Tombol Tambah -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Manajemen Pertemuan (Meetings)</h2>
            <p class="text-xs text-slate-500">Susun modul pembelajaran, materi, tugas, dan kuis berdasarkan urutan pertemuan.</p>
        </div>
        <button @click="addMeetingModal = true" class="inline-flex items-center space-x-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah Pertemuan</span>
        </button>
    </div>

    <!-- Accordion List Pertemuan (Meetings) -->
    <div class="space-y-4">
        @forelse($course->meetings->sortBy('meeting_number') as $meeting)
            <div x-data="{ expanded: false }" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition">
                
                <!-- Header Accordion (Baris Judul Pertemuan) -->
                <div class="p-5 flex items-center justify-between bg-white hover:bg-slate-50/80 transition cursor-pointer select-none" @click="expanded = !expanded">
                    <div class="flex items-center space-x-4">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 font-bold text-sm flex items-center justify-center flex-shrink-0">
                            {{ $meeting->meeting_number }}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">{{ $meeting->title }}</h3>
                            @if($meeting->available_from || $meeting->available_until)
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Akses: {{ $meeting->available_from ? \Carbon\Carbon::parse($meeting->available_from)->format('d M Y H:i') : 'Sekarang' }} 
                                    s/d {{ $meeting->available_until ? \Carbon\Carbon::parse($meeting->available_until)->format('d M Y H:i') : 'Selamanya' }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions & Toggle Arrow -->
                    <div class="flex items-center space-x-3" @click.stop>
                        <!-- Edit Button -->
                        <button @click="activeEditMeeting = { 
                                    id: '{{ $meeting->id }}', 
                                    title: '{{ addslashes($meeting->title) }}', 
                                    meeting_number: '{{ $meeting->meeting_number }}', 
                                    description: '{{ addslashes($meeting->description) }}', 
                                    available_from: '{{ $meeting->available_from ? \Carbon\Carbon::parse($meeting->available_from)->format('Y-m-d\TH:i') : '' }}', 
                                    available_until: '{{ $meeting->available_until ? \Carbon\Carbon::parse($meeting->available_until)->format('Y-m-d\TH:i') : '' }}' 
                                }; editMeetingModal = true" 
                                class="p-1.5 text-slate-400 hover:text-amber-600 transition rounded-lg hover:bg-slate-100" title="Edit Pertemuan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('teacher.meetings.destroy', $meeting->id) }}" method="POST" onsubmit="return confirm('Hapus pertemuan ini beserta seluruh isinya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 transition rounded-lg hover:bg-slate-100" title="Hapus Pertemuan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>

                        <!-- Chevron Icon (Collapse/Expand Indicator) -->
                        <div class="p-1.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': expanded }" @click="expanded = !expanded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Body Accordion (Kontainer Isi Materi, Tugas, Kuis) -->
                <div x-show="expanded" x-collapse class="border-t border-slate-100 bg-slate-50/50 p-6 space-y-5">
                    
                    <!-- Deskripsi / Pengantar Pertemuan -->
                    @if($meeting->description)
                        <div class="text-xs text-slate-600 bg-white p-3.5 rounded-xl border border-slate-200/60 shadow-sm">
                            <span class="font-semibold text-slate-700 block mb-0.5">Pengantar / Deskripsi:</span>
                            <p>{{ $meeting->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        
                        <!-- ================= 1. SECTION MATERI ================= -->
                        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <span class="text-xs font-bold text-slate-800">Materi ({{ $meeting->materials->count() }})</span>
                                    </div>
                                    <a href="{{ route('teacher.meetings.materials.create', $meeting->id) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                                        + Tambah
                                    </a>
                                </div>

                                <!-- Daftar Materi -->
                                @forelse($meeting->materials as $material)
                                    <div class="group flex items-center justify-between p-2.5 mb-2 rounded-lg bg-slate-50 border border-slate-100 hover:border-emerald-200 transition">
                                        <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                                            <!-- Icon Indicator (Embedded / Teks) -->
                                            <div class="w-7 h-7 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                                @if($material->media && $material->media->external_url)
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <span class="text-xs font-medium text-slate-700 truncate group-hover:text-emerald-600 transition">{{ $material->title }}</span>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex items-center space-x-1.5 flex-shrink-0">
                                            <a href="{{ route('teacher.materials.show', $material->id) }}" class="p-1 text-slate-400 hover:text-slate-600 transition" title="Lihat">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('teacher.materials.edit', $material->id) }}" class="p-1 text-slate-400 hover:text-amber-600 transition" title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L20 4.828a2 2 0 010 2.828l-8.586 8.586z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 5l2 2"/></svg>
                                            </a>
                                            <form action="{{ route('teacher.materials.destroy', $material->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition" title="Hapus">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 text-center border border-dashed border-slate-200 rounded-lg">
                                        <p class="text-xs text-slate-400">Belum ada materi dibuat</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- ================= 2. SECTION TUGAS (SUBMISSION) ================= -->
                        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                        <span class="text-xs font-bold text-slate-800">Tugas ({{ $meeting->assignments ? $meeting->assignments->where('type', 'submission')->count() : 0 }})</span>
                                    </div>
                                    <a href="{{ route('teacher.meetings.assignments.create', $meeting->id) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition">
                                        + Tambah
                                    </a>
                                </div>

                                <!-- Loop Tugas Submission -->
                                @if(isset($meeting->assignments) && $meeting->assignments->where('type', 'submission')->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($meeting->assignments->where('type', 'submission') as $assignment)
                                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100 hover:bg-slate-100/80 transition">
                                                <div class="overflow-hidden pr-2">
                                                    <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="text-xs font-semibold text-slate-800 hover:text-blue-600 transition truncate block">
                                                        {{ $assignment->title }}
                                                    </a>
                                                    <!-- Tanggal Dibuka & Ditutup -->
                                                    @if($assignment->available_from || $assignment->available_until)
                                                        <p class="text-[10px] text-slate-400 mt-0.5">
                                                            {{ $assignment->available_from ? \Carbon\Carbon::parse($assignment->available_from)->format('d M Y H:i') : 'Sekarang' }} 
                                                            s/d 
                                                            {{ $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('d M Y H:i') : 'Tanpa batas' }}
                                                        </p>
                                                    @else
                                                        <p class="text-[10px] text-slate-400 mt-0.5">Waktu pengerjaan: Fleksibel</p>
                                                    @endif
                                                </div>

                                                <!-- Aksi: Show, Edit, Delete -->
                                                <div class="flex items-center space-x-1.5 flex-shrink-0">
                                                    <!-- Detail/Show -->
                                                    <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="p-1 text-slate-400 hover:text-blue-600 transition rounded" title="Detail Tugas">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>

                                                    <!-- Edit -->
                                                    <a href="{{ route('teacher.assignments.edit', $assignment->id) }}" class="p-1 text-slate-400 hover:text-amber-600 transition rounded" title="Edit Tugas">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>

                                                    <!-- Delete -->
                                                    <form action="{{ route('teacher.assignments.destroy', $assignment->id) }}" method="POST" onsubmit="return confirm('Hapus tempat submission tugas ini beserta seluruh lampirannya?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition rounded" title="Hapus Tugas">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 text-center border border-dashed border-slate-200 rounded-lg">
                                        <p class="text-xs text-slate-400">Belum ada tugas dibuat</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- ================= 3. SECTION KUIS (QUIZ) ================= -->
                        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-xs font-bold text-slate-800">Kuis ({{ $meeting->assignments ? $meeting->assignments->where('type', 'quiz')->count() : 0 }})</span>
                                    </div>
                                    <a href="{{ route('teacher.meetings.quizzes.create', $meeting->id) }}" class="text-xs font-semibold text-amber-600 hover:text-amber-700 transition">
                                        + Tambah
                                    </a>
                                </div>

                                <!-- Loop Kuis (Quiz) -->
                                @if(isset($meeting->assignments) && $meeting->assignments->where('type', 'quiz')->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($meeting->assignments->where('type', 'quiz') as $quizAssignment)
                                            <div class="group flex items-center justify-between p-2.5 mb-2 rounded-lg bg-slate-50 border border-slate-100 hover:border-amber-200 hover:bg-slate-100/80 transition">
                                                <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                                                    <!-- Icon Indicator Kuis -->
                                                    <div class="w-7 h-7 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                        </svg>
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <a href="{{ route('teacher.quizzes.show', $quizAssignment->id) }}" class="text-xs font-semibold text-slate-800 hover:text-amber-600 transition truncate block">
                                                            {{ $quizAssignment->title }}
                                                        </a>
                                                        <!-- Tanggal Dibuka & Ditutup -->
                                                        @if($quizAssignment->available_from || $quizAssignment->available_until)
                                                            <p class="text-[10px] text-slate-400 mt-0.5">
                                                                {{ $quizAssignment->available_from ? \Carbon\Carbon::parse($quizAssignment->available_from)->format('d M Y H:i') : 'Sekarang' }} 
                                                                s/d 
                                                                {{ $quizAssignment->available_until ? \Carbon\Carbon::parse($quizAssignment->available_until)->format('d M Y H:i') : 'Tanpa batas' }}
                                                            </p>
                                                        @else
                                                            <p class="text-[10px] text-slate-400 mt-0.5">Waktu pengerjaan: Fleksibel</p>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Action Buttons: Show, Edit, Delete -->
                                                <div class="flex items-center space-x-1.5 flex-shrink-0">
                                                    <!-- Detail/Show -->
                                                    <a href="{{ route('teacher.quizzes.show', $quizAssignment->id) }}" class="p-1 text-slate-400 hover:text-amber-600 transition rounded" title="Detail Kuis & Bank Soal">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>

                                                    <!-- Edit -->
                                                    <a href="{{ route('teacher.quizzes.edit', $quizAssignment->id) }}" class="p-1 text-slate-400 hover:text-amber-600 transition rounded" title="Edit Konfigurasi Kuis">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>

                                                    <!-- Delete -->
                                                    <form action="{{ route('teacher.quizzes.destroy', $quizAssignment->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus kuis ini beserta seluruh bank soalnya?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition rounded" title="Hapus Kuis">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 text-center border border-dashed border-slate-200 rounded-lg">
                                        <p class="text-xs text-slate-400">Belum ada kuis dibuat</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">Belum Ada Pertemuan</h3>
                <p class="text-xs text-slate-500 mt-1">Buat pertemuan pertama untuk mulai mengunggah materi dan tugas.</p>
                <button @click="addMeetingModal = true" class="mt-4 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
                    + Tambah Pertemuan Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- MODAL 1: TAMBAH PERTEMUAN -->
    <div x-show="addMeetingModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="addMeetingModal = false"></div>

            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-xl transform transition-all relative z-10">
                <h3 class="text-base font-bold text-slate-800 border-b pb-3 mb-4">Tambah Pertemuan Baru</h3>

                <form action="{{ route('teacher.courses.meetings.store', $course->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Pertemuan <span class="text-red-500">*</span></label>
                        <input type="number" name="meeting_number" value="{{ $course->meetings->count() + 1 }}" required min="1"
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Pertemuan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" placeholder="Contoh: Titik Kritis Halal Penyembelihan Unggas" required
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pengantar / Deskripsi Singkat</label>
                        <textarea name="description" rows="3" placeholder="Penjelasan singkat mengenai pertemuan ini..."
                                  class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Dibuka Mulai</label>
                            <input type="datetime-local" name="available_from"
                                   class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Ditutup Pada</label>
                            <input type="datetime-local" name="available_until"
                                   class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="pt-3 flex justify-end space-x-2 border-t">
                        <button type="button" @click="addMeetingModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-xs font-semibold rounded-lg shadow-sm">Simpan Pertemuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: EDIT PERTEMUAN -->
    <div x-show="editMeetingModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="editMeetingModal = false"></div>

            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-xl transform transition-all relative z-10">
                <h3 class="text-base font-bold text-slate-800 border-b pb-3 mb-4">Edit Pertemuan</h3>

                <form :action="'/teacher/meetings/' + activeEditMeeting.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Pertemuan <span class="text-red-500">*</span></label>
                        <input type="number" name="meeting_number" x-model="activeEditMeeting.meeting_number" required min="1"
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Pertemuan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="activeEditMeeting.title" required
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pengantar / Deskripsi Singkat</label>
                        <textarea name="description" rows="3" x-model="activeEditMeeting.description"
                                  class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Dibuka Mulai</label>
                            <input type="datetime-local" name="available_from" x-model="activeEditMeeting.available_from"
                                   class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Ditutup Pada</label>
                            <input type="datetime-local" name="available_until" x-model="activeEditMeeting.available_until"
                                   class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="pt-3 flex justify-end space-x-2 border-t">
                        <button type="button" @click="editMeetingModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-xs font-semibold rounded-lg shadow-sm">Perbarui Pertemuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection