@extends('layouts.app')

@section('title', 'Buat Kursus Baru - LPP-JPH LMS')
@section('header_title', 'Buat Kursus Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    
    <!-- Tombol Navigasi Kembali ke Katalog -->
    <div class="flex items-center justify-start">
        <a href="{{ route('teacher.courses.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Katalog Pelatihan</span>
        </a>
    </div>

    <!-- Container Utama -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        
        <!-- Header Container Warna Hijau bg-[#28745B] -->
        <div class="bg-[#28745B] p-4 sm:p-5 px-6 text-white flex items-center space-x-2.5">
            <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <h2 class="text-base font-bold text-white tracking-wide">Informasi Pelatihan Baru</h2>
        </div>

        <!-- Body Form -->
        <form action="{{ route('teacher.courses.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            <!-- Kode & Judul -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="code" class="block text-xs font-bold text-[#123F35] mb-1">Kode Kursus <span class="text-red-500">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="SJH-2026" required
                           class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm font-bold uppercase text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                    @error('code')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="title" class="block text-xs font-bold text-[#123F35] mb-1">Judul Pelatihan <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Contoh: Pelatihan Sistem Jaminan Produk Halal" required
                           class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm font-semibold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                    @error('title')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kategori Pelatihan -->
            <div>
                <label for="category" class="block text-xs font-bold text-[#123F35] mb-1">Kategori Pelatihan <span class="text-red-500">*</span></label>
                <select id="category" name="category" required
                        class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm font-semibold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition cursor-pointer">
                    <option value="" disabled {{ old('category') ? '' : 'selected' }}>Pilih Kategori Pelatihan</option>
                    <option value="penyelia_halal" {{ old('category') === 'penyelia_halal' ? 'selected' : '' }}>Penyelia Halal</option>
                    <option value="juleha" {{ old('category') === 'juleha' ? 'selected' : '' }}>Juleha (Juru Sembelih Halal)</option>
                    <option value="auditor_halal" {{ old('category') === 'auditor_halal' ? 'selected' : '' }}>Auditor Halal</option>
                </select>
                @error('category')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Thumbnail Gambar -->
            <div>
                <label for="thumbnail" class="block text-xs font-bold text-[#123F35] mb-1">Gambar Thumbnail Kursus</label>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 py-1 px-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#28745B]/15 file:text-[#123F35] hover:file:bg-[#28745B]/25 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Ukuran maksimal 2MB.</p>
                @error('thumbnail')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Custom Background Sertifikat -->
            <div class="p-4 bg-[#F7F5EE] rounded-2xl border border-slate-300 space-y-2">
                <label for="certificate_background" class="block text-xs font-bold text-[#123F35] flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <span>Custom Template Background Sertifikat (Opsional)</span>
                </label>
                <input type="file" id="certificate_background" name="certificate_background" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 py-1 px-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#28745B]/20 file:text-[#123F35] hover:file:bg-[#28745B]/30 bg-white border border-slate-300 rounded-xl cursor-pointer">
                <p class="text-[11px] text-slate-500">Unggah template gambar landscape (A4). Jika dikosongkan, sertifikat akan menggunakan tampilan standar sistem.</p>
                @error('certificate_background')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Enrollment Key -->
            <div>
                <label for="enrollment_key" class="block text-xs font-bold text-[#123F35] mb-1">
                    Enrollment Key (Kunci Pendaftaran Student) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="enrollment_key" name="enrollment_key" value="{{ old('enrollment_key', $generatedKey) }}" required
                           class="w-full px-3.5 py-2.5 bg-[#F7F5EE] border border-slate-300 rounded-xl text-sm font-extrabold text-[#123F35] uppercase focus:outline-none focus:ring-2 focus:ring-[#28745B] tracking-wider transition">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Kunci ini dibuat secara otomatis oleh sistem. Anda dapat mengubahnya jika perlu.</p>
                @error('enrollment_key')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Assign Pengajar Tambahan -->
            <div>
                <label class="block text-xs font-bold text-[#123F35] mb-1">Pengajar Tambahan (Instruktur)</label>
                <div class="bg-[#F7F5EE]/60 border border-slate-300 rounded-xl p-3 max-h-40 overflow-y-auto space-y-2">
                    @forelse($teachers as $teacher)
                        <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer hover:bg-white p-1.5 rounded-lg transition">
                            <input type="checkbox" name="instructors[]" value="{{ $teacher->id }}"
                                   {{ is_array(old('instructors')) && in_array($teacher->id, old('instructors')) ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#28745B] border-slate-300 rounded focus:ring-[#28745B]">
                            <span class="font-semibold text-[#123F35]">{{ $teacher->name }} <span class="text-slate-400 font-normal">({{ $teacher->email }})</span></span>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400 italic">Tidak ada pengajar lain yang tersedia.</p>
                    @endforelse
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Anda secara otomatis menjadi pengajar utama (pembuat kursus).</p>
                @error('instructors')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-xs font-bold text-[#123F35] mb-1">Deskripsi Pelatihan</label>
                <textarea id="description" name="description" rows="4" placeholder="Jelaskan gambaran umum pelatihan..."
                          class="w-full p-3.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">{{ old('description') }}</textarea>
            </div>

            <!-- Status Pelatihan -->
            <div>
                <label for="status" class="block text-xs font-bold text-[#123F35] mb-1">Status Pelatihan <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                        class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm font-semibold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition cursor-pointer">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Belum Dipublikasikan)</option>
                    <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Aktif & Bisa Dicari Student)</option>
                    <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived (Diarsipkan)</option>
                </select>
                @error('status')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-200">
                <a href="{{ route('teacher.courses.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-xs">
                    Simpan Kursus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection