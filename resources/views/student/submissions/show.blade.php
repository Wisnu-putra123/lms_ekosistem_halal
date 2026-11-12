@extends('layouts.app')

@section('title', $assignment->title . ' - Ekosistem Halal')
@section('header_title', 'Pengumpulan Tugas Submission')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-5xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('student.courses.show', $assignment->course_id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>

        <span class="px-3 py-1.5 rounded-xl text-xs font-bold tracking-wider bg-[#28745B]/10 text-[#123F35] border border-[#28745B]/30 self-start sm:self-auto shadow-2xs">
            Pertemuan {{ $assignment->meeting->meeting_number }}: {{ $assignment->meeting->title }}
        </span>
    </div>

    <!-- Alert Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center space-x-2 shadow-2xs">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-300 text-red-800 rounded-2xl text-xs font-semibold flex items-center space-x-2 shadow-2xs">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Alert jika Enrollment Sudah Complete -->
    @if($isEnrollmentCompleted)
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-[#123F35] rounded-2xl text-xs font-semibold flex items-center space-x-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Status pelatihan Anda telah <strong>Selesai (Completed)</strong>. Pengumpulan atau perubahan attempt baru telah dikunci.</span>
        </div>
    @elseif($isPastDeadline)
        <!-- Alert jika Tenggat Waktu Berakhir -->
        <div class="p-4 bg-red-50 border border-red-300 text-red-800 rounded-2xl text-xs font-semibold flex items-center space-x-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Tenggat waktu pengumpulan tugas ini telah berakhir. Anda tidak dapat lagi mengirim atau memperbarui jawaban.</span>
        </div>
    @endif

    <!-- 1. TOP CARD FULL GRADASI HIJAU (DARI ATAS SAMPAI BAWAH) -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] rounded-2xl border border-[#28745B]/40 shadow-md p-5 sm:p-8 text-white space-y-6">
        
        <!-- Judul & Status Kelulusan -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-1.5">
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase backdrop-blur-xs">
                    Tugas Upload / Submission
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug">{{ $assignment->title }}</h1>
            </div>

            <!-- Status Kelulusan Berdasarkan Best Score & Tenggat Waktu -->
            <div class="flex-shrink-0">
                @if($isPastDeadline && $submissions->isEmpty())
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-red-500/20 text-red-200 border border-red-400/50 backdrop-blur-xs">
                        Tenggat Waktu Berakhir
                    </span>
                @elseif($submissions->isEmpty())
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] shadow-2xs">
                        Belum Mengumpulkan
                    </span>
                @elseif(!is_null($bestScore) && $bestScore >= $assignment->passing_score)
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] shadow-2xs">
                        ✓ LULUS (Skor Terbaik: {{ number_format($bestScore, 0) }})
                    </span>
                @elseif(!is_null($bestScore))
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-red-500/20 text-red-200 border border-red-400/50 backdrop-blur-xs">
                        BELUM LULUS (Skor Terbaik: {{ number_format($bestScore, 0) }})
                    </span>
                @else
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-extrabold bg-white/15 text-white border border-white/30 backdrop-blur-xs">
                        Menunggu Penilaian
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 sm:gap-3">
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Metode</span>
                <div class="text-xs font-bold text-white mt-1 uppercase">
                    @if($assignment->submission_method === 'file')
                        Unggah Berkas
                    @elseif($assignment->submission_method === 'text')
                        Teks Editor
                    @else
                        Berkas & Teks
                    @endif
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Batas Attempt</span>
                <div class="text-xs font-bold text-white mt-1">
                    {{ $assignment->max_attempts ? $submissions->count() . ' / ' . $assignment->max_attempts . 'x Attempt' : $submissions->count() . 'x (Tanpa Batas)' }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Passing Grade</span>
                <div class="text-xs font-extrabold text-[#E7C982] mt-1">
                    {{ number_format($assignment->passing_score, 0) }} / {{ number_format($assignment->max_score, 0) }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Waktu Dibuka</span>
                <div class="text-xs font-semibold text-white mt-1">
                    {{ $assignment->available_from ? \Carbon\Carbon::parse($assignment->available_from)->format('d M Y H:i') : 'Langsung' }}
                </div>
            </div>

            <div class="col-span-2 sm:col-span-1 p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Tenggat Waktu</span>
                <div class="text-xs font-bold {{ $isPastDeadline ? 'text-red-300' : 'text-[#E7C982]' }} mt-1">
                    {{ $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('d M Y H:i') : 'Tanpa Batas' }}
                </div>
            </div>
        </div>

        <!-- Deskripsi Soal (Terintegrasi di dalam Top Card) -->
        @if($assignment->description)
            <div class="pt-4 border-t border-white/20 space-y-2">
                <h3 class="text-xs font-bold text-[#E7C982] uppercase tracking-wider flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Petunjuk Pengerjaan</span>
                </h3>
                <div class="text-xs sm:text-sm text-emerald-50 bg-white/10 p-4 rounded-xl border border-white/20 leading-relaxed whitespace-pre-line backdrop-blur-xs">
                    {{ $assignment->description }}
                </div>
            </div>
        @endif

        <!-- Berkas Acuan Pengajar (Dengan opsi Buka di Tab Baru & Unduh) -->
        @if($assignment->attachments && $assignment->attachments->count() > 0)
            <div class="pt-4 border-t border-white/20 space-y-2.5">
                <h3 class="text-xs font-bold text-[#E7C982] uppercase tracking-wider flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    <span>Berkas Acuan / Soal Pengajar</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach($assignment->attachments as $attachment)
                        @if($attachment->media)
                            @php
                                $attUrl = asset('storage/' . $attachment->media->file_path);
                            @endphp
                            <div class="p-3 bg-white/10 rounded-xl border border-white/20 flex items-center justify-between gap-2 backdrop-blur-xs">
                                <div class="flex items-center space-x-2 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-white/15 text-[#E7C982] border border-white/20 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs text-white font-bold truncate">{{ $attachment->media->file_name }}</span>
                                </div>
                                <div class="flex items-center space-x-2 flex-shrink-0">
                                    <a href="{{ $attUrl }}" target="_blank" rel="noopener noreferrer" class="text-[11px] font-bold text-emerald-100 hover:text-white hover:underline">
                                        Buka
                                    </a>
                                    <span class="text-white/30">|</span>
                                    <a href="{{ $attUrl }}" download="{{ $attachment->media->file_name }}" class="text-[11px] font-extrabold text-[#E7C982] hover:underline">
                                        Unduh
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- 2. RIWAYAT SUBMISSION & FORM SUBMISSION (MULTI-ATTEMPT) -->
    @php
        $allSubmissionIds = $submissions->pluck('id')->toArray();
    @endphp

    <div x-data="{ 
            isCreatingNew: false, 
            allIds: {{ json_encode($allSubmissionIds) }},
            openAttempts: {{ json_encode($allSubmissionIds) }},
            toggleAttempt(id) {
                if (this.openAttempts.includes(id)) {
                    this.openAttempts = this.openAttempts.filter(i => i !== id);
                } else {
                    this.openAttempts.push(id);
                }
            },
            toggleAll() {
                if (this.openAttempts.length === this.allIds.length) {
                    this.openAttempts = [];
                } else {
                    this.openAttempts = [...this.allIds];
                }
            }
         }" class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        
        <!-- Ketentuan 2: Heading container utama menggunakan warna bg-[#28745B] -->
        <div class="bg-[#28745B] p-4 sm:px-6 border-b border-slate-300 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-white">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-white">Jawaban & Riwayat Percobaan</h2>
                <p class="text-xs text-emerald-100/85">Nilai tertinggi dari seluruh attempt yang lulus akan dihitung sebagai progres kelulusan Anda.</p>
            </div>

            <div class=" flex flex-wrap items-center gap-2">
                @if($submissions->isNotEmpty())
                    <button type="button" @click="toggleAll()" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white border border-white/30 text-xs font-bold rounded-xl transition flex items-center space-x-1.5 shadow-2xs backdrop-blur-xs">
                        <template x-if="openAttempts.length === allIds.length">
                            <span class="flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                <span>Tutup Semua Attempt</span>
                            </span>
                        </template>
                        <template x-if="openAttempts.length !== allIds.length">
                            <span class="flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                <span>Buka Semua Attempt</span>
                            </span>
                        </template>
                    </button>
                @endif

                <!-- Ketentuan 4: Tombol kirim attempt baru tidak bisa ditekan ketika status enrollment completed atau past deadline -->
                @if($isEnrollmentCompleted)
                    <button disabled class="px-4 py-2 bg-white/15 text-emerald-100/70 border border-white/20 text-xs font-bold rounded-xl cursor-not-allowed flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Pelatihan Selesai</span>
                    </button>
                @elseif($isPastDeadline)
                    <button disabled class="px-4 py-2 bg-white/15 text-emerald-100/70 border border-white/20 text-xs font-bold rounded-xl cursor-not-allowed flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Tenggat Waktu Berakhir</span>
                    </button>
                @elseif($canSubmitNewAttempt && (!$latestSubmission || $latestSubmission->status === 'graded'))
                    <button type="button" @click="isCreatingNew = true" class="px-4 py-2 bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] text-xs font-bold rounded-xl shadow-2xs transition flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Kirim Attempt Baru</span>
                    </button>
                @endif
            </div>
        </div>

        <div class="p-5 sm:p-8 space-y-6">

            <!-- FORM CREATING NEW ATTEMPT (HANYA MUNCUL JIKA BELUM LOCKED) -->
            @if(!$isSubmissionLocked)
                <template x-if="isCreatingNew || {{ $submissions->isEmpty() ? 'true' : 'false' }}">
                    <div class="bg-[#F7F5EE]/60 p-5 sm:p-6 rounded-2xl border border-slate-300 space-y-5 shadow-2xs">
                        <div class="flex items-center justify-between border-b border-slate-300 pb-3">
                            <h3 class="text-sm font-bold text-[#123F35]">Form Pengumpulan Attempt Baru</h3>
                            @if($submissions->isNotEmpty())
                                <button type="button" @click="isCreatingNew = false" class="text-xs font-bold text-slate-500 hover:text-[#123F35]">
                                    Batal
                                </button>
                            @endif
                        </div>

                        <form action="{{ route('student.submissions.store', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            <!-- Input Teks Jawaban -->
                            @if(in_array($assignment->submission_method, ['text', 'both']))
                                <div>
                                    <label class="block text-xs font-bold text-[#123F35] mb-1.5">
                                        Teks Jawaban 
                                        @if($assignment->submission_method === 'text')
                                            <span class="text-red-500">*</span>
                                        @elseif($assignment->submission_method === 'both')
                                            <span class="text-slate-500 font-normal">(Isi teks atau unggah berkas di bawah)</span>
                                        @endif
                                    </label>
                                    <textarea name="submission_text" rows="5" placeholder="Ketikkan jawaban tugas Anda di sini..."
                                              class="w-full p-3.5 bg-white border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#28745B] @error('submission_text') border-red-500 @enderror">{{ old('submission_text') }}</textarea>
                                    @error('submission_text')
                                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <!-- Upload Berkas Baru -->
                            @if(in_array($assignment->submission_method, ['file', 'both']))
                                <div x-data="attachmentUploader()" class="space-y-3">
                                    <label class="block text-xs font-bold text-[#123F35]">
                                        Unggah Berkas Lampiran Jawaban
                                        @if($assignment->submission_method === 'file')
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>

                                    <div class="border border-dashed border-slate-400 hover:border-[#28745B] rounded-2xl p-5 text-center transition bg-white relative cursor-pointer">
                                        <input type="file" x-ref="fileInput" @change="addFiles($event)" multiple
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <div class="space-y-1.5 pointer-events-none">
                                            <div class="w-9 h-9 bg-[#28745B]/10 text-[#28745B] border border-[#28745B]/20 rounded-full flex items-center justify-center mx-auto">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </div>
                                            <div class="text-xs text-[#123F35] font-bold">+ Pilih Berkas Jawaban</div>
                                            <p class="text-[10px] text-slate-500">PDF, DOCX, PPTX, ZIP, maks 10MB per file</p>
                                        </div>
                                    </div>

                                    <div x-ref="hiddenInputsContainer" class="hidden"></div>

                                    <!-- Preview Berkas Terpilih -->
                                    <template x-if="fileList.length > 0">
                                        <div class="space-y-2 pt-2 border-t border-slate-300">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-[#123F35] uppercase">Berkas Terpilih (<span x-text="fileList.length"></span>)</span>
                                                <button type="button" @click="removeAllFiles()" class="text-[11px] font-bold text-red-600 hover:underline">Hapus Semua</button>
                                            </div>
                                            <div class="space-y-1.5">
                                                <template x-for="(f, index) in fileList" :key="index">
                                                    <div class="p-3 bg-white rounded-xl border border-slate-300 flex items-center justify-between">
                                                        <span class="text-xs font-bold text-[#123F35] truncate" x-text="f.name"></span>
                                                        <button type="button" @click="removeFile(index)" class="p-1 text-slate-400 hover:text-red-600">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            @endif

                            <div class="pt-4 border-t border-slate-300 flex items-center justify-end space-x-2.5">
                                @if($submissions->isNotEmpty())
                                    <button type="button" @click="isCreatingNew = false" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                        Batal
                                    </button>
                                @endif
                                <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-2xs transition">
                                    Kumpulkan Tugas Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </template>
            @endif

            <!-- LIST DAFTAR ATTEMPT PENGERJAAN -->
            @if($submissions->isNotEmpty())
                <div class="space-y-4">
                    @foreach($submissions as $sub)
                        <div x-data="{ isEditingThis: false }" class="rounded-2xl border border-slate-300 overflow-hidden shadow-2xs {{ $sub->status === 'graded' ? ($sub->score >= $assignment->passing_score ? 'bg-emerald-50/40' : 'bg-red-50/40') : 'bg-[#F7F5EE]/40' }}">
                            
                            <!-- Ketentuan 3: Header Attempt menyatu warna dengan body & garis bawah sejajar dengan garis abu di bawahnya -->
                            <div class="mx-4 sm:mx-5 py-4 border-b border-slate-300 flex items-center justify-between gap-2">
                                <div class="flex items-center space-x-2.5">
                                    <button type="button" @click="toggleAttempt({{ $sub->id }})" class="w-7 h-7 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 flex items-center justify-center transition text-[#123F35] shadow-2xs">
                                        <svg class="w-4 h-4 transform transition-transform" :class="openAttempts.includes({{ $sub->id }}) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <span class="px-2.5 py-1 bg-[#123F35] text-white rounded-lg font-bold text-xs">
                                        Attempt #{{ $sub->attempt_number }}
                                    </span>
                                    <span class="text-xs text-slate-600 hidden sm:inline-block">
                                        Dikumpulkan: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') }} WIB</strong>
                                    </span>
                                </div>

                                <div class="flex items-center space-x-2">
                                    @if($sub->status === 'graded')
                                        <span class="px-3 py-1 rounded-lg border text-xs font-extrabold {{ $sub->score >= $assignment->passing_score ? 'bg-[#28745B]/15 text-[#28745B] border-[#28745B]/40' : 'bg-red-100 text-red-700 border-red-300' }}">
                                            Skor: {{ number_format($sub->score, 2) }} / {{ number_format($assignment->max_score, 0) }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-lg border border-[#E7C982] bg-[#E7C982]/40 text-[#123F35] text-xs font-extrabold">
                                            Menunggu Penilaian
                                        </span>
                                        
                                        <!-- Edit & Hapus Attempt HANYA jika belum dinilai DAN belum locked -->
                                        @if(!$isSubmissionLocked)
                                            <button type="button" @click="isEditingThis = !isEditingThis; if(!openAttempts.includes({{ $sub->id }})) openAttempts.push({{ $sub->id }})" class="p-1.5 bg-white border border-slate-300 text-amber-600 hover:bg-amber-50 rounded-lg transition shadow-2xs" title="Edit Attempt Ini">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <form action="{{ route('student.submissions.destroy', [$assignment->id, $sub->id]) }}" method="POST" onsubmit="return confirm('Batalkan & hapus attempt submission ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-white border border-slate-300 text-red-600 hover:bg-red-50 rounded-lg transition shadow-2xs" title="Batalkan Attempt Ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- BODY CONTAINER UNTUK TIAP ATTEMPT -->
                            <div x-show="openAttempts.includes({{ $sub->id }})" x-collapse class="p-4 sm:p-5 space-y-4">

                                <!-- VIEW MODE -->
                                <div x-show="!isEditingThis" class="space-y-4">
                                    <!-- Teks Jawaban Siswa -->
                                    @if($sub->submission_text)
                                        <div class="space-y-1.5">
                                            <span class="text-[11px] font-bold text-[#123F35] uppercase tracking-wider">Teks Jawaban:</span>
                                            <div class="p-3.5 bg-white rounded-xl border border-slate-300 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                                                {{ $sub->submission_text }}
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Lampiran Berkas Jawaban Siswa (Dengan Buka di Tab Baru & Unduh) -->
                                    @if($sub->attachments && $sub->attachments->count() > 0)
                                        <div class="space-y-1.5">
                                            <span class="text-[11px] font-bold text-[#123F35] uppercase tracking-wider">Berkas Jawaban Terlampir:</span>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                                @foreach($sub->attachments as $att)
                                                    @if($att->media)
                                                        @php
                                                            $subFileUrl = asset('storage/' . $att->media->file_path);
                                                        @endphp
                                                        <div class="p-3 bg-white rounded-xl border border-slate-300 flex items-center justify-between gap-2 text-xs shadow-2xs">
                                                            <span class="font-bold text-[#123F35] truncate">{{ $att->media->file_name }}</span>
                                                            <div class="flex items-center space-x-2 flex-shrink-0">
                                                                <a href="{{ $subFileUrl }}" target="_blank" rel="noopener noreferrer" class="text-[11px] font-bold text-slate-600 hover:text-[#123F35] hover:underline">
                                                                    Buka
                                                                </a>
                                                                <span class="text-slate-300">|</span>
                                                                <a href="{{ $subFileUrl }}" download="{{ $att->media->file_name }}" class="text-[11px] font-extrabold text-[#28745B] hover:text-[#123F35] hover:underline">
                                                                    Unduh
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Feedback, Nama Penilai & Lampiran Berkas Pengajar -->
                                    @if($sub->status === 'graded')
                                        <div class="pt-3 border-t border-slate-300 space-y-2.5">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                                <span class="text-[11px] font-bold text-[#123F35] uppercase tracking-wider">Catatan & Feedback Pengajar:</span>
                                                
                                                <!-- Menampilkan Nama Penilai (Grader) -->
                                                <div class="text-[11px] text-slate-600 flex items-center gap-1 font-medium">
                                                    <svg class="w-3.5 h-3.5 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    <span>Dinilai oleh:</span>
                                                    <strong class="text-[#123F35] font-bold">{{ $sub->grader->name ?? 'Instruktur' }}</strong>
                                                </div>
                                            </div>

                                            <p class="text-xs text-slate-700 italic bg-white p-3.5 rounded-xl border border-slate-300">
                                                {{ $sub->feedback ? '"' . $sub->feedback . '"' : 'Tidak ada catatan tertulis.' }}
                                            </p>

                                            @if($sub->feedbackAttachments && $sub->feedbackAttachments->count() > 0)
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                                    @foreach($sub->feedbackAttachments as $fAtt)
                                                        @if($fAtt->media)
                                                            @php
                                                                $fbFileUrl = asset('storage/' . $fAtt->media->file_path);
                                                            @endphp
                                                            <div class="p-3 bg-[#E7C982]/20 rounded-xl border border-[#E7C982] flex items-center justify-between gap-2 text-xs">
                                                                <span class="font-bold text-[#123F35] truncate">{{ $fAtt->media->file_name }}</span>
                                                                <div class="flex items-center space-x-2 flex-shrink-0">
                                                                    <a href="{{ $fbFileUrl }}" target="_blank" rel="noopener noreferrer" class="text-[11px] font-bold text-[#123F35] hover:underline">
                                                                        Buka
                                                                    </a>
                                                                    <span class="text-[#123F35]/30">|</span>
                                                                    <a href="{{ $fbFileUrl }}" download="{{ $fAtt->media->file_name }}" class="text-[11px] font-extrabold text-[#123F35] hover:underline">
                                                                        Unduh
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- EDIT MODE (HANYA DITAMPILKAN JIKA BELUM LOCKED) -->
                                @if(!$isSubmissionLocked)
                                    <div x-show="isEditingThis" class="p-4 sm:p-5 bg-white rounded-xl border border-slate-300 space-y-4 shadow-2xs">
                                        <div class="flex items-center justify-between border-b border-slate-300 pb-2.5">
                                            <h4 class="text-xs font-bold text-[#123F35]">Edit Jawaban Attempt #{{ $sub->attempt_number }}</h4>
                                            <button type="button" @click="isEditingThis = false" class="text-[11px] font-bold text-slate-500 hover:text-[#123F35]">
                                                Batal Edit
                                            </button>
                                        </div>

                                        <form action="{{ route('student.submissions.update', [$assignment->id, $sub->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                            @csrf
                                            @method('PUT')

                                            <!-- Edit Teks Jawaban -->
                                            @if(in_array($assignment->submission_method, ['text', 'both']))
                                                <div>
                                                    <label class="block text-xs font-bold text-[#123F35] mb-1">Perbarui Teks Jawaban</label>
                                                    <textarea name="submission_text" rows="4" class="w-full p-3 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">{{ old('submission_text', $sub->submission_text) }}</textarea>
                                                </div>
                                            @endif

                                            <!-- Berkas Terpasang Saat Ini -->
                                            @if($sub->attachments && $sub->attachments->count() > 0)
                                                <div class="space-y-1.5 pt-1">
                                                    <span class="text-[11px] font-bold text-[#123F35] uppercase">Berkas Terpasang (Centang untuk menghapus):</span>
                                                    <div class="space-y-1.5">
                                                        @foreach($sub->attachments as $att)
                                                            @if($att->media)
                                                                <div class="p-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl flex items-center justify-between text-xs">
                                                                    <span class="truncate font-semibold text-slate-700">{{ $att->media->file_name }}</span>
                                                                    <label class="text-red-600 font-bold text-[11px] cursor-pointer hover:underline flex items-center space-x-1.5 flex-shrink-0">
                                                                        <input type="checkbox" name="delete_attachments[]" value="{{ $att->id }}" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                                                        <span>Hapus</span>
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Unggah Tambahan Berkas Baru -->
                                            @if(in_array($assignment->submission_method, ['file', 'both']))
                                                <div x-data="attachmentUploader()" class="space-y-2 pt-2 border-t border-slate-300">
                                                    <label class="block text-xs font-bold text-[#123F35]">Tambah Berkas Lampiran Baru</label>
                                                    <div class="border border-dashed border-slate-400 hover:border-[#28745B] rounded-xl p-4 text-center transition bg-[#F7F5EE]/40 relative cursor-pointer">
                                                        <input type="file" x-ref="fileInput" @change="addFiles($event)" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                                        <div class="text-xs text-[#123F35] font-bold">+ Pilih Berkas Baru</div>
                                                    </div>

                                                    <div x-ref="hiddenInputsContainer" class="hidden"></div>

                                                    <template x-if="fileList.length > 0">
                                                        <div class="space-y-1.5">
                                                            <span class="text-[11px] font-bold text-[#123F35]">Berkas Baru Ditambahkan (<span x-text="fileList.length"></span>):</span>
                                                            <template x-for="(f, index) in fileList" :key="index">
                                                                <div class="p-2.5 bg-[#E7C982]/20 rounded-xl border border-[#E7C982] flex items-center justify-between text-xs">
                                                                    <span class="truncate font-bold text-[#123F35]" x-text="f.name"></span>
                                                                    <button type="button" @click="removeFile(index)" class="text-red-600 hover:text-red-700">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                    </button>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            @endif

                                            <div class="pt-3 border-t border-slate-300 flex justify-end space-x-2">
                                                <button type="button" @click="isEditingThis = false" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl transition">
                                                    Batal
                                                </button>
                                                <button type="submit" class="px-4 py-2 bg-[#28745B] hover:bg-[#123F35] text-white text-xs font-bold rounded-xl transition shadow-2xs">
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

</div>

<script>
    function attachmentUploader() {
        return {
            fileList: [],
            addFiles(event) {
                const selectedFiles = Array.from(event.target.files);
                selectedFiles.forEach(file => {
                    const exists = this.fileList.some(f => f.name === file.name && f.size === file.size);
                    if (!exists) {
                        this.fileList.push(file);
                    }
                });
                this.syncFormInputs();
                this.$refs.fileInput.value = '';
            },
            removeFile(index) {
                this.fileList.splice(index, 1);
                this.syncFormInputs();
            },
            removeAllFiles() {
                this.fileList = [];
                this.syncFormInputs();
            },
            syncFormInputs() {
                const dt = new DataTransfer();
                this.fileList.forEach(file => dt.items.add(file));

                const container = this.$refs.hiddenInputsContainer;
                container.innerHTML = '';

                const input = document.createElement('input');
                input.type = 'file';
                input.name = 'attachments[]';
                input.multiple = true;
                input.files = dt.files;
                container.appendChild(input);
            }
        }
    }
</script>
@endsection