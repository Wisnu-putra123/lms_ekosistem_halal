@extends('layouts.app')

@section('title', 'Mengerjakan Quiz - ' . $assignment->title)

@section('content')
<div x-data="quizTimer()" x-init="initTimer()" class="max-w-5xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header & Floating Timer Sticky Bar (Gradasi Hijau Tua) -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] text-white rounded-2xl p-4 sm:p-5 shadow-md flex items-center justify-between gap-3 top-4 z-40 border border-[#28745B]/40">
        <div class="min-w-0">
            <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase backdrop-blur-xs">
                Attempt #{{ $attempt->attempt_number }}
            </span>
            <h1 class="text-sm sm:text-base font-bold text-white truncate max-w-md mt-1">{{ $assignment->title }}</h1>
        </div>

        @if($attempt->expires_at)
            <div class="flex items-center space-x-2 bg-black/25 px-3.5 py-2 rounded-xl border border-[#E7C982]/40 backdrop-blur-xs flex-shrink-0">
                <svg class="w-4 h-4 text-[#E7C982] animate-pulse flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-xs sm:text-sm font-mono font-extrabold text-[#E7C982]" x-text="timeDisplay">00:00:00</div>
            </div>
        @else
            <span class="px-3 py-1.5 rounded-xl text-xs bg-white/10 text-emerald-100 border border-white/20 font-semibold flex-shrink-0">
                Tanpa Batas Waktu
            </span>
        @endif
    </div>

    <form id="quizForm" action="{{ route('student.quizzes.submit', [$assignment->id, $attempt->id]) }}" method="POST" x-data="quizEngine()" class="grid grid-cols-1 lg:grid-cols-4 gap-5 sm:gap-6">
        @csrf

        <!-- KANAN: Navigasi Nomor Soal (Desktop & Mobile Grid) -->
        <div class="lg:order-2 lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
                <!-- Heading Container bg-[#28745B] -->
                <div class="bg-[#28745B] px-4 py-3 border-b border-slate-300 text-white">
                    <h3 class="text-xs font-bold uppercase tracking-wider">Navigasi Soal</h3>
                </div>
                
                <div class="p-4 sm:p-5 space-y-4">
                    <div class="grid grid-cols-5 gap-2">
                        <template x-for="(q, idx) in questions" :key="q.id">
                            <button type="button" @click="goToQuestion(idx)"
                                    :class="{
                                        'bg-[#123F35] text-white font-extrabold border-[#123F35] shadow-2xs': currentIndex === idx,
                                        'bg-[#28745B]/15 text-[#28745B] font-bold border-[#28745B]/40': currentIndex !== idx && answers[q.id],
                                        'bg-[#F7F5EE]/60 text-slate-700 border-slate-300 hover:bg-slate-100': currentIndex !== idx && !answers[q.id]
                                    }"
                                    class="h-9 w-full rounded-xl border text-xs flex items-center justify-center transition cursor-pointer">
                                <span x-text="idx + 1"></span>
                            </button>
                        </template>
                    </div>

                    <div class="pt-3 border-t border-slate-300 text-[11px] space-y-2 text-slate-600 font-medium">
                        <div class="flex items-center space-x-2">
                            <span class="w-3.5 h-3.5 bg-[#123F35] rounded-md inline-block flex-shrink-0"></span>
                            <span>Sedang Aktif</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-3.5 h-3.5 bg-[#28745B]/20 border border-[#28745B]/40 rounded-md inline-block flex-shrink-0"></span>
                            <span>Sudah Dijawab</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-3.5 h-3.5 bg-[#F7F5EE] border border-slate-300 rounded-md inline-block flex-shrink-0"></span>
                            <span>Belum Dijawab</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Selesai & Kumpulkan (Warna #28745B) -->
            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengumpulkan seluruh jawaban Quiz ini?')" class="w-full bg-[#28745B] hover:bg-[#123F35] text-white font-extrabold py-3 px-4 rounded-xl text-xs shadow-2xs transition duration-150 flex items-center justify-center space-x-2 cursor-pointer">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Selesai & Kumpulkan</span>
            </button>
        </div>

        <!-- KIRI: Lembar Soal Aktif (3 Kolom Grid) -->
        <div class="lg:order-1 lg:col-span-3">
            <template x-for="(aq, idx) in questions" :key="aq.id">
                <div x-show="currentIndex === idx" class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
                    
                    <!-- Heading Container Lembar Soal bg-[#28745B] -->
                    <div class="bg-[#28745B] px-4 sm:px-5 py-3.5 border-b border-slate-300 flex flex-wrap items-center justify-between gap-2 text-white">
                        <span class="px-2.5 py-1 bg-[#123F35] text-white border border-white/15 rounded-lg font-bold text-xs">
                            Soal No. <span x-text="idx + 1"></span> / <span x-text="questions.length"></span>
                        </span>
                        
                        <!-- Status Auto-Save Indicator -->
                        <div class="flex items-center space-x-1.5 text-xs font-bold">
                            <template x-if="savingState[aq.id] === 'saving'">
                                <span class="text-[#E7C982] animate-pulse flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Menyimpan...
                                </span>
                            </template>
                            <template x-if="savingState[aq.id] === 'saved'">
                                <span class="text-[#E7C982] flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Tersimpan Otomatis
                                </span>
                            </template>
                            <template x-if="!savingState[aq.id] && answers[aq.id]">
                                <span class="text-emerald-100 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Tersimpan
                                </span>
                            </template>
                            <template x-if="!answers[aq.id] && savingState[aq.id] !== 'saving'">
                                <span class="text-emerald-100/75 font-medium">Belum Dijawab</span>
                            </template>
                        </div>
                    </div>

                    <!-- Body Soal & Opsi Jawaban -->
                    <div class="p-4 sm:p-8 space-y-6">
                        <!-- Pertanyaan Soal -->
                        <div class="space-y-3">
                            <div class="text-xs sm:text-sm font-bold text-[#123F35] leading-relaxed whitespace-pre-line break-words" x-text="aq.question.question_text"></div>

                            <!-- Media Soal (Gambar / Audio / Video / PDF) -->
                            <template x-if="aq.question.media">
                                <div class="pt-2">
                                    <!-- Gambar -->
                                    <template x-if="getMediaKind(aq.question.media) === 'image'">
                                        <div class="p-2.5 sm:p-3 bg-[#F7F5EE]/50 rounded-xl border border-slate-300 inline-block max-w-full">
                                            <a :href="'/storage/' + aq.question.media.file_path" target="_blank" rel="noopener noreferrer">
                                                <img :src="'/storage/' + aq.question.media.file_path"
                                                     :alt="aq.question.media.file_name || 'Media Soal'"
                                                     class="w-full sm:w-auto max-h-72 rounded-lg object-contain shadow-2xs">
                                            </a>
                                        </div>
                                    </template>

                                    <!-- Audio -->
                                    <template x-if="getMediaKind(aq.question.media) === 'audio'">
                                        <div class="p-3 bg-[#F7F5EE]/60 rounded-xl border border-slate-300 max-w-lg space-y-1.5">
                                            <div class="flex items-center space-x-1.5 text-[11px] font-bold text-[#123F35]">
                                                <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                                </svg>
                                                <span class="truncate" x-text="aq.question.media.file_name || 'Audio Soal'"></span>
                                            </div>
                                            <audio controls preload="metadata" class="w-full h-9">
                                                <source :src="'/storage/' + aq.question.media.file_path" :type="aq.question.media.mime_type">
                                                Browser Anda tidak mendukung pemutar audio.
                                            </audio>
                                        </div>
                                    </template>

                                    <!-- Video -->
                                    <template x-if="getMediaKind(aq.question.media) === 'video'">
                                        <div class="p-2.5 bg-[#F7F5EE]/60 rounded-xl border border-slate-300 max-w-xl space-y-1.5">
                                            <video controls preload="metadata" class="w-full max-h-72 rounded-lg bg-black">
                                                <source :src="'/storage/' + aq.question.media.file_path" :type="aq.question.media.mime_type">
                                                Browser Anda tidak mendukung pemutar video.
                                            </video>
                                            <p class="text-[10px] text-slate-500 truncate" x-text="aq.question.media.file_name"></p>
                                        </div>
                                    </template>

                                    <!-- Dokumen / PDF -->
                                    <template x-if="getMediaKind(aq.question.media) === 'document'">
                                        <a :href="'/storage/' + aq.question.media.file_path" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center space-x-2 px-3.5 py-2 bg-[#d1e4da] border border-slate-300 rounded-xl text-xs font-bold text-[#123F35] hover:bg-[#28745B] hover:text-white transition">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="truncate max-w-xs sm:max-w-md" x-text="'Buka Lampiran (' + (aq.question.media.file_name || 'Dokumen') + ')'"></span>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Opsi Pilihan Jawaban (Teks + Gambar / Audio / Video) -->
                        <div class="space-y-3 pt-1">
                            <template x-for="(opt, optIdx) in aq.question.options" :key="opt.id">
                                <label class="flex items-start p-3.5 rounded-xl border cursor-pointer transition select-none"
                                       :class="Number(answers[aq.id]) === Number(opt.id) ? 'bg-emerald-100/80 border-[#28745B] text-[#123F35] font-bold shadow-2xs' : 'bg-[#F7F5EE]/40 border-slate-300 text-slate-700 hover:bg-slate-100/80'">
                                    <input type="radio" :name="'answers[' + aq.id + ']'" :value="opt.id"
                                           x-model="answers[aq.id]"
                                           @change="saveAnswer(aq.id, opt.id)"
                                           class="mt-0.5 text-[#28745B] border-slate-300 focus:ring-[#28745B] w-4 h-4 flex-shrink-0 cursor-pointer">
                                    
                                    <div class="ml-3 flex-1 min-w-0 space-y-2.5">
                                        <div class="flex items-start space-x-2">
                                            <span class="font-extrabold text-xs flex-shrink-0" x-text="String.fromCharCode(65 + optIdx) + '.'"></span>
                                            <template x-if="opt.option_text && opt.option_text.trim() !== ''">
                                                <span class="text-xs leading-relaxed whitespace-pre-line break-words" x-text="opt.option_text"></span>
                                            </template>
                                        </div>

                                        <!-- Media Opsi Jawaban (Jika Ada) -->
                                        <template x-if="opt.media">
                                            <div @click.stop class="cursor-default">
                                                <!-- Gambar Opsi -->
                                                <template x-if="getMediaKind(opt.media) === 'image'">
                                                    <div class="p-2 bg-white rounded-lg border border-slate-300 inline-block max-w-full">
                                                        <img :src="'/storage/' + opt.media.file_path"
                                                             :alt="opt.media.file_name || 'Media Opsi'"
                                                             class="w-full sm:w-auto max-h-44 rounded-md object-contain">
                                                    </div>
                                                </template>

                                                <!-- Audio Opsi -->
                                                <template x-if="getMediaKind(opt.media) === 'audio'">
                                                    <div class="p-2.5 bg-white rounded-lg border border-slate-300 max-w-md space-y-1">
                                                        <p class="text-[10px] font-semibold text-slate-500 truncate" x-text="opt.media.file_name || 'Audio Opsi'"></p>
                                                        <audio controls preload="metadata" class="w-full h-8">
                                                            <source :src="'/storage/' + opt.media.file_path" :type="opt.media.mime_type">
                                                            Browser Anda tidak mendukung pemutar audio.
                                                        </audio>
                                                    </div>
                                                </template>

                                                <!-- Video Opsi -->
                                                <template x-if="getMediaKind(opt.media) === 'video'">
                                                    <div class="p-2 bg-white rounded-lg border border-slate-300 max-w-md space-y-1">
                                                        <video controls preload="metadata" class="w-full max-h-48 rounded-md bg-black">
                                                            <source :src="'/storage/' + opt.media.file_path" :type="opt.media.mime_type">
                                                            Browser Anda tidak mendukung pemutar video.
                                                        </video>
                                                        <p class="text-[10px] font-normal text-slate-500 truncate" x-text="opt.media.file_name"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <!-- Tombol Navigasi Next / Prev -->
                        <div class="pt-4 border-t border-slate-300 flex items-center justify-between gap-2">
                            <button type="button" @click="goToQuestion(currentIndex - 1)" x-show="currentIndex > 0" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 hover:text-[#123F35] hover:bg-slate-100 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                                &larr; Soal Sebelumnya
                            </button>
                            <div x-show="currentIndex === 0"></div>

                            <button type="button" @click="goToQuestion(currentIndex + 1)" x-show="currentIndex < questions.length - 1" class="px-4 py-2 bg-[#28745B] hover:bg-[#123F35] text-white rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                                Soal Berikutnya &rarr;
                            </button>
                        </div>
                    </div>

                </div>
            </template>
        </div>

    </form>

</div>

<script>
    function quizTimer() {
        return {
            expiresAt: '{{ $attempt->expires_at ? $attempt->expires_at->toIso8601String() : "" }}',
            timeDisplay: '00:00:00',
            timerInterval: null,
            initTimer() {
                if (!this.expiresAt) return;
                
                const targetTime = new Date(this.expiresAt).getTime();

                this.timerInterval = setInterval(() => {
                    const now = new Date().getTime();
                    const diff = targetTime - now;

                    if (diff <= 0) {
                        clearInterval(this.timerInterval);
                        this.timeDisplay = "00:00:00";
                        alert("Waktu pengerjaan Quiz telah habis!");
                        document.getElementById('quizForm').submit();
                    } else {
                        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                        this.timeDisplay = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    }
                }, 1000);
            }
        }
    }

    function quizEngine() {
        return {
            currentIndex: 0,
            questions: @json($attemptQuestions),
            answers: @json($attemptQuestions->mapWithKeys(fn($item) => [(string)$item->id => $item->selected_option_id])),
            savingState: {},
            
            init() {
                window.addEventListener('beforeunload', (e) => {
                    const isSaving = Object.values(this.savingState).includes('saving');
                    if (isSaving) {
                        e.preventDefault();
                        e.returnValue = 'Jawaban Anda sedang disimpan. Yakin ingin keluar?';
                    }
                });
            },

            getMediaKind(media) {
                if (!media) return null;
                if (media.type && ['image', 'audio', 'video', 'document'].includes(media.type)) {
                    return media.type;
                }
                const mime = (media.mime_type || '').toLowerCase();
                if (mime.includes('image')) return 'image';
                if (mime.includes('audio')) return 'audio';
                if (mime.includes('video')) return 'video';
                return 'document';
            },

            pauseAllMedia() {
                document.querySelectorAll('audio, video').forEach(el => {
                    if (!el.paused) el.pause();
                });
            },

            goToQuestion(index) {
                this.pauseAllMedia();
                this.currentIndex = index;
            },

            saveAnswer(attemptQuestionId, optionId) {
                this.savingState[attemptQuestionId] = 'saving';

                const payload = JSON.stringify({
                    attempt_question_id: attemptQuestionId,
                    selected_option_id: optionId
                });

                fetch('{{ route("student.quizzes.saveAnswer", [$assignment->id, $attempt->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: payload,
                    keepalive: true
                })
                .then(res => res.json())
                .then(data => {
                    if (data.expired) {
                        alert("Waktu kuis telah habis.");
                        document.getElementById('quizForm').submit();
                    } else if (data.success) {
                        this.savingState[attemptQuestionId] = 'saved';
                    }
                })
                .catch(err => {
                    console.error("Gagal auto-save jawaban:", err);
                    delete this.savingState[attemptQuestionId];
                });
            }
        }
    }
</script>
@endsection