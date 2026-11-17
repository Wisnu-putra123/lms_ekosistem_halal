@extends('layouts.app')

@section('title', 'Manajemen Pelatihan - LPP-JPH LMS')
@section('header_title', 'Manajemen Pelatihan')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-6 sm:p-8 text-white shadow-md border border-[#E7C982]/40">
        <!-- <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
        <div class="absolute -right-10 -bottom-10 -z-0 h-44 w-44 rounded-full border border-[#E7C982]/20 blur-xl"></div> -->

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1.5 max-w-2xl">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wider bg-[#E7C982]/20 text-[#E7C982] border border-[#E7C982]/40 uppercase">
                    Administrasi Kurikulum &amp; Kelas
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white">Manajemen Pelatihan</h1>
                <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
                    Kelola master data pelatihan Sistem Jaminan Halal, penugasan instruktur, enrollment key, template sertifikat, dan status publikasi kelas.
                </p>
            </div>

            <div class="flex-shrink-0">
                <a href="{{ route('admin.courses.create') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] px-4 py-2.5 rounded-xl text-xs font-extrabold shadow-xs transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Buat Pelatihan Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Feedback -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-semibold flex items-center space-x-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-50 border border-red-300 text-red-800 text-xs font-semibold flex items-center space-x-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl border border-slate-300 p-4 sm:p-5 shadow-2xs">
        <form action="{{ route('admin.courses.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Pencarian Judul / Kode -->
            <div class="sm:col-span-2 lg:col-span-4">
                <label for="search" class="block text-[11px] font-bold text-[#123F35] mb-1">Cari Pelatihan</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Judul pelatihan atau kode kelas..."
                           class="w-full pl-9 pr-3 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                </div>
            </div>

            <!-- Filter Kategori -->
            <div class="lg:col-span-2">
                <label for="category" class="block text-[11px] font-bold text-[#123F35] mb-1">Kategori</label>
                <select id="category" name="category"
                        class="w-full px-3 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs font-semibold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition cursor-pointer">
                    <option value="">Semua Kategori</option>
                    <option value="penyelia_halal" {{ request('category') === 'penyelia_halal' ? 'selected' : '' }}>Penyelia Halal</option>
                    <option value="juleha" {{ request('category') === 'juleha' ? 'selected' : '' }}>Juleha</option>
                    <option value="auditor_halal" {{ request('category') === 'auditor_halal' ? 'selected' : '' }}>Auditor Halal</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="lg:col-span-2">
                <label for="status" class="block text-[11px] font-bold text-[#123F35] mb-1">Status</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs font-semibold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <!-- Filter Pengajar -->
            <div class="sm:col-span-2 lg:col-span-2">
                <label for="instructor_id" class="block text-[11px] font-bold text-[#123F35] mb-1">Pengajar</label>
                <select id="instructor_id" name="instructor_id"
                        class="w-full px-3 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs font-semibold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition cursor-pointer">
                    <option value="">Semua Pengajar</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ (string) request('instructor_id') === (string) $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Filter & Reset -->
            <div class="sm:col-span-2 lg:col-span-2 flex items-center space-x-2">
                <button type="submit"
                        class="flex-1 bg-[#28745B] hover:bg-[#3d8e73] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'category', 'status', 'instructor_id']))
                    <a href="{{ route('admin.courses.index') }}"
                       class="px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-100 transition text-center whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Pelatihan (Responsif Horizontal Scroll) -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-300 flex items-center justify-between gap-2 bg-[#F7F5EE]/50">
            <div class="text-xs font-bold text-[#123F35]">
                Menampilkan <span class="text-[#28745B]">{{ $courses->count() }}</span> dari <span class="text-[#28745B]">{{ $courses->total() }}</span> pelatihan
            </div>
            <span class="sm:hidden text-[10px] font-semibold text-slate-500 flex items-center gap-1">
                <span>Geser tabel</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </span>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[920px] text-left border-collapse">
                <thead class="bg-[#28745B] text-white border-b border-slate-300">
                    <tr class="text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                        <th class="py-3.5 px-4 min-w-[260px]">Pelatihan</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Kategori</th>
                        <th class="py-3.5 px-4 min-w-[180px]">Instruktur</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Silabus</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Peserta (Aktif / Lulus)</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Status</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300 text-xs text-slate-700">
                    @php
                        $categoryLabels = [
                            'penyelia_halal' => 'Penyelia Halal',
                            'juleha'         => 'Juleha',
                            'auditor_halal'  => 'Auditor Halal',
                        ];
                    @endphp

                    @forelse($courses as $course)
                        <tr class="hover:bg-[#F7F5EE]/50 transition">
                            <!-- Info Pelatihan + Thumbnail -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}"
                                             alt="{{ $course->title }}"
                                             class="w-11 h-11 rounded-xl object-cover flex-shrink-0 border border-slate-300">
                                    @else
                                        <div class="w-11 h-11 rounded-xl bg-[#28745B]/15 text-[#123F35] flex items-center justify-center font-extrabold text-xs flex-shrink-0 border border-[#28745B]/30">
                                            {{ strtoupper(substr($course->code, 0, 4)) }}
                                        </div>
                                    @endif

                                    <div class="min-w-0 space-y-1">
                                        <a href="{{ route('admin.courses.show', $course) }}"
                                           class="block font-bold text-[#123F35] hover:text-[#28745B] transition line-clamp-1">
                                            {{ $course->title }}
                                        </a>
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500 whitespace-nowrap">
                                            <span class="inline-flex items-center font-mono font-bold text-[#123F35]/80 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-300">
                                                {{ $course->code }}
                                            </span>
                                            @if($course->enrollment_key)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-[#E7C982]/30 text-[#123F35] font-semibold border border-[#E7C982]">
                                                    Key: {{ $course->enrollment_key }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-[#F7F5EE] text-[#123F35] border border-[#28745B]/30 whitespace-nowrap">
                                    {{ $categoryLabels[$course->category] ?? ucfirst(str_replace('_', ' ', $course->category)) }}
                                </span>
                            </td>

                            <!-- Instruktur -->
                            <td class="py-3.5 px-4">
                                @if($course->instructors->isNotEmpty())
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($course->instructors as $instructor)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-[#123F35] border border-emerald-300 whitespace-nowrap">
                                                {{ $instructor->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-red-100 text-red-700 border border-red-300 whitespace-nowrap">
                                        Belum Ditugaskan
                                    </span>
                                @endif
                            </td>

                            <!-- Jumlah Pertemuan -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="font-bold text-[#123F35]">{{ $course->meetings_count }}</span>
                                <span class="text-[11px] text-slate-500">Pertemuan</span>
                            </td>

                            <!-- Peserta Aktif / Lulus -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-[#123F35]/10 text-[#123F35] font-extrabold whitespace-nowrap">
                                        {{ $course->active_enrollments_count }} Aktif
                                    </span>
                                    <span class="text-slate-400">/</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-emerald-100 text-emerald-800 font-extrabold whitespace-nowrap">
                                        {{ $course->completed_enrollments_count }} Lulus
                                    </span>
                                </div>
                            </td>

                            <!-- Status Publikasi -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($course->status === 'published')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Published
                                    </span>
                                @elseif($course->status === 'draft')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-300 whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Draft
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-200 text-slate-700 border border-slate-300 whitespace-nowrap">
                                        Archived
                                    </span>
                                @endif
                            </td>

                            <!-- Tombol Aksi -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end space-x-1.5">
                                    <a href="{{ route('admin.courses.show', $course) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-[#F7F5EE] hover:bg-[#28745B]/15 text-[#123F35] border border-slate-300 text-[11px] font-bold transition whitespace-nowrap">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.courses.edit', $course) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-[#123F35] hover:bg-[#28745B] text-white text-[11px] font-bold transition whitespace-nowrap">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus pelatihan {{ addslashes($course->title) }}? Tindakan ini tidak dapat dibatalkan.');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-700 hover:text-white border border-red-300 text-[11px] font-bold transition whitespace-nowrap cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <p class="text-xs font-bold text-[#123F35]">Belum ada data pelatihan yang ditemukan</p>
                                    <p class="text-[11px] text-slate-500">
                                        Silakan buat pelatihan baru atau ubah kata kunci filter pencarian Anda.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($courses->hasPages())
            <div class="px-5 py-4 border-t border-slate-300 bg-[#F7F5EE]/30">
                {{ $courses->links() }}
            </div>
        @endif
    </div>

</div>
@endsection