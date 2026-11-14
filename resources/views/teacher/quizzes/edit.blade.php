@extends('layouts.app')

@section('title', 'Edit Quiz - Ekosistem Halal')
@section('header_title', 'Edit Konfigurasi Quiz')

@section('content')
<div class="max-w-4xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Quiz</span>
        </a>

        <span class="px-3 py-1.5 rounded-xl text-xs font-bold tracking-wider bg-[#28745B]/10 text-[#123F35] border border-[#28745B]/30 self-start sm:self-auto shadow-2xs">
            Pertemuan {{ $assignment->meeting->meeting_number ?? '-' }} &bull; Edit Quiz
        </span>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <!-- Card Header Gradasi Hijau Tua -->
        <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-5 sm:p-8 border-b border-slate-300 text-white">
            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase mb-2">
                Pengaturan Aktivitas Quiz
            </span>
            <h1 class="text-lg sm:text-xl font-bold text-white">Edit Konfigurasi Quiz</h1>
            <p class="text-xs text-emerald-100/85 mt-1">Perbarui aturan pengerjaan, durasi, passing grade, atau jumlah soal yang diuji.</p>
        </div>

        <form action="{{ route('teacher.quizzes.update', $assignment->id) }}" method="POST" class="p-5 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Judul Quiz -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Judul Quiz <span class="text-red-600">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required
                       class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Petunjuk Pengerjaan -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Petunjuk Pengerjaan Quiz
                </label>
                <textarea name="description" rows="3" class="w-full p-3.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">{{ old('description', $assignment->description) }}</textarea>
            </div>

            <!-- 3. Konfigurasi Quiz -->
            <div class="p-4 sm:p-5 bg-[#F7F5EE] border border-slate-300 rounded-2xl space-y-4 shadow-2xs">
                <div>
                    <h3 class="text-xs font-extrabold text-[#123F35] uppercase tracking-wider">Aturan Pengerjaan & Randomisasi</h3>
                    <p class="text-xs text-slate-600 mt-0.5">Atur batas waktu pengerjaan, jumlah soal yang diujikan, serta pengacakan soal maupun opsi.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">Durasi Pengerjaan (Menit)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $assignment->quiz?->duration_minutes) }}" min="1" placeholder="Kosongkan jika tanpa timer"
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                        <p class="text-[10px] text-slate-500 mt-1">*Jika durasi habis, quiz otomatis tertutup & menyimpan jawaban terakhir.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                            Jumlah Soal Diuji Per Attempt <span class="text-red-600">*</span>
                        </label>
                        <input type="number" name="question_count" value="{{ old('question_count', $assignment->quiz?->question_count ?? 10) }}" min="1" required
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#123F35] focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                        <p class="text-[10px] text-slate-500 mt-1">*Bisa diatur lebih sedikit dari total bank soal agar soal teracak.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <label class="flex items-start space-x-3 bg-white p-3.5 rounded-xl border border-slate-300 cursor-pointer hover:border-[#28745B] has-[:checked]:border-[#28745B] has-[:checked]:bg-[#28745B]/5 transition shadow-2xs">
                        <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions', $assignment->quiz?->shuffle_questions) ? 'checked' : '' }} class="mt-0.5 rounded text-[#28745B] focus:ring-[#28745B]">
                        <div>
                            <span class="block text-xs font-bold text-[#123F35]">Acak Urutan Soal</span>
                            <span class="block text-[10px] text-slate-500 mt-0.5">Urutan soal berbeda untuk setiap attempt siswa</span>
                        </div>
                    </label>

                    <label class="flex items-start space-x-3 bg-white p-3.5 rounded-xl border border-slate-300 cursor-pointer hover:border-[#28745B] has-[:checked]:border-[#28745B] has-[:checked]:bg-[#28745B]/5 transition shadow-2xs">
                        <input type="checkbox" name="shuffle_answers" value="1" {{ old('shuffle_answers', $assignment->quiz?->shuffle_answers) ? 'checked' : '' }} class="mt-0.5 rounded text-[#28745B] focus:ring-[#28745B]">
                        <div>
                            <span class="block text-xs font-bold text-[#123F35]">Acak Pilihan Jawaban</span>
                            <span class="block text-[10px] text-slate-500 mt-0.5">Posisi opsi (A, B, C, D) diacak secara otomatis</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 4. Batas Attempt -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Batas Percobaan Pengerjaan (Max Attempts)
                </label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts', $assignment->max_attempts) }}" min="1" placeholder="Kosongkan jika Tanpa Batas (Infinite Attempts)"
                       class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition @error('max_attempts') border-red-500 @enderror">
                <p class="text-[11px] text-slate-500 mt-1">*Kosongkan jika siswa diperbolehkan retake quiz tanpa batasan jumlah attempt.</p>
                @error('max_attempts')
                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 5. Tanggal Dibuka & Ditutup -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 sm:p-5 bg-[#F7F5EE] rounded-2xl border border-slate-300 shadow-2xs">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">Waktu Dibuka (Opsional)</label>
                    <input type="datetime-local" name="available_from" value="{{ old('available_from', $assignment->available_from ? \Carbon\Carbon::parse($assignment->available_from)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">Waktu Ditutup / Tenggat (Opsional)</label>
                    <input type="datetime-local" name="available_until" value="{{ old('available_until', $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition @error('available_until') border-red-500 @enderror">
                    @error('available_until')
                        <p class="text-[10px] text-red-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 6. Skor Maksimal & Passing Grade -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                        Nilai Maksimal <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="max_score" value="{{ old('max_score', $assignment->max_score) }}" step="0.01" min="1" required
                           class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-[#123F35] focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                        Passing Grade (Nilai Kelulusan Minimal) <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', $assignment->passing_score) }}" step="0.01" min="0" required
                           class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold text-[#28745B] focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition @error('passing_score') border-red-500 @enderror">
                    @error('passing_score')
                        <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 7. Status Publikasi -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Status Publikasi <span class="text-red-600">*</span>
                </label>
                <select name="status" required class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs font-bold text-[#123F35] focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition cursor-pointer">
                    <option value="published" {{ old('status', $assignment->status) == 'published' ? 'selected' : '' }}>Published (Langsung dapat dilihat siswa)</option>
                    <option value="closed" {{ old('status', $assignment->status) == 'closed' ? 'selected' : '' }}>Closed (Ditutup)</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="pt-5 border-t border-slate-300 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3">
                <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-100 transition text-center shadow-2xs">
                    Batal
                </a>
                <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-6 py-2.5 rounded-xl text-xs font-extrabold shadow-2xs transition text-center">
                    Perbarui Konfigurasi Quiz
                </button>
            </div>
        </form>
    </div>

</div>
@endsection