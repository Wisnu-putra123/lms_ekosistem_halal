@extends('layouts.app')

@section('title', 'Kelola Kursus - Ekosistem Halal')
@section('header_title', 'Kelola Kursus')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center space-x-2">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>Daftar Pelatihan & Kursus</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Kelola kelas, materi, kuis, dan pendaftaran siswa dalam modul sertifikasi halal.</p>
        </div>
        <div>
            <a href="{{ route('teacher.courses.create') }}" class="inline-flex items-center space-x-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Kursus Baru</span>
            </a>
        </div>
    </div>

    <!-- Course Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses as $course)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition overflow-hidden">
                <!-- Thumbnail Kursus jika ada -->
                @if($course->thumbnail)
                    <div class="h-36 w-full bg-slate-100 overflow-hidden border-b border-slate-100">
                        <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="p-5 space-y-3 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider bg-slate-100 text-slate-600 uppercase">
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

                    <h3 class="font-bold text-slate-800 text-base line-clamp-1 hover:text-amber-600 transition">
                        <a href="{{ route('teacher.courses.show', $course->id) }}">{{ $course->title }}</a>
                    </h3>

                    <p class="text-xs text-slate-500 line-clamp-2">
                        {{ $course->description ?? 'Belum ada deskripsi untuk kursus ini.' }}
                    </p>

                    <!-- Enrollment Key Card -->
                    <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl flex items-center justify-between">
                        <span class="text-[11px] font-medium text-amber-800">Enrollment Key:</span>
                        <code class="text-xs font-bold text-amber-700 bg-amber-200/60 px-2 py-0.5 rounded">{{ $course->enrollment_key }}</code>
                    </div>
                </div>

                <!-- Footer Card Actions -->
                <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 rounded-b-2xl flex items-center justify-between text-xs">
                    <a href="{{ route('teacher.courses.show', $course->id) }}" class="font-semibold text-amber-600 hover:text-amber-700 flex items-center space-x-1">
                        <span>Kelola Materi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    
                    <div class="flex items-center space-x-1">
                        <!-- Edit Button -->
                        <a href="{{ route('teacher.courses.edit', $course->id) }}" class="p-1.5 text-slate-400 hover:text-amber-600 transition rounded-lg hover:bg-slate-100" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>

                        <!-- Delete Button -->
                        <form action="{{ route('teacher.courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini beserta seluruh pertemuannya?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 transition rounded-lg hover:bg-slate-100" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">Belum Ada Kursus</h3>
                <p class="text-xs text-slate-500 mt-1">Mulai buat pelatihan pertama Anda untuk membuka akses belajar bagi siswa.</p>
                <div class="mt-4">
                    <a href="{{ route('teacher.courses.create') }}" class="inline-flex items-center space-x-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
                        <span>+ Buat Kursus Pertama</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $courses->links() }}
    </div>
</div>
@endsection