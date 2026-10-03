@extends('layouts.app')

@section('title', 'Penilaian Tugas - ' . $submission->user->name)
@section('header_title', 'Penilaian Submission Siswa')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.assignments.show', $submission->assignment_id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-amber-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Tugas</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KIRI: Review Tugas Siswa (2 Kolom) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Siswa:</span>
                        <h2 class="text-base font-bold text-slate-800">{{ $submission->user->name }}</h2>
                        <p class="text-xs text-slate-400">{{ $submission->user->email }}</p>
                    </div>
                    <span class="text-xs text-slate-500">
                        Dikumpulkan: {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}
                    </span>
                </div>

                <!-- Teks Jawaban -->
                @if($submission->submission_text)
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Teks Jawaban Siswa:</label>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                            {{ $submission->submission_text }}
                        </div>
                    </div>
                @endif

                <!-- Lampiran Berkas Siswa -->
                @if($submission->attachments && $submission->attachments->count() > 0)
                    <div class="space-y-2 pt-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase">Lampiran Berkas Jawaban:</label>
                        <div class="space-y-2">
                            @foreach($submission->attachments as $att)
                                @if($att->media)
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 overflow-hidden pr-2">
                                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-xs text-slate-800 font-semibold truncate">{{ $att->media->file_name }}</span>
                                        </div>
                                        <a href="{{ asset('storage/' . $att->media->file_path) }}" download class="text-[11px] font-bold text-amber-600 hover:underline flex-shrink-0">Unduh Berkas</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- KANAN: Form Pengisian Nilai & Feedback (1 Kolom) -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3">Form Penilaian</h3>

                <!-- Informasi Parameter Tugas -->
                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Nilai Maksimal:</span>
                        <span class="font-bold text-slate-800">{{ number_format($submission->assignment->max_score, 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Passing Grade (KKM):</span>
                        <span class="font-bold text-emerald-700">{{ number_format($submission->assignment->passing_score, 0) }}</span>
                    </div>
                </div>

                <form action="{{ route('teacher.submissions.update-grade', $submission->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Input Nilai -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai / Skor <span class="text-red-500">*</span></label>
                        <input type="number" name="score" value="{{ old('score', $submission->score) }}" step="0.01" min="0" max="{{ $submission->assignment->max_score }}" required
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('score') border-red-500 @enderror">
                        @error('score')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Feedback -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Feedback</label>
                        <textarea name="feedback" rows="4" placeholder="Tuliskan masukan atau catatan untuk siswa..."
                                  class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('feedback', $submission->feedback) }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 rounded-xl text-xs shadow-sm transition">
                        Simpan Penilaian
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection