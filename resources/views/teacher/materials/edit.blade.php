@extends('layouts.app')

@section('title', 'Edit Materi - ' . $material->title)
@section('header_title', 'Edit Materi Pembelajaran')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-4xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.courses.show', $material->meeting->course_id) }}" 
           class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>

        <span class="px-3 py-1.5 rounded-xl text-xs font-bold tracking-wider bg-[#28745B]/10 text-[#123F35] border border-[#28745B]/30 uppercase self-start sm:self-auto shadow-2xs">
            Pertemuan {{ $material->meeting->meeting_number ?? '-' }}: {{ $material->meeting->title }}
        </span>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        
        <!-- Heading Container bg-[#28745B] -->
        <div class="bg-[#28745B] p-5 sm:p-7 text-white border-b border-slate-300">
            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase mb-1.5">
                Formulir Pembaruan Materi
            </span>
            <h2 class="text-lg sm:text-xl font-bold text-white leading-snug">Edit Materi: {{ $material->title }}</h2>
            <p class="text-xs text-emerald-100/85 mt-1">Perbarui informasi materi bacaan, tautan interaktif, atau lampiran berkas pembelajaran.</p>
        </div>

        <form action="{{ route('teacher.materials.update', $material->id) }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-8 space-y-5">
            @csrf
            @method('PUT')

            <!-- Judul Materi -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Judul Materi <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title', $material->title) }}" 
                       required 
                       placeholder="Contoh: Pengenalan Sertifikasi Halal"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#28745B]/20 focus:border-[#28745B] text-xs sm:text-sm transition">
                @error('title') <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Deskripsi Singkat -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#123F35] mb-1.5">Deskripsi Singkat</label>
                <textarea name="description" 
                          rows="2" 
                          placeholder="Catatan atau pengantar materi..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#28745B]/20 focus:border-[#28745B] text-xs sm:text-sm transition">{{ old('description', $material->description) }}</textarea>
            </div>

            @php
                $defaultType = 'none';
                if ($material->media) {
                    $defaultType = $material->media->type === 'embed' ? 'embed' : 'file';
                }
            @endphp

            <!-- Section Input Lampiran Media (Background Cream) -->
            <div x-data="{ mediaType: '{{ old('media_type', $defaultType) }}' }" class="p-5 rounded-2xl bg-[#F7F5EE] border border-slate-300 space-y-4">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35]">Lampiran Media (Atur / Ubah Lampiran)</label>
                    <p class="text-xs text-slate-600 mt-0.5">Pilih salah satu metode lampiran materi di bawah ini.</p>
                </div>

                <!-- Radio Selection -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <label class="flex items-center justify-center p-3 rounded-xl border text-xs font-bold cursor-pointer transition"
                           :class="mediaType === 'none' ? 'bg-[#123F35] border-[#123F35] text-white shadow-2xs' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50'">
                        <input type="radio" name="media_type" value="none" x-model="mediaType" class="sr-only">
                        <span>Hapus / Tanpa Media</span>
                    </label>

                    <label class="flex items-center justify-center p-3 rounded-xl border text-xs font-bold cursor-pointer transition"
                           :class="mediaType === 'embed' ? 'bg-[#123F35] border-[#123F35] text-white shadow-2xs' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50'">
                        <input type="radio" name="media_type" value="embed" x-model="mediaType" class="sr-only">
                        <span>Tautan Embedded Link</span>
                    </label>

                    <label class="flex items-center justify-center p-3 rounded-xl border text-xs font-bold cursor-pointer transition"
                           :class="mediaType === 'file' ? 'bg-[#123F35] border-[#123F35] text-white shadow-2xs' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50'">
                        <input type="radio" name="media_type" value="file" x-model="mediaType" class="sr-only">
                        <span>Unggah Berkas File</span>
                    </label>
                </div>

                <!-- Mode Embed Link -->
                <div x-show="mediaType === 'embed'" x-cloak class="space-y-2 pt-1">
                    <p class="text-xs text-slate-600">Masukkan link dari <strong>YouTube, Google Drive, Google Slides, Google Sheets, atau Canva</strong>.</p>
                    <input type="url" 
                           name="external_url" 
                           value="{{ old('external_url', $material->media ? $material->media->external_url : '') }}" 
                           placeholder="https://docs.google.com/presentation/d/... atau https://youtu.be/..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#28745B]/20 focus:border-[#28745B] text-xs sm:text-sm bg-white transition">
                    @error('external_url') <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Mode File Upload -->
                <div x-show="mediaType === 'file'" x-cloak class="space-y-2.5 pt-1">
                    @if($material->media && $material->media->file_path)
                        <div class="p-3 bg-[#d1e4da] rounded-xl border border-slate-300 text-xs text-[#123F35] flex items-center justify-between gap-2">
                            <span class="font-bold truncate">Berkas saat ini: {{ $material->media->file_name }}</span>
                            <a href="{{ asset('storage/' . $material->media->file_path) }}" target="_blank" class="px-2.5 py-1 bg-white hover:bg-[#123F35] text-[#123F35] hover:text-white border border-slate-300 rounded-lg font-bold flex-shrink-0 transition shadow-2xs">
                                Lihat Berkas
                            </a>
                        </div>
                    @endif
                    <p class="text-xs text-slate-600">Pilih berkas baru jika ingin mengganti berkas yang ada (PDF, DOCX, PPTX, PNG, JPG maks 20MB).</p>
                    <input type="file" name="file" 
                           class="w-full p-2 text-xs bg-white border border-slate-300 rounded-xl text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#28745B]/15 file:text-[#123F35] hover:file:bg-[#28745B]/25 transition cursor-pointer">
                    @error('file') <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Section Input Teks Content -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Isi Teks / Artikel Materi (Opsional)
                </label>
                <textarea name="content" 
                          rows="8" 
                          placeholder="Tuliskan isi materi lengkap jika berbentuk bacaan/teks..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#28745B]/20 focus:border-[#28745B] text-xs sm:text-sm transition">{{ old('content', $material->content) }}</textarea>
            </div>

            <!-- Urutan Tampil -->
            <div class="w-full sm:w-1/3">
                <label class="block text-xs font-bold uppercase tracking-wider text-[#123F35] mb-1.5">Urutan Tampil</label>
                <input type="number" 
                       name="sort_order" 
                       value="{{ old('sort_order', $material->sort_order) }}" 
                       min="1"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#28745B]/20 focus:border-[#28745B] text-xs sm:text-sm transition">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-300">
                <a href="{{ route('teacher.courses.show', $material->meeting->course_id) }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
                    Batal
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-[#123F35] hover:bg-[#28745B] text-white text-xs font-bold transition shadow-2xs">
                    Perbarui Materi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection