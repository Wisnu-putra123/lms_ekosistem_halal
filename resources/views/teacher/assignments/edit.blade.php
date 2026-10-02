@extends('layouts.app')

@section('title', 'Edit Submission Tugas - Ekosistem Halal')
@section('header_title', 'Edit Tempat Submission Tugas')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.courses.show', $assignment->course_id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-lg font-bold text-slate-800">Edit Tempat Submission Tugas</h1>
            <p class="text-xs text-slate-500">Perbarui informasi, parameter penilaian, atau lampiran tugas.</p>
        </div>

        <form action="{{ route('teacher.assignments.update', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Judul Tugas -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Tugas / Submission <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- 2. Deskripsi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi & Instruksi Pengerjaan</label>
                <textarea name="description" rows="4" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $assignment->description) }}</textarea>
            </div>

            <!-- 3. Tanggal Dibuka & Ditutup -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Dibuka</label>
                    <input type="datetime-local" name="available_from" value="{{ old('available_from', $assignment->available_from ? \Carbon\Carbon::parse($assignment->available_from)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Ditutup / Tenggat</label>
                    <input type="datetime-local" name="available_until" value="{{ old('available_until', $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- 4. Penilaian -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Maksimal <span class="text-red-500">*</span></label>
                    <input type="number" name="max_score" value="{{ old('max_score', $assignment->max_score) }}" step="0.01" min="1" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Passing Grade <span class="text-red-500">*</span></label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', $assignment->passing_score) }}" step="0.01" min="0" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-emerald-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- 5. Berkas Lampiran Yang Ada -->
            @if($assignment->attachments && $assignment->attachments->count() > 0)
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-700">Lampiran Berkas Saat Ini:</label>
                    <div class="space-y-1.5">
                        @foreach($assignment->attachments as $attachment)
                            @if($attachment->media)
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                                    <span class="text-xs text-slate-700 font-medium truncate">{{ $attachment->media->file_name }}</span>
                                    <label class="inline-flex items-center space-x-1 text-xs text-red-600 cursor-pointer">
                                        <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" class="rounded text-red-600 focus:ring-red-500">
                                        <span>Hapus</span>
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 6. Tambah Lampiran Berkas Baru -->
            <div x-data="{ files: [] }" class="space-y-2">
                <label class="block text-xs font-semibold text-slate-700">Tambah Lampiran Berkas Baru (Opsional)</label>
                <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-5 text-center transition bg-slate-50/50 cursor-pointer relative">
                    <input type="file" name="attachments[]" multiple @change="files = Array.from($event.target.files)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <span class="text-xs text-slate-500">Klik untuk memilih berkas tambahan</span>
                </div>
            </div>

            <!-- 7. Status -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Publikasi <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="published" {{ old('status', $assignment->status) == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ old('status', $assignment->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="closed" {{ old('status', $assignment->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition">
                    Perbarui Submission
                </button>
            </div>
        </form>
    </div>

</div>
@endsection