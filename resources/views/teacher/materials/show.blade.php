@extends('layouts.app')

@section('title', $material->title)
@section('header_title', $material->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="#" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>    
    </div>

    <!-- Header Materi -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ $material->title }}</h1>
        @if($material->description)
            <p class="text-slate-600 text-sm">{{ $material->description }}</p>
        @endif
    </div>

    <!-- Tampilan Embedded Document (Jika Ada) -->
    @if($material->media && $material->media->external_url)
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-bold text-slate-700 mb-3 flex items-center space-x-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span>Media & Lampiran Dokumen</span>
            </h3>

            <div class="aspect-video w-full rounded-xl overflow-hidden bg-slate-900 border border-slate-200">
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

    <!-- Tampilan Teks BACAAN (Jika Ada) -->
    @if($material->content)
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm prose max-w-none text-slate-800">
            {!! nl2br(e($material->content)) !!}
        </div>
    @endif

</div>
@endsection