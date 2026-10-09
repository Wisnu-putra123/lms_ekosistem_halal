@extends('layouts.app')

@section('title', 'Buat Quiz Baru - Ekosistem Halal')
@section('header_title', 'Buat Quiz Baru')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
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
            <h1 class="text-lg font-bold text-slate-800">Tambah Quiz Baru</h1>
            <p class="text-xs text-slate-500">Konfigurasikan judul, durasi waktu, passing grade, acak soal, serta jumlah soal yang ditampilkan.</p>
        </div>

        <form action="{{ route('teacher.meetings.quizzes.store', $meeting->id) }}" method="POST" class="space-y-6">
            @csrf

            <!-- 1. Judul Quiz -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Quiz <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Kuis Evaluasi Pemahaman Sertifikasi Halal" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Deskripsi / Petunjuk Pengerjaan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Petunjuk Pengerjaan Quiz</label>
                <textarea name="description" rows="3" placeholder="Tuliskan petunjuk pengerjaan quiz untuk siswa di sini..."
                          class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('description') }}</textarea>
            </div>

            <!-- 3. KONFIGURASI KHUSUS QUIZ (Durasi, Jumlah Soal, Acak) -->
            <div class="p-5 bg-amber-50/60 border border-amber-200/80 rounded-2xl space-y-4">
                <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Aturan Pengerjaan & Randomisasi</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Durasi pengerjaan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Durasi Pengerjaan (Menit)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1" placeholder="Contoh: 30 (Kosongkan jika tanpa timer)"
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <p class="text-[10px] text-amber-700 mt-1">*Jika durasi habis, quiz otomatis tertutup & menyimpan jawaban terakhir.</p>
                    </div>

                    <!-- Jumlah Soal Ditampilkan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Soal Diuji Per Attempt <span class="text-red-500">*</span></label>
                        <input type="number" name="question_count" value="{{ old('question_count', 10) }}" min="1" required
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <p class="text-[10px] text-amber-700 mt-1">*Bisa diatur lebih sedikit dari total bank soal agar acak.</p>
                    </div>
                </div>

                <!-- Toggle Options (Acak Soal & Jawaban) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <label class="flex items-center space-x-3 bg-white p-3 rounded-xl border border-amber-200/80 cursor-pointer hover:border-amber-400 transition">
                        <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', '1') == '1' ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Acak Urutan Soal</span>
                            <span class="block text-[10px] text-slate-500">Urutan soal berbeda untuk setiap attempt siswa</span>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 bg-white p-3 rounded-xl border border-amber-200/80 cursor-pointer hover:border-amber-400 transition">
                        <input type="checkbox" name="shuffle_answers" value="1" {{ old('shuffle_answers', '1') == '1' ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Acak Pilihan Jawaban</span>
                            <span class="block text-[10px] text-slate-500">Posisi opsi (A, B, C, D) diacak secara otomatis</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 4. BATAS PERCOBAAN PENGERJAAN (Max Attempts) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Percobaan Pengerjaan (Max Attempts)</label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts') }}" min="1" placeholder="Kosongkan jika Tanpa Batas (Infinite Attempts)"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('max_attempts') border-red-500 @enderror">
                <p class="text-[10px] text-slate-400 mt-1">*Kosongkan jika siswa diperbolehkan retake quiz tanpa batasan jumlah attempt.</p>
            </div>

            <!-- 5. Tanggal Dibuka & Ditutup -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Dibuka (Opsional)</label>
                    <input type="datetime-local" name="available_from" value="{{ old('available_from') }}"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
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

            <!-- 6. Penilaian: Nilai Maksimal & Passing Grade -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Maksimal <span class="text-red-500">*</span></label>
                    <input type="number" name="max_score" value="{{ old('max_score', 100) }}" step="0.01" min="1" max="1000" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Passing Grade (Nilai Kelulusan Minimal) <span class="text-red-500">*</span></label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', 75) }}" step="0.01" min="0" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-emerald-700 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('passing_score') border-red-500 @enderror">
                </div>
            </div>

            <!-- 7. Status Publikasi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Publikasi <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (Langsung dapat dilihat siswa)</option>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Disimpan sementara)</option>
                    <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed (Ditutup)</option>
                </select>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('teacher.courses.show', $meeting->course_id) }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition">
                    Simpan & Buat Bank Soal
                </button>
            </div>
        </form>
    </div>

</div>
@endsection