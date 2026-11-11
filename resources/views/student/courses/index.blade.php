@extends('layouts.app')

@section('title', 'Katalog & Kursus Saya - LPP-JPH LMS')
@section('header_title', isset($category) && isset($categoryLabels[$category]) ? 'Katalog: ' . $categoryLabels[$category] : 'Pembelajaran Saya')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div x-data="{ enrollModal: false, selectedCourse: {} }" class="space-y-8">

    <!-- Form Pencarian Course (Card Teratas Berwarna Hijau Deep Forest Green) -->
    <div class="bg-[#123F35] p-5 sm:p-6 rounded-2xl border border-[#E7C982]/20 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-white relative overflow-hidden">
        <!-- Accent Glow Background -->
        <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
        <div class="absolute -right-10 -bottom-10 -z-0 h-48 w-48 rounded-full border border-[#E7C982]/10 blur-xl"></div>
        
        <div class="relative z-10 space-y-1">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span>Cari Program Pelatihan</span>
            </h2>
            <p class="text-xs text-emerald-100/80">Temukan kelas sertifikasi dan modul kompetensi yang sesuai.</p>
        </div>

        <form action="{{ route('student.courses.index') }}" method="GET" class="flex items-center space-x-2 relative z-10">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative w-full sm:w-auto">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode..."
                       class="w-full sm:w-64 px-3.5 py-2 pl-9 bg-white/10 border border-white/20 rounded-xl text-xs text-white placeholder-emerald-100/60 focus:outline-none focus:ring-2 focus:ring-[#E7C982] focus:bg-white/20 transition backdrop-blur-xs">
                <svg class="w-4 h-4 text-emerald-100/70 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <!-- Tombol Pencarian -->
            <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs border border-white/10">
                Cari
            </button>
            @if($search || request('category'))
                <a href="{{ route('student.courses.index') }}" class="text-xs font-semibold text-emerald-200 hover:text-white transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Filter Tab Kategori Pelatihan -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-6 border-b border-[#28745B]/30 text-xs ">
        <a href="{{ route('student.courses.index', array_filter(['search' => $search])) }}" 
           class="px-4 py-2 rounded-xl font-bold whitespace-nowrap transition {{ !$category ? 'bg-[#123F35] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-[#F7F5EE] border border-slate-300' }}">
            Semua Kategori
        </a>
        <a href="{{ route('student.courses.index', array_filter(['category' => 'penyelia_halal', 'search' => $search])) }}" 
           class="px-4 py-2 rounded-xl font-bold whitespace-nowrap transition {{ $category === 'penyelia_halal' ? 'bg-[#123F35] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-[#F7F5EE] border border-slate-300' }}">
            Penyelia Halal
        </a>
        <a href="{{ route('student.courses.index', array_filter(['category' => 'juleha', 'search' => $search])) }}" 
           class="px-4 py-2 rounded-xl font-bold whitespace-nowrap transition {{ $category === 'juleha' ? 'bg-[#123F35] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-[#F7F5EE] border border-slate-300' }}">
            Juleha (Juru Sembelih)
        </a>
        <a href="{{ route('student.courses.index', array_filter(['category' => 'auditor_halal', 'search' => $search])) }}" 
           class="px-4 py-2 rounded-xl font-bold whitespace-nowrap transition {{ $category === 'auditor_halal' ? 'bg-[#123F35] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-[#F7F5EE] border border-slate-300' }}">
            Auditor Halal
        </a>
    </div>

    <!-- TAB 1: KURSUS SAYA (Aktif & Berjalan Masa Tenggang) -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-[#123F35] flex items-center space-x-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#28745B]"></span>
            <span>Pelatihan Yang Sedang Diikuti ({{ $myEnrollments->count() }})</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($myEnrollments as $enrollment)
                @php $course = $enrollment->course; @endphp
                <div class="bg-white rounded-2xl border border-[#28745B]/30 shadow-2xs flex flex-col justify-between overflow-hidden hover:shadow-md hover:border-[#28745B]/30 transition duration-200">
                    
                    {{-- CONTAINER THUMBNAIL BERUKURAN KONSISTEN --}}
                    <div class="h-36 w-full bg-[#F7F5EE] overflow-hidden border-b border-slate-100 relative">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                            {{-- FALLBACK DESAIN SAAT TIDAK ADA THUMBNAIL --}}
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-[#123F35]/10 via-[#28745B]/15 to-[#F7F5EE] p-4 text-center">
                                <svg class="w-10 h-10 text-[#28745B]/50 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#123F35]/60">{{ $course->category_label }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-5 space-y-3 flex-1 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold tracking-wider bg-[#F7F5EE] text-[#123F35] uppercase border border-[#28745B]/15">
                                    {{ $course->code }}
                                </span>
                                
                                {{-- BADGE STATUS ENROLLMENT --}}
                                @if($enrollment->status === 'completed')
                                    <span class="text-[11px] text-[#123F35] bg-[#E7C982]/30 border border-[#E7C982] px-2.5 py-0.5 rounded-full font-bold flex items-center space-x-1">
                                        <span>✓ Selesai (Lulus)</span>
                                    </span>
                                @else
                                    <span class="text-[11px] text-[#123F35] bg-[#28745B]/15 border border-[#28745B]/30 px-2 py-0.5 rounded-full font-bold">Terdaftar</span>
                                @endif
                            </div>

                            <h3 class="font-bold text-[#123F35] text-base line-clamp-1 hover:text-[#28745B] transition">
                                <a href="{{ route('student.courses.show', $course->id) }}" title="{{ $course->title }}">{{ $course->title }}</a>
                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-2">
                                {{ $course->description ?? 'Belum ada deskripsi.' }}
                            </p>
                        </div>

                        {{-- INFORMASI TANGGAL KEDALUWARSA AKSES --}}
                        @if($enrollment->status === 'completed' && $enrollment->access_expires_at)
                            <div class="pt-2 border-t border-slate-100 text-[11px] text-amber-800 font-medium flex items-center justify-between">
                                <span>Akses Kedaluwarsa:</span>
                                <span class="font-bold">{{ \Carbon\Carbon::parse($enrollment->access_expires_at)->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="px-5 py-3.5 bg-[#F7F5EE]/50 border-t border-[#28745B]/15 rounded-b-2xl flex items-center justify-between">
                        <span class="text-[11px] font-medium text-slate-500 truncate max-w-[140px]">Pengajar: {{ $course->creator->name ?? 'Instruktur' }}</span>
                        <!-- Tombol Buka Pelatihan -->
                        <a href="{{ route('student.courses.show', $course->id) }}" class="inline-flex items-center space-x-1 bg-[#28745B] hover:bg-[#123F35] text-white px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-xs">
                            <span>Buka Pelatihan</span>
                            <svg class="w-3.5 h-3.5 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-dashed border-[#28745B]/20 p-8 text-center text-xs text-slate-400">
                    Anda belum terdaftar dalam pelatihan aktif apapun. Silakan pilih kelas dari katalog di bawah.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 2: KATALOG COURSE TERSEDIA -->
    <div class="space-y-4 pt-6 border-t border-[#28745B]/30">
        <h2 class="text-lg font-bold text-[#123F35] flex items-center space-x-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#E7C982]"></span>
            <span>Katalog Pelatihan Lainnya</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($availableCourses as $course)
                <div class="bg-white rounded-2xl border border-[#28745B]/30 shadow-2xs flex flex-col justify-between overflow-hidden hover:shadow-md hover:border-[#28745B]/30 transition duration-200">
                    
                    {{-- CONTAINER THUMBNAIL BERUKURAN KONSISTEN --}}
                    <div class="h-36 w-full bg-[#F7F5EE] overflow-hidden border-b border-slate-100 relative">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                            {{-- FALLBACK DESAIN SAAT TIDAK ADA THUMBNAIL --}}
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-[#123F35]/10 via-[#28745B]/15 to-[#F7F5EE] p-4 text-center">
                                <svg class="w-10 h-10 text-[#28745B]/50 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#123F35]/60">{{ $course->category_label }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-5 space-y-3 flex-1 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold tracking-wider bg-[#E7C982]/20 text-[#123F35] uppercase border border-[#E7C982]/40">
                                    {{ $course->code }}
                                </span>
                                <span class="text-[10px] font-bold text-[#123F35] bg-[#28745B]/15 px-2 py-0.5 rounded border border-[#28745B]/30">
                                    {{ $course->category_label }}
                                </span>
                            </div>

                            <h3 class="font-bold text-[#123F35] text-base line-clamp-1" title="{{ $course->title }}">
                                {{ $course->title }}
                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-2">
                                {{ $course->description ?? 'Belum ada deskripsi.' }}
                            </p>
                        </div>
                    </div>

                    <div class="px-5 py-3.5 bg-[#F7F5EE]/50 border-t border-[#28745B]/15 rounded-b-2xl flex items-center justify-between">
                        <span class="text-[11px] font-medium text-slate-500 truncate max-w-[140px]">Pengajar: {{ $course->creator->name ?? 'Instruktur' }}</span>
                        <!-- Tombol Ambil Kursus -->
                        <button @click="selectedCourse = { id: '{{ $course->id }}', title: '{{ addslashes($course->title) }}', code: '{{ $course->code }}' }; enrollModal = true"
                                class="bg-[#28745B] hover:bg-[#123F35] text-white px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-xs">
                            + Ambil Kursus
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-dashed border-[#28745B]/20 p-8 text-center text-xs text-slate-400">
                    Tidak ada pelatihan lain yang tersedia saat ini pada kategori ini.
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
            <div class="fixed inset-0 bg-[#123F35]/60 backdrop-blur-xs transition-opacity" @click="enrollModal = false"></div>

            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-xl transform transition-all relative z-10 border border-[#28745B]/20">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <h3 class="text-base font-bold text-[#123F35]">Daftar Pelatihan</h3>
                    <span class="text-xs text-[#123F35] font-extrabold uppercase bg-[#E7C982]/30 px-2 py-0.5 rounded border border-[#E7C982]" x-text="selectedCourse.code"></span>
                </div>

                <p class="text-xs text-slate-600 mb-4" x-text="'Masukkan Enrollment Key dari pengajar untuk bergabung ke kelas ' + selectedCourse.title"></p>

                <form :action="'/student/courses/' + selectedCourse.id + '/enroll'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-[#123F35] mb-1">Enrollment Key <span class="text-red-500">*</span></label>
                        <input type="text" name="enrollment_key" placeholder="Contoh: HALAL-8A2B9C" required uppercase
                               class="w-full px-3.5 py-2.5 bg-[#F7F5EE] border border-[#28745B]/30 rounded-xl text-sm font-extrabold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] uppercase tracking-wider">
                        @error('enrollment_key')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-3 flex justify-end space-x-2 border-t border-slate-100">
                        <button type="button" @click="enrollModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">Batal</button>
                        <!-- Tombol Konfirmasi -->
                        <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-5 py-2 text-xs font-bold rounded-xl shadow-md transition">
                            Konfirmasi & Masuk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection