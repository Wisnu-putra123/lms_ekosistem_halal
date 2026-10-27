@extends('layouts.app')

@section('title', 'Verifikasi Keaslian Sertifikat - LPP-JPH')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 space-y-6">

    <!-- Header Section -->
    <div class="text-center space-y-2">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 mb-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-800">Verifikasi Keaslian Sertifikat</h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
            Masukkan Kode Verifikasi atau Nomor Sertifikat untuk memeriksa validitas dokumen yang diterbitkan oleh LPP-JPH Universitas Telkom.
        </p>
    </div>

    <!-- Form Pencarian / Input Kode -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('certificates.verify') }}" method="GET" class="space-y-4">
            <div>
                <label for="code" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Kode Verifikasi / Nomor Sertifikat
                </label>
                <div class="relative">
                    <input type="text" 
                           name="code" 
                           id="code" 
                           value="{{ $searchCode }}" 
                           placeholder="Contoh: A8F9-2K7L-90PQ atau CERT/HL/202610/0001" 
                           required 
                           class="w-full pl-4 pr-24 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500 uppercase tracking-wide">
                    <button type="submit" 
                            class="absolute right-1.5 top-1.5 bottom-1.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">
                        Cek Validitas
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- HASIL VERIFIKASI -->
    @if($searched)
        @if($certificate)
            <!-- CASE 1: SERTIFIKAT VALID -->
            <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-emerald-50 border-b border-emerald-100 flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                        ✓
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-emerald-900">Sertifikat Valid & Terverifikasi</h3>
                        <p class="text-[11px] text-emerald-700">Dokumen ini resmi diterbitkan oleh LPP-JPH Universitas Telkom.</p>
                    </div>
                </div>

                <div class="p-6 space-y-4 text-xs sm:text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="text-slate-400 text-xs block">Nomor Sertifikat:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $certificate->certificate_number }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs block">Kode Verifikasi:</span>
                            <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 inline-block">{{ $certificate->verification_code }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs block">Nama Penerima:</span>
                            <span class="font-bold text-slate-900 text-base">{{ $certificate->enrollment->user->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs block">Tanggal Diterbitkan:</span>
                            <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y (H:i T)') }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <span class="text-slate-400 text-xs block mb-1">Nama Kursus / Pelatihan:</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="font-bold text-slate-800 text-sm sm:text-base">{{ $certificate->enrollment->course->title }}</p>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">Kode Course: {{ $certificate->enrollment->course->code }}</p>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- CASE 2: SERTIFIKAT TIDAK DITEMUK -->
            <div class="bg-white rounded-2xl border border-red-200 shadow-sm p-6 text-center space-y-3">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto text-lg font-bold">
                    ✕
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Sertifikat Tidak Ditemukan</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Kode verifikasi <code class="bg-slate-100 px-1.5 py-0.5 rounded text-red-600 font-mono">{{ $searchCode }}</code> tidak cocok dengan data sertifikat mana pun. Pastikan Anda memasukkan kode dengan benar.
                    </p>
                </div>
            </div>
        @endif
    @endif

</div>
@endsection