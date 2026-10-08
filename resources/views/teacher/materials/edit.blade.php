@extends('layouts.app')

@section('title', 'Edit Materi - ' . $material->title)
@section('header_title', 'Edit Materi Pembelajaran')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
    
    <!-- Tombol Kembali Ke Course -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Edit Materi</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pertemuan: {{ $material->meeting->title }}</p>
        </div>
        <a href="{{ route('teacher.courses.show', $material->meeting->course_id) }}" 
           class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-emerald-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Course</span>
        </a>
    </div>

    <form action="{{ route('teacher.materials.update', $material->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
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
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Deskripsi Singkat -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Singkat</label>
            <textarea name="description" 
                      rows="2" 
                      placeholder="Catatan atau pengantar materi..."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm">{{ old('description', $material->description) }}</textarea>
        </div>

        @php
            $defaultType = 'none';
            if ($material->media) {
                $defaultType = $material->media->type === 'embed' ? 'embed' : 'file';
            }
        @endphp

        <!-- Section Input Lampiran Media -->
        <div x-data="{ mediaType: '{{ old('media_type', $defaultType) }}' }" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-800 mb-1">Lampiran Media (Atur/Ubah Lampiran)</label>
                <p class="text-xs text-slate-500">Pilih salah satu metode lampiran materi di bawah ini.</p>
            </div>

            <!-- Radio Selection -->
            <div class="grid grid-cols-3 gap-2">
                <label class="flex items-center justify-center p-3 rounded-xl border text-xs font-bold cursor-pointer transition"
                       :class="mediaType === 'none' ? 'bg-white border-emerald-500 text-emerald-700 shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-slate-100'">
                    <input type="radio" name="media_type" value="none" x-model="mediaType" class="sr-only">
                    <span>Hapus / Tanpa Media</span>
                </label>

                <label class="flex items-center justify-center p-3 rounded-xl border text-xs font-bold cursor-pointer transition"
                       :class="mediaType === 'embed' ? 'bg-white border-emerald-500 text-emerald-700 shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-slate-100'">
                    <input type="radio" name="media_type" value="embed" x-model="mediaType" class="sr-only">
                    <span>Tautan Embedded Link</span>
                </label>

                <label class="flex items-center justify-center p-3 rounded-xl border text-xs font-bold cursor-pointer transition"
                       :class="mediaType === 'file' ? 'bg-white border-emerald-500 text-emerald-700 shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-slate-100'">
                    <input type="radio" name="media_type" value="file" x-model="mediaType" class="sr-only">
                    <span>Unggah Berkas File</span>
                </label>
            </div>

            <!-- Mode Embed Link -->
            <div x-show="mediaType === 'embed'" x-cloak class="space-y-2 pt-2">
                <input type="url" 
                       name="external_url" 
                       value="{{ old('external_url', $material->media ? $material->media->external_url : '') }}" 
                       placeholder="https://docs.google.com/presentation/d/... atau https://youtu.be/..."
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                @error('external_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Mode File Upload -->
            <div x-show="mediaType === 'file'" x-cloak class="space-y-2 pt-2">
                @if($material->media && $material->media->file_path)
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
                        <span class="font-semibold truncate">Berkas saat ini: {{ $material->media->file_name }}</span>
                        <a href="{{ asset('storage/' . $material->media->file_path) }}" target="_blank" class="underline font-bold flex-shrink-0">Lihat Berkas</a>
                    </div>
                @endif
                <p class="text-xs text-slate-500">Pilih berkas baru jika ingin mengganti berkas yang ada (Maks 20MB).</p>
                <input type="file" name="file" 
                       class="w-full p-2 text-xs bg-white border border-slate-300 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 transition">
                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Section Input Teks Content -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
                Isi Teks / Artikel Materi (Opsional)
            </label>
            <textarea name="content" 
                      rows="8" 
                      placeholder="Tuliskan isi materi lengkap jika berbentuk bacaan/teks..."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm">{{ old('content', $material->content) }}</textarea>
        </div>

        <!-- Urutan Tampil -->
        <div class="w-1/3">
            <label class="block text-sm font-semibold text-slate-700 mb-1">Urutan Tampil</label>
            <input type="number" 
                   name="sort_order" 
                   value="{{ old('sort_order', $material->sort_order) }}" 
                   min="1"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm">
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