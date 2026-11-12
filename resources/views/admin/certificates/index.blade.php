@extends('layouts.app')

@section('title', 'Manajemen Sertifikat - Admin LPP-JPH LMS')
@section('header_title', 'Kelola & Penerbitan Sertifikat')

@section('content')
<div class="space-y-6" x-data="{
    selectedCerts: [],
    allIds: [{{ $certificates->pluck('id')->implode(',') }}],
    toggleAll(checked) {
        this.selectedCerts = checked ? [...this.allIds] : [];
    }
}">

    <!-- Header Banner & Action Bar -->
    <div class="bg-[#123F35] p-5 sm:p-6 rounded-2xl border border-[#E7C982]/20 shadow-md flex flex-col lg:flex-row lg:items-center justify-between gap-4 text-white relative overflow-hidden">
        <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
        <div class="absolute -right-10 -bottom-10 -z-0 h-48 w-48 rounded-full border border-[#E7C982]/10 blur-xl"></div>
        
        <div class="relative z-10 space-y-1">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                <span>Penerbitan & Manajemen Sertifikat Kelulusan</span>
            </h2>
            <p class="text-xs text-emerald-100/80">Kelola status penerbitan (Draft &harr; Published), ekspor paket ZIP untuk BPJPH, serta unggah massal sertifikat bertanda tangan.</p>
        </div>

        <!-- Global Batch Action Buttons -->
        <div class="relative z-10 flex flex-wrap items-center gap-2.5">
            <!-- Tombol Download Draft ZIP berdasarkan Filter Kelas -->
            @if($courseId)
                <a href="{{ route('admin.certificates.download-zip', ['course_id' => $courseId]) }}" 
                   class="inline-flex items-center space-x-2 bg-[#E7C982] hover:bg-[#d8b76e] text-[#123F35] px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-xs">
                    <svg class="w-4 h-4 text-[#123F35]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Download Draft ZIP (Kelas Ini)</span>
                </a>
            @else
                <button type="button" onclick="alert('Silakan pilih filter Kelas/Pelatihan terlebih dahulu, atau centang sertifikat pada tabel untuk mengunduh ZIP.')" 
                        class="inline-flex items-center space-x-2 bg-white/10 hover:bg-white/20 text-white/80 px-4 py-2.5 rounded-xl text-xs font-bold transition border border-white/20 cursor-pointer">
                    <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Download Draft ZIP</span>
                </button>
            @endif

            <!-- Tombol Buka Modal Bulk Upload TTD (Multi-PDF / ZIP) -->
            <button type="button" onclick="openBulkUploadModal()" 
                    class="inline-flex items-center space-x-2 bg-[#28745B] hover:bg-[#1e5a46] text-white border border-white/20 px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                <svg class="w-4 h-4 text-[#E7C982]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                <span>Bulk Upload TTD (PDF / ZIP)</span>
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center space-x-2">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-semibold flex items-center space-x-2">
            <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs space-y-1">
            <div class="font-bold">Terjadi kesalahan validasi:</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter & Baris Pencarian -->
    <div class="bg-white p-5 rounded-2xl border border-[#28745B]/15 shadow-2xs">
        <form action="{{ route('admin.certificates.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <!-- Pencarian -->
            <div>
                <label for="search" class="block font-bold text-[#123F35] mb-1">Cari Siswa / No. Sertifikat</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Nama, email, atau no. cert..."
                           class="w-full pl-9 pr-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-200 rounded-xl text-xs text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Filter Pelatihan -->
            <div>
                <label for="course_id" class="block font-bold text-[#123F35] mb-1">Filter Kelas / Pelatihan</label>
                <select id="course_id" name="course_id" class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-200 rounded-xl text-xs font-semibold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                    <option value="">-- Semua Pelatihan --</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ (string)$courseId === (string)$c->id ? 'selected' : '' }}>
                            [{{ $c->code }}] {{ $c->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label for="status" class="block font-bold text-[#123F35] mb-1">Filter Status Penerbitan</label>
                <select id="status" name="status" class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-200 rounded-xl text-xs font-semibold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                    <option value="">-- Semua Status --</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft (Menunggu Publish / BPJPH)</option>
                    <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Published (Terbit ke Student)</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-[#28745B] hover:bg-[#123F35] text-white py-2.5 rounded-xl font-bold transition shadow-xs text-center cursor-pointer">
                    Terapkan Filter
                </button>
                @if($search || $courseId || $status)
                    <a href="{{ route('admin.certificates.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bulk Action Toolbar (Tampil Otomatis Saat Ada Item Di-check) -->
    <div x-show="selectedCerts.length > 0" x-cloak 
         class="bg-[#E7C982]/25 border border-[#E7C982] p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition">
        <div class="text-xs font-bold text-[#123F35] flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#123F35] text-[#E7C982] text-[11px]" x-text="selectedCerts.length"></span>
            <span>sertifikat dipilih untuk aksi massal</span>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Form Bulk Download ZIP Terpilih -->
            <form action="{{ route('admin.certificates.download-zip') }}" method="POST" class="inline">
                @csrf
                <template x-for="id in selectedCerts" :key="'zip-' + id">
                    <input type="hidden" name="selected_ids[]" :value="id">
                </template>
                <button type="submit" 
                        class="inline-flex items-center space-x-1.5 bg-[#123F35] hover:bg-[#0d2e27] text-[#E7C982] px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Download ZIP Terpilih</span>
                </button>
            </form>

            <!-- Form Bulk Publish -->
            <form action="{{ route('admin.certificates.bulk-status') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="target_status" value="published">
                <template x-for="id in selectedCerts" :key="'pub-' + id">
                    <input type="hidden" name="certificate_ids[]" :value="id">
                </template>
                <button type="submit" onclick="return confirm('Publikasikan seluruh sertifikat terpilih ke Student?')" 
                        class="bg-[#28745B] hover:bg-[#1e5a46] text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                    Publish Terpilih
                </button>
            </form>

            <!-- Form Bulk Set to Draft (Unpublish) -->
            <form action="{{ route('admin.certificates.bulk-status') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="target_status" value="draft">
                <template x-for="id in selectedCerts" :key="'draft-' + id">
                    <input type="hidden" name="certificate_ids[]" :value="id">
                </template>
                <button type="submit" onclick="return confirm('Kembalikan status seluruh sertifikat terpilih menjadi Draft? Student tidak akan dapat mengunduhnya selama berstatus Draft.')" 
                        class="bg-white hover:bg-amber-50 text-amber-800 border border-amber-300 px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                    Jadikan Draft
                </button>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Sertifikat -->
    <div class="bg-white rounded-2xl border border-[#28745B]/15 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F7F5EE] border-b border-[#28745B]/15 text-[#123F35] font-extrabold uppercase tracking-wider">
                        <th class="p-4 w-10 text-center">
                            <input type="checkbox" @change="toggleAll($event.target.checked)" 
                                   :checked="allIds.length > 0 && selectedCerts.length === allIds.length"
                                   class="rounded border-slate-300 text-[#28745B] focus:ring-[#28745B] cursor-pointer">
                        </th>
                        <th class="p-4">Siswa & Kelas</th>
                        <th class="p-4">Nomor & Kode Verifikasi</th>
                        <th class="p-4 text-center">Status & Berkas</th>
                        <th class="p-4">Waktu</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($certificates as $cert)
                        <tr class="hover:bg-[#F7F5EE]/40 transition" :class="selectedCerts.includes({{ $cert->id }}) ? 'bg-[#E7C982]/10' : ''">
                            <td class="p-4 text-center">
                                <input type="checkbox" :value="{{ $cert->id }}" x-model.number="selectedCerts"
                                       class="rounded border-slate-300 text-[#28745B] focus:ring-[#28745B] cursor-pointer">
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-[#123F35] text-sm">{{ $cert->enrollment->user->name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $cert->enrollment->user->email ?? '' }}</div>
                                <div class="mt-1.5 inline-block text-[10px] font-bold text-[#28745B] bg-[#28745B]/10 px-2 py-0.5 rounded border border-[#28745B]/20">
                                    [{{ $cert->enrollment->course->code ?? '-' }}] {{ $cert->enrollment->course->title ?? '-' }}
                                </div>
                            </td>
                            <td class="p-4 space-y-1">
                                <div class="font-mono font-bold text-[#123F35]">{{ $cert->certificate_number }}</div>
                                <div class="text-[10px] text-slate-400">
                                    Kode: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 font-bold">{{ $cert->verification_code }}</code>
                                </div>
                                <div class="text-[10px] text-slate-400">ID Sistem: #{{ $cert->id }}</div>
                            </td>
                            <td class="p-4 text-center">
                                @if($cert->status === 'published')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                        Draft
                                    </span>
                                @endif

                                @if($cert->signed_media_id)
                                    <div class="text-[10px] text-[#28745B] font-bold mt-1">✓ PDF Ber-TTD BPJPH</div>
                                @else
                                    <div class="text-[10px] text-slate-400 mt-1">PDF Generate Sistem</div>
                                @endif
                            </td>
                            <td class="p-4 text-slate-500 text-[11px] space-y-0.5">
                                <div>Dibuat: {{ $cert->created_at->format('d M Y H:i') }}</div>
                                @if($cert->status === 'published' && $cert->issued_at)
                                    <div class="text-emerald-700 font-semibold">Terbit: {{ $cert->issued_at->format('d M Y') }}</div>
                                @endif
                                @if($cert->approver)
                                    <div class="text-[10px] text-slate-400">Admin: {{ $cert->approver->name }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end flex-wrap gap-1.5">
                                    
                                    <!-- Toggle Status Bolak-Balik: Publish <-> Draft -->
                                    <form action="{{ route('admin.certificates.toggle-status', $cert->id) }}" method="POST" class="inline">
                                        @csrf
                                        @if($cert->status === 'draft')
                                            <button type="submit" onclick="return confirm('Publikasikan sertifikat ini ke Student?')" 
                                                    class="px-2.5 py-1.5 bg-[#28745B] hover:bg-[#123F35] text-white text-[11px] font-bold rounded-lg transition shadow-2xs cursor-pointer"
                                                    title="Terbitkan ke Student">
                                                Publish
                                            </button>
                                        @else
                                            <button type="submit" onclick="return confirm('Kembalikan status sertifikat ini menjadi Draft? Student tidak akan bisa mengunduhnya sementara.')" 
                                                    class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 text-[11px] font-bold rounded-lg transition cursor-pointer"
                                                    title="Tarik kembali ke Draft">
                                                Set Draft
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Upload TTD Satuan -->
                                    <button type="button" onclick="openSingleUploadModal({{ $cert->id }}, '{{ addslashes($cert->enrollment->user->name ?? '') }}', '{{ $cert->certificate_number }}')" 
                                            class="px-2.5 py-1.5 bg-[#E7C982]/30 hover:bg-[#E7C982]/60 text-[#123F35] border border-[#E7C982] text-[11px] font-bold rounded-lg transition cursor-pointer">
                                        Upload TTD
                                    </button>

                                    <!-- Regenerate PDF -->
                                    <form action="{{ route('admin.certificates.regenerate', $cert->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Regenerate ulang file PDF sertifikat ini dari template?')" 
                                                class="p-1.5 text-slate-400 hover:text-[#28745B] transition rounded-lg hover:bg-slate-100 cursor-pointer" title="Regenerate PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>
                                    </form>

                                    <!-- Pratinjau PDF Aktif -->
                                    @if($cert->active_media)
                                        <a href="{{ asset('storage/' . $cert->active_media->file_path) }}?v={{ $cert->active_media->updated_at->timestamp }}" target="_blank" 
                                           class="p-1.5 text-slate-400 hover:text-[#123F35] transition rounded-lg hover:bg-slate-100" title="Lihat PDF Aktif">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Tidak ada data sertifikat yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $certificates->links() }}
        </div>
    </div>
</div>

<!-- Modal 1: Upload Satuan Sertifikat Ber-TTD BPJPH -->
<div id="singleUploadModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-[#28745B]/20 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-[#123F35]">Upload Sertifikat Ber-TTD (Satuan)</h3>
            <button type="button" onclick="closeSingleUploadModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
        </div>

        <form id="singleUploadForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div class="p-3 bg-[#F7F5EE] rounded-xl border border-[#28745B]/15 space-y-0.5">
                <div class="text-slate-500">Peserta: <strong id="modalStudentName" class="text-[#123F35]"></strong></div>
                <div class="text-slate-500">No. Sertifikat: <span id="modalCertNumber" class="font-mono font-bold text-[#28745B]"></span></div>
            </div>

            <div>
                <label for="signed_pdf" class="block font-bold text-[#123F35] mb-1">Pilih Berkas PDF Ber-TTD (*.pdf)</label>
                <input type="file" id="signed_pdf" name="signed_pdf" accept="application/pdf" required
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 py-1 px-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#28745B]/15 file:text-[#123F35] hover:file:bg-[#28745B]/25 bg-[#F7F5EE]/60 border border-slate-200 rounded-xl cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Maksimal ukuran file: 10MB.</p>
            </div>

            <div class="flex items-center space-x-2">
                <input type="checkbox" id="single_auto_publish" name="auto_publish" value="1" checked
                       class="rounded border-slate-300 text-[#28745B] focus:ring-[#28745B]">
                <label for="single_auto_publish" class="text-slate-700 font-semibold">Langsung publikasikan (Published) setelah diunggah</label>
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="closeSingleUploadModal()" class="px-4 py-2 font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 rounded-xl font-bold transition shadow-xs cursor-pointer">
                    Simpan Berkas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Bulk Upload Sertifikat Ber-TTD BPJPH (Multi-PDF / ZIP) -->
<div id="bulkUploadModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#28745B]/20 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-[#123F35]">Bulk Upload Sertifikat Ber-TTD (PDF / ZIP)</h3>
            <button type="button" onclick="closeBulkUploadModal()" class="text-slate-400 hover:text-slate-600 cursor-pointer">✕</button>
        </div>

        <form action="{{ route('admin.certificates.bulk-upload-signed') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div class="p-3.5 bg-[#F7F5EE] rounded-xl border border-[#E7C982]/60 space-y-1.5 text-slate-600">
                <div class="font-bold text-[#123F35]">Panduan Penamaan File Otomatis:</div>
                <p class="leading-relaxed">
                    Anda dapat mengunggah <strong>banyak file PDF sekaligus</strong> atau <strong>1 file arsip .ZIP</strong> balikan dari BPJPH. Sistem akan mencocokkan file secara otomatis berdasarkan:
                </p>
                <ul class="list-disc list-inside text-[11px] space-y-0.5 text-slate-600">
                    <li>Format bawaan Download ZIP: <code class="bg-white px-1 rounded">Sertifikat_[KODE]_[ID]_[Nama].pdf</code></li>
                    <li>Atau nama file yang memuat <strong>Nomor Sertifikat</strong> (contoh: <code class="bg-white px-1 rounded">CERT-SJH-2026-202611-0001.pdf</code>)</li>
                </ul>
            </div>

            <div>
                <label for="signed_files" class="block font-bold text-[#123F35] mb-1">Pilih Banyak File PDF atau File ZIP (*.pdf, *.zip)</label>
                <input type="file" id="signed_files" name="signed_files[]" accept=".pdf,.zip,application/pdf,application/zip" multiple required
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 py-1 px-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#28745B]/15 file:text-[#123F35] hover:file:bg-[#28745B]/25 bg-[#F7F5EE]/60 border border-slate-200 rounded-xl cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Gunakan Ctrl / Shift untuk memilih banyak file PDF sekaligus, atau pilih 1 file ZIP (Max 50MB).</p>
            </div>

            <div class="flex items-center space-x-2">
                <input type="checkbox" id="bulk_auto_publish" name="auto_publish" value="1" checked
                       class="rounded border-slate-300 text-[#28745B] focus:ring-[#28745B]">
                <label for="bulk_auto_publish" class="text-slate-700 font-semibold">Langsung publikasikan (Published) seluruh sertifikat yang cocok</label>
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                <button type="button" onclick="closeBulkUploadModal()" class="px-4 py-2 font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 rounded-xl font-bold transition shadow-xs cursor-pointer">
                    Mulai Proses Bulk Upload
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSingleUploadModal(certId, studentName, certNumber) {
        const modal = document.getElementById('singleUploadModal');
        const form = document.getElementById('singleUploadForm');
        document.getElementById('modalStudentName').innerText = studentName;
        document.getElementById('modalCertNumber').innerText = certNumber;

        form.action = `/admin/certificates/${certId}/upload-signed`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeSingleUploadModal() {
        const modal = document.getElementById('singleUploadModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function openBulkUploadModal() {
        const modal = document.getElementById('bulkUploadModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeBulkUploadModal() {
        const modal = document.getElementById('bulkUploadModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
@endsection