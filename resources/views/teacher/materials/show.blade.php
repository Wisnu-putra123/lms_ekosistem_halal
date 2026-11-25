@extends('layouts.app')

@section('title', $material->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Materi Pembelajaran')

@section('content')
<div class="max-w-5xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.courses.show', $material->meeting->course_id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>

        <div class="flex items-center space-x-2 self-start sm:self-auto">
            <a href="{{ route('teacher.materials.edit', $material->id) }}" class="inline-flex items-center space-x-1.5 bg-[#E7C982]/40 text-[#123F35] border border-[#E7C982] hover:bg-[#123F35] hover:text-white px-3.5 py-2 rounded-xl text-xs font-extrabold shadow-2xs transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Quiz</span>
            </a>

            <form action="{{ route('teacher.materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Hapus Quiz ini beserta seluruh bank soalnya?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center space-x-1.5 bg-red-50 hover:bg-red-600 text-red-700 hover:text-white border border-red-300 px-3.5 py-2 rounded-xl text-xs font-bold shadow-2xs transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Hapus Quiz</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Card Content -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        
        <!-- Heading Container menggunakan warna [#28745B] -->
        <div class="bg-[#28745B] p-5 sm:p-7 text-white border-b border-slate-300">
            <div class="space-y-1.5">
                <div class="flex items-center space-x-2 text-xs text-emerald-100/90">
                    <svg class="w-4 h-4 text-[#E7C982] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Pelatihan: <strong class="text-white">{{ $material->meeting->course->title }}</strong></span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug">{{ $material->title }}</h1>
            </div>
        </div>

        <!-- Body Card -->
        <div class="p-5 sm:p-8 space-y-6">

            <!-- Deskripsi Ringkas / Pengantar -->
            @if($material->description)
                <div class="p-4 bg-[#F7F5EE]/60 rounded-xl border border-slate-300 text-xs text-slate-700 space-y-1">
                    <span class="font-bold text-[#123F35] flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Pengantar / Catatan:</span>
                    </span>
                    <p class="leading-relaxed text-slate-600 pl-5.5">{{ $material->description }}</p>
                </div>
            @endif

            <!-- OPSI A: MEDIA EMBEDDED LINK (YouTube, Google Drive, Canva, dll) -->
            @if($material->media && $material->media->external_url)
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-[#d1e4da] p-3 px-4 rounded-xl border border-slate-300">
                        <span class="text-xs font-bold text-[#123F35] uppercase tracking-wider flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span>Media Interaktif</span>
                        </span>
                        <a href="{{ $material->media->external_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center space-x-1.5 text-xs bg-white hover:bg-[#123F35] text-[#123F35] hover:text-white border border-slate-300 px-3 py-1.5 rounded-lg font-bold transition shadow-2xs self-start sm:self-auto">
                            <span>Buka di Tab Baru</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Container Iframe Embedded -->
                    <div class="w-full aspect-video sm:h-[550px] bg-slate-900 rounded-2xl overflow-hidden border border-slate-300 shadow-2xs">
                        <iframe class="w-full h-full" 
                            src="{{ $material->media->external_url }}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen="true"
                            mozallowfullscreen="true" 
                            webkitallowfullscreen="true">
                        </iframe>
                    </div>
                </div>
            @endif

            <!-- OPSI B: UNGGAHAN BERKAS FILE DOKUMEN / GAMBAR (DIRECT PREVIEW) -->
            @if($material->media && $material->media->file_path)
                @php
                    $fileExtension = strtolower(pathinfo($material->media->file_name, PATHINFO_EXTENSION));
                    $fileUrl = asset('storage/' . $material->media->file_path);
                @endphp

                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-[#d1e4da] p-3 px-4 rounded-xl border border-slate-300">
                        <span class="text-xs font-bold text-[#123F35] tracking-wider flex items-center space-x-2 min-w-0">
                            <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 00.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="truncate">Lampiran: {{ $material->media->file_name }}</span>
                        </span>

                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center space-x-1.5 text-xs bg-white hover:bg-slate-100 text-[#123F35] border border-slate-300 px-3 py-1.5 rounded-lg font-bold transition shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                <span>Buka di Tab Baru</span>
                            </a>

                            <a href="{{ $fileUrl }}" download="{{ $material->media->file_name }}" class="inline-flex items-center space-x-1.5 text-xs bg-[#28745B] hover:bg-[#123F35] text-white border border-[#E7C982]/60 px-3 py-1.5 rounded-lg font-bold transition shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span>Unduh Berkas</span>
                            </a>
                        </div>
                    </div>

                    <!-- Preview Dokumen Sesuai Format -->
                    @if(in_array($fileExtension, ['pdf']))
                        <!-- Preview PDF -->
                        <div class="w-full h-[600px] bg-slate-100 rounded-2xl overflow-hidden border border-slate-300 shadow-inner">
                            <iframe src="{{ $fileUrl }}" class="w-full h-full"></iframe>
                        </div>
                    @elseif(in_array($fileExtension, ['png', 'jpg', 'jpeg', 'svg', 'webp']))
                        <!-- Preview Gambar -->
                        <div class="p-4 bg-[#F7F5EE]/40 rounded-2xl border border-slate-300 flex justify-center">
                            <img src="{{ $fileUrl }}" alt="{{ $material->title }}" class="max-h-[600px] object-contain rounded-xl shadow-2xs">
                        </div>
                    @else
                        <!-- Preview Office Document (DOCX / PPTX / XLSX) via Google Viewer -->
                        <div class="w-full h-[600px] bg-slate-100 rounded-2xl overflow-hidden border border-slate-300 shadow-inner">
                            <iframe src="https://docs.google.com/viewer?url={{ urlencode($fileUrl) }}&embedded=true" class="w-full h-full"></iframe>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Konten Utama Materi (Teks Bacaan / HTML) -->
            @if($material->content)
                <div class="pt-4 border-t border-slate-300 space-y-3">
                    <span class="text-xs font-bold text-[#123F35] uppercase tracking-wider flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        <span>Materi Bacaan</span>
                    </span>
                    <div class="p-4 sm:p-5 bg-white rounded-xl border border-slate-300 prose prose-slate max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed space-y-3">
                        {!! nl2br(e($material->content)) !!}
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- MONITORING AKTIVITAS BELAJAR PESERTA -->
    @php
        $toggleDirection = ($direction ?? 'asc') === 'asc' ? 'desc' : 'asc';
        $buildSortUrl = function (string $column) use ($material, $search, $status, $enrollmentStatus, $sort, $direction, $toggleDirection) {
            $nextDir = ($sort === $column) ? $toggleDirection : 'asc';
            return route('teacher.materials.show', [
                'material'          => $material->id,
                'search'            => $search ?: null,
                'status'            => $status !== 'all' ? $status : null,
                'enrollment_status' => $enrollmentStatus !== 'all' ? $enrollmentStatus : null,
                'sort'              => $column,
                'direction'         => $nextDir,
            ]) . '#aktivitas-peserta';
        };
    @endphp

    <div id="aktivitas-peserta" class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <!-- Header Seksi Monitoring -->
        <div class="bg-[#28745B] p-4 sm:px-6 border-b border-slate-300 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-[#123F35] text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
                    <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white">Aktivitas Belajar Peserta</h2>
                    <p class="text-[11px] text-white">Pantau peserta yang telah membuka dan mempelajari materi ini.</p>
                </div>
            </div>

            <!-- Ringkasan Rasio Keterbacaan -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-xl bg-[#123F35] text-white text-xs font-bold shadow-2xs">
                    {{ $stats['viewed'] }} / {{ $stats['total'] }} Peserta ({{ $stats['completion_rate'] }}%)
                </span>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-4">
            <!-- Kartu Statistik Ringkas -->
            <div class="grid grid-cols-3 gap-2.5 sm:gap-4">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-300">
                    <span class="block text-[10px] sm:text-xs font-semibold text-slate-500">Total Peserta</span>
                    <span class="mt-0.5 block text-base sm:text-lg font-extrabold text-slate-700">{{ $stats['total'] }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-300">
                    <span class="block text-[10px] sm:text-xs font-semibold text-slate-700">Sudah Dipelajari</span>
                    <span class="mt-0.5 block text-base sm:text-lg font-extrabold text-slate-700">{{ $stats['viewed'] }}</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-300">
                    <span class="block text-[10px] sm:text-xs font-semibold text-slate-500">Belum Dipelajari</span>
                    <span class="mt-0.5 block text-base sm:text-lg font-extrabold text-slate-700">{{ $stats['not_viewed'] }}</span>
                </div>
            </div>

            <!-- Bar Filter & Pencarian -->
            <form method="GET" action="{{ route('teacher.materials.show', $material->id) }}#aktivitas-peserta" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">

                <!-- Pencarian Peserta -->
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari nama, email, NIK, atau instansi peserta..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#28745B] focus:ring-[#28745B]">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Filter Status Enrollment -->
                <select name="enrollment_status"
                        {{-- onchange="this.form.submit()" --}}
                        class="py-2 px-3 text-xs font-semibold text-slate-700 rounded-xl border border-slate-300 bg-white focus:border-[#28745B] focus:ring-[#28745B]">
                    <option value="all" @selected($enrollmentStatus === 'all')>Semua Kepesertaan</option>
                    <option value="active" @selected($enrollmentStatus === 'active')>Aktif</option>
                    <option value="completed" @selected($enrollmentStatus === 'completed')>Selesai (Completed)</option>
                    <option value="dropped" @selected($enrollmentStatus === 'dropped')>Berhenti (Dropped)</option>
                </select>

                <!-- Filter Status Materi -->
                <select name="status"
                        {{-- onchange="this.form.submit()" --}}
                        class="py-2 px-3 text-xs font-semibold text-slate-700 rounded-xl border border-slate-300 bg-white focus:border-[#28745B] focus:ring-[#28745B]">
                    <option value="all" @selected($status === 'all')>Semua Status Belajar ({{ $stats['total'] }})</option>
                    <option value="viewed" @selected($status === 'viewed')>Sudah Dipelajari ({{ $stats['viewed'] }})</option>
                    <option value="not_viewed" @selected($status === 'not_viewed')>Belum Dipelajari ({{ $stats['not_viewed'] }})</option>
                </select>

                <div class="flex items-center gap-1.5">
                    <button type="submit"
                            class="flex-1 lg:flex-initial px-4 py-2 rounded-xl bg-[#28745B] hover:bg-[#123F35] text-white text-xs font-bold transition shadow-2xs cursor-pointer">
                        Cari
                    </button>

                    @if($search !== '' || $status !== 'all' || $enrollmentStatus !== 'all' || $sort !== 'name' || $direction !== 'asc')
                        <a href="{{ route('teacher.materials.show', $material->id) }}#aktivitas-peserta"
                           title="Reset Filter & Sortir"
                           class="px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-600 text-xs font-semibold transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Tabel Aktivitas Peserta -->
            <div class="rounded-xl border border-slate-300 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-xs">
                        <thead class="bg-[#28745B] text-white">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left font-bold uppercase tracking-wider w-10">No</th>
                                <th scope="col" class="px-4 py-3 text-left font-bold uppercase tracking-wider">
                                    <a href="{{ $buildSortUrl('name') }}" class="inline-flex items-center gap-1 hover:text-[#E7C982] transition">
                                        <span>Peserta</span>
                                        @if($sort === 'name')
                                            <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                        @else
                                            <span class="opacity-40">↕</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left font-bold uppercase tracking-wider hidden sm:table-cell">Instansi</th>
                                <th scope="col" class="px-4 py-3 text-left font-bold uppercase tracking-wider">
                                    <a href="{{ $buildSortUrl('enrollment_status') }}" class="inline-flex items-center gap-1 hover:text-[#E7C982] transition">
                                        <span>Status Kepesertaan</span>
                                        @if($sort === 'enrollment_status')
                                            <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                        @else
                                            <span class="opacity-40">↕</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left font-bold uppercase tracking-wider">
                                    <a href="{{ $buildSortUrl('status') }}" class="inline-flex items-center gap-1 hover:text-[#E7C982] transition">
                                        <span>Status Belajar</span>
                                        @if($sort === 'status')
                                            <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                        @else
                                            <span class="opacity-40">↕</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left font-bold uppercase tracking-wider">
                                    <a href="{{ $buildSortUrl('viewed_at') }}" class="inline-flex items-center gap-1 hover:text-[#E7C982] transition">
                                        <span>Waktu Pertama Dibuka</span>
                                        @if($sort === 'viewed_at')
                                            <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                                        @else
                                            <span class="opacity-40">↕</span>
                                        @endif
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($sortedEnrollments as $index => $item)
                                <tr class="hover:bg-[#F7F5EE]/60 transition">
                                    <td class="px-4 py-3 text-slate-500 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-[#123F35]">{{ $item->user->name ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $item->user->email ?? '-' }}</div>
                                        @if(!empty($item->user->nik))
                                            <div class="text-[10px] font-mono text-slate-400">NIK: {{ $item->user->nik }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 hidden sm:table-cell">
                                        {{ $item->user->institution ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($item->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-100 text-blue-800 border border-blue-200 text-[10px] font-bold">
                                                Selesai
                                            </span>
                                        @elseif($item->status === 'dropped')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-red-100 text-red-700 border border-red-200 text-[10px] font-bold">
                                                Dropped
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-bold">
                                                Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($item->has_viewed)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-[#28745B]/15 text-[#28745B] border border-[#28745B]/40 text-[10px] font-extrabold shadow-2xs">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Sudah Dipelajari
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-300 text-[10px] font-bold shadow-2xs">
                                                Belum Dipelajari
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                        @if($item->viewed_at)
                                            <div class="font-semibold text-slate-800">{{ $item->viewed_at->format('d M Y, H:i') }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $item->viewed_at->diffForHumans() }}</div>
                                        @else
                                            <span class="text-slate-400 italic">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 italic">
                                        Tidak ada data peserta yang sesuai dengan filter saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection