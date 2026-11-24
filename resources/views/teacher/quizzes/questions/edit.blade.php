@extends('layouts.app')

@section('title', 'Edit Soal Quiz - Ekosistem Halal')
@section('header_title', 'Edit Soal')

@section('content')
<div class="max-w-4xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Quiz</span>
        </a>

        <span class="px-3 py-1.5 rounded-xl text-xs font-bold tracking-wider bg-[#28745B]/10 text-[#123F35] border border-[#28745B]/30 self-start sm:self-auto shadow-2xs truncate max-w-full">
            {{ $assignment->title }}
        </span>
    </div>

    <!-- Flash / Validation Error Summary -->
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-300 text-red-800 rounded-2xl text-xs font-semibold flex items-start space-x-2 shadow-2xs">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="break-words">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-300 text-red-800 rounded-2xl text-xs space-y-1 shadow-2xs">
            <p class="font-extrabold">Terdapat kesalahan pada input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <!-- Card Header Gradasi Hijau Tua -->
        <div class="bg-[#28745B] p-4 sm:p-8 border-b border-slate-300 text-white">
            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase mb-2">
                Bank Soal Quiz &bull; Perbarui Butir Soal
            </span>
            <h1 class="text-lg sm:text-xl font-bold text-white">Edit Soal</h1>
            <p class="text-xs text-emerald-100/85 mt-1 leading-relaxed">Perbarui pertanyaan, media lampiran (gambar, audio, video, PDF), opsi jawaban, maupun kunci jawaban dan pembahasannya.</p>
        </div>

        <form action="{{ route('teacher.quizzes.questions.update', [$assignment->id, $question->id]) }}" method="POST" enctype="multipart/form-data" 
              x-data="questionForm()" class="p-4 sm:p-8 space-y-5 sm:space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Tipe Soal -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Tipe Soal <span class="text-red-600">*</span>
                </label>
                <select name="question_type" x-model="questionType" @change="handleTypeChange()" 
                        class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs font-bold text-[#123F35] focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition cursor-pointer">
                    <option value="multiple_choice" {{ old('question_type', $question->question_type) === 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda (Multiple Choice)</option>
                    <option value="true_false" {{ old('question_type', $question->question_type) === 'true_false' ? 'selected' : '' }}>Benar / Salah (True or False)</option>
                </select>
            </div>

            <!-- 2. Teks Pertanyaan -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Pertanyaan / Soal <span class="text-red-600">*</span>
                </label>
                <textarea name="question_text" rows="4" required 
                          class="w-full p-3.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition @error('question_text') border-red-500 @enderror">{{ old('question_text', $question->question_text) }}</textarea>
                @error('question_text')
                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Media Soal Terpasang & Upload File Baru -->
            <div class="space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35]">
                        Lampiran Media Soal <span class="text-slate-400 font-normal normal-case">(Opsional)</span>
                    </label>
                    <span class="text-[10px] text-slate-500">Gambar, Audio, Video, atau PDF &bull; Maks 20MB</span>
                </div>

                @if($question->media)
                    @php
                        $qMediaUrl  = asset('storage/' . $question->media->file_path);
                        $qMediaType = $question->media->type ?? '';
                        $qMimeType  = $question->media->mime_type ?? '';
                    @endphp
                    <input type="hidden" name="remove_question_media" :value="removeQuestionMedia ? 1 : 0">

                    <div class="p-3 sm:p-3.5 bg-[#d1e4da]/60 border border-slate-300 rounded-xl space-y-3 text-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#28745B] block" x-show="!removeQuestionMedia">
                                    Media Terpasang Saat Ini
                                </span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-red-600 block" x-show="removeQuestionMedia">
                                    Media Ditandai untuk Dihapus
                                </span>
                                <p class="truncate font-bold text-[#123F35] mt-0.5" :class="removeQuestionMedia ? 'line-through opacity-60' : ''">
                                    {{ $question->media->file_name }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2 w-full sm:w-auto flex-shrink-0">
                                <a href="{{ $qMediaUrl }}" target="_blank" rel="noopener noreferrer" 
                                   class="flex-1 sm:flex-initial text-center px-3 py-1.5 bg-white hover:bg-[#123F35] text-[#123F35] hover:text-white border border-slate-300 rounded-lg text-[11px] font-bold transition shadow-2xs">
                                    Buka Tab Baru
                                </a>
                                <button type="button"
                                        @click="removeQuestionMedia = !removeQuestionMedia"
                                        class="flex-1 sm:flex-initial text-center px-3 py-1.5 rounded-lg text-[11px] font-bold transition cursor-pointer"
                                        :class="removeQuestionMedia ? 'bg-slate-200 text-slate-700 hover:bg-slate-300' : 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-600 hover:text-white'">
                                    <span x-text="removeQuestionMedia ? 'Batalkan Hapus' : 'Hapus Media'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Pratinjau Media Terpasang -->
                        <div :class="removeQuestionMedia ? 'opacity-40 grayscale pointer-events-none' : ''" class="transition">
                            @if($qMediaType === 'image' || str_contains($qMimeType, 'image'))
                                <img src="{{ $qMediaUrl }}" alt="{{ $question->media->file_name }}"
                                     class="w-full sm:w-auto max-h-48 rounded-lg object-contain border border-slate-300 bg-white p-1.5">
                            @elseif($qMediaType === 'audio' || str_contains($qMimeType, 'audio'))
                                <audio controls preload="metadata" class="w-full max-w-md h-9">
                                    <source src="{{ $qMediaUrl }}" type="{{ $qMimeType }}">
                                    Browser Anda tidak mendukung pemutar audio.
                                </audio>
                            @elseif($qMediaType === 'video' || str_contains($qMimeType, 'video'))
                                <video controls preload="metadata" class="w-full max-w-lg max-h-60 rounded-lg bg-black border border-slate-300">
                                    <source src="{{ $qMediaUrl }}" type="{{ $qMimeType }}">
                                    Browser Anda tidak mendukung pemutar video.
                                </video>
                            @endif
                        </div>
                    </div>
                @endif

                <input type="file" name="question_media"
                       accept="image/jpeg,image/png,image/webp,audio/mpeg,audio/wav,audio/ogg,video/mp4,video/webm,video/quicktime,application/pdf"
                       @change="handleQuestionMediaSelect($event)"
                       class="w-full text-xs bg-[#F7F5EE]/50 p-2 border border-slate-300 rounded-xl overflow-hidden text-ellipsis file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#28745B] file:text-white hover:file:bg-[#123F35] cursor-pointer transition">

                <!-- Pratinjau File Baru yang Dipilih -->
                <template x-if="newQuestionMedia.url">
                    <div class="p-3 bg-emerald-50/80 border border-[#28745B]/40 rounded-xl space-y-2">
                        <div class="flex items-center justify-between gap-2 text-[11px]">
                            <span class="font-bold text-[#123F35] truncate min-w-0">
                                Pratinjau: <span x-text="newQuestionMedia.name"></span>
                            </span>
                            <span class="px-2 py-0.5 rounded bg-[#28745B] text-white text-[10px] font-bold uppercase flex-shrink-0" x-text="newQuestionMedia.type"></span>
                        </div>
                        <template x-if="newQuestionMedia.type === 'image'">
                            <img :src="newQuestionMedia.url" class="w-full sm:w-auto max-h-44 rounded-lg border border-slate-300 bg-white p-1 object-contain">
                        </template>
                        <template x-if="newQuestionMedia.type === 'audio'">
                            <audio :src="newQuestionMedia.url" controls class="w-full max-w-md h-9"></audio>
                        </template>
                        <template x-if="newQuestionMedia.type === 'video'">
                            <video :src="newQuestionMedia.url" controls class="w-full max-w-lg max-h-56 rounded-lg bg-black"></video>
                        </template>
                    </div>
                </template>

                @error('question_media')
                    <p class="text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. DAFTAR OPSIONAL PILIHAN JAWABAN -->
            <div class="p-3.5 sm:p-5 bg-[#F7F5EE] border border-slate-300 rounded-2xl space-y-4 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-300 pb-3.5">
                    <div>
                        <h3 class="text-xs font-extrabold text-[#123F35] uppercase tracking-wider">Opsi Pilihan Jawaban</h3>
                        <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">Pilih salah satu bulatan radio sebagai <strong class="text-[#123F35]">Kunci Jawaban yang Benar</strong>. Setiap opsi dapat berisi teks, media (gambar/audio/video), atau keduanya.</p>
                    </div>

                    <template x-if="questionType === 'multiple_choice'">
                        <button type="button" @click="addOption()" class="w-full sm:w-auto px-3.5 py-2 bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] border border-[#123F35]/20 rounded-xl text-xs font-extrabold shadow-2xs transition cursor-pointer text-center">
                            + Tambah Opsi
                        </button>
                    </template>
                </div>

                <div class="space-y-3">
                    <template x-for="(opt, index) in options" :key="index">
                        <div class="p-3 sm:p-4 rounded-xl border transition shadow-2xs space-y-3"
                             :class="Number(correctOption) === index ? 'bg-emerald-50/90 border-[#28745B]' : 'bg-white border-slate-300'">

                            <!-- Baris Header Opsi (Radio + Huruf Opsi + Badge Kunci + Tombol Hapus Opsi) -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center space-x-2 min-w-0">
                                    <input type="radio" name="correct_option" :value="index" x-model="correctOption" required 
                                           class="text-[#28745B] focus:ring-[#28745B] w-4 h-4 flex-shrink-0 cursor-pointer">

                                    <div class="w-7 h-7 rounded-lg font-extrabold text-xs flex items-center justify-center flex-shrink-0 transition"
                                         :class="Number(correctOption) === index ? 'bg-[#28745B] text-white' : 'bg-[#F7F5EE] border border-slate-300 text-[#123F35]'"
                                         x-text="String.fromCharCode(65 + index)"></div>

                                    <span x-show="Number(correctOption) === index"
                                          class="inline-flex items-center px-2 py-0.5 rounded-md bg-[#28745B]/15 text-[10px] font-extrabold text-[#28745B] uppercase tracking-wider truncate">
                                        ✓ Kunci Jawaban
                                    </span>
                                </div>

                                <template x-if="questionType === 'multiple_choice' && options.length > 2">
                                    <button type="button" @click="removeOption(index)" class="p-1.5 bg-white border border-slate-300 rounded-lg text-slate-500 hover:text-red-600 hover:border-red-300 transition flex-shrink-0 shadow-2xs cursor-pointer" title="Hapus Opsi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </template>
                            </div>

                            <!-- Area Input Teks & Media Opsi (Full Width pada Mobile) -->
                            <div class="space-y-2.5">
                                <!-- Hidden ID opsi lama agar media opsi tetap terpaut jika tidak diubah -->
                                <input type="hidden" :name="`options[${index}][id]`" :value="opt.id ?? ''">
                                <input type="hidden" :name="`options[${index}][remove_media]`" :value="opt.remove_media ? 1 : 0">

                                <!-- Teks Opsi (Opsional jika menggunakan opsi media saja) -->
                                <input type="text" :name="`options[${index}][text]`" x-model="opt.text"
                                       :required="!opt.media_url && !opt.new_media_url"
                                       placeholder="Teks opsi jawaban (wajib diisi jika tanpa media)..."
                                       class="w-full px-3 py-2 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">

                                <!-- Pratinjau Media Opsi yang Sudah Terpasang (Gambar / Audio / Video) -->
                                <template x-if="opt.media_url">
                                    <div class="p-2.5 sm:p-3 bg-[#d1e4da]/50 border border-slate-300 rounded-xl space-y-2.5">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                            <div class="min-w-0 flex-1 text-[11px]">
                                                <p class="font-bold text-[#123F35] truncate" x-show="!opt.remove_media" x-text="opt.media_name"></p>
                                                <p class="font-bold text-red-600 truncate" x-show="opt.remove_media">Media ditandai untuk dihapus</p>
                                            </div>

                                            <div class="flex items-center gap-2 w-full sm:w-auto flex-shrink-0">
                                                <a :href="opt.media_url" target="_blank" rel="noopener noreferrer"
                                                   class="flex-1 sm:flex-initial text-center px-2.5 py-1.5 bg-white hover:bg-[#123F35] text-[#123F35] hover:text-white border border-slate-300 rounded-lg text-[11px] font-bold transition">
                                                    Buka Tab Baru
                                                </a>

                                                <button type="button"
                                                        @click="opt.remove_media = !opt.remove_media"
                                                        class="flex-1 sm:flex-initial text-center px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition cursor-pointer"
                                                        :class="opt.remove_media ? 'bg-slate-200 text-slate-700 hover:bg-slate-300' : 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-600 hover:text-white'">
                                                    <span x-text="opt.remove_media ? 'Batalkan' : 'Hapus Media'"></span>
                                                </button>
                                            </div>
                                        </div>

                                        <div :class="opt.remove_media ? 'opacity-40 grayscale pointer-events-none' : ''" class="transition">
                                            <template x-if="opt.media_type === 'image'">
                                                <img :src="opt.media_url" :alt="opt.media_name"
                                                     class="w-full sm:w-auto max-h-36 rounded-lg object-contain border border-slate-300 bg-white p-1">
                                            </template>
                                            <template x-if="opt.media_type === 'audio'">
                                                <audio :src="opt.media_url" controls preload="metadata" class="w-full h-9"></audio>
                                            </template>
                                            <template x-if="opt.media_type === 'video'">
                                                <video :src="opt.media_url" controls preload="metadata" class="w-full max-h-44 rounded-lg bg-black"></video>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Input Upload Media Opsi Baru (Gambar / Audio / Video) -->
                                <template x-if="questionType === 'multiple_choice'">
                                    <div class="space-y-2">
                                        <input type="file" :name="`options[${index}][media]`"
                                               accept="image/jpeg,image/png,image/webp,audio/mpeg,audio/wav,audio/ogg,video/mp4,video/webm,video/quicktime"
                                               @change="handleOptionMediaSelect($event, index)"
                                               class="block w-full text-[11px] text-slate-600 bg-[#F7F5EE]/40 p-1.5 border border-slate-200 rounded-xl overflow-hidden text-ellipsis file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border file:border-slate-300 file:text-[10px] file:font-bold file:bg-white file:text-[#123F35] hover:file:bg-[#28745B] hover:file:text-white cursor-pointer transition">

                                        <!-- Pratinjau Media Opsi Baru -->
                                        <template x-if="opt.new_media_url">
                                            <div class="p-2.5 bg-emerald-50/80 border border-[#28745B]/40 rounded-xl space-y-2">
                                                <div class="flex items-center justify-between gap-2 text-[10px]">
                                                    <span class="font-bold text-[#123F35] truncate min-w-0" x-text="'File Baru: ' + opt.new_media_name"></span>
                                                    <span class="px-1.5 py-0.5 rounded bg-[#28745B] text-white font-bold uppercase flex-shrink-0" x-text="opt.new_media_type"></span>
                                                </div>
                                                <template x-if="opt.new_media_type === 'image'">
                                                    <img :src="opt.new_media_url" class="w-full sm:w-auto max-h-32 rounded border border-slate-300 bg-white p-0.5 object-contain">
                                                </template>
                                                <template x-if="opt.new_media_type === 'audio'">
                                                    <audio :src="opt.new_media_url" controls class="w-full h-9"></audio>
                                                </template>
                                                <template x-if="opt.new_media_type === 'video'">
                                                    <video :src="opt.new_media_url" controls class="w-full max-h-40 rounded bg-black"></video>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 5. Pembahasan -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Pembahasan / Penjelasan Jawaban (Opsional)
                </label>
                <textarea name="explanation" rows="3" 
                          class="w-full p-3.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">{{ old('explanation', $question->explanation) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="pt-5 border-t border-slate-300 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3">
                <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-100 transition text-center shadow-2xs">
                    Batal
                </a>
                <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-6 py-2.5 rounded-xl text-xs font-extrabold shadow-2xs transition text-center cursor-pointer">
                    Perbarui Soal
                </button>
            </div>
        </form>
    </div>

</div>

@php
    $initialOptions = $question->options->map(function ($opt) {
        $mediaType = null;
        if ($opt->media) {
            $mime = $opt->media->mime_type ?? '';
            $mediaType = $opt->media->type ?: match (true) {
                str_contains($mime, 'image') => 'image',
                str_contains($mime, 'audio') => 'audio',
                str_contains($mime, 'video') => 'video',
                default => 'document',
            };
        }

        return [
            'id'             => $opt->id,
            'text'           => $opt->option_text,
            'media_url'      => $opt->media ? asset('storage/' . $opt->media->file_path) : null,
            'media_name'     => $opt->media ? $opt->media->file_name : null,
            'media_type'     => $mediaType,
            'remove_media'   => false,
            'new_media_url'  => null,
            'new_media_name' => null,
            'new_media_type' => null,
        ];
    })->values();
@endphp

<script>
    function questionForm() {
        return {
            questionType: '{{ old('question_type', $question->question_type) }}',
            correctOption: {{ old('correct_option', $question->options->search(fn($opt) => $opt->is_correct) ?? 0) }},
            removeQuestionMedia: false,
            newQuestionMedia: {
                url: null,
                name: null,
                type: null,
            },
            options: @json($initialOptions),
            detectMediaType(file) {
                if (!file) return null;
                if (file.type.startsWith('image/')) return 'image';
                if (file.type.startsWith('audio/')) return 'audio';
                if (file.type.startsWith('video/')) return 'video';
                return 'document';
            },
            handleQuestionMediaSelect(event) {
                const file = event.target.files[0];
                if (this.newQuestionMedia.url) {
                    URL.revokeObjectURL(this.newQuestionMedia.url);
                }
                if (file) {
                    this.removeQuestionMedia = false;
                    this.newQuestionMedia = {
                        url: URL.createObjectURL(file),
                        name: file.name,
                        type: this.detectMediaType(file),
                    };
                } else {
                    this.newQuestionMedia = { url: null, name: null, type: null };
                }
            },
            handleOptionMediaSelect(event, index) {
                const file = event.target.files[0];
                const opt = this.options[index];
                if (opt.new_media_url) {
                    URL.revokeObjectURL(opt.new_media_url);
                }
                if (file) {
                    opt.remove_media = false;
                    opt.new_media_url = URL.createObjectURL(file);
                    opt.new_media_name = file.name;
                    opt.new_media_type = this.detectMediaType(file);
                } else {
                    opt.new_media_url = null;
                    opt.new_media_name = null;
                    opt.new_media_type = null;
                }
            },
            addOption() {
                this.options.push({
                    id: null,
                    text: '',
                    media_url: null,
                    media_name: null,
                    media_type: null,
                    remove_media: false,
                    new_media_url: null,
                    new_media_name: null,
                    new_media_type: null,
                });
            },
            removeOption(index) {
                if (this.options[index].new_media_url) {
                    URL.revokeObjectURL(this.options[index].new_media_url);
                }
                this.options.splice(index, 1);
                if (Number(this.correctOption) >= this.options.length) {
                    this.correctOption = 0;
                }
            },
            handleTypeChange() {
                if (this.questionType === 'true_false') {
                    this.options = [
                        { id: null, text: 'Benar', media_url: null, media_name: null, media_type: null, remove_media: false, new_media_url: null, new_media_name: null, new_media_type: null },
                        { id: null, text: 'Salah', media_url: null, media_name: null, media_type: null, remove_media: false, new_media_url: null, new_media_name: null, new_media_type: null }
                    ];
                    this.correctOption = 0;
                }
            }
        }
    }
</script>

@endsection