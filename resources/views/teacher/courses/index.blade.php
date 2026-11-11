@extends('layouts.app')

@section('title', 'Kelola Kursus - LPP-JPH LMS')
@section('header_title', isset($category) && isset($categoryLabels[$category]) ? 'Kelola Kelas: ' . $categoryLabels[$category] : 'Kelola Kursus & Pelatihan')

@section('content')
<div class="space-y-8">

    <!-- Form Pencarian Course (Card Teratas Berwarna Hijau Deep Forest Green) -->
    <div class="bg-[#123F35] p-5 sm:p-6 rounded-2xl border border-[#E7C982]/20 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-white relative overflow-hidden">
        <!-- Accent Glow Background -->
        <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
        <div class="absolute -right-10 -bottom-10 -z-0 h-48 w-48 rounded-full border border-[#E7C982]/10 blur-xl"></div>
        
        <div class="relative z-10 space-y-1">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>Kelola Program Pelatihan</span>
            </h2>
            <p class="text-xs text-emerald-100/80">Kelola kelas, materi, kuis, dan pendaftaran siswa dalam modul sertifikasi halal.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 relative z-10">
            <!-- Form Pencarian -->
            <form action="{{ route('teacher.courses.index') }}" method="GET" class="flex items-center space-x-2">
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
                <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs border border-white/10">
                    Cari
                </button>
                @if($search || request('category'))
                    <a href="{{ route('teacher.courses.index') }}" class="text-xs font-semibold text-emerald-200 hover:text-white transition">Reset</a>
                @endif
            </form>

            <!-- Tombol Buat Kursus Baru -->
            <a href="{{ route('teacher.courses.create') }}" class="inline-flex items-center justify-center space-x-2 bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 rounded-xl text-xs font-bold shadow-xs transition border border-[#E7C982]/40 whitespace-nowrap">
                <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Kursus Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Tab Kategori Pelatihan -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-6 border-b border-[#28745B]/30 text-xs">
        <a href="{{ route('teacher.courses.index', array_filter(['search' => $search])) }}" 
           class="px-4 py-2 rounded-xl font-bold whitespace-nowrap transition {{ !$category ? 'bg-[#123F35] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-[#F7F5EE] border border-slate-300' }}">
            Semua Kategori
        </a>
        <a href="{{ route('teacher.courses.index', array_filter(['category' => 'penyelia_halal', 'search' => $search])) }}" 
           class="px-4 py-2 rounded-xl font-bold whitespace-nowrap transition {{ $category === 'penyelia_halal' ? 'bg-[#123F35] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-[#F7F5EE] border border-slate-300' }}">
            Penyelia Halal
        </a>
        <a href="{{ route('teacher.courses.index', array_filter(['category' => 'juleha', 'search' => $search])) }}" 
           class="px-4 py-2 rounded-xl font-bold whitespace-nowrap transition {{ $category === 'juleha' ? 'bg-[#123F35] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-[#F7F5EE] border border-slate-300' }}">
            Juleha (Juru Sembelih)
        </a>
        <a href="{{ route('teacher.courses.index', array_filter(['category' => 'auditor_halal', 'search' => $search])) }}" 
           class="px-4 py-2 rounded-xl font-bold whitespace-nowrap transition {{ $category === 'auditor_halal' ? 'bg-[#123F35] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-[#F7F5EE] border border-slate-300' }}">
            Auditor Halal
        </a>
    </div>

    <!-- TAB 1: KELAS YANG DIAJAR (Dikelola Oleh Pengajar Ini) -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-[#123F35] flex items-center space-x-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#28745B]"></span>
            <span>Pelatihan Yang Saya Ajar ({{ $myCourses->count() }})</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($myCourses as $course)
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
                                
                                {{-- BADGE STATUS KELAS --}}
                                @if($course->status === 'published')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#28745B]/15 text-[#123F35] border border-[#28745B]/30">Published</span>
                                @elseif($course->status === 'draft')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#E7C982]/30 text-[#123F35] border border-[#E7C982]">Draft</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Archived</span>
                                @endif
                            </div>

                            <div class="flex items-center space-x-1.5">
                                <span class="text-[10px] font-bold text-[#123F35] bg-[#E7C982]/20 px-2 py-0.5 rounded border border-[#E7C982]/40">
                                    {{ $course->category_label }}
                                </span>
                            </div>

                            <h3 class="font-bold text-[#123F35] text-base line-clamp-1 hover:text-[#28745B] transition" title="{{ $course->title }}">
                                <a href="{{ route('teacher.courses.show', $course->id) }}">{{ $course->title }}</a>
                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-2">
                                {{ $course->description ?? 'Belum ada deskripsi untuk kursus ini.' }}
                            </p>
                        </div>

                        <!-- Enrollment Key Box -->
                        <div class="p-2.5 bg-[#F7F5EE] border border-[#28745B]/20 rounded-xl flex items-center justify-between">
                            <span class="text-[11px] font-medium text-slate-600">Enrollment Key:</span>
                            <code class="text-xs font-extrabold text-[#123F35] bg-white px-2 py-0.5 rounded border border-[#28745B]/20 tracking-wider">{{ $course->enrollment_key }}</code>
                        </div>
                    </div>

                    <!-- Footer Card Actions -->
                    <div class="px-5 py-3.5 bg-[#F7F5EE]/50 border-t border-[#28745B]/15 rounded-b-2xl flex items-center justify-between text-xs">
                        <a href="{{ route('teacher.courses.show', $course->id) }}" class="font-bold text-[#28745B] hover:text-[#123F35] flex items-center space-x-1 transition">
                            <span>Kelola Materi</span>
                            <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        
                        <div class="flex items-center space-x-1">
                            <!-- Edit Button -->
                            <a href="{{ route('teacher.courses.edit', $course->id) }}" class="p-1.5 text-slate-400 hover:text-[#28745B] transition rounded-lg hover:bg-[#F7F5EE]" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            <!-- Delete Button -->
                            <form action="{{ route('teacher.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini beserta seluruh pertemuannya?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 transition rounded-lg hover:bg-red-50" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-dashed border-[#28745B]/20 p-8 text-center text-xs text-slate-400">
                    Anda belum membuat atau mengajar pelatihan aktif apapun. Silakan klik "Buat Kursus Baru" di atas.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 2: KATALOG COURSE TERSEDIA LAINNYA (Tidak Diajar Oleh Pengajar Ini) -->
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

                    <div class="px-5 py-3.5 bg-[#F7F5EE]/50 border-t border-[#28745B]/15 rounded-b-2xl flex items-center justify-between text-xs">
                        <span class="text-[11px] font-medium text-slate-500 truncate">Pengajar: {{ $course->creator->name ?? 'Instruktur Utama' }}</span>
                        <span class="text-[10px] font-extrabold text-[#123F35] bg-[#E7C982]/30 px-2 py-1 rounded border border-[#E7C982]">Tersedia</span>
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

</div>
@endsection