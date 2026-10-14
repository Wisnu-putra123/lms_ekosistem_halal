@extends('layouts.app')

@section('title', 'Detail Attempt - ' . $user->name)
@section('header_title', 'Riwayat Attempt Siswa')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase {{ $assignment->type === 'quiz' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ strtoupper($assignment->type) }}
                </span>
                <span class="text-xs text-slate-400">KKM: <strong class="text-slate-700">{{ $assignment->passing_score }}</strong> | Max: <strong class="text-slate-700">{{ $assignment->max_score }}</strong></span>
            </div>
            <h1 class="text-xl font-bold text-slate-800 mt-1">{{ $assignment->title }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Siswa: <span class="font-semibold text-slate-700">{{ $user->name }}</span> ({{ $user->email }})</p>
        </div>

        <div>
            <a href="{{ route('teacher.courses.gradebook.show', $course->id) }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 px-4 py-2 rounded-xl transition shadow-sm">
                &larr; <span>Kembali ke Matriks Rekap</span>
            </a>
        </div>
    </div>

    <!-- List Tabel Attempt -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Daftar Percobaan Pengerjaan (Total: {{ $attempts->count() }})</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider">
                        <th class="p-4 font-bold text-center w-20">Attempt</th>
                        <th class="p-4 font-bold">Waktu Submit / Selesai</th>
                        <th class="p-4 font-bold text-center">Status</th>
                        <th class="p-4 font-bold text-center">Skor Akhir</th>
                        <th class="p-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attempts as $attempt)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Percobaan Ke- -->
                            <td class="p-4 text-center font-extrabold text-slate-700">
                                #{{ $attempt->attempt_number }}
                            </td>

                            <!-- Tanggal Submit -->
                            <td class="p-4 text-slate-700 font-medium">
                                {{ $attempt->submitted_at ? \Carbon\Carbon::parse($attempt->submitted_at)->format('d M Y, H:i:s WIB') : '-' }}
                            </td>

                            <!-- Status Attempt -->
                            <td class="p-4 text-center">
                                @if($attempt->status === 'graded' || $attempt->status === 'submitted')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Selesai / Dinilai</span>
                                @elseif($attempt->status === 'expired')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Waktu Habis</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">In Progress</span>
                                @endif
                            </td>

                            <!-- Nilai -->
                            <td class="p-4 text-center font-extrabold text-sm">
                                @if(is_null($attempt->score))
                                    <span class="text-slate-400 text-xs font-normal italic">Belum Dinilai</span>
                                @else
                                    <span class="{{ $attempt->score >= $assignment->passing_score ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $attempt->score }}
                                    </span>
                                @endif
                            </td>

                            <!-- Tombol Aksi Penilaian -->
                            <td class="p-4 text-center">
                                @if($assignment->type === 'submission')
                                    <a href="{{ route('teacher.submissions.grade', $attempt->id) }}" class="inline-flex items-center space-x-1 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-semibold px-3 py-1.5 rounded-lg transition shadow-sm">
                                        <span>Beri Nilai / Feedback</span>
                                    </a>
                                @else
                                    <a href="{{ route('teacher.quizzes.attempts.show', $attempt->id) }}" class="inline-flex items-center space-x-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-semibold px-3 py-1.5 rounded-lg transition shadow-sm">
                                        <span>Lihat Jawaban Siswa</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center text-slate-400 text-xs">
                                Siswa belum pernah melakukan pengerjaan untuk item ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection