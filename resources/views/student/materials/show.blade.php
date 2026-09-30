@extends('layouts.app')

@section('title', $material->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Materi Pembelajaran')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.courses.show', $material->meeting->course_id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-amber-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>
        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider bg-amber-100 text-amber-800 uppercase">
            Pertemuan {{ $material->meeting->meeting_number }}
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
                <span class="font-bold text-slate-700">Pengantar:</span>
                <p class="mt-1 leading-relaxed">{{ $material->description }}</p>
            </div>
        @endif

        <!-- Konten Utama Materi (Teks / HTML) -->
        @if($material->content)
            <div class="prose prose-slate max-w-none text-sm text-slate-700 leading-relaxed space-y-4">
                {!! $material->content !!}
            </div>
        @endif

        <!-- 1. EMBEDDED IFRAME: FILE LAMPIRAN / DOKUMEN (MEDIA) -->
        @if($material->media)
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Pratinjau Dokumen Lampiran:</span>
                    <a href="" class="text-xs text-amber-600 hover:underline font-semibold">
                        Unduh Berkas
                    </a>
                </div>

                <!-- Container Iframe Pratinjau Dokumen -->
                <div class="w-full h-[600px] bg-slate-100 rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
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
    </div>

</div>
@endsection