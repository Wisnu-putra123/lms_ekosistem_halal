@extends('layouts.app')

@section('title', 'Detail Pelatihan - ' . $course->title)
@section('header_title', 'Detail Pelatihan')

@section('content')
@php
    $categoryLabels = [
        'penyelia_halal' => 'Penyelia Halal',
        'juleha'         => 'Juleha (Juru Sembelih Halal)',
        'auditor_halal'  => 'Auditor Halal',
    ];
@endphp

<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.courses.index') }}"
           class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Pelatihan</span>
        </a>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.courses.edit', $course) }}"
               class="inline-flex items-center space-x-1.5 bg-[#123F35] hover:bg-[#28745B] text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Pelatihan &amp; Instruktur</span>
            </a>

            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus pelatihan {{ addslashes($course->title) }}? Tindakan ini tidak dapat dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center space-x-1.5 bg-red-50 hover:bg-red-600 text-red-700 hover:text-white border border-red-300 px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer">
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Feedback -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-semibold flex items-center space-x-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-50 border border-red-300 text-red-800 text-xs font-semibold flex items-center space-x-2.5 shadow-2xs">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Course Hero Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] p-6 sm:p-8 text-white shadow-md border border-[#E7C982]/40">
        <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
        <div class="absolute -right-10 -bottom-10 -z-0 h-48 w-48 rounded-full border border-[#E7C982]/20 blur-xl"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-mono text-xs font-extrabold bg-[#E7C982] text-[#123F35]">
                        {{ $course->code }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/15 text-white border border-white/25">
                        {{ $categoryLabels[$course->category] ?? ucfirst(str_replace('_', ' ', $course->category)) }}
                    </span>
                    @if($course->status === 'published')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-400/20 text-emerald-200 border border-emerald-300/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Published
                        </span>
                    @elseif($course->status === 'draft')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-400/20 text-amber-200 border border-amber-300/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            Draft
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-slate-400/20 text-slate-200 border border-slate-300/30">
                            Archived
                        </span>
                    @endif
                </div>

                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white leading-snug">
                    {{ $course->title }}
                </h1>

                <p class="text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
                    {{ $course->description ?: 'Belum ada deskripsi untuk pelatihan ini.' }}
                </p>

                <div class="pt-1 flex flex-wrap items-center gap-x-5 gap-y-2 text-[11px] text-emerald-100/80">
                    <div class="inline-flex items-center gap-1.5">
                        <span>Enrollment Key:</span>
                        @if($course->enrollment_key)
                            <span class="font-mono font-extrabold text-[#E7C982] bg-black/20 px-2 py-0.5 rounded">
                                {{ $course->enrollment_key }}
                            </span>
                        @else
                            <span class="italic text-emerald-200/70">Terbuka (Tanpa Kunci)</span>
                        @endif

                        <form action="{{ route('admin.courses.regenerateKey', $course) }}" method="POST"
                            onsubmit="return confirm('Generate ulang Enrollment Key untuk pelatihan ini? Kunci lama tidak akan berlaku lagi.');"
                            class="inline-flex items-center">
                            @csrf
                            <button type="submit"
                                    title="Generate Ulang Enrollment Key"
                                    class="p-1 rounded-lg bg-white/10 hover:bg-[#E7C982] text-[#E7C982] hover:text-[#123F35] border border-[#E7C982]/40 transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <div>
                        Dibuat oleh: <span class="font-bold text-white">{{ $course->creator->name ?? 'Sistem' }}</span>
                    </div>
                    <div>
                        Diperbarui: <span class="font-semibold text-white">{{ $course->updated_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Thumbnail Preview -->
            @if($course->thumbnail)
                <div class="w-full sm:w-64 lg:w-72 flex-shrink-0">
                    <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}"
                         alt="{{ $course->title }}"
                         class="w-full h-40 object-cover rounded-2xl border-2 border-[#E7C982]/50 shadow-md">
                </div>
            @endif
        </div>
    </div>

    <!-- Grid Informasi: Instruktur & Template Background Sertifikat -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kartu Instruktur (2 Kolom) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-300 p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-300 pb-3">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-[#123F35]">Pengajar / Instruktur Ditugaskan</h2>
                    <p class="text-xs text-slate-500">Daftar pengajar yang berwenang mengelola silabus dan menilai peserta pada kelas ini.</p>
                </div>
                <a href="{{ route('admin.courses.edit', $course) }}"
                   class="text-xs font-bold text-[#28745B] hover:text-[#123F35] transition whitespace-nowrap">
                    Atur Instruktur &rarr;
                </a>
            </div>

            @if($course->instructors->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($course->instructors as $instructor)
                        <div class="p-3.5 rounded-xl bg-[#F7F5EE]/60 border border-slate-300 flex items-center space-x-3">
                            <img src="{{ $instructor->profile_photo_url }}" alt="{{ $instructor->name }}"
                                 class="w-10 h-10 rounded-full object-cover border border-[#28745B]/40 flex-shrink-0">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-[#123F35] truncate">{{ $instructor->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $instructor->email }}</p>
                                @if($instructor->institution)
                                    <p class="text-[10px] text-[#28745B] font-semibold truncate mt-0.5">{{ $instructor->institution }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-5 rounded-xl bg-red-50 border border-red-200 text-center space-y-2">
                    <p class="text-xs font-bold text-red-800">Belum ada instruktur yang ditugaskan pada pelatihan ini.</p>
                    <a href="{{ route('admin.courses.edit', $course) }}"
                       class="inline-flex items-center px-3.5 py-1.5 rounded-lg bg-red-700 hover:bg-red-800 text-white text-xs font-bold transition">
                        Tugaskan Sekarang
                    </a>
                </div>
            @endif
        </div>

        <!-- Kartu Template Background Sertifikat (1 Kolom) -->
        <div class="bg-white rounded-2xl border border-slate-300 p-5 sm:p-6 shadow-2xs space-y-3">
            <h2 class="text-sm sm:text-base font-bold text-[#123F35] border-b border-slate-300 pb-3">
                Background Sertifikat
            </h2>

            @if($course->certificateBg)
                <div class="space-y-2">
                    <a href="{{ asset('storage/' . $course->certificateBg->file_path) }}" target="_blank" class="block group">
                        <img src="{{ asset('storage/' . $course->certificateBg->file_path) }}"
                             alt="Template Sertifikat"
                             class="w-full h-36 object-cover rounded-xl border border-slate-300 group-hover:opacity-90 transition">
                    </a>
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-600 truncate max-w-[180px]">{{ $course->certificateBg->file_name }}</span>
                        <a href="{{ asset('storage/' . $course->certificateBg->file_path) }}" target="_blank"
                           class="font-bold text-[#28745B] hover:text-[#123F35] whitespace-nowrap">
                            Pratinjau &rarr;
                        </a>
                    </div>
                </div>
            @else
                <div class="h-36 rounded-xl bg-[#F7F5EE]/70 border border-dashed border-slate-300 flex flex-col items-center justify-center p-4 text-center space-y-1">
                    <p class="text-xs font-bold text-[#123F35]">Template Standar Sistem</p>
                    <p class="text-[11px] text-slate-500">Belum ada background kustom yang diunggah.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Monitoring Silabus Pertemuan (Read-Only) -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-300 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-[#123F35]">Struktur Silabus &amp; Pertemuan (Monitoring)</h2>
                <p class="text-xs text-slate-500">Ringkasan pertemuan, bahan ajar, dan evaluasi yang telah disusun oleh instruktur.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-xl bg-[#F7F5EE] text-[#123F35] text-xs font-bold border border-[#28745B]/30">
                Total {{ $course->meetings->count() }} Pertemuan
            </span>
        </div>

        @if($course->meetings->isNotEmpty())
            <div class="divide-y divide-slate-300">
                @foreach($course->meetings as $meeting)
                    <div class="p-4 sm:px-6 hover:bg-[#F7F5EE]/40 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold uppercase bg-[#123F35] text-white whitespace-nowrap">
                                    Pertemuan {{ $meeting->meeting_number }}
                                </span>
                                <h3 class="text-xs sm:text-sm font-bold text-[#123F35]">{{ $meeting->title }}</h3>
                            </div>
                            @if($meeting->description)
                                <p class="text-xs text-slate-500 line-clamp-2">{{ $meeting->description }}</p>
                            @endif
                            @if($meeting->available_from || $meeting->available_until)
                                <p class="text-[10px] text-slate-400">
                                    Jadwal Akses:
                                    {{ $meeting->available_from ? \Carbon\Carbon::parse($meeting->available_from)->translatedFormat('d M Y H:i') : 'Mulai bebas' }}
                                    &ndash;
                                    {{ $meeting->available_until ? \Carbon\Carbon::parse($meeting->available_until)->translatedFormat('d M Y H:i') : 'Tanpa batas' }}
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#F7F5EE] text-[#123F35] border border-slate-300 whitespace-nowrap">
                                {{ $meeting->materials_count }} Materi
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-[#28745B] border border-emerald-300 whitespace-nowrap">
                                {{ $meeting->assignments_count }} Tugas / Kuis
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-xs text-slate-500 bg-[#F7F5EE]/30">
                Instruktur belum menambahkan pertemuan pada pelatihan ini.
            </div>
        @endif
    </div>

    <!-- Daftar Peserta Terdaftar (Enrollments) -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-300 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-[#123F35]">Daftar Peserta Terdaftar (Enrollments)</h2>
                <p class="text-xs text-slate-500">Pantau progres belajar, status kelulusan, dan penerbitan sertifikat peserta pada kelas ini.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-xl bg-[#F7F5EE] text-[#123F35] text-xs font-bold border border-[#28745B]/30">
                {{ $enrollments->total() }} Peserta
            </span>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[820px] text-left border-collapse">
                <thead class="bg-[#28745B] text-white border-b border-slate-300">
                    <tr class="text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                        <th class="py-3.5 px-4 min-w-[220px]">Peserta</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Tanggal Daftar</th>
                        <th class="py-3.5 px-4 min-w-[160px]">Progres Belajar</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Status</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Sertifikat</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300 text-xs text-slate-700">
                    @forelse($enrollments as $enrollment)
                        @php
                            $progressPct = round($enrollment->progress->progress_percentage ?? 0);
                        @endphp
                        <tr class="hover:bg-[#F7F5EE]/50 transition">
                            <!-- Peserta -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $enrollment->user->profile_photo_url }}" alt="{{ $enrollment->user->name }}"
                                         class="w-9 h-9 rounded-full object-cover border border-[#28745B]/40 flex-shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#123F35] truncate">{{ $enrollment->user->name }}</p>
                                        <p class="text-[10px] text-slate-500 truncate">{{ $enrollment->user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Tanggal Daftar -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-600">
                                {{ $enrollment->enrolled_at ? \Carbon\Carbon::parse($enrollment->enrolled_at)->translatedFormat('d M Y') : '-' }}
                            </td>

                            <!-- Progres Belajar -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="w-24 bg-slate-200 h-1.5 rounded-full overflow-hidden border border-slate-300/60">
                                        <div class="bg-[#28745B] h-full rounded-full" style="width: {{ $progressPct }}%"></div>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-[#123F35]">{{ $progressPct }}%</span>
                                </div>
                            </td>

                            <!-- Status Enrollment -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($enrollment->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 whitespace-nowrap">
                                        Completed
                                    </span>
                                @elseif($enrollment->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-sky-100 text-sky-800 border border-sky-300 whitespace-nowrap">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-red-100 text-red-700 border border-red-300 whitespace-nowrap">
                                        Dropped
                                    </span>
                                @endif
                            </td>

                            <!-- Status Sertifikat -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($enrollment->certificate)
                                    @if($enrollment->certificate->status === 'published')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-[#28745B] border border-emerald-300 whitespace-nowrap">
                                            Terbit ({{ $enrollment->certificate->certificate_number }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300 whitespace-nowrap">
                                            Draft Sertifikat
                                        </span>
                                    @endif
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum Ada</span>
                                @endif
                            </td>

                            <!-- Aksi ke Detail Enrollment / User -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                @if(Route::has('admin.enrollments.show'))
                                    <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-[#123F35] hover:bg-[#28745B] text-white text-[11px] font-bold transition whitespace-nowrap">
                                        Detail Akademik &rarr;
                                    </a>
                                @else
                                    <a href="{{ route('admin.users.edit', $enrollment->user) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-[#F7F5EE] hover:bg-[#28745B]/15 text-[#123F35] border border-slate-300 text-[11px] font-bold transition whitespace-nowrap">
                                        Profil Peserta
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 px-4 text-center text-xs text-slate-400 italic">
                                Belum ada peserta yang mendaftar pada pelatihan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
            <div class="px-5 py-4 border-t border-slate-300 bg-[#F7F5EE]/30">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection