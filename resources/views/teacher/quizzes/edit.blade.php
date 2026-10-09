@extends('layouts.app')

@section('title', 'Edit Quiz - Ekosistem Halal')
@section('header_title', 'Edit Konfigurasi Quiz')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-amber-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Quiz</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-lg font-bold text-slate-800">Edit Konfigurasi Quiz</h1>
            <p class="text-xs text-slate-500">Perbarui aturan pengerjaan, durasi, passing grade, atau jumlah soal yang diuji.</p>
        </div>

        <form action="{{ route('teacher.quizzes.update', $assignment->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Judul Quiz -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Quiz <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <!-- Petunjuk Pengerjaan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Petunjuk Pengerjaan Quiz</label>
                <textarea name="description" rows="3" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('description', $assignment->description) }}</textarea>
            </div>

            <!-- Konfigurasi Quiz -->
            <div class="p-5 bg-amber-50/60 border border-amber-200/80 rounded-2xl space-y-4">
                <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Aturan Pengerjaan & Randomisasi</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Durasi Pengerjaan (Menit)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $assignment->quiz?->duration_minutes) }}" min="1" placeholder="Kosongkan jika tanpa timer"
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Soal Diuji Per Attempt <span class="text-red-500">*</span></label>
                        <input type="number" name="question_count" value="{{ old('question_count', $assignment->quiz?->question_count ?? 10) }}" min="1" required
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <label class="flex items-center space-x-3 bg-white p-3 rounded-xl border border-amber-200/80 cursor-pointer hover:border-amber-400 transition">
                        <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', $assignment->quiz?->shuffle_questions) ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Acak Urutan Soal</span>
                            <span class="block text-[10px] text-slate-500">Urutan soal berbeda untuk setiap attempt siswa</span>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 bg-white p-3 rounded-xl border border-amber-200/80 cursor-pointer hover:border-amber-400 transition">
                        <input type="checkbox" name="shuffle_answers" value="1" {{ old('shuffle_answers', $assignment->quiz?->shuffle_answers) ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Acak Pilihan Jawaban</span>
                            <span class="block text-[10px] text-slate-500">Posisi opsi (A, B, C, D) diacak secara otomatis</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Batas Attempt -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Percobaan Pengerjaan (Max Attempts)</label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts', $assignment->max_attempts) }}" min="1" placeholder="Kosongkan jika Tanpa Batas (Infinite Attempts)"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <!-- Tanggal Dibuka & Ditutup -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Dibuka (Opsional)</label>
                    <input type="datetime-local" name="available_from" value="{{ old('available_from', $assignment->available_from ? \Carbon\Carbon::parse($assignment->available_from)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Ditutup / Tenggat (Opsional)</label>
                    <input type="datetime-local" name="available_until" value="{{ old('available_until', $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <!-- Skor Maksimal & Passing Grade -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Maksimal <span class="text-red-500">*</span></label>
                    <input type="number" name="max_score" value="{{ old('max_score', $assignment->max_score) }}" step="0.01" min="1" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Passing Grade (Nilai Kelulusan Minimal) <span class="text-red-500">*</span></label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', $assignment->passing_score) }}" step="0.01" min="0" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-emerald-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <!-- Status Publikasi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Publikasi <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="published" {{ old('status', $assignment->status) == 'published' ? 'selected' : '' }}>Published (Langsung dapat dilihat siswa)</option>
                    <option value="draft" {{ old('status', $assignment->status) == 'draft' ? 'selected' : '' }}>Draft (Disimpan sementara)</option>
                    <option value="closed" {{ old('status', $assignment->status) == 'closed' ? 'selected' : '' }}>Closed (Ditutup)</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition">
                    Perbarui Konfigurasi Quiz
                </button>
            </div>
        </form>
    </div>

</div>
@endsection