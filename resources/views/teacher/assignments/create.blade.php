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
            <p class="text-xs text-slate-500">Atur petunjuk, metode pengumpulan, batas attempt, parameter penilaian, serta lampirkan berkas instruksi tugas.</p>
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

            <!-- 3. PILIHAN METODE PENGUMPULAN TUGAS (submission_method) -->
            <div class="space-y-2 p-4 bg-amber-50/60 border border-amber-200/80 rounded-2xl">
                <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
                    Metode Pengumpulan Jawaban Siswa <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-amber-700 mb-3">Tentukan format pengumpulan yang diizinkan untuk dikirimkan oleh siswa.</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="relative flex items-center p-3.5 bg-white rounded-xl border border-amber-200 cursor-pointer hover:border-amber-400 transition shadow-sm">
                        <input type="radio" name="submission_method" value="file" {{ old('submission_method', 'file') == 'file' ? 'checked' : '' }} required class="text-amber-600 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-800">Unggah Berkas</span>
                            <span class="block text-[10px] text-slate-500">Siswa mengunggah file (PDF, Docx, Zip)</span>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3.5 bg-white rounded-xl border border-amber-200 cursor-pointer hover:border-amber-400 transition shadow-sm">
                        <input type="radio" name="submission_method" value="text" {{ old('submission_method') == 'text' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-800">Teks Langsung</span>
                            <span class="block text-[10px] text-slate-500">Siswa mengetik di Text Editor web</span>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3.5 bg-white rounded-xl border border-amber-200 cursor-pointer hover:border-amber-400 transition shadow-sm">
                        <input type="radio" name="submission_method" value="both" {{ old('submission_method') == 'both' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-800">Berkas & Teks</span>
                            <span class="block text-[10px] text-slate-500">Siswa bisa unggah berkas dan isi teks</span>
                        </div>
                    </label>
                </div>
                @error('submission_method')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. BATAS PERCOBAAN PENGERJAAN (Max Attempts) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Percobaan Pengerjaan (Max Attempts)</label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts') }}" min="1" placeholder="Kosongkan jika Tanpa Batas (Infinite Attempts)"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('max_attempts') border-red-500 @enderror">
                <p class="text-[10px] text-slate-400 mt-1">*Kosongkan jika siswa diperbolehkan mencoba/retake tugas tanpa batasan jumlah attempt.</p>
                @error('max_attempts')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 5. Tanggal Dibuka & Ditutup (Optional) -->
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

            <!-- 6. Penilaian: Nilai Maksimal & Passing Grade -->
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

            <!-- 7. LAMPIRAN BERKAS PENGAJAR -->
            <div x-data="attachmentUploader()" class="space-y-3">
                <label class="block text-xs font-semibold text-slate-700">Lampirkan Berkas Soal / Acuan (Opsional)</label>

                <div class="border-2 border-dashed border-slate-200 hover:border-amber-400 rounded-2xl p-5 text-center transition bg-slate-50/50 relative">
                    <input type="file" x-ref="fileInput" @change="addFiles($event)" multiple
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="space-y-1.5 pointer-events-none">
                        <div class="w-9 h-9 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div class="text-xs text-slate-700 font-semibold">+ Pilih atau Seret Berkas ke Sini</div>
                        <p class="text-[10px] text-slate-400">PDF, DOCX, PPTX, ZIP, gambar maks 10MB per berkas</p>
                    </div>
                </div>

                <div x-ref="hiddenInputsContainer" class="hidden"></div>

                <template x-if="fileList.length > 0">
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Daftar Lampiran Berkas (<span x-text="fileList.length"></span>)
                            </span>
                            <button type="button" @click="removeAllFiles()" class="text-[11px] font-semibold text-red-500 hover:underline">
                                Hapus Semua
                            </button>
                        </div>

                        <div class="space-y-1.5">
                            <template x-for="(f, index) in fileList" :key="index">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between hover:bg-slate-100 transition">
                                    <div class="flex items-center space-x-3 overflow-hidden pr-2">
                                        <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-xs font-semibold text-slate-800 truncate" x-text="f.name"></div>
                                            <div class="text-[10px] text-slate-400" x-text="(f.size / 1024 / 1024).toFixed(2) + ' MB'"></div>
                                        </div>
                                    </div>

                                    <button type="button" @click="removeFile(index)" class="p-1 text-slate-400 hover:text-red-600 transition" title="Hapus Berkas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- 8. Status Publikasi -->
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