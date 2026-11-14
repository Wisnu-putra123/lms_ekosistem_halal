@extends('layouts.app')

@section('title', 'Edit Submission Tugas - Ekosistem Halal')
@section('header_title', 'Edit Tempat Submission Tugas')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-4xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Tugas</span>
        </a>

        <span class="px-3 py-1.5 rounded-xl text-xs font-bold tracking-wider bg-[#28745B]/10 text-[#123F35] border border-[#28745B]/30 self-start sm:self-auto shadow-2xs">
            Pertemuan {{ $assignment->meeting->meeting_number ?? '-' }} &bull; Edit Submission
        </span>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <!-- Card Header Gradasi Hijau Tua -->
        <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-5 sm:p-8 border-b border-slate-300 text-white">
            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase mb-2">
                Pengaturan Tempat Submission
            </span>
            <h1 class="text-lg sm:text-xl font-bold text-white">Edit Tempat Submission Tugas</h1>
            <p class="text-xs text-emerald-100/85 mt-1">Perbarui petunjuk, metode pengumpulan, batas attempt, parameter penilaian, atau lampiran tugas.</p>
        </div>

        <form action="{{ route('teacher.assignments.update', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Judul Tugas -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Judul Tugas / Submission <span class="text-red-600">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required
                       class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Deskripsi / Instruksi -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Deskripsi & Instruksi Pengerjaan
                </label>
                <textarea name="description" rows="4" class="w-full p-3.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">{{ old('description', $assignment->description) }}</textarea>
            </div>

            <!-- 3. PILIHAN METODE PENGUMPULAN TUGAS (submission_method) -->
            <div class="space-y-3 p-4 sm:p-5 bg-[#F7F5EE] border border-slate-300 rounded-2xl shadow-2xs">
                <div>
                    <label class="block text-xs font-extrabold text-[#123F35] uppercase tracking-wider">
                        Metode Pengumpulan Jawaban Siswa <span class="text-red-600">*</span>
                    </label>
                    <p class="text-xs text-slate-600 mt-0.5">Tentukan format pengumpulan yang diizinkan untuk dikirimkan oleh siswa.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="relative flex items-start p-3.5 bg-white rounded-xl border border-slate-300 cursor-pointer hover:border-[#28745B] has-[:checked]:border-[#28745B] has-[:checked]:bg-[#28745B]/5 transition shadow-2xs">
                        <input type="radio" name="submission_method" value="file" {{ old('submission_method', $assignment->submission_method) == 'file' ? 'checked' : '' }} required class="mt-0.5 text-[#28745B] focus:ring-[#28745B]">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-[#123F35]">Unggah Berkas</span>
                            <span class="block text-[10px] text-slate-500 mt-0.5">Siswa mengunggah file (PDF, Docx, Zip)</span>
                        </div>
                    </label>

                    <label class="relative flex items-start p-3.5 bg-white rounded-xl border border-slate-300 cursor-pointer hover:border-[#28745B] has-[:checked]:border-[#28745B] has-[:checked]:bg-[#28745B]/5 transition shadow-2xs">
                        <input type="radio" name="submission_method" value="text" {{ old('submission_method', $assignment->submission_method) == 'text' ? 'checked' : '' }} class="mt-0.5 text-[#28745B] focus:ring-[#28745B]">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-[#123F35]">Teks Langsung</span>
                            <span class="block text-[10px] text-slate-500 mt-0.5">Siswa mengetik di Text Editor web</span>
                        </div>
                    </label>

                    <label class="relative flex items-start p-3.5 bg-white rounded-xl border border-slate-300 cursor-pointer hover:border-[#28745B] has-[:checked]:border-[#28745B] has-[:checked]:bg-[#28745B]/5 transition shadow-2xs">
                        <input type="radio" name="submission_method" value="both" {{ old('submission_method', $assignment->submission_method) == 'both' ? 'checked' : '' }} class="mt-0.5 text-[#28745B] focus:ring-[#28745B]">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-[#123F35]">Berkas & Teks</span>
                            <span class="block text-[10px] text-slate-500 mt-0.5">Siswa bisa unggah berkas dan isi teks</span>
                        </div>
                    </label>
                </div>
                @error('submission_method')
                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. BATAS PERCOBAAN PENGERJAAN (Max Attempts) -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Batas Percobaan Pengerjaan (Max Attempts)
                </label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts', $assignment->max_attempts) }}" min="1" placeholder="Kosongkan jika Tanpa Batas (Infinite Attempts)"
                       class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition @error('max_attempts') border-red-500 @enderror">
                <p class="text-[11px] text-slate-500 mt-1">*Kosongkan jika siswa diperbolehkan mencoba/retake tugas tanpa batasan jumlah attempt.</p>
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

            <!-- 6. Penilaian: Nilai Maksimal & Passing Grade -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                        Nilai Maksimal <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="max_score" value="{{ old('max_score', $assignment->max_score) }}" step="0.01" min="1" max="1000" required
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

            <!-- 7. Berkas Lampiran Yang Sudah Ada -->
            @if($assignment->attachments && $assignment->attachments->count() > 0)
                <div class="space-y-2.5">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35]">Lampiran Berkas Saat Ini:</label>
                    <div class="space-y-2">
                        @foreach($assignment->attachments as $attachment)
                            @if($attachment->media)
                                <div class="p-3 bg-[#d1e4da]/60 rounded-xl border border-slate-300 flex items-center justify-between gap-2">
                                    <div class="flex items-center space-x-2.5 overflow-hidden pr-2">
                                        <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-xs text-[#123F35] font-bold truncate">{{ $attachment->media->file_name }}</span>
                                    </div>
                                    <label class="inline-flex items-center space-x-1.5 text-xs text-red-600 bg-white px-2.5 py-1 rounded-lg border border-red-300 cursor-pointer hover:bg-red-50 font-bold flex-shrink-0 shadow-2xs">
                                        <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}" class="rounded text-red-600 focus:ring-red-500">
                                        <span>Hapus Berkas</span>
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 8. TAMBAH LAMPIRAN BERKAS BARU -->
            <div x-data="attachmentUploader()" class="space-y-3">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35]">Tambah Lampiran Berkas Baru (Opsional)</label>

                <div class="border-2 border-dashed border-slate-300 hover:border-[#28745B] rounded-2xl p-6 text-center transition bg-[#F7F5EE]/60 relative">
                    <input type="file" x-ref="fileInput" @change="addFiles($event)" multiple
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="space-y-1.5 pointer-events-none">
                        <div class="w-10 h-10 bg-[#28745B]/15 text-[#28745B] border border-[#28745B]/30 rounded-xl flex items-center justify-center mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div class="text-xs text-[#123F35] font-bold">+ Pilih Tambahan Berkas</div>
                        <p class="text-[11px] text-slate-500">PDF, DOCX, ZIP, maks 10MB per berkas</p>
                    </div>
                </div>

                <div x-ref="hiddenInputsContainer" class="hidden"></div>

                <template x-if="fileList.length > 0">
                    <div class="space-y-2.5 pt-3 border-t border-slate-300">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-[#123F35] uppercase tracking-wider">
                                Berkas Baru Yang Akan Ditambahkan (<span x-text="fileList.length"></span>)
                            </span>
                            <button type="button" @click="removeAllFiles()" class="text-[11px] font-bold text-red-600 hover:underline">
                                Hapus Semua
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(f, index) in fileList" :key="index">
                                <div class="p-3 bg-[#d1e4da]/60 rounded-xl border border-slate-300 flex items-center justify-between transition">
                                    <div class="flex items-center space-x-3 overflow-hidden pr-2">
                                        <div class="w-8 h-8 rounded-lg bg-[#28745B] text-white flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="text-xs font-bold text-[#123F35] truncate" x-text="f.name"></div>
                                            <div class="text-[10px] text-slate-600" x-text="(f.size / 1024 / 1024).toFixed(2) + ' MB'"></div>
                                        </div>
                                    </div>

                                    <button type="button" @click="removeFile(index)" class="p-1.5 bg-white border border-slate-300 rounded-lg text-slate-500 hover:text-red-600 hover:border-red-300 transition shadow-2xs" title="Hapus Berkas">
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

            <!-- 9. Status Publikasi -->
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
                <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-100 transition text-center shadow-2xs">
                    Batal
                </a>
                <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-6 py-2.5 rounded-xl text-xs font-extrabold shadow-2xs transition text-center">
                    Perbarui Submission
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