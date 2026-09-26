@extends('layouts.app')

@section('title', 'Buat Kursus Baru - Ekosistem Halal')
@section('header_title', 'Buat Kursus Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-4 mb-5 flex items-center space-x-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Informasi Pelatihan Baru</span>
        </h2>

        <form action="{{ route('teacher.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Kode & Judul -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 mb-1">Kode Kursus <span class="text-red-500">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="HL-101" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm uppercase focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                    @error('code')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="title" class="block text-xs font-semibold text-slate-700 mb-1">Judul Pelatihan <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Contoh: Penyelia Halal Level Dasar" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                    @error('title')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Upload Thumbnail Gambar -->
            <div>
                <label for="thumbnail" class="block text-xs font-semibold text-slate-700 mb-1">Gambar Thumbnail Kursus</label>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 py-1 px-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 bg-slate-50 border border-slate-200 rounded-lg cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Ukuran maksimal 2MB.</p>
                @error('thumbnail')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Enrollment Key -->
            <div>
                <label for="enrollment_key" class="block text-xs font-semibold text-slate-700 mb-1">
                    Enrollment Key (Kunci Pendaftaran Student) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="enrollment_key" name="enrollment_key" value="{{ old('enrollment_key', $generatedKey) }}" required
                           class="w-full px-3.5 py-2.5 bg-amber-50 border border-amber-200 rounded-lg text-sm font-bold text-amber-800 uppercase focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Kunci ini dibuat secara otomatis oleh sistem. Anda dapat mengubahnya jika perlu.</p>
                @error('enrollment_key')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Assign Pengajar Tambahan (course_instructors) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pengajar Tambahan (Instruktur)</label>
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 max-h-40 overflow-y-auto space-y-2">
                    @forelse($teachers as $teacher)
                        <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer hover:bg-slate-100 p-1.5 rounded transition">
                            <input type="checkbox" name="instructors[]" value="{{ $teacher->id }}"
                                   {{ is_array(old('instructors')) && in_array($teacher->id, old('instructors')) ? 'checked' : '' }}
                                   class="w-4 h-4 text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                            <span>{{ $teacher->name }} <span class="text-slate-400">({{ $teacher->email }})</span></span>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400">Tidak ada pengajar lain yang tersedia.</p>
                    @endforelse
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Anda secara otomatis menjadi pengajar utama (pembuat kursus).</p>
                @error('instructors')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Pelatihan</label>
                <textarea id="description" name="description" rows="4" placeholder="Jelaskan gambaran umum pelatihan..."
                          class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition">{{ old('description') }}</textarea>
            </div>

            <!-- Status Pelatihan (Enum) -->
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Status Pelatihan <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Belum Dipublikasikan)</option>
                    <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Published (Aktif & Bisa Dicari Student)</option>
                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived (Diarsipkan)</option>
                </select>
                @error('status')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ route('teacher.courses.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-5 py-2.5 rounded-lg transition shadow-sm">
                    Simpan Kursus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection