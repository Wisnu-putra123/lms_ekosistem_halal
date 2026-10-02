@extends('layouts.app')

@section('title', $assignment->title . ' - Ekosistem Halal')
@section('header_title', 'Detail Tempat Submission Tugas')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.courses.show', $assignment->course_id) }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Detail Pelatihan</span>
        </a>
        <div class="flex items-center space-x-2">
            <a href="{{ route('teacher.assignments.edit', $assignment->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition">
                Edit Tugas
            </a>
            <form action="{{ route('teacher.assignments.destroy', $assignment->id) }}" method="POST" onsubmit="return confirm('Hapus tempat submission tugas ini beserta seluruh lampirannya?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Ringkasan Informasi Tugas -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-blue-100 text-blue-700 uppercase">
                    Pertemuan {{ $assignment->meeting->meeting_number }}
                </span>
                <h1 class="text-xl font-bold text-slate-800 mt-1">{{ $assignment->title }}</h1>
            </div>
            <div class="flex items-center space-x-2">
                @if($assignment->status === 'published')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Published</span>
                @elseif($assignment->status === 'draft')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Draft</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">Closed</span>
                @endif
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Metode Pengumpulan</span>
                <div class="text-xs font-bold text-blue-700 mt-1 uppercase">
                    @if($assignment->submission_method === 'file')
                        Unggah Berkas
                    @elseif($assignment->submission_method === 'text')
                        Teks Editor
                    @else
                        Berkas & Teks
                    @endif
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Nilai Maksimal</span>
                <div class="text-base font-bold text-slate-800 mt-0.5">{{ number_format($assignment->max_score, 0) }}</div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Passing Grade</span>
                <div class="text-base font-bold text-emerald-700 mt-0.5">{{ number_format($assignment->passing_score, 0) }}</div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Waktu Dibuka</span>
                <div class="text-xs font-semibold text-slate-700 mt-1">
                    {{ $assignment->available_from ? \Carbon\Carbon::parse($assignment->available_from)->format('d M Y H:i') : 'Langsung' }}
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                <span class="text-[10px] font-semibold text-slate-400 uppercase">Waktu Ditutup</span>
                <div class="text-xs font-semibold text-slate-700 mt-1">
                    {{ $assignment->available_until ? \Carbon\Carbon::parse($assignment->available_until)->format('d M Y H:i') : 'Tanpa Batas' }}
                </div>
            </div>
        </div>

        <!-- Deskripsi / Instruksi -->
        @if($assignment->description)
            <div class="space-y-1">
                <h3 class="text-xs font-bold text-slate-700 uppercase">Instruksi / Petunjuk Pengerjaan:</h3>
                <p class="text-xs text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100 leading-relaxed">{{ $assignment->description }}</p>
            </div>
        @endif

        <!-- Berkas Lampiran Pengajar -->
        @if($assignment->attachments && $assignment->attachments->count() > 0)
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-700 uppercase">Berkas Lampiran Pengajar:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($assignment->attachments as $attachment)
                        @if($attachment->media)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                                <div class="flex items-center space-x-2 overflow-hidden pr-2">
                                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-xs text-slate-800 font-semibold truncate">{{ $attachment->media->file_name }}</span>
                                </div>
                                <a href="{{ asset('storage/' . $attachment->media->file_path) }}" download class="text-[11px] font-bold text-blue-600 hover:underline flex-shrink-0">Unduh</a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Tabel Pengumpulan Siswa (Submissions) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h2 class="text-base font-bold text-slate-800">Daftar Pengumpulan Siswa ({{ $assignment->submissions->count() }})</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase">
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Waktu Mengumpulkan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Nilai</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($assignment->submissions as $sub)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $sub->user->name ?? 'Siswa' }}</td>
                            <td class="py-3.5 px-4">{{ $sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y H:i') : '-' }}</td>
                            <td class="py-3.5 px-4">
                                @if($sub->status === 'graded')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Dinilai</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Menunggu Penilaian</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold">
                                {{ $sub->score !== null ? number_format($sub->score, 2) : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="#" class="text-blue-600 hover:underline font-semibold">Beri Nilai &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs text-slate-400">Belum ada siswa yang mengumpulkan tugas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection