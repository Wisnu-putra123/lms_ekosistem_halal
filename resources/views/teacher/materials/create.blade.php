@extends('layouts.app')

@section('title', 'Tambah Materi')
@section('header_title', 'Tambah Materi Pembelajaran')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
    <h2 class="text-lg font-bold text-slate-800 mb-4">Materi untuk: {{ $meeting->title }}</h2>

    <form action="{{ route('teacher.meetings.materials.store', $meeting->id) }}" method="POST" class="space-y-5">
        @csrf

        <!-- Judul Materi -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Judul Materi <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Pengenalan Sertifikasi Halal"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Deskripsi Singkat -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Singkat</label>
            <textarea name="description" rows="2" placeholder="Catatan atau pengantar materi..."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">{{ old('description') }}</textarea>
        </div>

        <!-- Section Input Embedded Link -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
            <label class="block text-sm font-semibold text-slate-800">URL Dokumen / Video Embedded (Opsional)</label>
            <p class="text-xs text-slate-500">Masukkan link dari <strong>YouTube, Google Drive, Google Slides, Google Sheets, atau Canva</strong>. Sistem akan mengonversinya menjadi dokumen embedded secara otomatis.</p>
            <input type="url" name="external_url" value="{{ old('external_url') }}" placeholder="https://docs.google.com/presentation/d/... atau https://youtu.be/..."
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white">
            @error('external_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Section Input Teks / HTML Content -->
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Isi Teks / Artikel Materi (Opsional)</label>
            <textarea name="content" rows="8" placeholder="Tuliskan isi materi lengkap jika berbentuk bacaan/teks..."
                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm">{{ old('content') }}</textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
            <a href="{{ route('teacher.courses.show', $meeting->course_id) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm font-medium hover:bg-slate-50 transition">Batal</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">Simpan Materi</button>
        </div>
    </form>
</div>
@endsection