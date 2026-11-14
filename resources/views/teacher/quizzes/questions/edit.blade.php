@extends('layouts.app')

@section('title', 'Edit Soal Quiz - Ekosistem Halal')
@section('header_title', 'Edit Soal')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <!-- Card Header Gradasi Hijau Tua -->
        <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-5 sm:p-8 border-b border-slate-300 text-white">
            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase mb-2">
                Bank Soal Quiz &bull; Perbarui Butir Soal
            </span>
            <h1 class="text-lg sm:text-xl font-bold text-white">Edit Soal</h1>
            <p class="text-xs text-emerald-100/85 mt-1">Perbarui pertanyaan, media lampiran, opsi jawaban, maupun kunci jawaban dan pembahasannya.</p>
        </div>

        <form action="{{ route('teacher.quizzes.questions.update', [$assignment->id, $question->id]) }}" method="POST" enctype="multipart/form-data" 
              x-data="questionForm()" class="p-5 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Tipe Soal -->
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35] mb-1.5">
                    Tipe Soal <span class="text-red-600">*</span>
                </label>
                <select name="question_type" x-model="questionType" @change="handleTypeChange()" 
                        class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs font-bold text-[#123F35] focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition cursor-pointer">
                    <option value="multiple_choice" {{ $question->question_type === 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda (Multiple Choice)</option>
                    <option value="true_false" {{ $question->question_type === 'true_false' ? 'selected' : '' }}>Benar / Salah (True or False)</option>
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

            <!-- 3. Media Soal Terpasang & File Baru -->
            <div class="space-y-2.5">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-[#123F35]">Lampiran Media Soal</label>
                @if($question->media)
                    <div class="p-3 bg-[#d1e4da]/60 border border-slate-300 rounded-xl flex items-center justify-between gap-2 text-xs">
                        <div class="flex items-center space-x-2 overflow-hidden">
                            <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="truncate font-bold text-[#123F35]">Media Terpasang: {{ $question->media->file_name }}</span>
                        </div>
                        <a href="{{ asset('storage/' . $question->media->file_path) }}" target="_blank" rel="noopener noreferrer" 
                           class="px-3 py-1 bg-white hover:bg-[#123F35] text-[#123F35] hover:text-white border border-slate-300 rounded-lg text-[11px] font-bold transition shadow-2xs flex-shrink-0">
                            Lihat Media
                        </a>
                    </div>
                @endif
                <input type="file" name="question_media" 
                       class="w-full text-xs bg-[#F7F5EE]/50 p-2 border border-slate-300 rounded-xl file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#28745B] file:text-white hover:file:bg-[#123F35] cursor-pointer transition">
            </div>

            <!-- 4. DAFTAR OPSIONAL PILIHAN JAWABAN -->
            <div class="p-4 sm:p-5 bg-[#F7F5EE] border border-slate-300 rounded-2xl space-y-4 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-300 pb-3.5">
                    <div>
                        <h3 class="text-xs font-extrabold text-[#123F35] uppercase tracking-wider">Opsi Pilihan Jawaban</h3>
                        <p class="text-[11px] text-slate-600 mt-0.5">Pilih salah satu bulatan radio sebagai <strong class="text-[#123F35]">Kunci Jawaban yang Benar</strong>.</p>
                    </div>

                    <template x-if="questionType === 'multiple_choice'">
                        <button type="button" @click="addOption()" class="px-3.5 py-2 bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] border border-[#123F35]/20 rounded-xl text-xs font-extrabold shadow-2xs transition self-start sm:self-auto">
                            + Tambah Opsi
                        </button>
                    </template>
                </div>

                <div class="space-y-3">
                    <template x-for="(opt, index) in options" :key="index">
                        <div class="p-3.5 rounded-xl border transition shadow-2xs flex items-start space-x-2.5 sm:space-x-3"
                             :class="Number(correctOption) === index ? 'bg-emerald-50/90 border-[#28745B]' : 'bg-white border-slate-300'">
                            <div class="pt-2 flex-shrink-0">
                                <input type="radio" name="correct_option" :value="index" x-model="correctOption" required 
                                       class="text-[#28745B] focus:ring-[#28745B] w-4 h-4 cursor-pointer">
                            </div>

                            <div class="w-7 h-7 rounded-lg font-extrabold text-xs flex items-center justify-center flex-shrink-0 mt-0.5 transition"
                                 :class="Number(correctOption) === index ? 'bg-[#28745B] text-white' : 'bg-[#F7F5EE] border border-slate-300 text-[#123F35]'"
                                 x-text="String.fromCharCode(65 + index)"></div>

                            <div class="flex-1 min-w-0 space-y-2">
                                <div class="flex items-center justify-between gap-2" x-show="Number(correctOption) === index">
                                    <span class="inline-block text-[10px] font-extrabold text-[#28745B] uppercase tracking-wider">
                                        ✓ Kunci Jawaban Benar
                                    </span>
                                </div>

                                <input type="text" :name="`options[${index}][text]`" x-model="opt.text" required
                                       class="w-full px-3 py-2 bg-[#F7F5EE]/50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">

                                <template x-if="questionType === 'multiple_choice'">
                                    <input type="file" :name="`options[${index}][media]`" 
                                           class="block w-full text-[10px] text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border file:border-slate-300 file:text-[10px] file:font-bold file:bg-[#F7F5EE] file:text-[#123F35] hover:file:bg-[#28745B] hover:file:text-white cursor-pointer transition">
                                </template>
                            </div>

                            <template x-if="questionType === 'multiple_choice' && options.length > 2">
                                <button type="button" @click="removeOption(index)" class="p-1.5 bg-white border border-slate-300 rounded-lg text-slate-500 hover:text-red-600 hover:border-red-300 transition flex-shrink-0 shadow-2xs" title="Hapus Opsi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </template>
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
                <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-6 py-2.5 rounded-xl text-xs font-extrabold shadow-2xs transition text-center">
                    Perbarui Soal
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function questionForm() {
        return {
            questionType: '{{ $question->question_type }}',
            correctOption: {{ $question->options->search(fn($opt) => $opt->is_correct) ?? 0 }},
            options: @json($question->options->map(fn($opt) => ['text' => $opt->option_text])),
            addOption() {
                this.options.push({ text: '' });
            },
            removeOption(index) {
                this.options.splice(index, 1);
                if (Number(this.correctOption) >= this.options.length) {
                    this.correctOption = 0;
                }
            },
            handleTypeChange() {
                if (this.questionType === 'true_false') {
                    this.options = [
                        { text: 'Benar' },
                        { text: 'Salah' }
                    ];
                    this.correctOption = 0;
                }
            }
        }
    }
</script>
@endsection