@extends('layouts.app')

@section('title', 'Sertifikat Saya - Ekosistem Halal')
@section('header_title', 'Sertifikat Saya')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-[#123F35] rounded-2xl p-6 sm:p-8 text-white relative overflow-hidden border border-[#E7C982]/20 shadow-md">
        <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
        <div class="absolute -right-10 -bottom-10 -z-0 h-48 w-48 rounded-full border border-[#E7C982]/10 blur-xl"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-1.5 max-w-2xl">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-[#E7C982]/20 text-[#E7C982] border border-[#E7C982]/30 mb-1">
                    Portofolio Kelulusan
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-white">Koleksi Sertifikat Kelulusan</h1>
                <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed">
                    Sertifikat resmi yang telah Anda peroleh setelah menyelesaikan semua pelatihan.
                </p>
            </div>

            <!-- Stat Badge -->
            <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-md border border-white/15 px-5 py-3.5 rounded-2xl flex-shrink-0">
                <div class="w-11 h-11 rounded-xl bg-[#E7C982] text-[#28745B] font-bold flex items-center justify-center text-xl shadow-xs">
                    <svg xmlns="http://w3.org" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                        <path fill-rule="evenodd" d="M5.166 2.621v.858c-1.035.148-2.059.33-3.071.543a.75.75 0 0 0-.584.859 6.753 6.753 0 0 0 6.138 5.6 6.73 6.73 0 0 0 2.743 1.346A6.707 6.707 0 0 1 9.279 15H8.54c-1.036 0-1.875.84-1.875 1.875V19.5h-.75a2.25 2.25 0 0 0-2.25 2.25c0 .414.336.75.75.75h15a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-2.25-2.25h-.75v-2.625c0-1.036-.84-1.875-1.875-1.875h-.739a6.706 6.706 0 0 1-1.112-3.173 6.73 6.73 0 0 0 2.743-1.347 6.753 6.753 0 0 0 6.139-5.6.75.75 0 0 0-.585-.858 47.077 47.077 0 0 0-3.07-.543V2.62a.75.75 0 0 0-.658-.744 49.22 49.22 0 0 0-6.093-.377c-2.063 0-4.096.128-6.093.377a.75.75 0 0 0-.657.744Zm0 2.629c0 1.196.312 2.32.857 3.294A5.266 5.266 0 0 1 3.16 5.337a45.6 45.6 0 0 1 2.006-.343v.256Zm13.5 0v-.256c.674.1 1.343.214 2.006.343a5.265 5.265 0 0 1-2.863 3.207 6.72 6.72 0 0 0 .857-3.294Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] text-emerald-200 font-medium block">Total Sertifikat:</span>
                    <span class="text-xl font-extrabold text-white">{{ $certificates->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Message Error (misal saat mencoba akses pratinjau sertifikat draft) -->
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-semibold flex items-center space-x-2">
            <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Grid Daftar Sertifikat -->
    @if($certificates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($certificates as $certificate)
                @php
                    $isPublished = $certificate->isPublished();
                    $course      = $certificate->enrollment->course ?? null;
                @endphp

                <div class="bg-white rounded-2xl border border-[#28745B]/30 shadow-2xs hover:shadow-md transition overflow-hidden flex flex-col justify-between">
                    
                    <div>
                        <!-- Thumbnail Header / Course Preview -->
                        <div class="relative h-44 bg-[#F7F5EE] border-b border-[#28745B]/10 overflow-hidden">
                            @if($course && $course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" 
                                     alt="{{ $course->title }}" 
                                     class="w-full h-full object-cover">
                            @else   
                                <div class="w-full h-full bg-gradient-to-br from-[#123F35] to-[#28745B] flex items-center justify-center text-[#E7C982] font-bold text-2xl tracking-wider">
                                    {{ substr($course->code ?? 'CERT', 0, 4) }}
                                </div>
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-black/15"></div>

                            <!-- Course Code -->
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold tracking-wider bg-white/95 backdrop-blur-xs text-[#123F35] uppercase shadow-xs">
                                    {{ $course->code ?? 'PELATIHAN' }}
                                </span>
                            </div>

                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                @if($isPublished)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#28745B] text-white shadow-xs flex items-center space-x-1">
                                        <span>✓ Terbit Resmi</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#E7C982] text-[#123F35] shadow-xs flex items-center space-x-1">
                                        <span>⏳ Proses Verifikasi</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Info Sertifikat & Course -->
                        <div class="p-5 space-y-3">
                            <h3 class="font-bold text-[#123F35] text-base line-clamp-2">
                                {{ $course->title ?? 'Pelatihan Halal' }}
                            </h3>

                            <div class="space-y-2 pt-2 text-xs border-t border-slate-100">
                                <div class="flex items-center justify-between text-slate-500">
                                    <span>No. Sertifikat:</span>
                                    <span class="font-mono font-bold text-[#123F35]">{{ $certificate->certificate_number }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-500">
                                    <span>Tanggal Diterbitkan:</span>
                                    <span class="font-medium text-slate-700">
                                        {{ $certificate->issued_at ? $certificate->issued_at->format('d M Y') : '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-slate-500">
                                    <span>Kode Verifikasi:</span>
                                    <code class="font-mono font-bold text-[#28745B] bg-[#28745B]/10 px-2 py-0.5 rounded border border-[#28745B]/20 text-[11px]">
                                        {{ $certificate->verification_code }}
                                    </code>
                                </div>
                            </div>

                            <!-- Keterangan jika sertifikat masih berstatus Draft -->
                            @if(!$isPublished)
                                <div class="p-3 bg-[#F7F5EE] border border-[#E7C982]/50 rounded-xl text-[11px] text-slate-600 space-y-1">
                                    <div class="font-bold text-[#123F35]">Menunggu Penerbitan / TTD BPJPH</div>
                                    <p class="text-[10px] leading-relaxed text-slate-500">
                                        Sertifikat kelulusan Anda sudah tercatat dan sedang dalam tahap verifikasi atau penandatanganan resmi. Tombol unduh akan aktif otomatis setelah dipublikasikan oleh Admin.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="p-5 pt-0">
                        @if($isPublished)
                            <div class="grid grid-cols-2 gap-2">
                                <!-- Tombol Pratinjau -->
                                <a href="{{ route('student.certificates.show', $certificate->id) }}" 
                                   class="inline-flex items-center justify-center space-x-1.5 bg-[#F7F5EE] hover:bg-[#28745B]/10 text-[#123F35] font-bold text-xs py-2.5 px-3 rounded-xl transition border border-[#28745B]/20">
                                    <svg class="w-4 h-4 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span>Pratinjau</span>
                                </a>

                                <!-- Tombol Unduh PDF Resmi -->
                                <a href="{{ route('student.certificates.download', $certificate->id) }}" 
                                   class="inline-flex items-center justify-center space-x-1.5 bg-[#28745B] hover:bg-[#123F35] text-white font-bold text-xs py-2.5 px-3 rounded-xl transition shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    <span>Unduh PDF</span>
                                </a>
                            </div>
                        @else
                            <button type="button" disabled 
                                    class="w-full inline-flex items-center justify-center space-x-2 bg-slate-100 text-slate-400 font-bold text-xs py-2.5 px-4 rounded-xl cursor-not-allowed border border-slate-200">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span>Belum Dapat Diunduh</span>
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl border border-[#28745B]/15 p-12 text-center space-y-4 shadow-2xs">
            <div class="w-16 h-16 bg-[#F7F5EE] border border-[#E7C982]/50 text-[#123F35] rounded-2xl flex items-center justify-center mx-auto text-3xl shadow-2xs">
                🎓
            </div>
            <div class="space-y-1">
                <h3 class="font-bold text-[#123F35] text-base">Belum Ada Sertifikat Diterbitkan</h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                    Sertifikat akan otomatis dibuat setelah Anda menyelesaikan seluruh modul, tugas, dan kuis pada pelatihan (progres mencapai 100%).
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('student.courses.index') }}" 
                   class="inline-flex items-center space-x-2 bg-[#28745B] hover:bg-[#123F35] text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-2xs">
                    <span>Lihat Kursus Saya</span>
                </a>
            </div>
        </div>
    @endif

</div>
@endsection