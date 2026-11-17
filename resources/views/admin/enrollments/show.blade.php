@extends('layouts.app')

@section('title', 'Detail Kepesertaan - ' . ($enrollment->user->name ?? 'Peserta'))

@section('content')
@php
    $progressPercent = round($enrollment->progress->progress_percentage ?? 0);
    $isExpired = $enrollment->access_expires_at && $enrollment->access_expires_at->isPast();
    $initialAccessMode = old('access_mode', $enrollment->access_expires_at ? 'limited' : 'unlimited');
    $defaultExpiresAt = old('access_expires_at', $enrollment->access_expires_at ? $enrollment->access_expires_at->format('Y-m-d\TH:i') : '');
@endphp

<div class="min-h-screen bg-white py-8 px-4 sm:px-6 lg:px-8"
     x-data="{
        accessMode: '{{ $initialAccessMode }}',
        previewModal: false,
        previewUrl: '',
        previewTitle: '',
        previewType: 'other',
        openPreview(url, title, mimeOrExt) {
            this.previewUrl = url;
            this.previewTitle = title;
            const lower = (mimeOrExt || '').toLowerCase();
            if (lower.includes('pdf') || url.toLowerCase().endsWith('.pdf')) {
                this.previewType = 'pdf';
            } else if (lower.includes('image') || /\.(jpg|jpeg|png|gif|webp)$/i.test(url)) {
                this.previewType = 'image';
            } else {
                this.previewType = 'other';
            }
            this.previewModal = true;
        }
     }">

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Tombol Kembali (Atas Sebelah Kiri) --}}
        <div>
            <a href="{{ route('admin.enrollments.index') }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            <span>Kembali ke Manajemen Enrollment</span>
        </a>
        </div>

        {{-- Header Banner --}}
        <div class="bg-gradient-to-r from-[#123F35] via-[#1B5749] to-[#28745B] rounded-2xl p-6 sm:p-8 text-white shadow-sm relative overflow-hidden">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <nav class="flex items-center gap-2 text-xs text-emerald-100/90 mb-3">
                        <a href="{{ route('admin.enrollments.index') }}" class="hover:text-white font-medium transition">
                            Manajemen Kepesertaan
                        </a>
                        <span>/</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-white/15 text-white font-semibold">
                            Detail #{{ $enrollment->id }}
                        </span>
                    </nav>

                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                        {{ $enrollment->user->name ?? 'Peserta' }}
                    </h1>

                    <p class="mt-1.5 text-sm text-emerald-100/90 flex flex-wrap items-center gap-2">
                        <span>Pelatihan: <strong class="text-white">{{ $enrollment->course->title ?? '-' }}</strong></span>
                        @if (!empty($enrollment->course->code))
                            <span class="px-2 py-0.5 rounded-md bg-white/15 border border-white/20 font-mono text-xs text-white">
                                {{ $enrollment->course->code }}
                            </span>
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <form method="POST" action="{{ route('admin.enrollments.recalculate', $enrollment) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white text-[#123F35] hover:bg-emerald-50 text-sm font-semibold shadow-sm transition">
                            <svg class="w-4 h-4 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Hitung Ulang Progres
                        </button>
                    </form>

                    <form method="POST"
                          action="{{ route('admin.enrollments.purgeFiles', $enrollment) }}"
                          onsubmit="return confirm('Hapus seluruh file fisik jawaban tugas dan lampiran feedback milik peserta ini? Nilai dan status kelulusan tidak akan terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-amber-300 bg-amber-50 text-amber-800 text-sm font-semibold hover:bg-amber-100 transition">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Bersihkan File Tugas
                        </button>
                    </form>

                    @if (!$enrollment->certificate || $enrollment->certificate->status !== 'published')
                        <form method="POST"
                              action="{{ route('admin.enrollments.destroy', $enrollment) }}"
                              onsubmit="return confirm('Yakin ingin menghapus seluruh data enrollment peserta ini? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-red-600/90 hover:bg-red-600 text-white border border-red-400/30 text-sm font-semibold shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-emerald-800 shadow-2xs">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 flex items-center gap-3 text-red-800 shadow-2xs">
                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        {{-- Grid Utama: Kiri (Profil & Pengaturan Akses) | Kanan (Detail Akademik) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- KOLOM KIRI (4 Kolom) --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- Kartu Profil Peserta --}}
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
                    <div class="p-5 bg-[#28745B] text-white flex items-center gap-3.5">
                        @if ($enrollment->user?->profile_photo_url)
                            <img src="{{ $enrollment->user->profile_photo_url }}"
                                 alt="{{ $enrollment->user->name }}"
                                 class="h-12 w-12 rounded-full object-cover border-2 border-white/30 shrink-0">
                        @else
                            <div class="h-12 w-12 rounded-full bg-white/15 flex items-center justify-center font-bold text-base shrink-0">
                                {{ strtoupper(mb_substr($enrollment->user->name ?? 'U', 0, 2)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h2 class="text-base font-bold truncate">{{ $enrollment->user->name ?? '-' }}</h2>
                            <p class="text-xs text-emerald-100 truncate">{{ $enrollment->user->email ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="p-5 space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">NIK</span>
                            <span class="font-mono font-semibold text-gray-800">{{ $enrollment->user->nik ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Instansi</span>
                            <span class="font-medium text-gray-800 text-right">{{ $enrollment->user->institution ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Tanggal Daftar</span>
                            <span class="font-medium text-gray-800">{{ optional($enrollment->enrolled_at)->format('d M Y, H:i') ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Tanggal Selesai</span>
                            <span class="font-medium text-gray-800">{{ optional($enrollment->completed_at)->format('d M Y, H:i') ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-gray-500">Status Sertifikat</span>
                            @if ($enrollment->certificate)
                                <span class="px-2.5 py-0.5 rounded-full font-semibold {{ $enrollment->certificate->status === 'published' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ ucfirst($enrollment->certificate->status) }}
                                    @if ($enrollment->certificate->certificate_number)
                                        ({{ $enrollment->certificate->certificate_number }})
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400">Belum diterbitkan</span>
                            @endif
                        </div>

                        {{-- Progress Bar --}}
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-semibold text-gray-700">Progres Pembelajaran</span>
                                <span class="text-sm font-bold text-[#123F35]">{{ $progressPercent }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-2.5 rounded-full transition-all duration-300 {{ $progressPercent >= 100 ? 'bg-emerald-600' : 'bg-[#28745B]' }}"
                                     style="width: {{ min(100, max(0, $progressPercent)) }}%"></div>
                            </div>
                            @if ($enrollment->progress?->last_activity_at)
                                <p class="mt-1.5 text-[11px] text-gray-400">
                                    Aktivitas terakhir: {{ $enrollment->progress->last_activity_at->format('d M Y H:i') }} ({{ $enrollment->progress->last_activity_at->diffForHumans() }})
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Form Pengaturan Status & Waktu Akses --}}
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 bg-[#28745B] text-white flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-white/15 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </span>
                        <h3 class="text-sm font-bold">Pengaturan Status & Masa Akses</h3>
                    </div>

                    <form method="POST" action="{{ route('admin.enrollments.update', $enrollment) }}" class="p-5 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="status" class="block text-xs font-semibold text-gray-700 mb-1.5">Status Kepesertaan</label>
                            <select name="status" id="status"
                                    class="w-full py-2.5 px-3 text-sm rounded-xl border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                                <option value="active" @selected(old('status', $enrollment->status) === 'active')>Aktif</option>
                                <option value="completed" @selected(old('status', $enrollment->status) === 'completed')>Selesai (Completed)</option>
                                <option value="dropped" @selected(old('status', $enrollment->status) === 'dropped')>Berhenti (Dropped)</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Batasan Waktu Akses</label>
                            <div class="grid grid-cols-2 gap-2.5">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs font-semibold transition"
                                       :class="accessMode === 'unlimited' ? 'border-[#28745B] bg-[#28745B]/5 text-[#123F35]' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                                    <input type="radio" name="access_mode" value="unlimited" x-model="accessMode" class="text-[#28745B] focus:ring-[#28745B]">
                                    Tanpa Batas
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs font-semibold transition"
                                       :class="accessMode === 'limited' ? 'border-[#28745B] bg-[#28745B]/5 text-[#123F35]' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                                    <input type="radio" name="access_mode" value="limited" x-model="accessMode" class="text-[#28745B] focus:ring-[#28745B]">
                                    Berbatas Waktu
                                </label>
                            </div>
                        </div>

                        <div x-show="accessMode === 'limited'" x-cloak>
                            <label for="access_expires_at" class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Tanggal & Waktu Berakhir
                            </label>
                            <input type="datetime-local"
                                   name="access_expires_at"
                                   id="access_expires_at"
                                   value="{{ $defaultExpiresAt }}"
                                   class="w-full py-2.5 px-3 text-sm rounded-xl border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                            @if ($isExpired)
                                <p class="mt-1.5 text-xs text-red-600 font-medium">
                                    Status saat ini: Masa akses telah kedaluwarsa.
                                </p>
                            @endif
                            @error('access_expires_at')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 px-4 rounded-xl bg-[#28745B] text-white text-sm font-semibold hover:bg-[#123F35] transition shadow-2xs">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            {{-- KOLOM KANAN: Rincian Pertemuan, Tugas, Submission, & Kuis (8 Kolom) --}}
            <div class="lg:col-span-8 space-y-5">

                @forelse ($enrollment->course->meetings ?? [] as $meeting)
                    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
                        {{-- Header Pertemuan --}}
                        <div class="px-6 py-4 bg-[#28745B] text-white flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-100">
                                    Pertemuan {{ $meeting->meeting_number }}
                                </span>
                                <h3 class="text-base font-bold text-white">
                                    {{ $meeting->title }}
                                </h3>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-white/15 border border-white/20 text-xs font-medium text-white">
                                {{ $meeting->materials->count() }} Materi &bull; {{ $meeting->assignments->count() }} Tugas/Kuis
                            </span>
                        </div>

                        <div class="p-6 space-y-5">
                            @if ($meeting->assignments->isEmpty())
                                <p class="text-xs text-gray-500 italic">Tidak ada tugas atau kuis pada pertemuan ini.</p>
                            @else
                                @foreach ($meeting->assignments as $assignment)
                                    @php
                                        $assignmentSubmissions = $submissions->get($assignment->id, collect());
                                        $quiz = $assignment->quiz;
                                        $attempts = $quiz ? $quizAttempts->get($quiz->id, collect()) : collect();
                                    @endphp

                                    <div class="rounded-xl border border-gray-200 p-4 space-y-4 bg-gray-50/70">
                                        {{-- Judul Tugas / Kuis --}}
                                        <div class="flex flex-wrap items-start justify-between gap-2 border-b border-gray-200/80 pb-3">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase {{ $quiz ? 'bg-purple-100 text-purple-800' : 'bg-[#123F35]/10 text-[#123F35]' }}">
                                                        {{ $quiz ? 'Kuis' : 'Tugas' }}
                                                    </span>
                                                    <h4 class="text-sm font-bold text-gray-900">{{ $assignment->title }}</h4>
                                                </div>
                                                @if ($assignment->due_date)
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        Tenggat: {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y, H:i') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 bg-white px-2.5 py-1 rounded-lg border border-gray-200">
                                                Maks. Nilai: <span class="font-bold text-gray-800">{{ $assignment->max_score ?? 100 }}</span>
                                            </div>
                                        </div>

                                        {{-- JIKA KUIS: Tampilkan Riwayat Quiz Attempts --}}
                                        @if ($quiz)
                                            @if ($attempts->isEmpty())
                                                <p class="text-xs text-gray-500">Peserta belum mengerjakan kuis ini.</p>
                                            @else
                                                <div class="space-y-2.5">
                                                    @foreach ($attempts as $attempt)
                                                        <div class="bg-white rounded-xl border border-gray-200 p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                            <div class="space-y-1 text-xs">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="font-bold text-[#123F35]">Percobaan #{{ $attempt->attempt_number }}</span>
                                                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $attempt->status === 'submitted' || $attempt->status === 'graded' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                                        {{ ucfirst($attempt->status) }}
                                                                    </span>
                                                                </div>
                                                                <div class="text-gray-500">
                                                                    Mulai: {{ optional($attempt->started_at)->format('d M Y H:i') ?? '-' }}
                                                                    @if ($attempt->submitted_at)
                                                                        &bull; Selesai: {{ $attempt->submitted_at->format('d M Y H:i') }}
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-3">
                                                                <div class="text-right">
                                                                    <span class="text-[11px] text-gray-400 block">Skor</span>
                                                                    <span class="text-sm font-bold text-[#123F35]">
                                                                        {{ !is_null($attempt->score) ? number_format($attempt->score, 1) : '-' }}
                                                                    </span>
                                                                </div>

                                                                <form method="POST"
                                                                      action="{{ route('admin.enrollments.quizAttempts.reset', [$enrollment, $attempt]) }}"
                                                                      onsubmit="return confirm('Reset percobaan kuis #{{ $attempt->attempt_number }} ini? Data jawaban pada percobaan ini akan dihapus permanen.')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                            class="px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-600 hover:text-white transition">
                                                                        Reset Attempt
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                        {{-- JIKA TUGAS: Tampilkan Riwayat Submissions, File Jawaban, & Feedback --}}
                                        @else
                                            @if ($assignmentSubmissions->isEmpty())
                                                <p class="text-xs text-gray-500">Peserta belum mengumpulkan tugas ini.</p>
                                            @else
                                                <div class="space-y-3">
                                                    @foreach ($assignmentSubmissions as $sub)
                                                        <div class="bg-white rounded-xl border border-gray-200 p-4 space-y-3">
                                                            {{-- Header Submission --}}
                                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                                <div class="flex items-center gap-2 text-xs">
                                                                    <span class="font-bold text-[#123F35]">Submission #{{ $sub->attempt_number }}</span>
                                                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                                                        {{ $sub->status === 'graded' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($sub->status === 'returned' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                                                                        {{ ucfirst($sub->status) }}
                                                                    </span>
                                                                    @if ($sub->submitted_at)
                                                                        <span class="text-gray-400">
                                                                            Dikumpulkan: {{ $sub->submitted_at->format('d M Y H:i') }}
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                <div class="flex items-center gap-3">
                                                                    <div class="text-xs">
                                                                        Nilai:
                                                                        <span class="font-bold text-sm text-[#123F35]">
                                                                            {{ !is_null($sub->score) ? number_format($sub->score, 1) : 'Belum dinilai' }}
                                                                        </span>
                                                                    </div>

                                                                    <form method="POST"
                                                                          action="{{ route('admin.enrollments.submissions.reset', [$enrollment, $sub]) }}"
                                                                          onsubmit="return confirm('Hapus & reset submission #{{ $sub->attempt_number }} ini beserta seluruh berkas lampirannya?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                                class="px-2.5 py-1 rounded-lg border border-red-200 bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-600 hover:text-white transition">
                                                                            Reset Submission
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                            {{-- Teks Jawaban Peserta --}}
                                                            @if (!empty($sub->submission_text))
                                                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100 text-xs text-gray-700 whitespace-pre-line">
                                                                    <span class="block text-[11px] font-semibold text-gray-400 uppercase mb-1">Catatan / Jawaban Teks Peserta:</span>
                                                                    {{ $sub->submission_text }}
                                                                </div>
                                                            @endif

                                                            {{-- Lampiran Berkas Jawaban Peserta --}}
                                                            @if ($sub->attachments->isNotEmpty())
                                                                <div>
                                                                    <span class="block text-[11px] font-semibold text-gray-500 mb-1.5">Berkas Jawaban Peserta:</span>
                                                                    <div class="flex flex-wrap gap-2">
                                                                        @foreach ($sub->attachments as $att)
                                                                            @php
                                                                                $media = $att->media;
                                                                                $fileUrl = $media && $media->file_path ? asset('storage/' . $media->file_path) : '#';
                                                                                $fileName = $media->original_name ?? basename($media->file_path ?? 'Berkas Jawaban');
                                                                                $mimeType = $media->mime_type ?? '';
                                                                            @endphp
                                                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-xs">
                                                                                <span class="font-medium text-gray-800 max-w-[200px] truncate" title="{{ $fileName }}">
                                                                                    {{ $fileName }}
                                                                                </span>
                                                                                @if ($fileUrl !== '#')
                                                                                    <button type="button"
                                                                                            @click="openPreview('{{ $fileUrl }}', '{{ addslashes($fileName) }}', '{{ $mimeType }}')"
                                                                                            class="text-[#28745B] hover:underline font-semibold">
                                                                                        Preview
                                                                                    </button>
                                                                                    <a href="{{ $fileUrl }}" download="{{ $fileName }}"
                                                                                       class="text-gray-600 hover:text-gray-900 font-semibold">
                                                                                        Unduh
                                                                                    </a>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            {{-- Feedback Instruktur & Lampiran Feedback --}}
                                                            @if (!empty($sub->feedback) || $sub->feedbackAttachments->isNotEmpty())
                                                                <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/80 space-y-2 text-xs">
                                                                    <div class="flex items-center justify-between">
                                                                        <span class="font-bold text-[#123F35]">
                                                                            Feedback Instruktur {{ $sub->grader ? "({$sub->grader->name})" : '' }}
                                                                        </span>
                                                                        @if ($sub->graded_at)
                                                                            <span class="text-[11px] text-gray-500">{{ $sub->graded_at->format('d M Y H:i') }}</span>
                                                                        @endif
                                                                    </div>

                                                                    @if (!empty($sub->feedback))
                                                                        <p class="text-gray-700 whitespace-pre-line">{{ $sub->feedback }}</p>
                                                                    @endif

                                                                    @if ($sub->feedbackAttachments->isNotEmpty())
                                                                        <div class="pt-1 flex flex-wrap gap-2">
                                                                            @foreach ($sub->feedbackAttachments as $fbAtt)
                                                                                @php
                                                                                    $fbMedia = $fbAtt->media;
                                                                                    $fbUrl = $fbMedia && $fbMedia->file_path ? asset('storage/' . $fbMedia->file_path) : '#';
                                                                                    $fbName = $fbMedia->original_name ?? basename($fbMedia->file_path ?? 'Lampiran Feedback');
                                                                                    $fbMime = $fbMedia->mime_type ?? '';
                                                                                @endphp
                                                                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-white border border-emerald-200 text-xs">
                                                                                    <span class="font-medium text-gray-800 max-w-[180px] truncate">{{ $fbName }}</span>
                                                                                    @if ($fbUrl !== '#')
                                                                                        <button type="button"
                                                                                                @click="openPreview('{{ $fbUrl }}', '{{ addslashes($fbName) }}', '{{ $fbMime }}')"
                                                                                                class="text-[#28745B] hover:underline font-semibold">
                                                                                            Preview
                                                                                        </button>
                                                                                        <a href="{{ $fbUrl }}" download="{{ $fbName }}"
                                                                                           class="text-gray-600 hover:text-gray-900 font-semibold">
                                                                                            Unduh
                                                                                        </a>
                                                                                    @endif
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-gray-200/80 p-10 text-center">
                        <p class="text-sm font-semibold text-gray-700">Belum ada struktur pertemuan pada pelatihan ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL PREVIEW BERKAS (PDF / GAMBAR / UNDUH) --}}
    <div x-show="previewModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/65 backdrop-blur-xs">
        <div @click.away="previewModal = false"
             class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="px-5 py-3.5 bg-[#28745B] text-white flex items-center justify-between gap-4">
                <h3 class="text-sm font-bold truncate" x-text="previewTitle"></h3>
                <div class="flex items-center gap-3 shrink-0">
                    <a :href="previewUrl"
                       :download="previewTitle"
                       class="px-3 py-1 rounded-lg bg-white/15 hover:bg-white/25 text-xs font-semibold transition">
                        Unduh Berkas
                    </a>
                    <button type="button" @click="previewModal = false" class="p-1 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-4 bg-gray-100 flex-1 overflow-auto flex items-center justify-center min-h-[60vh]">
                <template x-if="previewType === 'pdf'">
                    <iframe :src="previewUrl" class="w-full h-[72vh] rounded-lg border border-gray-300 bg-white"></iframe>
                </template>

                <template x-if="previewType === 'image'">
                    <img :src="previewUrl" :alt="previewTitle" class="max-h-[72vh] max-w-full rounded-lg shadow object-contain">
                </template>

                <template x-if="previewType === 'other'">
                    <div class="text-center p-8 bg-white rounded-xl border border-gray-200 max-w-md">
                        <p class="text-sm font-semibold text-gray-800 mb-2">Pratinjau langsung tidak tersedia untuk format berkas ini.</p>
                        <p class="text-xs text-gray-500 mb-4">Silakan unduh berkas untuk membukanya di perangkat Anda.</p>
                        <a :href="previewUrl"
                           :download="previewTitle"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#28745B] text-white text-xs font-semibold hover:bg-[#123F35] transition">
                            Unduh Berkas Sekarang
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection