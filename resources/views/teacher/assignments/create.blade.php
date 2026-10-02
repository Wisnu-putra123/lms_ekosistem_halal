@extends('layouts.app')

@section('title', 'Buat Submission Tugas - Ekosistem Halal')
@section('header_title', 'Buat Tempat Submission Tugas')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.courses.show', $meeting->course_id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-amber-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
            Pertemuan {{ $meeting->meeting_number }}: {{ $meeting->title }}
        </span>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-lg font-bold text-slate-800">Tambah Tempat Submission Baru</h1>
            <p class="text-xs text-slate-500">Siswa akan dapat mengunggah berkas jawaban tugas pada modul ini.</p>
        </div>

        <form action="{{ route('teacher.meetings.assignments.store', $meeting->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- 1. Judul Tugas -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Tugas / Submission <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Laporan Titik Kritis Penyembelihan Halal" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Deskripsi / Instruksi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi & Instruksi Pengerjaan</label>
                <textarea name="description" rows="4" placeholder="Tuliskan petunjuk pengerjaan tugas secara jelas di sini..."
                          class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('description') }}</textarea>
            </div>

            <!-- 3. Tanggal Dibuka & Ditutup (Optional) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Dibuka (Opsional)</label>
                    <input type="datetime-local" name="available_from" value="{{ old('available_from') }}"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika tugas dapat dikerjakan kapan saja.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Ditutup / Tenggat (Opsional)</label>
                    <input type="datetime-local" name="available_until" value="{{ old('available_until') }}"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-amber-500 @error('available_until') border-red-500 @enderror">
                    @error('available_until')
                        <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 4. Penilaian: Nilai Maksimal & Passing Grade -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Maksimal <span class="text-red-500">*</span></label>
                    <input type="number" name="max_score" value="{{ old('max_score', 100) }}" step="0.01" min="1" max="1000" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-400 mt-1">Default skor tertinggi adalah 100.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Passing Grade (Nilai Kelulusan Minimal) <span class="text-red-500">*</span></label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', 75) }}" step="0.01" min="0" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-emerald-700 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('passing_score') border-red-500 @enderror">
                    @error('passing_score')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 5. Lampiran Berkas Pengajar (Multiple File Upload) -->
            <div x-data="{ files: [] }" class="space-y-2">
                <label class="block text-xs font-semibold text-slate-700">Lampirkan Berkas Soal / Acuan (Opsional)</label>
                <div class="border-2 border-dashed border-slate-200 hover:border-amber-400 rounded-2xl p-6 text-center transition bg-slate-50/50 cursor-pointer relative">
                    <input type="file" name="attachments[]" multiple 
                           @change="files = Array.from($event.target.files)"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="space-y-2 pointer-events-none">
                        <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div class="text-xs text-slate-600 font-medium">Klik atau seret beberapa berkas ke sini untuk melampirkan file instruksi/template</div>
                        <p class="text-[10px] text-slate-400">PDF, DOCX, PPTX, ZIP, PNG, JPG (Maksimal 10MB per berkas)</p>
                    </div>
                </div>

                <!-- Preview Daftar Berkas Yang Dipilih -->
                <template x-if="files.length > 0">
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl space-y-1 mt-2">
                        <span class="text-[11px] font-bold text-amber-800">Berkas terpilih (<span x-text="files.length"></span>):</span>
                        <ul class="text-xs text-amber-900 list-disc list-inside space-y-0.5">
                            <template x-for="f in files" :key="f.name">
                                <li x-text="f.name + ' (' + (f.size / 1024 / 1024).toFixed(2) + ' MB)'"></li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>

            <!-- 6. Status Publikasi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Publikasi <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (Langsung dapat dilihat siswa)</option>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Disimpan sementara)</option>
                    <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed (Ditutup)</option>
                </select>
            </div>

            <!-- Form Submit Actions -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('teacher.courses.show', $meeting->course_id) }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition">
                    Simpan Submission
                </button>
            </div>
        </form>
    </div>

</div>
@endsection