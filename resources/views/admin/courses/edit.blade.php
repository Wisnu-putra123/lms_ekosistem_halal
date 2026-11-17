@extends('layouts.app')

@section('title', 'Edit Pelatihan - LPP-JPH LMS')
@section('header_title', 'Edit Pelatihan')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <!-- Tombol Navigasi Kembali -->
    <div class="flex items-center justify-between gap-2">
        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Daftar</span>
        </a>

        <a href="{{ route('admin.courses.show', $course) }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-[#123F35] hover:text-[#28745B] transition bg-[#F7F5EE] px-3.5 py-2 rounded-xl border border-[#28745B]/30">
            <span>Lihat Detail Pelatihan</span>
            <span>&rarr;</span>
        </a>
    </div>

    <!-- Container Utama -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">

        <!-- Header Container Warna Hijau bg-[#28745B] -->
        <div class="bg-[#28745B] p-4 sm:p-5 px-6 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <h2 class="text-base font-bold text-white tracking-wide">Edit Informasi Pelatihan</h2>
            </div>
            <span class="px-2.5 py-1 rounded-lg bg-white/15 text-[#E7C982] font-mono text-xs font-bold">
                {{ $course->code }}
            </span>
        </div>

        <!-- Body Form -->
        <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <!-- Kode & Judul -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="code" class="block text-xs font-bold text-[#123F35] mb-1">Kode Pelatihan <span class="text-red-500">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code', $course->code) }}" required
                           class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm font-bold uppercase text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                    @error('code')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="title" class="block text-xs font-bold text-[#123F35] mb-1">Judul Pelatihan <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" required
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
                    <option value="penyelia_halal" {{ old('category', $course->category) === 'penyelia_halal' ? 'selected' : '' }}>Penyelia Halal</option>
                    <option value="juleha" {{ old('category', $course->category) === 'juleha' ? 'selected' : '' }}>Juleha (Juru Sembelih Halal)</option>
                    <option value="auditor_halal" {{ old('category', $course->category) === 'auditor_halal' ? 'selected' : '' }}>Auditor Halal</option>
                </select>
                @error('category')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Thumbnail Gambar -->
            <div class="space-y-2">
                <label for="thumbnail" class="block text-xs font-bold text-[#123F35]">Gambar Thumbnail Pelatihan</label>

                @if($course->thumbnail)
                    <div class="flex items-center space-x-3 p-2.5 bg-[#F7F5EE]/60 rounded-xl border border-slate-300">
                        <img src="{{ asset('storage/' . $course->thumbnail->file_path) }}" alt="Thumbnail saat ini"
                             class="w-16 h-12 rounded-lg object-cover border border-slate-300 flex-shrink-0">
                        <div class="min-w-0 text-[11px]">
                            <p class="font-bold text-[#123F35] truncate">{{ $course->thumbnail->file_name }}</p>
                            <p class="text-slate-500">Unggah file baru di bawah jika ingin mengganti thumbnail ini.</p>
                        </div>
                    </div>
                @endif

                <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 py-1 px-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#28745B]/15 file:text-[#123F35] hover:file:bg-[#28745B]/25 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl cursor-pointer">
                <p class="text-[11px] text-slate-400">Format: JPG, PNG, WEBP. Ukuran maksimal 2MB.</p>
                @error('thumbnail')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Custom Background Sertifikat -->
            <div class="p-4 bg-[#F7F5EE] rounded-2xl border border-slate-300 space-y-2.5">
                <label for="certificate_bg" class="block text-xs font-bold text-[#123F35] flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <span>Custom Template Background Sertifikat</span>
                </label>

                @if($course->certificateBg)
                    <div class="flex items-center space-x-3 p-2.5 bg-white rounded-xl border border-slate-300">
                        <img src="{{ asset('storage/' . $course->certificateBg->file_path) }}" alt="Background sertifikat saat ini"
                             class="w-16 h-12 rounded-lg object-cover border border-slate-300 flex-shrink-0">
                        <div class="min-w-0 text-[11px]">
                            <p class="font-bold text-[#123F35] truncate">{{ $course->certificateBg->file_name }}</p>
                            <p class="text-slate-500">Template background aktif. Unggah file baru untuk menggantinya.</p>
                        </div>
                    </div>
                @endif

                <input type="file" id="certificate_bg" name="certificate_bg" accept="image/jpeg,image/png,image/jpg"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 py-1 px-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#28745B]/20 file:text-[#123F35] hover:file:bg-[#28745B]/30 bg-white border border-slate-300 rounded-xl cursor-pointer">
                <p class="text-[11px] text-slate-500">Format: JPG, PNG (maks. 5MB), ukuran landscape A4.</p>
                @error('certificate_bg')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Enrollment Key -->
            <div>
                <label for="enrollment_key" class="block text-xs font-bold text-[#123F35] mb-1">
                    Enrollment Key (Kunci Pendaftaran Peserta)
                </label>
                <input type="text" id="enrollment_key" name="enrollment_key"
                       value="{{ old('enrollment_key', $course->enrollment_key) }}"
                       placeholder="Kosongkan jika pendaftaran terbuka tanpa kunci"
                       class="w-full px-3.5 py-2.5 bg-[#F7F5EE] border border-slate-300 rounded-xl text-sm font-extrabold text-[#123F35] uppercase focus:outline-none focus:ring-2 focus:ring-[#28745B] tracking-wider transition">
                @error('enrollment_key')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Assign Pengajar (Instruktur) -->
            @php
                $selectedInstructors = old('instructors', $assignedInstructorIds);
            @endphp
            <div>
                <label class="block text-xs font-bold text-[#123F35] mb-1">Tugaskan Pengajar (Instruktur)</label>
                <div class="bg-[#F7F5EE]/60 border border-slate-300 rounded-xl p-3 max-h-44 overflow-y-auto space-y-2">
                    @forelse($teachers as $teacher)
                        <label class="flex items-center space-x-2.5 text-xs text-slate-700 cursor-pointer hover:bg-white p-1.5 rounded-lg transition">
                            <input type="checkbox" name="instructors[]" value="{{ $teacher->id }}"
                                   {{ is_array($selectedInstructors) && in_array($teacher->id, $selectedInstructors) ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#28745B] border-slate-300 rounded focus:ring-[#28745B]">
                            <span class="font-semibold text-[#123F35]">
                                {{ $teacher->name }}
                                <span class="text-slate-400 font-normal">({{ $teacher->email }})</span>
                            </span>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada akun pengajar (role teacher) yang tersedia.</p>
                    @endforelse
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Centang pengajar yang ditugaskan untuk mengelola dan mengajar kelas ini.</p>
                @error('instructors')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-xs font-bold text-[#123F35] mb-1">Deskripsi Pelatihan</label>
                <textarea id="description" name="description" rows="4" placeholder="Jelaskan gambaran umum pelatihan..."
                          class="w-full p-3.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Pelatihan -->
            <div>
                <label for="status" class="block text-xs font-bold text-[#123F35] mb-1">Status Publikasi <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                        class="w-full px-3.5 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm font-semibold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition cursor-pointer">
                    <option value="draft" {{ old('status', $course->status) === 'draft' ? 'selected' : '' }}>Draft (Belum Dipublikasikan)</option>
                    <option value="published" {{ old('status', $course->status) === 'published' ? 'selected' : '' }}>Published (Aktif &amp; Dapat Diakses Peserta)</option>
                    <option value="archived" {{ old('status', $course->status) === 'archived' ? 'selected' : '' }}>Archived (Diarsipkan)</option>
                </select>
                @error('status')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-200">
                <a href="{{ route('admin.courses.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection