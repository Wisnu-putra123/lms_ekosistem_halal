@extends('layouts.app')

@section('title', 'Tambah Soal Quiz - Ekosistem Halal')
@section('header_title', 'Tambah Soal Baru')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-amber-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Detail Quiz</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h1 class="text-lg font-bold text-slate-800">Tambah Soal Baru</h1>
            <p class="text-xs text-slate-500">Buat pertanyaan, unggah gambar/file pendukung, serta fleksibel menentukan opsi pilihan jawaban.</p>
        </div>

        <form action="{{ route('teacher.quizzes.questions.store', $assignment->id) }}" method="POST" enctype="multipart/form-data" 
              x-data="questionForm()" class="space-y-6">
            @csrf

            <!-- 1. Tipe Soal -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Soal <span class="text-red-500">*</span></label>
                <select name="question_type" x-model="questionType" @change="handleTypeChange()" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="multiple_choice">Pilihan Ganda (Multiple Choice)</option>
                    <option value="true_false">Benar / Salah (True or False)</option>
                </select>
            </div>

            <!-- 2. Teks Pertanyaan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pertanyaan / Soal <span class="text-red-500">*</span></label>
                <textarea name="question_text" rows="4" placeholder="Tuliskan pertanyaan soal di sini..." required
                          class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('question_text') }}</textarea>
            </div>

            <!-- 3. Lampiran Media Soal (Gambar/Audio/File) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Lampiran Media Soal (Gambar/Audio - Opsional)</label>
                <input type="file" name="question_media" class="w-full text-xs bg-slate-50 p-2 border border-slate-200 rounded-xl file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
            </div>

            <!-- 4. DAFTAR OPSIONAL PILIHAN JAWABAN (FLEKSIBEL) -->
            <div class="p-5 bg-amber-50/50 border border-amber-200/80 rounded-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-amber-200/80 pb-3">
                    <div>
                        <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Opsi Pilihan Jawaban</h3>
                        <p class="text-[11px] text-amber-700">Pilih salah satu bulatan radio sebagai **Kunci Jawaban yang Benar**.</p>
                    </div>

                    <template x-if="questionType === 'multiple_choice'">
                        <button type="button" @click="addOption()" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold shadow-sm transition">
                            + Tambah Opsi
                        </button>
                    </template>
                </div>

                <div class="space-y-3">
                    <template x-for="(opt, index) in options" :key="index">
                        <div class="p-3 bg-white rounded-xl border border-amber-200 flex items-start space-x-3 shadow-sm">
                            <!-- Radio Kunci Jawaban -->
                            <div class="pt-2 flex-shrink-0">
                                <input type="radio" name="correct_option" :value="index" x-model="correctOption" required class="text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer" title="Pilih sebagai kunci jawaban">
                            </div>

                            <!-- Indikator Huruf -->
                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5"
                                 x-text="String.fromCharCode(65 + index)"></div>

                            <!-- Input Teks & Media Opsi -->
                            <div class="flex-1 space-y-2">
                                <input type="text" :name="`options[${index}][text]`" x-model="opt.text" placeholder="Isi pilihan jawaban..." required
                                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">

                                <template x-if="questionType === 'multiple_choice'">
                                    <input type="file" :name="`options[${index}][media]`" class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                                </template>
                            </div>

                            <!-- Tombol Hapus Opsi -->
                            <template x-if="questionType === 'multiple_choice' && options.length > 2">
                                <button type="button" @click="removeOption(index)" class="p-1.5 text-slate-400 hover:text-red-600 transition flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 5. Pembahasan / Penjelasan Jawaban (Opsional) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pembahasan / Penjelasan Jawaban (Opsional)</label>
                <textarea name="explanation" rows="2" placeholder="Tuliskan penjelasan mengapa jawaban tersebut benar..."
                          class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('explanation') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition">
                    Simpan Soal
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function questionForm() {
        return {
            questionType: 'multiple_choice',
            correctOption: 0,
            options: [
                { text: '' },
                { text: '' },
                { text: '' },
                { text: '' }
            ],
            addOption() {
                this.options.push({ text: '' });
            },
            removeOption(index) {
                this.options.splice(index, 1);
                if (this.correctOption >= this.options.length) {
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
                } else if (this.options.length < 4) {
                    this.options = [
                        { text: '' },
                        { text: '' },
                        { text: '' },
                        { text: '' }
                    ];
                }
            }
        }
    }
</script>
@endsection