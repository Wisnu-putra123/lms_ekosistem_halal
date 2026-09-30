@extends('layouts.app')

@section('title', 'Edit Materi - ' . $material->title)
@section('header_title', 'Edit Materi Pembelajaran')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Edit Materi</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pertemuan: {{ $material->meeting->title }}</p>
        </div>
        <a href="{{ route('teacher.courses.show', $material->meeting->course_id) }}" 
           class="text-xs font-semibold text-slate-500 hover:text-slate-700 flex items-center space-x-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Course</span>
        </a>
    </div>

    <form action="{{ route('teacher.materials.update', $material->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <!-- Judul Materi -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Judul Materi <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="title" 
                   value="{{ old('title', $material->title) }}" 
                   required 
                   placeholder="Contoh: Pengenalan Sertifikasi Halal"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
            @error('title') 
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
            @enderror
        </div>

        <!-- Deskripsi Singkat -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Singkat</label>
            <textarea name="description" 
                      rows="2" 
                      placeholder="Catatan atau pengantar materi..."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">{{ old('description', $material->description) }}</textarea>
            @error('description') 
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
            @enderror
        </div>

        <!-- Section Input Embedded Link -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
            <div class="flex items-center justify-between">
                <label class="block text-sm font-semibold text-slate-800">
                    URL Dokumen / Video Embedded (Opsional)
                </label>
                @if($material->media && $material->media->external_url)
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-700">
                        Media Terhubung
                    </span>
                @endif
            </div>
            
            <p class="text-xs text-slate-500">
                Masukkan link dari <strong>YouTube, Google Drive, Google Slides, Google Sheets, atau Canva</strong>. Sistem akan mengonversinya menjadi dokumen embedded secara otomatis. <em>Kosongkan jika ingin menghapus lampiran media.</em>
            </p>

            <input type="url" 
                   name="external_url" 
                   value="{{ old('external_url', $material->media->external_url ?? '') }}" 
                   placeholder="https://docs.google.com/presentation/d/... atau https://youtu.be/..."
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white">
            
            @error('external_url') 
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
            @enderror

            <!-- Pratinjau Link Jika Ada -->
            @if($material->media && $material->media->external_url)
                <div class="pt-2">
                    <p class="text-[11px] text-slate-500">
                        Link Embed Aktif: 
                        <a href="{{ $material->media->external_url }}" target="_blank" class="text-emerald-600 underline font-medium truncate inline-block max-w-full align-bottom">
                            {{ $material->media->external_url }}
                        </a>
                    </p>
                </div>
            @endif
        </div>

        <!-- Section Input Teks / HTML Content -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Isi Teks / Artikel Materi (Opsional)
            </label>
            <textarea name="content" 
                      rows="8" 
                      placeholder="Tuliskan isi materi lengkap jika berbentuk bacaan/teks..."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm">{{ old('content', $material->content) }}</textarea>
            @error('content') 
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
            @enderror
        </div>

        <!-- Urutan Tampil (Sort Order) -->
        <div class="w-1/3">
            <label class="block text-sm font-semibold text-slate-700 mb-1">Urutan Tampil</label>
            <input type="number" 
                   name="sort_order" 
                   value="{{ old('sort_order', $material->sort_order) }}" 
                   min="1"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
            @error('sort_order') 
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
            <a href="{{ route('teacher.courses.show', $material->meeting->course_id) }}" 
               class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-medium hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" 
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                Perbarui Materi
            </button>
        </div>
    </form>
</div>
@endsection