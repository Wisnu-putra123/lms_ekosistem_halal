@extends('layouts.app')

@section('title', 'Dashboard Siswa - LPP-JPH LMS')
@section('header_title', 'Dashboard Pembelajaran')

@section('content')
<div class="space-y-6">

    <!-- Welcome Card Banner (Deep Forest Green dengan Aksen Muted Gold) -->
    <div class="relative overflow-hidden rounded-2xl bg-[#123F35] p-6 sm:p-8 text-white shadow-md border border-[#E7C982]/40">
        <!-- Background Radial Gradient & Glow Effect -->
        <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
        <div class="absolute -right-10 -bottom-10 -z-0 h-48 w-48 rounded-full border border-[#E7C982]/20 blur-xl"></div>

        <div class="relative z-10 space-y-2 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wider bg-[#E7C982]/20 text-[#E7C982] border border-[#E7C982]/40 uppercase">
                Selamat Datang Kembali
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Halo, {{ Auth::user()->name }}</h1>
            <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
                Pantau tenggat waktu tugas, lanjutkan pelatihan aktif Anda, dan raih sertifikasi kompetensi Sistem Jaminan Halal Anda di sini.
            </p>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Stats 1: Pelatihan Saya -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 shadow-2xs flex items-center justify-between hover:border-[#28745B]/60 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#123F35]/70">Pelatihan Saya</span>
                <div class="text-2xl font-black text-[#123F35]">{{ $activeEnrollments->count() }} Kelas</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold border border-[#28745B]/30">
                <svg class="w-6 h-6 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>

        <!-- Stats 2: Tugas / Quiz Belum Tuntas -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 shadow-2xs flex items-center justify-between hover:border-[#28745B]/60 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#123F35]/70">Tugas / Quiz Belum Tuntas</span>
                <div class="text-2xl font-black {{ $pendingTasks->count() > 0 ? 'text-amber-600' : 'text-[#28745B]' }}">
                    {{ $pendingTasks->count() }} Item
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold border border-[#28745B]/30">
                <svg class="w-6 h-6 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Stats 3: Rata-Rata Progres Pelatihan -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 shadow-2xs flex items-center justify-between hover:border-[#28745B]/60 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#123F35]/70">Rata-Rata Progres Pelatihan</span>
                <div class="text-2xl font-black text-[#28745B]">{{ number_format($avgProgress ?? 0, 1) }}%</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#F7F5EE] text-[#123F35] flex items-center justify-center font-bold border border-[#28745B]/30">
                <svg class="w-6 h-6 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Layout Grid 2 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KIRI: Timeline / Deadlines Tugas & Quiz -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-300 p-6 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-300 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-[#123F35] flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Timeline & Tugas Mendatang</span>
                        </h2>
                        <p class="text-xs text-slate-500">Daftar tugas dan kuis yang membutuhkan tindakan Anda.</p>
                    </div>
                    <span class="px-3 py-1 bg-[#F7F5EE] text-[#123F35] rounded-xl text-xs font-bold border border-[#28745B]/30">
                        {{ $pendingTasks->count() }} Perlu Dikerjakan
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($pendingTasks as $task)
                        @php
                            $now = now();
                            $until = $task['available_until'] ? \Carbon\Carbon::parse($task['available_until']) : null;
                            $isUrgent = $until && $until->diffInHours($now) < 48 && $now->lessThan($until);
                            $isExpired = $until && $now->greaterThan($until);
                        @endphp

                        <div class="p-4 rounded-xl border transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ $isExpired ? 'bg-red-50/50 border-red-300' : ($isUrgent ? 'bg-amber-50/50 border-amber-300' : 'bg-[#F7F5EE]/40 border-slate-300') }}">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $task['type'] === 'quiz' ? 'bg-[#E7C982]/30 text-[#123F35] border border-[#E7C982]' : 'bg-[#28745B]/15 text-[#123F35] border border-[#28745B]/30' }}">
                                        {{ strtoupper($task['type']) }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-semibold truncate">{{ $task['course_title'] }} • Pertemuan {{ $task['meeting_number'] }}</span>
                                </div>
                                
                                <h3 class="text-xs font-bold text-[#123F35] truncate">{{ $task['title'] }}</h3>

                                <!-- Badge Keterangan Status Pengerjaan -->
                                <div class="pt-0.5">
                                    @if($task['status_badge'] === 'unassessed')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                                            {{ $task['status_label'] }}
                                        </span>
                                    @elseif($task['status_badge'] === 'failed')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            {{ $task['status_label'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700 border border-slate-300">
                                            {{ $task['status_label'] }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Keterangan Waktu Deadlines -->
                                <div class="flex items-center space-x-2 text-[10px] pt-1">
                                    @if($isExpired)
                                        <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-bold border border-red-300">Tenggat Lewat: {{ $until->format('d M Y, H:i') }}</span>
                                    @elseif($until)
                                        <span class="px-2 py-0.5 rounded {{ $isUrgent ? 'bg-amber-100 text-amber-900 font-bold border border-amber-300 animate-pulse' : 'bg-emerald-100 text-emerald-800 font-semibold border border-emerald-300' }}">
                                            Tenggat: {{ $until->format('d M Y, H:i') }} ({{ $until->diffForHumans() }})
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold border border-slate-300">Tanpa Batas Waktu</span>
                                    @endif
                                    <span class="text-slate-500">• KKM: {{ $task['passing_score'] }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end space-x-2 flex-shrink-0">
                                <a href="{{ $task['route'] }}" class="px-4 py-2 bg-[#123F35] hover:bg-[#28745B] text-white rounded-xl text-xs font-bold transition shadow-xs">
                                    {{ $task['status_badge'] === 'unassessed' ? 'Lihat Kiriman →' : ($task['status_badge'] === 'failed' ? 'Coba Lagi →' : 'Kerjakan →') }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-500 bg-[#F7F5EE]/40 rounded-xl border border-dashed border-slate-300">
                            Tidak terdapat tugas untuk saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- KANAN: Akses Cepat Pelatihan & Feedback Nilai Terbaru -->
        <div class="space-y-6">

            <!-- Card Pelatihan Saya -->
            <div class="bg-white rounded-2xl border border-slate-300 p-6 shadow-2xs space-y-4">
                <h2 class="text-sm font-bold text-[#123F35] border-b border-slate-300 pb-2.5">Pelatihan Saya</h2>

                <div class="space-y-3">
                    @forelse($activeEnrollments as $en)
                        <a href="{{ route('student.courses.show', $en->course_id) }}" class="p-3 bg-[#F7F5EE]/50 hover:bg-[#F7F5EE] rounded-xl border border-slate-300 flex items-center justify-between gap-3 transition group">
                            <div class="flex items-center space-x-3 min-w-0">
                                @if($en->course->thumbnail)
                                    <img src="{{ asset('storage/' . $en->course->thumbnail->file_path) }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 border border-slate-300">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-[#28745B]/15 text-[#123F35] flex items-center justify-center font-bold text-xs flex-shrink-0 border border-[#28745B]/30">
                                        {{ substr($en->course->code, 0, 3) }}
                                    </div>
                                @endif
                                <div class="min-w-0 space-y-1">
                                    <div class="text-xs font-bold text-[#123F35] group-hover:text-[#28745B] truncate transition flex items-center gap-1.5">
                                        <span class="truncate">{{ $en->course->title }}</span>
                                        @if($en->status === 'completed')
                                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black bg-emerald-100 text-emerald-800 flex-shrink-0 border border-emerald-300">LULUS</span>
                                        @endif
                                    </div>
                                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden w-28 border border-slate-300/60">
                                        <div class="bg-[#28745B] h-full rounded-full" style="width: {{ $en->progress->progress_percentage ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <span class="text-[10px] font-extrabold text-[#28745B] flex-shrink-0">{{ number_format($en->progress->progress_percentage ?? 0, 0) }}%</span>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 italic">Anda belum terdaftar dalam kelas apapun.</p>
                    @endforelse
                </div>
            </div>

            <!-- Card Feedback Pengajar Terbaru -->
            <div class="bg-white rounded-2xl border border-slate-300 p-6 shadow-2xs space-y-4">
                <h2 class="text-sm font-bold text-[#123F35] border-b border-slate-300 pb-2.5">Nilai & Feedback Terbaru</h2>

                <div class="space-y-3">
                    @forelse($recentFeedbacks as $fb)
                        <div class="p-3 bg-[#F7F5EE]/50 rounded-xl border border-slate-300 space-y-1">
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="font-semibold text-slate-700 truncate max-w-[140px]">{{ $fb->assignment->title }}</span>
                                <span class="font-extrabold px-2 py-0.5 rounded border {{ $fb->score >= $fb->assignment->passing_score ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-red-100 text-red-800 border-red-300' }}">
                                    Skor: {{ number_format($fb->score, 0) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-700 italic">
                                "{{ $fb->feedback ? \Str::limit($fb->feedback, 60) : 'Tugas telah dinilai oleh pengajar.' }}"
                            </p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada feedback nilai terbaru.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection