@extends('layouts.app')

@section('title', $assignment->title . ' - Ekosistem Halal')
@section('header_title', 'Detail & Hasil Quiz')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


<div class="max-w-5xl mx-auto space-y-6" x-data="{ activeTab: 'attempts' }">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.courses.show', $assignment->course_id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-amber-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>

        <div class="flex items-center space-x-2">
            <a href="{{ route('teacher.quizzes.edit', $assignment->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition">
                Edit Quiz
            </a>
            <form action="{{ route('teacher.quizzes.destroy', $assignment->id) }}" method="POST" onsubmit="return confirm('Hapus Quiz ini beserta seluruh bank soalnya?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition">
                    Hapus Quiz
                </button>
            </form>
        </div>
    </div>

    <!-- Flash Notification -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Card Konfigurasi Ringkas Quiz -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-amber-100 text-amber-800 uppercase">
                    Pertemuan {{ $assignment->meeting->meeting_number }}
                </span>
                <h1 class="text-xl font-bold text-slate-800 mt-1">{{ $assignment->title }}</h1>
            </div>
            <div>
                @if($assignment->status === 'published')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Published</span>
                @elseif($assignment->status === 'draft')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Draft</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">Closed</span>
                @endif
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Durasi Timer</span>
                <div class="text-xs font-bold text-slate-800 mt-1">
                    {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' Menit' : 'Tanpa Batas' }}
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Soal Diuji / Bank</span>
                <div class="text-xs font-bold text-amber-700 mt-1">
                    {{ $quiz->question_count }} / {{ $quiz->questions->count() }} Soal
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Poin / Soal</span>
                <div class="text-xs font-bold text-blue-700 mt-1">
                    ± {{ $pointPerQuestion }} Poin
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Passing Grade</span>
                <div class="text-xs font-bold text-emerald-700 mt-1">{{ number_format($assignment->passing_score, 0) }} / {{ number_format($assignment->max_score, 0) }}</div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Batas Attempt</span>
                <div class="text-xs font-bold text-slate-800 mt-1">
                    {{ $assignment->max_attempts ? $assignment->max_attempts . 'x Attempt' : 'Tanpa Batas' }}
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Status Acak</span>
                <div class="text-[10px] font-bold text-slate-700 mt-1">
                    Soal: {{ $quiz->shuffle_questions ? 'Ya' : 'Tidak' }} | Opsi: {{ $quiz->shuffle_answers ? 'Ya' : 'Tidak' }}
                </div>
            </div>
        </div>

        @if($quiz->questions->count() < $quiz->question_count)
            <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-center justify-between">
                <span>* Jumlah soal di bank soal (<strong>{{ $quiz->questions->count() }}</strong>) masih kurang dari target soal yang diuji (<strong>{{ $quiz->question_count }}</strong>). Mohon tambahkan soal baru.</span>
            </div>
        @endif
    </div>

    <!-- TAB HEADER SWITCHER (Bank soal diletakkan di tombol pertama) -->
    <div class="flex items-center space-x-2 border-b border-slate-200 pb-1 overflow-x-auto">
        <button type="button" @click="activeTab = 'attempts'" 
                :class="activeTab === 'attempts' ? 'bg-amber-500 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                class="px-5 py-2.5 rounded-xl text-xs transition flex items-center space-x-2 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <!-- Menggunakan total() untuk mendapatkan jumlah data secara keseluruhan -->
            <span>Hasil & Nilai Siswa ({{ $quizAttempts->total() }})</span>
        </button>

        <button type="button" @click="activeTab = 'questions'" 
                :class="activeTab === 'questions' ? 'bg-amber-500 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                class="px-5 py-2.5 rounded-xl text-xs transition flex items-center space-x-2 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Bank Soal Quiz ({{ $quiz->questions->count() }})</span>
        </button>
    </div>

    <!-- TAB DAFTAR BANK SOAL (DITAMPILKAN PERTAMA) -->
    <div x-show="activeTab === 'questions'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-bold text-slate-800">Daftar Bank Soal ({{ $quiz->questions->count() }})</h2>
                <p class="text-xs text-slate-400">Kelola soal dan kunci jawaban yang digunakan untuk quiz ini.</p>
            </div>

            <a href="{{ route('teacher.quizzes.questions.create', $assignment->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Soal Baru</span>
            </a>
        </div>

        <div class="space-y-4">
            @forelse($quiz->questions as $index => $q)
                <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-3">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200/60 pb-3">
                        <div class="flex items-start space-x-3">
                            <span class="w-6 h-6 rounded-lg bg-amber-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <div class="text-xs font-bold text-slate-800 leading-relaxed">{!! nl2br(e($q->question_text)) !!}</div>
                                @if($q->media)
                                    <div class="mt-2">
                                        @if(str_contains($q->media->mime_type, 'image'))
                                            <img src="{{ asset('storage/' . $q->media->file_path) }}" alt="Soal Media" class="max-h-40 rounded-lg border border-slate-200 object-cover">
                                        @else
                                            <a href="{{ asset('storage/' . $q->media->file_path) }}" download class="text-xs font-bold text-amber-600 hover:underline">
                                                📁 Lampiran Soal ({{ $q->media->file_name }})
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <a href="{{ route('teacher.quizzes.questions.edit', [$assignment->id, $q->id]) }}" class="p-1.5 bg-amber-100 text-amber-700 hover:bg-amber-600 hover:text-white rounded-lg transition" title="Edit Soal">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('teacher.quizzes.questions.destroy', [$assignment->id, $q->id]) }}" method="POST" onsubmit="return confirm('Hapus soal ini dari bank soal?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition" title="Hapus Soal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Opsi Jawaban -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pl-9">
                        @foreach($q->options as $optIndex => $opt)
                            <div class="p-2.5 rounded-xl border text-xs flex items-center justify-between {{ $opt->is_correct ? 'bg-emerald-50 border-emerald-300 font-semibold text-emerald-900' : 'bg-white border-slate-200 text-slate-700' }}">
                                <div class="flex items-center space-x-2 overflow-hidden pr-2">
                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold {{ $opt->is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }}">
                                        {{ chr(65 + $optIndex) }}
                                    </span>
                                    <span class="truncate">{!! e($opt->option_text) !!}</span>
                                </div>
                                @if($opt->is_correct)
                                    <span class="text-[10px] bg-emerald-600 text-white px-2 py-0.5 rounded-md font-bold flex-shrink-0">Kunci Jawaban</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    Belum ada soal yang ditambahkan pada bank soal Quiz ini. Silakan klik tombol "+ Tambah Soal Baru".
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB TABEL PENILAIAN / ATTEMPTS SISWA -->
    <div x-show="activeTab === 'attempts'" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        
        <!-- Filter & Sort Form -->
        <form method="GET" action="{{ route('teacher.quizzes.show', $assignment->id) }}" class="flex flex-col lg:flex-row gap-3 p-4 bg-slate-50 border border-slate-100 rounded-xl">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="border border-slate-300 rounded-lg px-3 py-2 text-xs w-full lg:w-1/4 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
            
            <select name="enrollment_status" class="border border-slate-300 rounded-lg px-3 py-2 text-xs w-full lg:w-auto outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                <option value="">Semua Status Enrollment</option>
                <option value="active" {{ request('enrollment_status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ request('enrollment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            
            <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 text-xs w-full lg:w-auto outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                <option value="">Semua Status Attempt</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted (Selesai)</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress (Mengerjakan)</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired (Waktu Habis)</option>
            </select>
            
            <select name="sort" class="border border-slate-300 rounded-lg px-3 py-2 text-xs w-full lg:w-auto outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru Dimulai</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama Dimulai</option>
                <option value="score_high" {{ request('sort') == 'score_high' ? 'selected' : '' }}>Nilai Tertinggi</option>
                <option value="score_low" {{ request('sort') == 'score_low' ? 'selected' : '' }}>Nilai Terendah</option>
            </select>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-amber-500 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-amber-600 transition">Filter</button>
                <a href="{{ route('teacher.quizzes.show', $assignment->id) }}" class="bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-lg text-xs font-semibold hover:bg-slate-100 transition flex items-center justify-center">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase">
                        <th class="py-3 px-4">Siswa & Enrollment</th>
                        <th class="py-3 px-4">Attempt Ke-</th>
                        <th class="py-3 px-4">Waktu Selesai</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Nilai</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($quizAttempts as $attempt)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">{{ $attempt->enrollment->user->name ?? 'Siswa Tidak Ditemukan' }}</div>
                                <div class="text-[10px] mt-0.5 flex items-center space-x-1">
                                    <span class="text-slate-400">Status ID #{{ $attempt->enrollment_id }}:</span>
                                    <span class="uppercase font-bold {{ $attempt->enrollment->status === 'active' ? 'text-emerald-600' : 'text-slate-500' }}">
                                        {{ $attempt->enrollment->status }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-md font-bold text-[11px]">
                                    Attempt #{{ $attempt->attempt_number }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($attempt->status === 'submitted' || $attempt->status === 'expired')
                                    @if($attempt->score >= $assignment->passing_score)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Lulus</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">Belum Lulus</span>
                                    @endif
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Sedang Mengerjakan</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                {{ $attempt->score !== null ? number_format($attempt->score, 2) : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- Tombol Lihat Detail Jawaban Siswa -->
                                    <a href="{{ route('teacher.quizzes.attempts.show', $attempt->id) }}" 
                                    class="p-1.5 bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white rounded-lg transition" 
                                    title="Lihat Detail Jawaban Siswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <!-- Tombol Hapus Attempt -->
                                    <form action="{{ route('teacher.quizzes.attempts.destroy', $attempt->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus attempt pengerjaan kuis siswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition" title="Hapus Attempt">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-xs text-slate-400 font-semibold bg-slate-50/50 rounded-xl">
                                Tidak ada data pengerjaan kuis siswa yang cocok dengan filter tersebut.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- NAVIGASI PAGINASI -->
        @if($quizAttempts->hasPages())
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div>
                    Menampilkan <span class="font-bold text-slate-800">{{ $quizAttempts->firstItem() }}</span> - 
                    <span class="font-bold text-slate-800">{{ $quizAttempts->lastItem() }}</span> dari 
                    <span class="font-bold text-slate-800">{{ $quizAttempts->total() }}</span> attempt siswa
                </div>
                <div>
                    {{ $quizAttempts->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection