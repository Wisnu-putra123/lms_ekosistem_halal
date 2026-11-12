@extends('layouts.app')

@section('title', 'Sertifikat - ' . ($certificate->enrollment->course->title ?? 'Kelulusan'))
@section('header_title', 'Pratinjau Sertifikat Resmi')

@section('content')
@php
    $activeMedia = $certificate->active_media;
    $pdfUrl      = $activeMedia
        ? asset('storage/' . $activeMedia->file_path) . '?v=' . ($activeMedia->updated_at ? $activeMedia->updated_at->timestamp : time())
        : null;
@endphp

<div class="max-w-5xl mx-auto py-4 space-y-5 sm:space-y-6">

    <!-- Top Navigation: Tombol Kembali (Gaya Standar di Atas) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('student.certificates.index') }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Daftar Sertifikat</span>
        </a>
    </div>

    <!-- Action Header -->
    <div class="bg-[#123F35] p-5 sm:p-6 rounded-2xl border border-[#E7C982]/20 shadow-md flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-white relative overflow-hidden">
        <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
        
        <div class="relative z-10 space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-[#E7C982]/20 text-[#E7C982] border border-[#E7C982]/30">
                    {{ $certificate->signed_media_id ? 'Sertifikat Resmi Ber-TTD BPJPH' : 'Sertifikat Kelulusan Resmi' }}
                </span>
            </div>
            <h1 class="text-base sm:text-lg font-bold text-white">
                {{ $certificate->enrollment->course->title ?? 'Pelatihan Ekosistem Halal' }}
            </h1>
            <p class="text-xs text-emerald-100/80">
                No. Sertifikat: <span class="font-mono font-bold text-[#E7C982]">{{ $certificate->certificate_number }}</span>
                &bull; Diterbitkan pada {{ $certificate->issued_at ? $certificate->issued_at->format('d F Y') : '-' }}
            </p>
        </div>

        <div class="relative z-10 flex flex-wrap items-center gap-2.5 w-full sm:w-auto justify-end">
            <a href="{{ route('student.certificates.download', $certificate->id) }}" 
               class="w-full sm:w-auto justify-center px-4 py-2.5 bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Unduh PDF Resmi</span>
            </a>
        </div>
    </div>

    <!-- Detail Metadata Verifikasi -->
    <div class="bg-white rounded-2xl border border-[#28745B]/15 p-4 sm:p-5 shadow-2xs grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <div class="p-3 bg-[#F7F5EE]/70 rounded-xl border border-[#28745B]/10">
            <span class="text-slate-500 block text-[11px]">Nama Peserta</span>
            <span class="font-bold text-[#123F35] text-sm">{{ $certificate->enrollment->user->name ?? '-' }}</span>
        </div>
        <div class="p-3 bg-[#F7F5EE]/70 rounded-xl border border-[#28745B]/10">
            <span class="text-slate-500 block text-[11px]">Kode Verifikasi Keabsahan</span>
            <code class="font-mono font-extrabold text-[#28745B] text-sm">{{ $certificate->verification_code }}</code>
        </div>
        <div class="p-3 bg-[#F7F5EE]/70 rounded-xl border border-[#28745B]/10">
            <span class="text-slate-500 block text-[11px]">Tautan Verifikasi Publik</span>
            <a href="{{ url('/verify-certificate/' . $certificate->verification_code) }}" target="_blank" 
               class="text-[#28745B] hover:underline font-semibold break-all text-[11px]">
                {{ url('/verify-certificate/' . $certificate->verification_code) }}
            </a>
        </div>
    </div>

    <!-- Penampil PDF Asli dari Storage (100% Identik dengan File yang Diunduh) -->
    <div class="bg-white rounded-2xl border border-[#28745B]/20 shadow-md overflow-hidden p-2 sm:p-4">
        @if($pdfUrl && Storage::disk('public')->exists($activeMedia->file_path))
            <div class="w-full aspect-[1.414/1] min-h-[420px] sm:min-h-[600px] rounded-xl overflow-hidden border border-slate-200 bg-slate-100">
                <iframe src="{{ $pdfUrl }}#toolbar=0&navpanes=0&view=FitH" 
                        class="w-full h-full border-0" 
                        title="Pratinjau Sertifikat {{ $certificate->certificate_number }}">
                </iframe>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-400 px-1">
                <span>Menampilkan berkas PDF resmi yang tersimpan di server.</span>
                <a href="{{ $pdfUrl }}" target="_blank" class="text-[#28745B] hover:underline font-semibold">
                    Buka PDF di Tab Baru &nearr;
                </a>
            </div>
        @else
            <div class="p-12 text-center space-y-2 text-slate-500">
                <p class="font-bold text-sm text-[#123F35]">Berkas PDF Sertifikat Tidak Ditemukan</p>
                <p class="text-xs">Silakan hubungi administrator LPP-JPH untuk melakukan generate ulang berkas sertifikat Anda.</p>
            </div>
        @endif
    </div>

</div>
@endsection