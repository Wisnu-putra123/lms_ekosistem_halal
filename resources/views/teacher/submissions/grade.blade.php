@extends('layouts.app')

@section('title', 'Penilaian Tugas - ' . $submission->user->name)
@section('header_title', 'Penilaian Submission Siswa')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('teacher.assignments.show', $submission->assignment_id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-amber-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali ke Detail Tugas</span>
            </a>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-md font-bold text-xs">Attempt #{{ $submission->attempt_number }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Siswa:</span>
                            <h2 class="text-base font-bold text-slate-800">{{ $submission->user->name }}</h2>
                            <p class="text-xs text-slate-400">{{ $submission->user->email }}</p>
                        </div>
                        <span class="text-xs text-slate-500">Dikumpulkan: {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}</span>
                    </div>

                    @if($submission->submission_text)
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Teks Jawaban Siswa:</label>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-800 leading-relaxed whitespace-pre-line">{{ $submission->submission_text }}</div>
                        </div>
                    @endif

                    @if($submission->attachments && $submission->attachments->count() > 0)
                        <div class="space-y-2 pt-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Lampiran Berkas Jawaban Siswa:</label>
                            <div class="space-y-2">
                                @foreach($submission->attachments as $att)
                                    @if($att->media)
                                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                                            <div class="flex items-center space-x-2 overflow-hidden pr-2">
                                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
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

            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3">Form Penilaian & Feedback</h3>

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

                    <form action="{{ route('teacher.submissions.update-grade', $submission->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai / Skor <span class="text-red-500">*</span></label>
                            <input type="number" name="score" value="{{ old('score', $submission->score) }}" step="0.01" min="0" max="{{ $submission->assignment->max_score }}" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('score') border-red-500 @enderror">
                            @error('score')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Feedback Teks</label>
                            <textarea name="feedback" rows="4" placeholder="Tuliskan masukan atau catatan untuk siswa..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('feedback', $submission->feedback) }}</textarea>
                        </div>

                        @if($submission->feedbackAttachments && $submission->feedbackAttachments->count() > 0)
                            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                <label class="block text-xs font-semibold text-slate-700">Berkas Feedback Terpasang:</label>
                                @foreach($submission->feedbackAttachments as $fAtt)
                                    @if($fAtt->media)
                                        <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between text-xs">
                                            <span class="truncate font-medium text-slate-700">{{ $fAtt->media->file_name }}</span>
                                            <label class="text-red-600 font-semibold text-[11px] cursor-pointer hover:underline flex items-center space-x-1 flex-shrink-0">
                                                <input type="checkbox" name="delete_feedback_attachments[]" value="{{ $fAtt->id }}" class="rounded text-red-600 focus:ring-red-500">
                                                <span>Hapus</span>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div class="pt-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Lampirkan Berkas Feedback</label>
                            <input type="file" id="feedbackAttachmentInput" name="feedback_attachments[]" multiple class="w-full text-xs bg-slate-50 p-2 border border-slate-200 rounded-xl file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                            <p class="text-[10px] text-slate-400 mt-1">Pilih file yang akan anda input. Maksimal 10MB/file.</p>

                            @error('feedback_attachments.*')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror

                            <div id="selectedFeedbackFiles" class="mt-3 space-y-2 hidden">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-slate-700">File yang akan dikirim:</span>
                                    <span id="fileCount" class="text-[10px] font-semibold text-slate-400">0 file</span>
                                </div>
                                <div id="feedbackFileList" class="space-y-2"></div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 rounded-xl text-xs shadow-sm transition">Simpan Penilaian & Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('feedbackAttachmentInput');
        const fileList = document.getElementById('feedbackFileList');
        const container = document.getElementById('selectedFeedbackFiles');
        const fileCount = document.getElementById('fileCount');
        let selectedFiles = [];

        input.addEventListener('change', function(event) {
            Array.from(event.target.files).forEach(file => {
                const maxSize = 10 * 1024 * 1024;

                if (file.size > maxSize) {
                    alert(`File "${file.name}" melebihi ukuran maksimal 10MB.`);
                    return;
                }

                const exists = selectedFiles.some(existingFile =>
                    existingFile.name === file.name &&
                    existingFile.size === file.size &&
                    existingFile.lastModified === file.lastModified
                );

                if (!exists) {
                    selectedFiles.push(file);
                }
            });

            // Reset input terlebih dahulu agar file yang sama
            // tetap dapat dipilih kembali
            input.value = '';

            // Masukkan kembali seluruh file yang sudah dipilih
            // ke dalam input sebelum form disubmit
            updateFileInput();
            renderFileList();
        });

        function updateFileInput() {
            const dataTransfer = new DataTransfer();

            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });

            input.files = dataTransfer.files;
        }

        function renderFileList() {
            fileList.innerHTML = '';

            if (selectedFiles.length === 0) {
                container.classList.add('hidden');
                fileCount.textContent = '0 file';
                return;
            }

            container.classList.remove('hidden');
            fileCount.textContent = `${selectedFiles.length} file${selectedFiles.length > 1 ? 's' : ''}`;

            selectedFiles.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'p-2.5 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between gap-2';

                const info = document.createElement('div');
                info.className = 'flex items-center min-w-0 overflow-hidden';
                info.innerHTML = `
                    <svg class="w-4 h-4 text-amber-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                `;

                const textContainer = document.createElement('div');
                textContainer.className = 'min-w-0';

                const fileName = document.createElement('div');
                fileName.className = 'text-[11px] font-semibold text-slate-700 truncate';
                fileName.textContent = file.name;

                const fileSize = document.createElement('div');
                fileSize.className = 'text-[9px] text-slate-400';
                fileSize.textContent = formatFileSize(file.size);

                textContainer.appendChild(fileName);
                textContainer.appendChild(fileSize);
                info.appendChild(textContainer);

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'text-[10px] font-semibold text-red-500 hover:text-red-700 hover:underline flex-shrink-0';
                removeButton.textContent = 'Hapus';

                removeButton.addEventListener('click', function() {
                    selectedFiles.splice(index, 1);
                    updateFileInput();
                    renderFileList();
                });

                wrapper.appendChild(info);
                wrapper.appendChild(removeButton);
                fileList.appendChild(wrapper);
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';

            const units = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));

            return `${parseFloat((bytes / Math.pow(1024, i)).toFixed(2))} ${units[i]}`;
        }
    });
    </script>
@endsection