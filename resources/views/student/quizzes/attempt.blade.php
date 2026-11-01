@extends('layouts.app')

@section('title', 'Mengerjakan Quiz - ' . $assignment->title)

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div x-data="quizTimer()" x-init="initTimer()" class="max-w-5xl mx-auto space-y-6">

    <!-- Header & Floating Timer Sticky Bar -->
    <div class="bg-[#123F35] text-white rounded-2xl p-4 sm:p-5 shadow-lg flex items-center justify-between sticky top-4 z-40 border border-[#E7C982]/20">
        <div>
            <span class="text-[10px] font-bold tracking-wider text-[#E7C982] uppercase">Attempt #{{ $attempt->attempt_number }}</span>
            <h1 class="text-sm sm:text-base font-bold truncate max-w-md">{{ $assignment->title }}</h1>
        </div>

        @if($attempt->expires_at)
            <div class="flex items-center space-x-2 bg-black/20 px-3.5 py-1.5 rounded-xl border border-[#E7C982]/30 backdrop-blur-sm">
                <svg class="w-4 h-4 text-[#E7C982] animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-xs sm:text-sm font-mono font-bold text-[#E7C982]" x-text="timeDisplay">00:00:00</div>
            </div>
        @else
            <span class="text-xs text-emerald-100/70 font-semibold">Tanpa Batas Waktu</span>
        @endif
    </div>

    <form id="quizForm" action="{{ route('student.quizzes.submit', [$assignment->id, $attempt->id]) }}" method="POST" x-data="quizEngine()" class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        @csrf

        <!-- KANAN: Navigasi Nomor Soal (Desktop & Mobile Grid) -->
        <div class="lg:order-2 lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl border border-[#28745B]/15 shadow-sm p-5 space-y-3">
                <h3 class="text-xs font-bold text-[#123F35] uppercase tracking-wider border-b border-slate-100 pb-2">Navigasi Soal</h3>
                
                <div class="grid grid-cols-5 gap-2">
                    <template x-for="(q, idx) in questions" :key="q.id">
                        <button type="button" @click="currentIndex = idx"
                                :class="{
                                    'bg-[#123F35] text-white font-bold border-[#123F35] shadow-sm': currentIndex === idx,
                                    'bg-[#28745B]/10 text-[#28745B] font-bold border-[#28745B]/30': currentIndex !== idx && answers[q.id],
                                    'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100': currentIndex !== idx && !answers[q.id]
                                }"
                                class="h-9 w-full rounded-xl border text-xs flex items-center justify-center transition">
                            <span x-text="idx + 1"></span>
                        </button>
                    </template>
                </div>

                <div class="pt-2 border-t border-slate-100 text-[10px] space-y-1.5 text-slate-600">
                    <div class="flex items-center space-x-2"><span class="w-3 h-3 bg-[#123F35] rounded-sm inline-block"></span><span>Aktif</span></div>
                    <div class="flex items-center space-x-2"><span class="w-3 h-3 bg-[#28745B]/20 border border-[#28745B]/40 rounded-sm inline-block"></span><span>Sudah Dijawab</span></div>
                    <div class="flex items-center space-x-2"><span class="w-3 h-3 bg-slate-50 border border-slate-200 rounded-sm inline-block"></span><span>Belum Dijawab</span></div>
                </div>
            </div>

            <!-- Tombol Selesai & Kumpulkan -->
            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengumpulkan seluruh jawaban Quiz ini?')" class="w-full bg-[#123F35] hover:bg-[#28745B] text-white font-bold py-3 rounded-xl text-xs shadow-md transition duration-150">
                Selesai & Kumpulkan
            </button>
        </div>

        <!-- KIRI: Lembar Soal Aktif (3 Kolom Grid) -->
        <div class="lg:order-1 lg:col-span-3 space-y-6">
            <template x-for="(aq, idx) in questions" :key="aq.id">
                <div x-show="currentIndex === idx" class="bg-white rounded-2xl border border-[#28745B]/15 shadow-sm p-6 sm:p-8 space-y-6">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <span class="px-3 py-1 bg-[#F7F5EE] text-[#123F35] border border-[#28745B]/20 rounded-lg font-bold text-xs">
                            Soal No. <span x-text="idx + 1"></span> / <span x-text="questions.length"></span>
                        </span>
                        
                        <!-- Status Auto-Save Indicator -->
                        <div class="flex items-center space-x-1.5 text-xs font-semibold">
                            <template x-if="savingState[aq.id] === 'saving'">
                                <span class="text-amber-600 animate-pulse flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Menyimpan...
                                </span>
                            </template>
                            <template x-if="savingState[aq.id] === 'saved'">
                                <span class="text-[#28745B] flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Tersimpan Otomatis
                                </span>
                            </template>
                            <template x-if="!savingState[aq.id] && answers[aq.id]">
                                <span class="text-[#28745B]">Tersimpan</span>
                            </template>
                            <template x-if="!answers[aq.id]">
                                <span class="text-slate-400">Belum Dijawab</span>
                            </template>
                        </div>
                    </div>

                    <!-- Pertanyaan Soal -->
                    <div class="space-y-3">
                        <div class="text-sm font-semibold text-slate-800 leading-relaxed" x-html="aq.question.question_text"></div>

                        <!-- Media Soal (Jika Ada) -->
                        <template x-if="aq.question.media">
                            <div class="pt-2">
                                <template x-if="aq.question.media.mime_type.includes('image')">
                                    <img :src="'/storage/' + aq.question.media.file_path" class="max-h-60 rounded-xl border border-slate-200 object-cover shadow-xs">
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Opsi Pilihan Jawaban -->
                    <div class="space-y-2.5 pt-2">
                        <template x-for="(opt, optIdx) in aq.question.options" :key="opt.id">
                            <label class="flex items-start p-3.5 rounded-xl border cursor-pointer transition select-none"
                                   :class="answers[aq.id] === opt.id ? 'bg-[#28745B]/10 border-[#28745B] text-[#123F35] font-semibold shadow-xs' : 'bg-[#F7F5EE]/40 border-slate-200 text-slate-700 hover:bg-slate-100'">
                                <input type="radio" :name="'answers[' + aq.id + ']'" :value="opt.id"
                                       x-model="answers[aq.id]"
                                       @change="saveAnswer(aq.id, opt.id)"
                                       class="mt-0.5 text-[#28745B] focus:ring-[#28745B] w-4 h-4">
                                <div class="ml-3 flex-1 flex items-start space-x-2">
                                    <span class="font-bold text-xs" x-text="String.fromCharCode(65 + optIdx) + '.'"></span>
                                    <span class="text-xs leading-relaxed" x-text="opt.option_text"></span>
                                </div>
                            </label>
                        </template>
                    </div>

                    <!-- Tombol Navigasi Next / Prev -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" @click="currentIndex--" x-show="currentIndex > 0" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-200 transition">
                            ← Soal Sebelumnya
                        </button>
                        <div x-show="currentIndex === 0"></div>

                        <button type="button" @click="currentIndex++" x-show="currentIndex < questions.length - 1" class="px-4 py-2 bg-[#123F35] text-white rounded-xl text-xs font-semibold hover:bg-[#28745B] transition">
                            Soal Berikutnya →
                        </button>
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
                // Peringatan jika siswa mencoba menutup / refresh tab saat kuis berlangsung
                window.addEventListener('beforeunload', (e) => {
                    const isSaving = Object.values(this.savingState).includes('saving');
                    if (isSaving) {
                        e.preventDefault();
                        e.returnValue = 'Jawaban Anda sedang disimpan. Yakin ingin keluar?';
                    }
                });
            },

            saveAnswer(attemptQuestionId, optionId) {
                this.savingState[attemptQuestionId] = 'saving';

                const payload = JSON.stringify({
                    attempt_question_id: attemptQuestionId,
                    selected_option_id: optionId
                });

                // Gunakan keepalive: true agar request HTTP tidak terputus jika halaman ditutup/ditinggalkan
                fetch('{{ route("student.quizzes.saveAnswer", [$assignment->id, $attempt->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: payload,
                    keepalive: true // MENCEGAH DROP REQUEST SAAT KELUAR TAB/HALAMAN
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