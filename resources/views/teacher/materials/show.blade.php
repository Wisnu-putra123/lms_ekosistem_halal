@extends('layouts.app')

@section('title', $material->title)
@section('header_title', $material->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Tombol Kembali Ke Course -->
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.courses.show', $material->meeting->course_id) }}" 
           class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-emerald-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>    
    </div>

    <!-- Header Materi -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-2">
        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-slate-100 text-slate-700 uppercase">
            Pertemuan {{ $material->meeting->meeting_number ?? '-' }}: {{ $material->meeting->title }}
        </span>
        <h1 class="text-2xl font-bold text-slate-900">{{ $material->title }}</h1>
        @if($material->description)
            <p class="text-slate-600 text-sm leading-relaxed">{{ $material->description }}</p>
        @endif
    </div>

    <!-- Tampilan Embedded Document / Link Media -->
    @if($material->media && $material->media->external_url)
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-700 flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span>Media Embedded</span>
            </h3>

            <div class="aspect-video w-full rounded-xl overflow-hidden bg-slate-900 border border-slate-200">
                <iframe class="w-full h-full" 
                        src="{{ $material->media->external_url }}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen="true">
                </iframe>
            </div>
        </div>
    @endif

    <!-- Tampilan File Document Preview / Direct Inline View (PDF/Image/Dokumen) -->
    @if($material->media && $material->media->file_path)
        @php
            $fileExtension = strtolower(pathinfo($material->media->file_name, PATHINFO_EXTENSION));
            $fileUrl = asset('storage/' . $material->media->file_path);
        @endphp

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-700 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Berkas Dokumen Lampiran: {{ $material->media->file_name }}</span>
                </h3>
                <a href="{{ $fileUrl }}" download class="text-xs font-bold text-emerald-600 hover:underline">Unduh Berkas</a>
            </div>

            <!-- Preview Inline Tanpa Perlu Download -->
            @if(in_array($fileExtension, ['pdf']))
                <div class="w-full h-[650px] rounded-xl overflow-hidden border border-slate-200">
                    <iframe src="{{ $fileUrl }}" class="w-full h-full"></iframe>
                </div>
            @elseif(in_array($fileExtension, ['png', 'jpg', 'jpeg', 'svg', 'webp']))
                <div class="p-2 bg-slate-50 rounded-xl border border-slate-200 flex justify-center">
                    <img src="{{ $fileUrl }}" alt="{{ $material->title }}" class="max-h-[600px] object-contain rounded-lg">
                </div>
            @else
                <!-- Untuk Format Office / Lainnya (Word/PPT) Menggunakan Previewer Google Docs -->
                <div class="w-full h-[650px] rounded-xl overflow-hidden border border-slate-200">
                    <iframe src="https://docs.google.com/viewer?url={{ urlencode($fileUrl) }}&embedded=true" class="w-full h-full"></iframe>
                </div>
            @endif
        </div>
    @endif

    <!-- Tampilan Teks Bacaan Materi (Jika Ada) -->
    @if($material->content)
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm prose max-w-none text-slate-800 leading-relaxed">
            <h3 class="text-sm font-bold text-slate-700 mb-3 uppercase tracking-wider">Isi Materi Bacaan</h3>
            {!! nl2br(e($material->content)) !!}
        </div>
    @endif

</div>
@endsection