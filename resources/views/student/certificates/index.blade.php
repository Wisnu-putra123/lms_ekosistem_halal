@extends('layouts.app')

@section('title', 'Sertifikat Saya - Ekosistem Halal')
@section('header_title', 'Sertifikat Saya')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-xl font-bold text-slate-800">Koleksi Sertifikat Kelulusan</h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-2xl">
                    Berikut adalah seluruh sertifikat resmi yang telah berhasil Anda dapatkan setelah menyelesaikan modul pelatihan di LPP-JPH Universitas Telkom.
                </p>
            </div>
            <!-- Stat Badge -->
            <div class="flex items-center space-x-3 bg-amber-50 border border-amber-100 px-4 py-3 rounded-2xl flex-shrink-0">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white font-bold flex items-center justify-center text-lg">
                    🏆
                </div>
                <div>
                    <span class="text-xs text-amber-800 font-medium block">Total Sertifikat:</span>
                    <span class="text-lg font-extrabold text-amber-900">{{ $certificates->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Daftar Sertifikat -->
    @if($certificates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($certificates as $certificate)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col justify-between">
                    
                    <div>
                        <!-- Thumbnail Header / Course Preview -->
                        <div class="relative h-40 bg-slate-100 border-b border-slate-100 overflow-hidden">
                            @if($certificate->enrollment->course->certificateBackground)
                                <img src="{{ asset('storage/' . $certificate->enrollment->course->certificateBackground->file_path) }}" 
                                     alt="{{ $certificate->enrollment->course->title }}" 
                                     class="w-full h-full object-cover">
                            @else   
                                <div class="w-full h-full bg-gradient-to-br from-amber-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl">
                                    {{ substr($certificate->enrollment->course->code, 0, 3) }}
                                </div>
                            @endif

                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold tracking-wider bg-white/90 backdrop-blur-sm text-slate-800 uppercase shadow-sm">
                                    {{ $certificate->enrollment->course->code }}
                                </span>
                            </div>

                            <div class="absolute top-3 right-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500 text-white shadow-sm flex items-center space-x-1">
                                    <span>✓ Lulus</span>
                                </span>
                            </div>
                        </div>

                        <!-- Info Sertifikat & Course -->
                        <div class="p-5 space-y-3">
                            <h3 class="font-bold text-slate-800 text-base line-clamp-2 hover:text-amber-600 transition">
                                {{ $certificate->enrollment->course->title }}
                            </h3>

                            <div class="space-y-1.5 pt-1 text-xs text-slate-500 border-t border-slate-100">
                                <div class="flex items-center justify-between">
                                    <span>No. Sertifikat:</span>
                                    <span class="font-mono font-semibold text-slate-700">{{ $certificate->certificate_number }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Tanggal Diterbitkan:</span>
                                    <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($certificate->issued_at)->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Kode Verifikasi:</span>
                                    <code class="font-mono font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100 text-[11px]">{{ $certificate->verification_code }}</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Button -->
                    <div class="p-5 pt-0">
                        <a href="{{ route('student.certificates.show', $certificate->id) }}" 
                           class="w-full inline-flex items-center justify-center space-x-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs py-2.5 px-4 rounded-xl transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span>Lihat Sertifikat Digital</span>
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State jika siswa belum memiliki sertifikat -->
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center space-y-3">
            <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-2xl">
                🎓
            </div>
            <h3 class="font-bold text-slate-800 text-base">Belum Ada Sertifikat Diterbitkan</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                Sertifikat akan otomatis diterbitkan begitu Anda menyelesaikan seluruh modul, tugas, dan kuis pada pelatihan (progres mencapai 100%).
            </p>
            <div class="pt-2">
                <a href="{{ route('student.courses.index') }}" class="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                    <span>Lihat Kursus Saya</span>
                </a>
            </div>
        </div>
    @endif

</div>
@endsection