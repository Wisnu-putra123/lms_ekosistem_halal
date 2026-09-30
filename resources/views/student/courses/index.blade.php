@extends('layouts.app')

@section('title', 'Katalog & Kursus Saya - Ekosistem Halal')
@section('header_title', 'Pembelajaran Saya')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div x-data="{ enrollModal: false, selectedCourse: {} }" class="space-y-8">

    <!-- Form Pencarian Course -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-800">Cari Pelatihan Halal</h2>
            <p class="text-xs text-slate-500">Temukan kelas sertifikasi dan modul kompetensi halal yang sesuai.</p>
        </div>
        <form action="{{ route('student.courses.index') }}" method="GET" class="flex items-center space-x-2">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode..."
                       class="w-64 px-3.5 py-2 pl-9 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-xs font-semibold transition">
                Cari
            </button>
            @if($search)
                <a href="{{ route('student.courses.index') }}" class="text-xs text-slate-400 hover:text-slate-600">Reset</a>
            @endif
        </form>
    </div>

    <!-- TAB 1: KURSUS SAYA (Sudah Di-enroll) -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-800 flex items-center space-x-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <span>Pelatihan Yang Sedang Diikuti ({{ $myEnrollments->count() }})</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($myEnrollments as $enrollment)
                @php $course = $enrollment->course; @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between overflow-hidden hover:shadow-md transition">
                    @if($course->thumbnail)
                        <div class="h-36 w-full bg-slate-100 overflow-hidden">
                            <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="p-5 space-y-3 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider bg-slate-100 text-slate-600 uppercase">
                                {{ $course->code }}
                            </span>
                            <span class="text-[11px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full font-medium">Terdaftar</span>
                        </div>

                        <h3 class="font-bold text-slate-800 text-base line-clamp-1 hover:text-amber-600 transition">
                            <a href="{{ route('student.courses.show', $course->id) }}">{{ $course->title }}</a>
                        </h3>

                        <p class="text-xs text-slate-500 line-clamp-2">
                            {{ $course->description ?? 'Belum ada deskripsi.' }}
                        </p>
                    </div>

                    <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Pengajar: {{ $course->creator->name ?? 'Instruktur' }}</span>
                        <a href="{{ route('student.courses.show', $course->id) }}" class="inline-flex items-center space-x-1 bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                            <span>Buka Pelatihan</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-8 text-center text-xs text-slate-400">
                    Anda belum terdaftar dalam pelatihan apapun. Silakan pilih kelas dari katalog di bawah.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 2: KATALOG COURSE TERSEDIA -->
    <div class="space-y-4 pt-4 border-t border-slate-200">
        <h2 class="text-lg font-bold text-slate-800 flex items-center space-x-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            <span>Katalog Pelatihan Lainnya</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($availableCourses as $course)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between overflow-hidden hover:shadow-md transition">
                    @if($course->thumbnail)
                        <div class="h-36 w-full bg-slate-100 overflow-hidden">
                            <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="p-5 space-y-3 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider bg-amber-50 text-amber-700 uppercase">
                                {{ $course->code }}
                            </span>
                            <span class="text-[11px] text-slate-400">Tersedia</span>
                        </div>

                        <h3 class="font-bold text-slate-800 text-base line-clamp-1">
                            {{ $course->title }}
                        </h3>

                        <p class="text-xs text-slate-500 line-clamp-2">
                            {{ $course->description ?? 'Belum ada deskripsi.' }}
                        </p>
                    </div>

                    <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Pengajar: {{ $course->creator->name ?? 'Instruktur' }}</span>
                        <button @click="selectedCourse = { id: '{{ $course->id }}', title: '{{ addslashes($course->title) }}', code: '{{ $course->code }}' }; enrollModal = true"
                                class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold transition">
                            + Ambil Kursus
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-8 text-center text-xs text-slate-400">
                    Tidak ada pelatihan lain yang tersedia saat ini.
                </div>
            @endforelse
        </div>

        <div>
            {{ $availableCourses->links() }}
        </div>
    </div>

    <!-- MODAL MASUKKAN ENROLLMENT KEY -->
    <div x-show="enrollModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="enrollModal = false"></div>

            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-xl transform transition-all relative z-10">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h3 class="text-base font-bold text-slate-800">Daftar Pelatihan</h3>
                    <span class="text-xs text-amber-600 font-bold uppercase" x-text="selectedCourse.code"></span>
                </div>

                <p class="text-xs text-slate-600 mb-4" x-text="'Masukkan Enrollment Key dari pengajar untuk bergabung ke kelas ' + selectedCourse.title"></p>

                <form :action="'/student/courses/' + selectedCourse.id + '/enroll'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Enrollment Key <span class="text-red-500">*</span></label>
                        <input type="text" name="enrollment_key" placeholder="Contoh: HALAL-8A2B9C" required uppercase
                               class="w-full px-3.5 py-2.5 bg-amber-50 border border-amber-200 rounded-lg text-sm font-bold text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 uppercase">
                        @error('enrollment_key')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-3 flex justify-end space-x-2 border-t">
                        <button type="button" @click="enrollModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 text-xs font-semibold rounded-lg shadow-sm">
                            Konfirmasi & Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection