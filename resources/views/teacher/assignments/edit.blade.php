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
            <p class="text-xs text-slate-500">Perbarui petunjuk, metode pengumpulan, parameter penilaian, atau lampiran tugas.</p>
        </div>

        <form action="{{ route('teacher.assignments.update', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Judul Tugas -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Tugas / Submission <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Deskripsi / Instruksi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi & Instruksi Pengerjaan</label>
                <textarea name="description" rows="4" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $assignment->description) }}</textarea>
            </div>

            <!-- 3. PILIHAN METODE PENGUMPULAN TUGAS (submission_method) -->
            <div class="space-y-2 p-4 bg-blue-50/60 border border-blue-200/80 rounded-2xl">
                <label class="block text-xs font-bold text-blue-900 uppercase tracking-wider">
                    Metode Pengumpulan Jawaban Siswa <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-blue-700 mb-3">Tentukan format pengumpulan yang diizinkan untuk dikirimkan oleh siswa.</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Opsi 1: Unggah Berkas -->
                    <label class="relative flex items-center p-3.5 bg-white rounded-xl border border-blue-200 cursor-pointer hover:border-blue-400 transition shadow-sm">
                        <input type="radio" name="submission_method" value="file" {{ old('submission_method', $assignment->submission_method) == 'file' ? 'checked' : '' }} required class="text-blue-600 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-800">Unggah Berkas</span>
                            <span class="block text-[10px] text-slate-500">Siswa mengunggah file (PDF, Docx, Zip)</span>
                        </div>
                    </label>

                    <!-- Opsi 2: Teks Langsung -->
                    <label class="relative flex items-center p-3.5 bg-white rounded-xl border border-blue-200 cursor-pointer hover:border-blue-400 transition shadow-sm">
                        <input type="radio" name="submission_method" value="text" {{ old('submission_method', $assignment->submission_method) == 'text' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-800">Teks Langsung</span>
                            <span class="block text-[10px] text-slate-500">Siswa mengetik di Text Editor web</span>
                        </div>
                    </label>

                    <!-- Opsi 3: Keduanya -->
                    <label class="relative flex items-center p-3.5 bg-white rounded-xl border border-blue-200 cursor-pointer hover:border-blue-400 transition shadow-sm">
                        <input type="radio" name="submission_method" value="both" {{ old('submission_method', $assignment->submission_method) == 'both' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
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

            <!-- 4. Tanggal Dibuka & Ditutup -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Dibuka (Opsional)</label>
                    <input type="datetime-local" name="available_from" value="{{ old('available_from', $assignment->available_from ? \Carbon\Carbon::parse($assignment->available_from)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Ditutup / Tenggat (Opsional)</label>
                    <input type="datetime-local" name="available_until" value="{{ old('available_until', $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 @error('available_until') border-red-500 @enderror">
                    @error('available_until')
                        <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 5. Penilaian: Nilai Maksimal & Passing Grade -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Maksimal <span class="text-red-500">*</span></label>
                    <input type="number" name="max_score" value="{{ old('max_score', $assignment->max_score) }}" step="0.01" min="1" max="1000" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Passing Grade (Nilai Kelulusan Minimal) <span class="text-red-500">*</span></label>
                    <input type="number" name="passing_score" value="{{ old('passing_score', $assignment->passing_score) }}" step="0.01" min="0" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-emerald-700 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('passing_score') border-red-500 @enderror">
                    @error('passing_score')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 6. Berkas Lampiran Yang Sudah Ada -->
            @if($assignment->attachments && $assignment->attachments->count() > 0)
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-700">Lampiran Berkas Saat Ini:</label>
                    <div class="space-y-1.5">
                        @foreach($assignment->attachments as $attachment)
                            @if($attachment->media)
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                                    <div class="flex items-center space-x-2 overflow-hidden pr-2">
                                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-xs text-slate-700 font-medium truncate">{{ $attachment->media->file_name }}</span>
                                    </div>
                                    <label class="inline-flex items-center space-x-1.5 text-xs text-red-600 cursor-pointer hover:underline font-semibold flex-shrink-0">
                                        <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" class="rounded text-red-600 focus:ring-red-500">
                                        <span>Hapus Berkas</span>
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 7. TAMBAH LAMPIRAN BERKAS BARU (Upload bertahap/satu-satu + daftar di bawah) -->
            <div x-data="attachmentUploader()" class="space-y-3">
                <label class="block text-xs font-semibold text-slate-700">Tambah Lampiran Berkas Baru (Opsional)</label>

                <!-- Box Dropzone Input File -->
                <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-5 text-center transition bg-slate-50/50 relative">
                    <input type="file" x-ref="fileInput" @change="addFiles($event)" multiple
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="space-y-1.5 pointer-events-none">
                        <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div class="text-xs text-slate-700 font-semibold">+ Pilih Tambahan Berkas</div>
                        <p class="text-[10px] text-slate-400">Pilih file satu per satu atau sekaligus untuk ditambahkan ke lampiran (PDF, DOCX, ZIP, maks 10MB)</p>
                    </div>
                </div>

                <!-- Hidden Input Container untuk Mengirimkan Files ke Request Laravel -->
                <div x-ref="hiddenInputsContainer" class="hidden"></div>

                <!-- Daftar Berkas Baru Terpilih -->
                <template x-if="fileList.length > 0">
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Berkas Baru Yang Akan Ditingkatkan (<span x-text="fileList.length"></span>)
                            </span>
                            <button type="button" @click="removeAllFiles()" class="text-[11px] font-semibold text-red-500 hover:underline">
                                Hapus Semua
                            </button>
                        </div>

                        <div class="space-y-1.5">
                            <template x-for="(f, index) in fileList" :key="index">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between hover:bg-slate-100 transition">
                                    <div class="flex items-center space-x-3 overflow-hidden pr-2">
                                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0">
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
                <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="published" {{ old('status', $assignment->status) == 'published' ? 'selected' : '' }}>Published (Langsung dapat dilihat siswa)</option>
                    <option value="draft" {{ old('status', $assignment->status) == 'draft' ? 'selected' : '' }}>Draft (Disimpan sementara)</option>
                    <option value="closed" {{ old('status', $assignment->status) == 'closed' ? 'selected' : '' }}>Closed (Ditutup)</option>
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

<!-- Alpine JS Component Script untuk Pengelolaan Dynamic Multiple File Input -->
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