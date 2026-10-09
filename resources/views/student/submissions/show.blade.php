@extends('layouts.app')

@section('title', $assignment->title . ' - Ekosistem Halal')
@section('header_title', 'Pengumpulan Tugas Submission')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.courses.show', $assignment->course_id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-emerald-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
            Pertemuan {{ $assignment->meeting->meeting_number }}: {{ $assignment->meeting->title }}
        </span>
    </div>

    <!-- Alert Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs font-semibold flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 1. INFORMASI DETAIL ASSIGNMENT SUBMISSION -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-slate-100 text-slate-700 uppercase">
                    Tugas Upload / Submission
                </span>
                <h1 class="text-xl font-bold text-slate-800 mt-1">{{ $assignment->title }}</h1>
            </div>

            <!-- Status Kelulusan Berdasarkan Best Score -->
            <div>
                @if($submissions->isEmpty())
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Belum Mengumpulkan</span>
                @elseif(!is_null($bestScore) && $bestScore >= $assignment->passing_score)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">LULUS (Skor Terbaik: {{ number_format($bestScore, 0) }})</span>
                @elseif(!is_null($bestScore))
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">BELUM LULUS (Skor Terbaik: {{ number_format($bestScore, 0) }})</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Menunggu Penilaian</span>
                @endif
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Metode Pengumpulan</span>
                <div class="text-xs font-bold text-slate-800 mt-1 uppercase">
                    @if($assignment->submission_method === 'file')
                        Unggah Berkas
                    @elseif($assignment->submission_method === 'text')
                        Teks Editor
                    @else
                        Berkas & Teks
                    @endif
                </div>
            </div>

            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Batas Attempt</span>
                <div class="text-xs font-bold text-slate-800 mt-1">
                    {{ $assignment->max_attempts ? $submissions->count() . ' / ' . $assignment->max_attempts . 'x Attempt' : $submissions->count() . 'x Attempt (Tanpa Batas)' }}
                </div>
            </div>

            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Passing Grade</span>
                <div class="text-xs font-bold text-emerald-700 mt-1">{{ number_format($assignment->passing_score, 0) }} / {{ number_format($assignment->max_score, 0) }}</div>
            </div>

            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Waktu Dibuka</span>
                <div class="text-xs font-semibold text-slate-700 mt-1">
                    {{ $assignment->available_from ? \Carbon\Carbon::parse($assignment->available_from)->format('d M Y H:i') : 'Langsung' }}
                </div>
            </div>

            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Tenggat Waktu</span>
                <div class="text-xs font-semibold text-slate-700 mt-1">
                    {{ $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('d M Y H:i') : 'Tanpa Batas' }}
                </div>
            </div>
        </div>

        <!-- Deskripsi Soal -->
        @if($assignment->description)
            <div class="space-y-1">
                <h3 class="text-xs font-bold text-slate-700 uppercase">Petunjuk Pengerjaan:</h3>
                <div class="text-xs text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100 leading-relaxed whitespace-pre-line">
                    {{ $assignment->description }}
                </div>
            </div>
        @endif

        <!-- Berkas Acuan Pengajar -->
        @if($assignment->attachments && $assignment->attachments->count() > 0)
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-700 uppercase">Berkas Acuan / Soal Pengajar:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($assignment->attachments as $attachment)
                        @if($attachment->media)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                                <div class="flex items-center space-x-2 overflow-hidden pr-2">
                                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-xs text-slate-800 font-semibold truncate">{{ $attachment->media->file_name }}</span>
                                </div>
                                <a href="{{ asset('storage/' . $attachment->media->file_path) }}" download class="text-[11px] font-bold text-emerald-600 hover:underline flex-shrink-0">Unduh</a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- 2. RIWAYAT SUBMISSION & FORM SUBMISSION (MULTI-ATTEMPT) -->
    <div x-data="{ 
            isCreatingNew: false, 
            openAttempts: [{{ $submissions->pluck('id')->implode(',') }}],
            toggleAttempt(id) {
                if (this.openAttempts.includes(id)) {
                    this.openAttempts = this.openAttempts.filter(i => i !== id);
                } else {
                    this.openAttempts.push(id);
                }
            },
            expandAll() {
                this.openAttempts = [{{ $submissions->pluck('id')->implode(',') }}];
            },
            collapseAll() {
                this.openAttempts = [];
            }
         }" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-4 gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Jawaban & Riwayat Percobaan</h2>
                <p class="text-xs text-slate-500">Nilai tertinggi dari seluruh attempt yang lulus akan dihitung sebagai progress kelulusan Anda.</p>
            </div>

            <div class="flex items-center space-x-2">
                @if($submissions->isNotEmpty())
                    <div class="flex items-center space-x-1 border-r border-slate-200 pr-2">
                        <button type="button" @click="expandAll()" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold rounded-lg transition">
                            Buka Semua
                        </button>
                        <button type="button" @click="collapseAll()" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold rounded-lg transition">
                            Tutup Semua
                        </button>
                    </div>
                @endif

                @if($canSubmitNewAttempt && (!$latestSubmission || $latestSubmission->status === 'graded'))
                    <button type="button" @click="isCreatingNew = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Kirim Attempt Baru</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- FORM CREATING NEW ATTEMPT (JIKA BELUM ADA ATTEMPT ATAU MAU BUAT BARU) -->
        <template x-if="isCreatingNew || {{ $submissions->isEmpty() ? 'true' : 'false' }}">
            <div class="bg-slate-50 p-6 rounded-2xl border border-emerald-200 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Form Pengumpulan Attempt Baru</h3>
                    @if($submissions->isNotEmpty())
                        <button type="button" @click="isCreatingNew = false" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Batal
                        </button>
                    @endif
                </div>

                <form action="{{ route('student.submissions.store', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Input Teks Jawaban -->
                    @if(in_array($assignment->submission_method, ['text', 'both']))
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Teks Jawaban 
                                @if($assignment->submission_method === 'text')
                                    <span class="text-red-500">*</span>
                                @elseif($assignment->submission_method === 'both')
                                    <span class="text-slate-400 font-normal">(Isi teks atau unggah berkas di bawah)</span>
                                @endif
                            </label>
                            <textarea name="submission_text" rows="5" placeholder="Ketikkan jawaban tugas Anda di sini..."
                                      class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('submission_text') border-red-500 @enderror">{{ old('submission_text') }}</textarea>
                            @error('submission_text')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Upload Berkas Baru -->
                    @if(in_array($assignment->submission_method, ['file', 'both']))
                        <div x-data="attachmentUploader()" class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-700">
                                Unggah Berkas Lampiran Jawaban
                                @if($assignment->submission_method === 'file')
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>

                            <div class="border-2 border-dashed border-slate-200 hover:border-emerald-400 rounded-2xl p-5 text-center transition bg-white relative cursor-pointer">
                                <input type="file" x-ref="fileInput" @change="addFiles($event)" multiple
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <div class="space-y-1.5 pointer-events-none">
                                    <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                    <div class="text-xs text-slate-700 font-semibold">+ Pilih Berkas Jawaban</div>
                                    <p class="text-[10px] text-slate-400">PDF, DOCX, PPTX, ZIP, maks 10MB per file</p>
                                </div>
                            </div>

                            <div x-ref="hiddenInputsContainer" class="hidden"></div>

                            <!-- Preview Berkas Terpilih -->
                            <template x-if="fileList.length > 0">
                                <div class="space-y-2 pt-2 border-t border-slate-200">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700 uppercase">Berkas Terpilih (<span x-text="fileList.length"></span>)</span>
                                        <button type="button" @click="removeAllFiles()" class="text-[11px] font-semibold text-red-500 hover:underline">Hapus Semua</button>
                                    </div>
                                    <div class="space-y-1.5">
                                        <template x-for="(f, index) in fileList" :key="index">
                                            <div class="p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                                                <span class="text-xs font-semibold text-slate-800 truncate" x-text="f.name"></span>
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

                    <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-3">
                        @if($submissions->isNotEmpty())
                            <button type="button" @click="isCreatingNew = false" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200 transition">
                                Batal
                            </button>
                        @endif
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition">
                            Kumpulkan Tugas Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </template>

        <!-- LIST DAFTAR ATTEMPT PENGERJAAN -->
        @if($submissions->isNotEmpty())
            <div class="space-y-4">
                @foreach($submissions as $sub)
                    <div x-data="{ isEditingThis: false }" class="p-5 rounded-2xl border transition space-y-4 {{ $sub->status === 'graded' ? ($sub->score >= $assignment->passing_score ? 'bg-emerald-50/40 border-emerald-200' : 'bg-red-50/40 border-red-200') : 'bg-slate-50/80 border-slate-200' }}">
                        
                        <!-- Header Attempt Card -->
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                            <div class="flex items-center space-x-3">
                                <button type="button" @click="toggleAttempt({{ $sub->id }})" class="p-1 hover:bg-slate-200/60 rounded-md transition text-slate-500">
                                    <svg class="w-4 h-4 transform transition-transform" :class="openAttempts.includes({{ $sub->id }}) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <span class="px-2.5 py-1 bg-slate-800 text-white rounded-lg font-bold text-xs">
                                    Attempt #{{ $sub->attempt_number }}
                                </span>
                                <span class="text-xs text-slate-500 hidden sm:inline-block">
                                    Dikumpulkan: {{ \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') }} WIB
                                </span>
                            </div>

                            <div class="flex items-center space-x-2">
                                @if($sub->status === 'graded')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $sub->score >= $assignment->passing_score ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        Skor: {{ number_format($sub->score, 2) }} / {{ number_format($assignment->max_score, 0) }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Menunggu Penilaian</span>
                                    
                                    <!-- Edit & Hapus Attempt jika belum dinilai -->
                                    <button type="button" @click="isEditingThis = !isEditingThis; if(!openAttempts.includes({{ $sub->id }})) openAttempts.push({{ $sub->id }})" class="p-1.5 bg-amber-100 text-amber-700 hover:bg-amber-600 hover:text-white rounded-lg transition" title="Edit Attempt Ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    <form action="{{ route('student.submissions.destroy', [$assignment->id, $sub->id]) }}" method="POST" onsubmit="return confirm('Batalkan & hapus attempt submission ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition" title="Batalkan Attempt Ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- BODY CONTAINER UNTUK TIAP ATTEMPT (BISA EXPAND/COLLAPSE) -->
                        <div x-show="openAttempts.includes({{ $sub->id }})" x-collapse class="space-y-4 pt-1">

                            <!-- VIEW MODE (TAMPILAN JAWABAN BIASA) -->
                            <div x-show="!isEditingThis" class="space-y-4">
                                <!-- Teks Jawaban Siswa -->
                                @if($sub->submission_text)
                                    <div class="space-y-1">
                                        <span class="text-[11px] font-bold text-slate-600 uppercase">Teks Jawaban:</span>
                                        <div class="p-3 bg-white rounded-xl border border-slate-200 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                                            {{ $sub->submission_text }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Lampiran Berkas Jawaban Siswa -->
                                @if($sub->attachments && $sub->attachments->count() > 0)
                                    <div class="space-y-1.5">
                                        <span class="text-[11px] font-bold text-slate-600 uppercase">Berkas Jawaban Terlampir:</span>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($sub->attachments as $att)
                                                @if($att->media)
                                                    <div class="p-2.5 bg-white rounded-xl border border-slate-200 flex items-center justify-between text-xs">
                                                        <span class="font-semibold text-slate-800 truncate">{{ $att->media->file_name }}</span>
                                                        <a href="{{ asset('storage/' . $att->media->file_path) }}" download class="text-[11px] font-bold text-emerald-600 hover:underline">Unduh</a>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Feedback & Lampiran Berkas Pengajar -->
                                @if($sub->status === 'graded')
                                    <div class="pt-3 border-t border-slate-200/80 space-y-2">
                                        <span class="text-[11px] font-bold text-slate-700 uppercase">Catatan & Lampiran Feedback Pengajar:</span>
                                        <p class="text-xs text-slate-700 italic bg-white p-3 rounded-xl border border-slate-200">
                                            {{ $sub->feedback ? '"' . $sub->feedback . '"' : 'Tidak ada catatan tertulis.' }}
                                        </p>

                                        @if($sub->feedbackAttachments && $sub->feedbackAttachments->count() > 0)
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                                @foreach($sub->feedbackAttachments as $fAtt)
                                                    @if($fAtt->media)
                                                        <div class="p-2.5 bg-amber-50 rounded-xl border border-amber-200 flex items-center justify-between text-xs">
                                                            <span class="font-semibold text-slate-800 truncate">{{ $fAtt->media->file_name }}</span>
                                                            <a href="{{ asset('storage/' . $fAtt->media->file_path) }}" download class="text-[11px] font-bold text-amber-700 hover:underline">Unduh Feedback</a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- EDIT MODE (FORM EDIT YANG TERDAPAT DI DALAM CONTAINER ATTEMPT) -->
                            <div x-show="isEditingThis" class="p-4 bg-white rounded-xl border border-amber-300 space-y-4">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <h4 class="text-xs font-bold text-amber-800">Edit Jawaban Attempt #{{ $sub->attempt_number }}</h4>
                                    <button type="button" @click="isEditingThis = false" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800">
                                        Batal Edit
                                    </button>
                                </div>

                                <form action="{{ route('student.submissions.update', [$assignment->id, $sub->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <!-- Edit Teks Jawaban -->
                                    @if(in_array($assignment->submission_method, ['text', 'both']))
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Perbarui Teks Jawaban</label>
                                            <textarea name="submission_text" rows="4" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('submission_text', $sub->submission_text) }}</textarea>
                                        </div>
                                    @endif

                                    <!-- Berkas Terpasang Saat Ini -->
                                    @if($sub->attachments && $sub->attachments->count() > 0)
                                        <div class="space-y-1.5 pt-1">
                                            <span class="text-[11px] font-bold text-slate-600 uppercase">Berkas Terpasang (Centang untuk menghapus):</span>
                                            <div class="space-y-1">
                                                @foreach($sub->attachments as $att)
                                                    @if($att->media)
                                                        <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between text-xs">
                                                            <span class="truncate font-medium text-slate-700">{{ $att->media->file_name }}</span>
                                                            <label class="text-red-600 font-semibold text-[11px] cursor-pointer hover:underline flex items-center space-x-1 flex-shrink-0">
                                                                <input type="checkbox" name="delete_attachments[]" value="{{ $att->id }}" class="rounded text-red-600 focus:ring-red-500">
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
                                        <div x-data="attachmentUploader()" class="space-y-2 pt-1 border-t border-slate-100">
                                            <label class="block text-xs font-semibold text-slate-700">Tambah Berkas Lampiran Baru</label>
                                            <div class="border-2 border-dashed border-slate-200 hover:border-amber-400 rounded-xl p-4 text-center transition bg-slate-50 relative cursor-pointer">
                                                <input type="file" x-ref="fileInput" @change="addFiles($event)" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                                <div class="text-xs text-slate-700 font-semibold">+ Pilih Berkas Baru</div>
                                            </div>

                                            <div x-ref="hiddenInputsContainer" class="hidden"></div>

                                            <template x-if="fileList.length > 0">
                                                <div class="space-y-1.5">
                                                    <span class="text-[11px] font-bold text-slate-600">Berkas Baru Ditambahkan (<span x-text="fileList.length"></span>):</span>
                                                    <template x-for="(f, index) in fileList" :key="index">
                                                        <div class="p-2 bg-amber-50 rounded-lg border border-amber-200 flex items-center justify-between text-xs">
                                                            <span class="truncate font-medium text-slate-800" x-text="f.name"></span>
                                                            <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    @endif

                                    <div class="pt-2 flex justify-end space-x-2">
                                        <button type="button" @click="isEditingThis = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                                            Batal
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>

                    </div>
                @endforeach
            </div>
        @endif

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