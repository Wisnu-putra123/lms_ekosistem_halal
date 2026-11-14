@extends('layouts.app')

@section('title', 'Penilaian Tugas - ' . ($submission->enrollment->user->name ?? 'Siswa'))
@section('header_title', 'Penilaian Submission Siswa')

@section('content')
    <div class="max-w-5xl mx-auto space-y-5 sm:space-y-6">

        <!-- Header Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
            <a href="{{ route('teacher.assignments.show', $submission->assignment_id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Detail Tugas</span>
            </a>

            <div class="flex items-center space-x-2 self-start sm:self-auto">
                <span class="px-3 py-1.5 bg-[#123F35] text-[#E7C982] rounded-xl font-extrabold text-xs shadow-2xs">
                    Sedang Meninjau: Attempt #{{ $submission->attempt_number }} dari {{ $allAttempts->count() }}
                </span>
            </div>
        </div>

        <!-- Flash Notification -->
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6">
            
            <!-- KOLOM KIRI: DETAIL JAWABAN & RIWAYAT SEMUA ATTEMPT SISWA -->
            <div class="lg:col-span-2 space-y-5 sm:space-y-6">
                
                <!-- Card Jawaban Siswa Saat Ini -->
                <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
                    <!-- Header Gradasi Hijau Tua -->
                    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-5 sm:p-6 border-b border-slate-300 text-white">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase">
                                        Attempt #{{ $submission->attempt_number }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase {{ $submission->enrollment->status === 'active' ? 'bg-[#28745B] text-white border border-white/20' : 'bg-white/15 text-emerald-100 border border-white/20' }}">
                                        Enrollment #{{ $submission->enrollment_id }} ({{ $submission->enrollment->status }})
                                    </span>
                                </div>
                                <h2 class="text-base sm:text-lg font-bold text-white">{{ $submission->enrollment->user->name ?? 'Siswa Tidak Ditemukan' }}</h2>
                                <p class="text-xs text-emerald-100/80">{{ $submission->enrollment->user->email ?? '-' }}</p>
                            </div>

                            <div class="text-left sm:text-right flex-shrink-0">
                                <span class="block text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Waktu Dikumpulkan</span>
                                <span class="text-xs font-bold text-[#E7C982]">
                                    {{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">
                        @if($submission->submission_text)
                            <div class="space-y-1.5">
                                <label class="block text-xs font-extrabold text-[#123F35] uppercase tracking-wider">Teks Jawaban Siswa:</label>
                                <div class="p-4 bg-[#F7F5EE]/70 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line">{{ $submission->submission_text }}</div>
                            </div>
                        @endif

                        @if($submission->attachments && $submission->attachments->count() > 0)
                            <div class="space-y-2">
                                <label class="block text-xs font-extrabold text-[#123F35] uppercase tracking-wider">Lampiran Berkas Jawaban Siswa:</label>
                                <div class="space-y-2">
                                    @foreach($submission->attachments as $att)
                                        @if($att->media)
                                            <div class="p-3 bg-[#d1e4da]/60 rounded-xl border border-slate-300 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                                <div class="flex items-center space-x-2.5 overflow-hidden">
                                                    <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span class="text-xs text-[#123F35] font-bold truncate" title="{{ $att->media->file_name }}">{{ $att->media->file_name }}</span>
                                                </div>

                                                <div class="flex items-center space-x-1.5 flex-shrink-0 self-end sm:self-auto">
                                                    <a href="{{ asset('storage/' . $att->media->file_path) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center space-x-1 px-3 py-1 bg-white hover:bg-[#123F35] text-[#123F35] hover:text-white border border-slate-300 rounded-lg text-[11px] font-bold transition shadow-2xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        <span>Lihat</span>
                                                    </a>
                                                    <a href="{{ asset('storage/' . $att->media->file_path) }}" download="{{ $att->media->file_name }}" class="inline-flex items-center space-x-1 px-3 py-1 bg-[#28745B] hover:bg-[#123F35] text-white rounded-lg text-[11px] font-bold transition shadow-2xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                        </svg>
                                                        <span>Unduh</span>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!$submission->submission_text && (!$submission->attachments || $submission->attachments->count() === 0))
                            <div class="p-6 text-center text-xs text-slate-400 italic bg-[#F7F5EE]/50 rounded-xl border border-dashed border-slate-300">
                                Siswa tidak menyertakan teks jawaban maupun berkas lampiran pada attempt ini.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Riwayat Semua Attempt Siswa Ini -->
                <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
                    <div class="bg-[#28745B] p-4 sm:px-6 border-b border-slate-300 text-white flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-white">Riwayat Semua Attempt Siswa ({{ $allAttempts->count() }})</h3>
                            <p class="text-[11px] text-emerald-100/85">Daftar seluruh percobaan pengumpulan tugas oleh {{ $submission->enrollment->user->name ?? 'siswa ini' }}.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-[#F7F5EE] text-[#123F35] border-b border-slate-300">
                                <tr class="text-[10px] font-extrabold uppercase tracking-wider whitespace-nowrap">
                                    <th class="py-3 px-4 sm:px-5 min-w-[100px]">Attempt</th>
                                    <th class="py-3 px-4 min-w-[140px]">Waktu Submit</th>
                                    <th class="py-3 px-4 min-w-[130px]">Status</th>
                                    <th class="py-3 px-4 min-w-[80px]">Nilai</th>
                                    <th class="py-3 px-4 sm:px-5 text-right min-w-[120px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-300 text-xs text-slate-700">
                                @foreach($allAttempts as $attItem)
                                    @php
                                        $isCurrent = $attItem->id === $submission->id;
                                    @endphp
                                    <tr class="{{ $isCurrent ? 'bg-[#E7C982]/20' : 'hover:bg-[#F7F5EE]/50' }} transition">
                                        <td class="py-3 px-4 sm:px-5 whitespace-nowrap">
                                            <div class="flex items-center space-x-1.5">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg font-bold text-[11px] whitespace-nowrap {{ $isCurrent ? 'bg-[#123F35] text-[#E7C982]' : 'bg-slate-200 text-slate-700' }}">
                                                    #{{ $attItem->attempt_number }}
                                                </span>
                                                @if($isCurrent)
                                                    <span class="text-[10px] font-extrabold text-[#123F35] uppercase">(Aktif)</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                            {{ $attItem->submitted_at ? \Carbon\Carbon::parse($attItem->submitted_at)->format('d M Y H:i') : '-' }}
                                        </td>

                                        <td class="py-3 px-4 whitespace-nowrap">
                                            @if($attItem->status === 'graded')
                                                @if($attItem->score >= $submission->assignment->passing_score)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-[#28745B]/40 bg-[#28745B]/15 text-[#28745B] text-[10px] font-extrabold whitespace-nowrap">
                                                        Lulus
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-red-300 bg-red-100 text-red-700 text-[10px] font-extrabold whitespace-nowrap">
                                                        Belum Lulus
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-[#E7C982] bg-[#E7C982]/40 text-[#123F35] text-[10px] font-extrabold whitespace-nowrap">
                                                    Menunggu Nilai
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-3 px-4 font-extrabold text-[#123F35] whitespace-nowrap">
                                            {{ $attItem->score !== null ? number_format($attItem->score, 2) : '-' }}
                                        </td>

                                        <td class="py-3 px-4 sm:px-5 text-right whitespace-nowrap">
                                            @if($isCurrent)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-[#123F35]/10 text-[#123F35] border border-[#123F35]/20 text-[10px] font-extrabold whitespace-nowrap">
                                                    Sedang Dibuka
                                                </span>
                                            @else
                                                <a href="{{ route('teacher.submissions.grade', $attItem->id) }}" class="inline-flex items-center px-3 py-1 rounded-lg bg-[#28745B] hover:bg-[#123F35] text-white text-[11px] font-bold transition shadow-2xs whitespace-nowrap">
                                                    Tinjau &rarr;
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- KOLOM KANAN: FORM PENILAIAN & FEEDBACK -->
            <div class="space-y-5 sm:space-y-6">
                <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
                    <div class="bg-[#28745B] p-4 sm:px-5 border-b border-slate-300 text-white">
                        <h3 class="text-sm font-bold text-white">Form Penilaian & Feedback</h3>
                        <p class="text-[11px] text-emerald-100/85">Untuk Attempt #{{ $submission->attempt_number }}</p>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="p-3.5 bg-[#F7F5EE] rounded-xl border border-slate-300 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-600 font-semibold">Nilai Maksimal:</span>
                                <span class="font-extrabold text-[#123F35]">{{ number_format($submission->assignment->max_score, 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600 font-semibold">Passing Grade (KKM):</span>
                                <span class="font-extrabold text-[#28745B]">{{ number_format($submission->assignment->passing_score, 0) }}</span>
                            </div>
                            @if($submission->status === 'graded' && $submission->grader)
                                <div class="flex justify-between pt-1.5 border-t border-slate-300">
                                    <span class="text-slate-600 font-semibold">Terakhir Dinilai:</span>
                                    <span class="font-bold text-[#123F35]">{{ $submission->grader->name }}</span>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('teacher.submissions.update-grade', $submission->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                                    Nilai / Skor <span class="text-red-600">*</span>
                                </label>
                                <input type="number" name="score" value="{{ old('score', $submission->score) }}" step="0.01" min="0" max="{{ $submission->assignment->max_score }}" required 
                                       class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-sm font-extrabold text-[#123F35] focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition @error('score') border-red-500 @enderror">
                                @error('score')
                                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                                    Catatan / Feedback Teks
                                </label>
                                <textarea name="feedback" rows="4" placeholder="Tuliskan masukan atau catatan untuk siswa..." 
                                          class="w-full p-3 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">{{ old('feedback', $submission->feedback) }}</textarea>
                            </div>

                            @if($submission->feedbackAttachments && $submission->feedbackAttachments->count() > 0)
                                <div class="space-y-2 pt-3 border-t border-slate-300">
                                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35]">Berkas Feedback Terpasang:</label>
                                    <div class="space-y-1.5">
                                        @foreach($submission->feedbackAttachments as $fAtt)
                                            @if($fAtt->media)
                                                <div class="p-2.5 bg-[#d1e4da]/60 border border-slate-300 rounded-xl flex items-center justify-between gap-2 text-xs">
                                                    <a href="{{ asset('storage/' . $fAtt->media->file_path) }}" target="_blank" rel="noopener noreferrer" class="truncate font-bold text-[#123F35] hover:text-[#28745B] hover:underline" title="Lihat berkas di tab baru">
                                                        {{ $fAtt->media->file_name }}
                                                    </a>
                                                    <label class="text-red-600 bg-white px-2 py-0.5 rounded-lg border border-red-300 font-bold text-[10px] cursor-pointer hover:bg-red-50 flex items-center space-x-1 flex-shrink-0 shadow-2xs">
                                                        <input type="checkbox" name="delete_feedback_attachments[]" value="{{ $fAtt->id }}" class="rounded text-red-600 focus:ring-red-500">
                                                        <span>Hapus</span>
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="pt-2 border-t border-slate-300">
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">Lampirkan Berkas Feedback</label>
                                <input type="file" id="feedbackAttachmentInput" name="feedback_attachments[]" multiple class="w-full text-xs bg-[#F7F5EE]/50 p-2 border border-slate-300 rounded-xl file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#28745B] file:text-white hover:file:bg-[#123F35] cursor-pointer transition">
                                <p class="text-[10px] text-slate-500 mt-1">Pilih file yang akan diunggah. Maksimal 10MB/file.</p>

                                @error('feedback_attachments.*')
                                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror

                                <div id="selectedFeedbackFiles" class="mt-3 space-y-2 hidden">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-extrabold text-[#123F35]">File yang akan dikirim:</span>
                                        <span id="fileCount" class="text-[10px] font-bold text-slate-500">0 file</span>
                                    </div>
                                    <div id="feedbackFileList" class="space-y-2"></div>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-[#28745B] hover:bg-[#123F35] text-white font-extrabold py-2.5 rounded-xl text-xs shadow-2xs transition">
                                Simpan Penilaian & Feedback
                            </button>
                        </form>
                    </div>
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

            input.value = '';
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
            fileCount.textContent = `${selectedFiles.length} file`;

            selectedFiles.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'p-2.5 bg-[#d1e4da]/60 border border-slate-300 rounded-xl flex items-center justify-between gap-2';

                const info = document.createElement('div');
                info.className = 'flex items-center min-w-0 overflow-hidden';
                info.innerHTML = `
                    <svg class="w-4 h-4 text-[#28745B] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                `;

                const textContainer = document.createElement('div');
                textContainer.className = 'min-w-0';

                const fileName = document.createElement('div');
                fileName.className = 'text-[11px] font-bold text-[#123F35] truncate';
                fileName.textContent = file.name;

                const fileSize = document.createElement('div');
                fileSize.className = 'text-[9px] text-slate-600';
                fileSize.textContent = formatFileSize(file.size);

                textContainer.appendChild(fileName);
                textContainer.appendChild(fileSize);
                info.appendChild(textContainer);

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'text-[10px] font-bold text-red-600 hover:underline flex-shrink-0';
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