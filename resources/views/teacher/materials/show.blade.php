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

        <span class="px-3 py-1.5 rounded-xl text-xs font-bold tracking-wider bg-[#28745B]/10 text-[#123F35] border border-[#28745B]/30 uppercase self-start sm:self-auto shadow-2xs">
            Pertemuan {{ $material->meeting->meeting_number ?? '-' }}
        </span>
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

</div>
@endsection
