@extends('layouts.app')

@section('title', 'Manajemen Kepesertaan (Enrollments)')

@section('content')
<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8"
     x-data="{
        openEnrollModal: {{ $errors->has('user_ids') || ($errors->has('course_id') && !old('bulk_mode')) ? 'true' : 'false' }},
        openBulkAccessModal: {{ old('bulk_mode') ? 'true' : 'false' }},
        bulkAccessMode: '{{ old('access_mode', 'limited') }}',
        studentSearch: ''
     }">

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header Banner --}}
        <div class="bg-gradient-to-r from-[#123F35] via-[#1B5749] to-[#28745B] rounded-2xl p-6 sm:p-8 text-white shadow-sm relative overflow-hidden">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-emerald-100 text-xs font-medium mb-3">
                        <span>Admin Panel</span>
                        <span>•</span>
                        <span>Kepesertaan Pelatihan</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                        Manajemen Kepesertaan Pelatihan
                    </h1>
                    <p class="mt-1.5 text-sm text-emerald-100/90 max-w-2xl">
                        Kelola pendaftaran peserta, pengaturan masa berlaku akses kelas, pantau progres belajar, serta status kelulusan.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    
                    <button type="button"
                            @click="openEnrollModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-[#123F35] hover:bg-emerald-50 text-sm font-semibold shadow-sm transition cursor-pointer">
                        <svg class="w-4 h-4 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Daftarkan Peserta
                    </button>

                    <button type="button"
                            @click="openBulkAccessModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/20 text-sm font-semibold backdrop-blur-xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Atur Akses Massal
                    </button>

                    <form method="POST"
                        action="{{ route('admin.enrollments.purgeExpiredFiles') }}"
                        onsubmit="return confirm('Hapus seluruh file fisik jawaban & feedback dari {{ $stats['expired'] }} kepesertaan yang sudah kedaluwarsa? Nilai dan riwayat kelulusan tetap tersimpan.')">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ request('course_id') }}">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-300 bg-amber-50 text-amber-800 text-sm font-semibold hover:bg-amber-100 transition shadow-sm cursor-pointer">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Bersihkan File Kedaluwarsa ({{ $stats['expired'] }})
                        </button>
                    </form>
                    
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-emerald-800 shadow-2xs">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 flex items-center gap-3 text-red-800 shadow-2xs">
                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        {{-- Kartu Statistik Ringkas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Kepesertaan</p>
                    <p class="mt-1.5 text-2xl font-bold text-[#123F35]">{{ number_format($stats['total']) }}</p>
                    <p class="mt-0.5 text-xs text-gray-400">Seluruh riwayat enrollment</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#123F35]/10 text-[#123F35] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Peserta Aktif</p>
                    <p class="mt-1.5 text-2xl font-bold text-emerald-700">{{ number_format($stats['active']) }}</p>
                    <p class="mt-0.5 text-xs text-gray-400">Sedang mengikuti kelas</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Selesai / Lulus</p>
                    <p class="mt-1.5 text-2xl font-bold text-blue-700">{{ number_format($stats['completed']) }}</p>
                    <p class="mt-0.5 text-xs text-gray-400">Menyelesaikan pelatihan</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Akses Kedaluwarsa</p>
                    <p class="mt-1.5 text-2xl font-bold text-amber-700">{{ number_format($stats['expired']) }}</p>
                    <p class="mt-0.5 text-xs text-gray-400">Melewati batas waktu</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Filter & Pencarian --}}
        <div class="bg-[#F7F5EE] rounded-2xl p-5 border border-gray-200/80 shadow-xs">
            <form method="GET" action="{{ route('admin.enrollments.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-4">
                    <label for="search" class="block text-xs font-semibold text-gray-700 mb-1.5">Cari Peserta / Pelatihan</label>
                    <div class="relative">
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Nama, email, NIK, atau kode kelas..."
                               class="w-full pl-9 pr-3 py-2.5 bg-white text-sm rounded-xl border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label for="course_id" class="block text-xs font-semibold text-gray-700 mb-1.5">Pelatihan</label>
                    <select name="course_id" id="course_id"
                            class="w-full py-2.5 px-3 bg-white text-sm rounded-xl border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                        <option value="">Semua Pelatihan</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>
                                {{ $course->code ? "[{$course->code}] " : '' }}{{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-1.5">Status Kepesertaan</label>
                    <select name="status" id="status"
                            class="w-full py-2.5 px-3 bg-white text-sm rounded-xl border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                        <option value="">Semua Status</option>
                        <option value="active" @selected(request('status') === 'active')>Aktif</option>
                        <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                        <option value="dropped" @selected(request('status') === 'dropped')>Berhenti (Dropped)</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex items-end gap-2">
                    <div class="flex-1">
                        <label for="access_status" class="block text-xs font-semibold text-gray-700 mb-1.5">Masa Akses</label>
                        <select name="access_status" id="access_status"
                                class="w-full py-2.5 px-3 bg-white text-sm rounded-xl border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                            <option value="">Semua Akses</option>
                            <option value="unlimited" @selected(request('access_status') === 'unlimited')>Tanpa Batas</option>
                            <option value="active_limited" @selected(request('access_status') === 'active_limited')>Berbatas (Aktif)</option>
                            <option value="expired" @selected(request('access_status') === 'expired')>Kedaluwarsa</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-[#28745B] text-white text-sm font-semibold hover:bg-[#439479] transition shadow-2xs cursor-pointer">
                        Filter
                    </button>

                    @if (request()->hasAny(['search', 'course_id', 'status', 'access_status']))
                        <a href="{{ route('admin.enrollments.index') }}"
                           title="Reset Filter"
                           class="p-2.5 rounded-xl border bg-white border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Daftar Enrollment --}}
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-[#123F35]">Daftar Kepesertaan Terdaftar</h2>
                <span class="text-xs text-gray-500">Menampilkan {{ $enrollments->count() }} dari {{ $enrollments->total() }} data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#28745B]">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-white text-left text-xs font-bold uppercase tracking-wider text-[#123F35]">Peserta</th>
                            <th scope="col" class="px-6 py-3.5 text-white text-left text-xs font-bold uppercase tracking-wider text-[#123F35]">Pelatihan</th>
                            <th scope="col" class="px-6 py-3.5 text-white text-left text-xs font-bold uppercase tracking-wider text-[#123F35]">Progres Belajar</th>
                            <th scope="col" class="px-6 py-3.5 text-white text-left text-xs font-bold uppercase tracking-wider text-[#123F35]">Status & Sertifikat</th>
                            <th scope="col" class="px-6 py-3.5 text-white text-left text-xs font-bold uppercase tracking-wider text-[#123F35]">Batas Akses</th>
                            <th scope="col" class="px-6 py-3.5 text-white text-right text-xs font-bold uppercase tracking-wider text-[#123F35]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($enrollments as $enrollment)
                            @php
                                $progress = round($enrollment->progress->progress_percentage ?? 0);
                                $isExpired = $enrollment->access_expires_at && $enrollment->access_expires_at->isPast();
                            @endphp
                            <tr class="hover:bg-[#F7F5EE]/70 transition">
                                {{-- Peserta --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($enrollment->user?->profile_photo_url)
                                            <img src="{{ $enrollment->user->profile_photo_url }}"
                                                 alt="{{ $enrollment->user->name }}"
                                                 class="h-10 w-10 rounded-full object-cover border border-gray-200 shrink-0">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-[#123F35] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(mb_substr($enrollment->user->name ?? 'U', 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $enrollment->user->name ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $enrollment->user->email ?? '-' }}
                                            </div>
                                            @if (!empty($enrollment->user->nik))
                                                <div class="text-[11px] text-gray-400 font-mono mt-0.5">
                                                    NIK: {{ $enrollment->user->nik }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Pelatihan --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 line-clamp-1">
                                        {{ $enrollment->course->title ?? '-' }}
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        @if (!empty($enrollment->course->code))
                                            <span class="px-2 py-0.5 rounded-md bg-[#123F35]/10 text-[#123F35] font-mono font-semibold text-[11px]">
                                                {{ $enrollment->course->code }}
                                            </span>
                                        @endif
                                        <span>Terdaftar: {{ optional($enrollment->enrolled_at)->format('d M Y') }}</span>
                                    </div>
                                </td>

                                {{-- Progres --}}
                                <td class="px-6 py-4 w-48">
                                    <div class="flex items-center justify-between text-xs mb-1.5">
                                        <span class="font-bold text-[#123F35]">{{ $progress }}%</span>
                                        @if ($enrollment->progress?->last_activity_at)
                                            <span class="text-[11px] text-gray-400" title="Aktivitas terakhir">
                                                {{ $enrollment->progress->last_activity_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full transition-all duration-300 {{ $progress >= 100 ? 'bg-emerald-600' : 'bg-[#28745B]' }}"
                                             style="width: {{ min(100, max(0, $progress)) }}%"></div>
                                    </div>
                                </td>

                                {{-- Status & Sertifikat --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-1.5">
                                        @if ($enrollment->status === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                Selesai
                                            </span>
                                        @elseif ($enrollment->status === 'dropped')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                                Dropped
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                Aktif
                                            </span>
                                        @endif

                                        @if ($enrollment->certificate)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium {{ $enrollment->certificate->status === 'published' ? 'text-emerald-700' : 'text-amber-700' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Sertifikat: {{ ucfirst($enrollment->certificate->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Batas Akses --}}
                                <td class="px-6 py-4 text-xs">
                                    @if (is_null($enrollment->access_expires_at))
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 font-medium">
                                            Tanpa Batas
                                        </span>
                                    @elseif ($isExpired)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-red-50 text-red-700 border border-red-200 font-semibold">
                                            Kedaluwarsa ({{ $enrollment->access_expires_at->format('d M Y H:i') }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 font-medium">
                                            s.d. {{ $enrollment->access_expires_at->format('d M Y H:i') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#123F35]/10 text-[#123F35] hover:bg-[#123F35] hover:text-white text-xs font-semibold transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </a>

                                        @if (!$enrollment->certificate || $enrollment->certificate->status !== 'published')
                                            <form method="POST"
                                                  action="{{ route('admin.enrollments.destroy', $enrollment) }}"
                                                  onsubmit="return confirm('Yakin ingin menghapus data kepesertaan ini? Seluruh progres terkait akan ikut terhapus.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Hapus Enrollment"
                                                        class="p-1.5 rounded-lg text-red-600 bg-red-50/60 hover:bg-red-600 hover:text-white transition cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <div class="mx-auto w-12 h-12 rounded-2xl bg-[#123F35]/10 flex items-center justify-center text-[#123F35] mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-800">Belum ada data kepesertaan ditemukan</p>
                                    <p class="text-xs text-gray-500 mt-1">Ubah filter pencarian atau daftarkan peserta baru secara manual.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($enrollments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL 1: Daftarkan Peserta Manual --}}
    <div x-show="openEnrollModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
        <div @click.away="openEnrollModal = false"
             class="bg-white rounded-2xl max-w-xl w-full shadow-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-[#123F35] to-[#28745B] text-white flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold">Daftarkan Peserta ke Pelatihan</h3>
                    <p class="text-xs text-emerald-100/90 mt-0.5">Tambahkan satu atau beberapa peserta sekaligus ke dalam kelas.</p>
                </div>
                <button type="button" @click="openEnrollModal = false" class="p-1 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.enrollments.store') }}" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Pelatihan <span class="text-red-500">*</span></label>
                    <select name="course_id" required
                            class="w-full py-2.5 px-3 text-sm rounded-xl border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                        <option value="">-- Pilih Pelatihan --</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                {{ $course->code ? "[{$course->code}] " : '' }}{{ $course->title }} ({{ ucfirst($course->status) }})
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-gray-700">Pilih Peserta <span class="text-red-500">*</span></label>
                        <input type="text"
                               x-model="studentSearch"
                               placeholder="Cari nama / NIK..."
                               class="text-xs px-2.5 py-1.5 rounded-lg border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                    </div>

                    <div class="max-h-52 overflow-y-auto border border-gray-300 rounded-xl divide-y divide-gray-100 p-2 space-y-1">
                        @foreach ($students as $student)
                            @php
                                $searchKey = strtolower($student->name . ' ' . $student->email . ' ' . ($student->nik ?? ''));
                            @endphp
                            <label x-show="studentSearch === '' || '{{ addslashes($searchKey) }}'.includes(studentSearch.toLowerCase())"
                                   class="flex items-center gap-3 p-2 rounded-lg hover:bg-[#F7F5EE] cursor-pointer transition">
                                <input type="checkbox"
                                       name="user_ids[]"
                                       value="{{ $student->id }}"
                                       @checked(is_array(old('user_ids')) && in_array($student->id, old('user_ids')))
                                       class="rounded border-gray-300 text-[#123F35] focus:ring-[#28745B]">
                                <div class="text-xs">
                                    <div class="font-semibold text-gray-900">{{ $student->name }}</div>
                                    <div class="text-gray-500">
                                        {{ $student->email }} {{ $student->nik ? "• NIK: {$student->nik}" : '' }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('user_ids')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Batas Waktu Akses (Opsional)</label>
                    <input type="datetime-local"
                           name="access_expires_at"
                           value="{{ old('access_expires_at') }}"
                           class="w-full py-2.5 px-3 text-sm rounded-xl border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                    <p class="mt-1 text-[11px] text-gray-500">Kosongkan jika peserta diberikan akses tanpa batas waktu.</p>
                </div>

                <label class="flex items-center gap-2.5 pt-1 cursor-pointer">
                    <input type="checkbox"
                           name="allow_re_enroll"
                           value="1"
                           @checked(old('allow_re_enroll'))
                           class="rounded border-gray-300 text-[#123F35] focus:ring-[#28745B]">
                    <span class="text-xs text-gray-700">Izinkan pendaftaran ulang (buat enrollment baru meski peserta masih berstatus aktif)</span>
                </label>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-gray-100">
                    <button type="button"
                            @click="openEnrollModal = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#123F35] text-white text-sm font-semibold hover:bg-[#28745B] transition shadow-2xs">
                        Simpan Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 2: Pengaturan Batas Waktu Akses Massal --}}
    <div x-show="openBulkAccessModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
        <div @click.away="openBulkAccessModal = false"
             class="bg-white rounded-2xl max-w-lg w-full shadow-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-[#123F35] to-[#28745B] text-white flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold">Atur Waktu Akses Massal</h3>
                    <p class="text-xs text-emerald-100/90 mt-0.5">Perbarui batas masa aktif pelatihan secara serentak.</p>
                </div>
                <button type="button" @click="openBulkAccessModal = false" class="p-1 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.enrollments.bulkAccess') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="bulk_mode" value="1">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Pelatihan <span class="text-red-500">*</span></label>
                    <select name="course_id" required
                            class="w-full py-2.5 px-3 text-sm rounded-xl border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                        <option value="">-- Pilih Pelatihan --</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                {{ $course->code ? "[{$course->code}] " : '' }}{{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Mode Akses <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                               :class="bulkAccessMode === 'limited' ? 'border-[#28745B] bg-[#28745B]/5' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="access_mode" value="limited" x-model="bulkAccessMode" class="text-[#123F35] focus:ring-[#28745B]">
                            <span class="text-xs font-semibold text-gray-800">Berbatas Waktu</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                               :class="bulkAccessMode === 'unlimited' ? 'border-[#28745B] bg-[#28745B]/5' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="access_mode" value="unlimited" x-model="bulkAccessMode" class="text-[#123F35] focus:ring-[#28745B]">
                            <span class="text-xs font-semibold text-gray-800">Tanpa Batas</span>
                        </label>
                    </div>
                </div>

                <div x-show="bulkAccessMode === 'limited'">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal & Waktu Berakhir Akses <span class="text-red-500">*</span></label>
                    <input type="datetime-local"
                           name="access_expires_at"
                           value="{{ old('access_expires_at') }}"
                           class="w-full py-2.5 px-3 text-sm rounded-xl border border-gray-300 focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 transition">
                </div>

                <label class="flex items-center gap-2.5 pt-1 cursor-pointer">
                    <input type="checkbox"
                           name="only_active"
                           value="1"
                           checked
                           class="rounded border-gray-300 text-[#123F35] focus:ring-[#28745B]">
                    <span class="text-xs text-gray-700">Terapkan hanya pada peserta berstatus <strong>Aktif</strong></span>
                </label>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-gray-100">
                    <button type="button"
                            @click="openBulkAccessModal = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#123F35] text-white text-sm font-semibold hover:bg-[#28745B] transition shadow-2xs">
                        Terapkan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection