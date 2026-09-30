@extends('layouts.app')

@section('title', $course->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Pelatihan')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="space-y-6">

    <!-- Header Detail Kursus -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-start space-x-4">
            @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="{{ $course->title }}" class="w-20 h-20 rounded-xl object-cover border border-slate-200">
            @else
                <div class="w-20 h-20 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xl border border-amber-200">
                    {{ substr($course->code, 0, 3) }}
                </div>
            @endif

            <div class="space-y-1">
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-slate-100 text-slate-700 uppercase">
                    {{ $course->code }}
                </span>
                <h1 class="text-xl font-bold text-slate-800">{{ $course->title }}</h1>
                <p class="text-xs text-slate-500 max-w-2xl">{{ $course->description ?? 'Tidak ada deskripsi.' }}</p>
                <div class="pt-1 text-[11px] text-slate-400">
                    Pengajar: <span class="font-semibold text-slate-600">{{ $course->creator->name ?? 'Instruktur' }}</span>
                </div>
            </div>
        </div>

        <a href="{{ route('student.courses.index') }}" class="text-xs font-semibold text-slate-600 hover:bg-slate-100 px-4 py-2 rounded-lg transition border border-slate-200">
            &larr; Kembali ke Katalog
        </a>
    </div>

    <!-- Pertemuan & Modul Pembelajaran -->
    <div class="space-y-4">
        <h2 class="text-base font-bold text-slate-800">Daftar Pertemuan & Modul Pembelajaran</h2>

        @forelse($course->meetings->sortBy('meeting_number') as $meeting)
            <div x-data="{ expanded: true }" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                
                <!-- Baris Judul Pertemuan (Accordion Header) -->
                <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between cursor-pointer select-none" @click="expanded = !expanded">
                    <div class="flex items-center space-x-3">
                        <span class="w-8 h-8 rounded-lg bg-amber-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                            {{ $meeting->meeting_number }}
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">{{ $meeting->title }}</h3>
                            @if($meeting->description)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $meeting->description }}</p>
                            @endif
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>

                <!-- Konten Pertemuan (Materi, Tugas Upload, & Kuis) -->
                <div x-show="expanded" class="p-5 space-y-5">

                    <!-- 1. MATERI PEMBELAJARAN -->
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span>Materi Pembelajaran</span>
                        </div>

                        @if($meeting->materials && $meeting->materials->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($meeting->materials as $material)
                                    <div class="p-3 bg-slate-50 hover:bg-amber-50/50 rounded-xl border border-slate-200/80 transition flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5 overflow-hidden pr-2">
                                            <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <span class="text-xs text-slate-800 font-semibold truncate">{{ $material->title }}</span>
                                        </div>
                                        <a href="{{ route('student.materials.show', $material->id) }}" class="text-[11px] text-amber-600 font-bold hover:underline flex-shrink-0">
                                            Buka &rarr;
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[11px] text-slate-400 italic">Belum ada materi dipublikasikan.</p>
                        @endif
                    </div>

                    <hr class="border-slate-100">

                    <!-- 2. TUGAS UPLOAD (Assignment jenis 'submission') -->
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span>Tugas Upload (Submission)</span>
                        </div>

                        @if($meeting->assignments && $meeting->assignments->where('type', 'submission')->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($meeting->assignments->where('type', 'submission') as $assignment)
                                    <div class="p-3 bg-slate-50 hover:bg-emerald-50/50 rounded-xl border border-slate-200/80 transition flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5 overflow-hidden pr-2">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                </svg>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="text-xs text-slate-800 font-semibold truncate">{{ $assignment->title }}</div>
                                                @if($assignment->available_until)
                                                    <div class="text-[10px] text-slate-400">Tenggat: {{ \Carbon\Carbon::parse($assignment->available_until)->format('d M Y H:i') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('student.submissions.show', $assignment->id) }}" class="text-[11px] text-emerald-600 font-bold hover:underline flex-shrink-0">
                                            Kirim Tugas &rarr;
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[11px] text-slate-400 italic">Belum ada tugas upload.</p>
                        @endif
                    </div>

                    <hr class="border-slate-100">

                    <!-- 3. KUIS (Quiz) -->
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Kuis</span>
                        </div>

                        @if($meeting->quizzes && $meeting->quizzes->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($meeting->quizzes as $quiz)
                                    <div class="p-3 bg-slate-50 hover:bg-indigo-50/50 rounded-xl border border-slate-200/80 transition flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5 overflow-hidden pr-2">
                                            <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="text-xs text-slate-800 font-semibold truncate">{{ $quiz->assignment->title ?? 'Kuis Pertemuan' }}</div>
                                                <div class="text-[10px] text-slate-400">
                                                    Durasi: {{ $quiz->duration_minutes }} menit | {{ $quiz->question_count }} Soal
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('student.quizzes.show', $quiz->id) }}" class="text-[11px] text-indigo-600 font-bold hover:underline flex-shrink-0">
                                            Kerjakan &rarr;
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[11px] text-slate-400 italic">Belum ada kuis untuk pertemuan ini.</p>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-xs text-slate-400">
                Belum ada pertemuan yang dibuat oleh instruktur untuk kelas ini.
            </div>
        @endforelse
    </div>

</div>
@endsection