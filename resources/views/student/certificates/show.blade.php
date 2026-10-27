@extends('layouts.app')

@section('title', 'Sertifikat - ' . $certificate->enrollment->course->title)

@section('content')
<div class="max-w-5xl mx-auto py-6 space-y-6">

    <!-- Action Header (Tidak ikut tercetak saat di-print) -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm print:hidden">
        <div>
            <h1 class="text-base font-bold text-slate-800">Sertifikat Kelulusan Digital</h1>
            <p class="text-xs text-slate-500">Diterbitkan otomatis pada {{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y') }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('student.certificates.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                &larr; Kembali
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Tampilan Sertifikat Digital (A4 Landscape aspect ratio) -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-lg overflow-hidden p-2 sm:p-4 print:p-0 print:border-none print:shadow-none">
        
        <div class="relative w-full aspect-[1.414/1] bg-slate-50 rounded-xl overflow-hidden border border-slate-200 print:border-none print:rounded-none flex flex-col justify-between p-8 sm:p-14 select-none">
            
            {{-- CASE 1: MENGGUNAKAN CUSTOM BACKGROUND DARI COURSE --}}
            @if($certificate->enrollment->course->certificateBackground)
                <img src="{{ asset('storage/' . $certificate->enrollment->course->certificateBackground->file_path) }}" 
                     alt="Certificate Background" 
                     class="absolute inset-0 w-full h-full object-cover z-0">
                
                <!-- Overlay Konten Teks di atas Custom Background -->
                <div class="relative z-10 h-full flex flex-col justify-between text-center py-6">
                    <div>
                        <p class="text-xs sm:text-sm font-semibold tracking-widest text-slate-600 uppercase">Sertifikat Kelulusan</p>
                        <p class="text-[10px] sm:text-xs text-slate-500 font-mono mt-1">No: {{ $certificate->certificate_number }}</p>
                    </div>

                    <div class="my-auto space-y-3">
                        <p class="text-xs sm:text-sm text-slate-600">Diberikan kepada:</p>
                        <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-wide font-serif border-b-2 border-slate-800/20 inline-block pb-2 px-8">
                            {{ $certificate->enrollment->user->name }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 max-w-xl mx-auto pt-2">
                            Atas keberhasilannya menyelesaikan seluruh modul pembelajaran pada kursus:
                        </p>
                        <h3 class="text-lg sm:text-2xl font-bold text-indigo-900">
                            {{ $certificate->enrollment->course->title }}
                        </h3>
                    </div>

                    <div class="flex items-end justify-between text-left text-[10px] sm:text-xs text-slate-600 border-t border-slate-300/60 pt-4">
                        <div>
                            <p class="font-semibold text-slate-800">LPP-JPH Universitas Telkom</p>
                            <p>Diterbitkan: {{ \Carbon\Carbon::parse($certificate->issued_at)->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-mono text-[9px] sm:text-[11px] text-slate-500">Kode Verifikasi:</p>
                            <code class="font-bold text-slate-800 bg-slate-100/80 px-2 py-0.5 rounded border border-slate-300/50">{{ $certificate->verification_code }}</code>
                            <p class="text-[9px] text-slate-400 mt-1">
                                Verifikasi: {{ route('certificates.verify', $certificate->verification_code) }}
                            </p>
                        </div>
                    </div>
                </div>

            {{-- CASE 2: TEMPLATE DEFAULT (HTML / CSS FALLBACK) --}}
            @else
                <div class="absolute inset-2 border-4 border-double border-amber-600/40 rounded-lg pointer-events-none"></div>

                <div class="relative z-10 h-full flex flex-col justify-between text-center py-4">
                    <!-- Header Sertifikat Standard -->
                    <div class="space-y-1">
                        <div class="w-12 h-12 bg-amber-500 text-white font-bold rounded-full flex items-center justify-center mx-auto text-lg shadow-sm">
                            ✓
                        </div>
                        <h1 class="text-xl sm:text-3xl font-extrabold tracking-widest text-slate-800 uppercase font-serif">Sertifikat Kelulusan</h1>
                        <p class="text-xs text-amber-700 font-semibold tracking-wider uppercase">Lembaga Pelaksana Pelatihan Jaminan Produk Halal</p>
                        <p class="text-[11px] text-slate-400 font-mono pt-1">No: {{ $certificate->certificate_number }}</p>
                    </div>

                    <!-- Body Teks -->
                    <div class="my-auto space-y-2 sm:space-y-4">
                        <p class="text-xs sm:text-sm text-slate-500 italic">Dengan ini menyatakan bahwa:</p>
                        <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-wide font-serif border-b-2 border-amber-500/30 inline-block pb-1 px-8">
                            {{ $certificate->enrollment->user->name }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto">
                            Telah lulus dan menyelesaikan seluruh kriteria penilaian pada pelatihan:
                        </p>
                        <h3 class="text-base sm:text-2xl font-bold text-amber-800 uppercase tracking-wide">
                            {{ $certificate->enrollment->course->title }}
                        </h3>
                    </div>

                    <!-- Footer Info & Verifikasi -->
                    <div class="flex items-end justify-between text-left text-[10px] sm:text-xs text-slate-600 pt-4 border-t border-slate-200">
                        <div class="space-y-0.5">
                            <p class="font-bold text-slate-800">Universitas Telkom</p>
                            <p class="text-slate-500">Tanggal Terbit: {{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y') }}</p>
                        </div>
                        <div class="text-right space-y-0.5">
                            <p class="text-[10px] text-slate-400">Kode Verifikasi Keabsahan:</p>
                            <code class="font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">{{ $certificate->verification_code }}</code>
                            <p class="text-[9px] text-slate-400">Cek keaslian di: <span class="underline">{{ route('certificates.verify') }}</span></p>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>

<!-- CSS khusus instruksi cetak PDF Landscape -->
<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            background: white !important;
            padding: 0 !important;
        }
        .print\:hidden {
            display: none !important;
        }
    }
</style>
@endsection