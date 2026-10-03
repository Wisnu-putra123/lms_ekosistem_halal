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

            <!-- Status Pengumpulan Student -->
            <div>
                @if(!$submission)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Belum Mengumpulkan</span>
                @elseif($submission->status === 'graded')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Sudah Dinilai</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Sudah Dikumpulkan</span>
                @endif
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
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

    <!-- 2. STATUS PENILAIAN & FEEDBACK TEACHER (JIKA SUDAH DINILAI) -->
    @if($submission && $submission->status === 'graded')
        <div class="p-6 rounded-2xl border shadow-sm space-y-4 {{ $submission->score >= $assignment->passing_score ? 'bg-emerald-50/70 border-emerald-200' : 'bg-red-50/70 border-red-200' }}">
            <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 {{ $submission->score >= $assignment->passing_score ? 'text-emerald-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-sm font-bold text-slate-800">Hasil Penilaian Pengajar</h3>
                </div>

                @if($submission->score >= $assignment->passing_score)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-sm">LULUS</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-600 text-white shadow-sm">BELUM LULUS</span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="text-[11px] font-semibold text-slate-500 uppercase">Nilai Akhir:</span>
                    <div class="text-2xl font-black {{ $submission->score >= $assignment->passing_score ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ number_format($submission->score, 2) }} <span class="text-xs text-slate-400 font-normal">/ {{ number_format($assignment->max_score, 0) }}</span>
                    </div>
                </div>

                <div>
                    <span class="text-[11px] font-semibold text-slate-500 uppercase">Catatan / Feedback Pengajar:</span>
                    <p class="text-xs text-slate-700 mt-1 italic bg-white/80 p-3 rounded-xl border border-slate-200/60">
                        {{ $submission->feedback ? '"' . $submission->feedback . '"' : 'Tidak ada catatan feedback tertulis.' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- 3. FORM SUBMISSION / EDIT / RESUBMIT -->
    <div x-data="{ isEditing: {{ $submission ? 'false' : 'true' }} }" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        
        <!-- Mode Tampilan (Sudah Dikumpulkan) -->
        <template x-if="!isEditing">
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Jawaban Anda</h2>
                        <p class="text-xs text-slate-400">
                            Dikumpulkan pada {{ $submission?->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') : '-' }} WIB
                        </p>
                    </div>

                    @if($submission)
                        <div class="flex items-center space-x-2">
                            <button type="button" @click="isEditing = true" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition">
                                Edit Jawaban
                            </button>

                            <form action="{{ route('student.submissions.destroy', [$assignment->id, $submission->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengumpulan tugas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3.5 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-xl transition">
                                    Batalkan Pengumpulan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                @if($submission)
                    <!-- Teks Jawaban Terkumpul -->
                    @if(in_array($assignment->submission_method, ['text', 'both']) && $submission->submission_text)
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-700 uppercase">Teks Jawaban:</span>
                            <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                                {{ $submission->submission_text }}
                            </div>
                        </div>
                    @endif

                    <!-- Lampiran Berkas Terkumpul -->
                    @if(in_array($assignment->submission_method, ['file', 'both']) && $submission->attachments && $submission->attachments->count() > 0)
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 uppercase">Berkas Jawaban Terlampir:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($submission->attachments as $att)
                                    @if($att->media)
                                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                                            <div class="flex items-center space-x-2 overflow-hidden pr-2">
                                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-xs font-semibold text-slate-800 truncate">{{ $att->media->file_name }}</span>
                                            </div>
                                            <a href="{{ asset('storage/' . $att->media->file_path) }}" download class="text-[11px] font-bold text-emerald-600 hover:underline flex-shrink-0">Unduh</a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800">
                        Anda belum mengirimkan jawaban untuk tugas ini. Silakan klik tombol atau gunakan formulir di bawah.
                    </div>
                @endif
            </div>
        </template>

        <!-- Mode Form Input (Store / Update) -->
        <template x-if="isEditing">
            <div>
                <div class="border-b border-slate-100 pb-4 mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">{{ $submission ? 'Edit Pengumpulan Tugas' : 'Form Pengumpulan Tugas' }}</h2>
                        <p class="text-xs text-slate-500">Isi formulir pengumpulan sesuai petunjuk yang diberikan.</p>
                    </div>
                    @if($submission)
                        <button type="button" @click="isEditing = false" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Batal Edit
                        </button>
                    @endif
                </div>

                <form action="{{ $submission ? route('student.submissions.update', [$assignment->id, $submission->id]) : route('student.submissions.store', $assignment->id) }}" 
                      method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @if($submission)
                        @method('PUT')
                    @endif

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
                            <textarea name="submission_text" rows="6" placeholder="Ketikkan jawaban tugas Anda di sini..."
                                      class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('submission_text') border-red-500 @enderror">{{ old('submission_text', $submission ? $submission->submission_text : '') }}</textarea>
                            @error('submission_text')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Hapus Berkas Lama -->
                    @if($submission && $submission->attachments && $submission->attachments->count() > 0 && in_array($assignment->submission_method, ['file', 'both']))
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-slate-700">Lampiran Berkas Saat Ini:</label>
                            <div class="space-y-1.5">
                                @foreach($submission->attachments as $att)
                                    @if($att->media)
                                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                                            <span class="text-xs text-slate-700 font-medium truncate">{{ $att->media->file_name }}</span>
                                            <label class="inline-flex items-center space-x-1.5 text-xs text-red-600 cursor-pointer font-semibold">
                                                <input type="checkbox" name="delete_attachments[]" value="{{ $att->id }}" class="rounded text-red-600 focus:ring-red-500">
                                                <span>Hapus Berkas</span>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Upload Berkas Baru -->
                    @if(in_array($assignment->submission_method, ['file', 'both']))
                        <div x-data="attachmentUploader()" class="space-y-3">
                            <label class="block text-xs font-semibold text-slate-700">
                                {{ $submission ? 'Tambah Berkas Lampiran Baru (Opsional)' : 'Unggah Berkas Lampiran Jawaban' }}
                                @if(!$submission && $assignment->submission_method === 'file')
                                    <span class="text-red-500">*</span>
                                @elseif(!$submission && $assignment->submission_method === 'both')
                                    <span class="text-slate-400 font-normal">(Unggah berkas atau isi teks di atas)</span>
                                @endif
                            </label>

                            <div class="border-2 border-dashed border-slate-200 hover:border-emerald-400 rounded-2xl p-5 text-center transition bg-slate-50/50 relative cursor-pointer">
                                <input type="file" x-ref="fileInput" @change="addFiles($event)" multiple
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <div class="space-y-1.5 pointer-events-none">
                                    <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </div>
                                    <div class="text-xs text-slate-700 font-semibold">+ Pilih Berkas Jawaban</div>
                                    <p class="text-[10px] text-slate-400">Dapat memilih berkas satu per satu atau sekaligus (PDF, DOCX, ZIP maks 10MB)</p>
                                </div>
                            </div>

                            <div x-ref="hiddenInputsContainer" class="hidden"></div>

                            <!-- Preview Berkas -->
                            <template x-if="fileList.length > 0">
                                <div class="space-y-2 pt-2 border-t border-slate-100">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Berkas Baru Terpilih (<span x-text="fileList.length"></span>)
                                        </span>
                                        <button type="button" @click="removeAllFiles()" class="text-[11px] font-semibold text-red-500 hover:underline">
                                            Hapus Semua
                                        </button>
                                    </div>

                                    <div class="space-y-1.5">
                                        <template x-for="(f, index) in fileList" :key="index">
                                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between hover:bg-slate-100 transition">
                                                <div class="flex items-center space-x-3 overflow-hidden pr-2">
                                                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <div class="text-xs font-semibold text-slate-800 truncate" x-text="f.name"></div>
                                                        <div class="text-[10px] text-slate-400" x-text="(f.size / 1024 / 1024).toFixed(2) + ' MB'"></div>
                                                    </div>
                                                </div>

                                                <button type="button" @click="removeFile(index)" class="p-1 text-slate-400 hover:text-red-600 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            @error('attachments')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                        @if($submission)
                            <button type="button" @click="isEditing = false" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                                Batal
                            </button>
                        @endif
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition">
                            {{ $submission ? 'Perbarui Tugas' : 'Kumpulkan Tugas Sekarang' }}
                        </button>
                    </div>
                </form>
            </div>
        </template>
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