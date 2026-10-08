@extends('layouts.app')

@section('title', $material->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Materi Pembelajaran')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header & Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.courses.show', $material->meeting->course_id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-emerald-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>
        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider bg-emerald-100 text-emerald-800 uppercase">
            Pertemuan {{ $material->meeting->meeting_number ?? '-' }}
        </span>
    </div>

    <!-- Main Card Content -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        
        <!-- Title & Info -->
        <div class="border-b border-slate-100 pb-4 space-y-1">
            <h1 class="text-xl font-bold text-slate-800">{{ $material->title }}</h1>
            <p class="text-xs text-slate-400">
                Pelatihan: <span class="font-semibold text-slate-600">{{ $material->meeting->course->title }}</span>
            </p>
        </div>

        <!-- Deskripsi Ringkas / Pengantar -->
        @if($material->description)
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/80 text-xs text-slate-600">
                <span class="font-bold text-slate-700">Pengantar / Catatan:</span>
                <p class="mt-1 leading-relaxed">{{ $material->description }}</p>
            </div>
        @endif

        <!-- OPSI A: MEDIA EMBEDDED LINK (YouTube, Google Drive, Canva, dll) -->
        @if($material->media && $material->media->type === 'embed' && $material->media->external_url)
            <div class="pt-2 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span>Media Interaktif:</span>
                    </span>
                    <a href="{{ $material->media->external_url }}" target="_blank" class="text-xs text-emerald-600 hover:underline font-semibold flex items-center space-x-1">
                        <span>Buka di Tab Baru</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>

                <!-- Container Iframe Embedded -->
                <div class="w-full aspect-video sm:h-[550px] bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
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
        @if($material->media && $material->media->type !== 'embed' && $material->media->file_path)
            @php
                $fileExtension = strtolower(pathinfo($material->media->file_name, PATHINFO_EXTENSION));
                $fileUrl = asset('storage/' . $material->media->file_path);
            @endphp

            <div class="pt-2 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Dokumen Lampiran: {{ $material->media->file_name }}</span>
                    </span>
                    <a href="{{ $fileUrl }}" download class="text-xs text-emerald-600 hover:underline font-bold flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Berkas</span>
                    </a>
                </div>

                <!-- Preview Dokumen Sesuai Format -->
                @if(in_array($fileExtension, ['pdf']))
                    <!-- Preview PDF -->
                    <div class="w-full h-[600px] bg-slate-100 rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
                        <iframe src="{{ $fileUrl }}" class="w-full h-full"></iframe>
                    </div>
                @elseif(in_array($fileExtension, ['png', 'jpg', 'jpeg', 'svg', 'webp']))
                    <!-- Preview Gambar -->
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex justify-center">
                        <img src="{{ $fileUrl }}" alt="{{ $material->title }}" class="max-h-[600px] object-contain rounded-xl shadow-sm">
                    </div>
                @else
                    <!-- Preview Office Document (DOCX / PPTX / XLSX) via Google Viewer -->
                    <div class="w-full h-[600px] bg-slate-100 rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
                        <iframe src="https://docs.google.com/viewer?url={{ urlencode($fileUrl) }}&embedded=true" class="w-full h-full"></iframe>
                    </div>
                @endif
            </div>
        @endif

        <!-- Konten Utama Materi (Teks Bacaan / HTML) -->
        @if($material->content)
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Materi Bacaan:</span>
                <div class="prose prose-slate max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed space-y-3">
                    {!! nl2br(e($material->content)) !!}
                </div>
            </div>
        @endif

    </div>

</div>
@endsection