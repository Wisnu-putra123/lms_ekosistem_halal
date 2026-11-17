@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru - LPP-JPH LMS')
@section('header_title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Tombol Kembali ke Daftar Pengguna -->
    <div class="flex items-center justify-start">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-[#123F35] hover:text-[#28745B] transition bg-white px-4 py-2.5 rounded-xl border border-[#28745B]/40 shadow-2xs">
            <svg class="w-4 h-4 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Daftar Pengguna</span>
        </a>
    </div>

    <!-- Page Header Title -->
    <div class="space-y-1">
        <h2 class="text-xl sm:text-2xl font-extrabold text-[#123F35] flex items-center space-x-2.5">
            <svg class="w-6 h-6 text-[#28745B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            <span>Tambah Pengguna Baru</span>
        </h2>
        <p class="text-xs sm:text-sm text-slate-500">Daftarkan akun pengguna baru (Student, Teacher, atau Admin). Kolom dengan tanda <span class="text-red-500 font-bold">*</span> wajib diisi, sedangkan identitas tambahan &amp; berkas dapat dilengkapi menyusul.</p>
    </div>

    <!-- Form Utama Tambah Pengguna -->
    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- SECTION 1: Informasi Akun & Hak Akses (Wajib) -->
        <div class="bg-white rounded-2xl border border-[#28745B]/40 shadow-2xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 bg-[#28745B]">
                <h3 class="font-bold text-white text-sm sm:text-base flex items-center space-x-2">
                    <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    <span>Informasi Akun &amp; Kredensial Utama</span>
                </h3>
            </div>

            <div class="p-5 sm:p-6 space-y-5">
                <!-- Nama Lengkap & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-[#123F35] mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap pengguna" required
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50/60 border border-slate-300 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                        @error('name')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-[#123F35] mb-1">Alamat Email <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50/60 border border-slate-300 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                        @error('email')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Role & Kata Sandi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="role" class="block text-xs font-bold text-[#123F35] mb-1">Role / Hak Akses Pengguna <span class="text-red-500">*</span></label>
                        <select id="role" name="role" required
                                class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold text-[#123F35] focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition cursor-pointer">
                            <option value="student" {{ old('role', 'student') === 'student' ? 'selected' : '' }}>Student (Peserta)</option>
                            <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher (Pengajar)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                        @error('role')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-[#123F35] mb-1">Kata Sandi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </span>
                            <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required
                                   class="w-full pl-9 pr-10 py-2.5 bg-slate-50/60 border border-slate-300 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                            <button type="button" onclick="toggleCreateUserPassword('password', this)" aria-label="Tampilkan kata sandi"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-[#123F35] transition cursor-pointer">
                                <svg class="w-4 h-4 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg class="w-4 h-4 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Identitas Diri & Berkas Persyaratan (Opsional) -->
        <div class="bg-white rounded-2xl border border-[#28745B]/40 shadow-2xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 bg-[#28745B] flex items-center justify-between">
                <h3 class="font-bold text-white text-sm sm:text-base flex items-center space-x-2">
                    <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Identitas Diri &amp; Berkas Persyaratan</span>
                </h3>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/15 text-[#E7C982] border border-white/20">
                    Opsional
                </span>
            </div>

            <div class="p-5 sm:p-6 space-y-5">
                <!-- Unggah Foto Profil -->
                <div>
                    <label for="profile_photo" class="block text-xs font-bold text-[#123F35] mb-1">Unggah Foto Profil</label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#28745B]/10 file:text-[#123F35] hover:file:bg-[#28745B]/20 transition border border-slate-300 rounded-xl bg-slate-50/60">
                    <p class="text-[11px] text-slate-400 mt-1">Format berkas: JPG, PNG, WEBP. Maksimal 2MB.</p>
                    @error('profile_photo')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grid No. Telepon & Asal Instansi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="block text-xs font-bold text-[#123F35] mb-1">No. Telepon / WhatsApp</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </span>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890"
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50/60 border border-slate-300 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                        @error('phone')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="institution" class="block text-xs font-bold text-[#123F35] mb-1">Asal Instansi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12"></path>
                                </svg>
                            </span>
                            <input type="text" id="institution" name="institution" value="{{ old('institution') }}" placeholder="Nama universitas / perusahaan / lembaga"
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50/60 border border-slate-300 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                        @error('institution')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Grid NIK KTP & Berkas KTP -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label for="nik" class="block text-xs font-bold text-[#123F35] mb-1">Nomor NIK KTP</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3 3 0 00-3 3h6a3 3 0 00-3-3z"></path>
                                </svg>
                            </span>
                            <input type="text" id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="16 digit angka NIK"
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50/60 border border-slate-300 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                        @error('nik')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ktp" class="block text-xs font-bold text-[#123F35] mb-1">Unggah Berkas KTP</label>
                        <input type="file" id="ktp" name="ktp" accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#123F35] file:text-white hover:file:bg-[#28745B] border border-slate-300 rounded-xl bg-slate-50/60 focus:outline-none cursor-pointer transition">
                        <p class="text-[11px] text-slate-400 mt-1">PDF, JPG, JPEG, atau PNG. Maksimal 5MB.</p>
                        @error('ktp')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Grid Nomor KK & Berkas KK -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="family_card_number" class="block text-xs font-bold text-[#123F35] mb-1">Nomor Kartu Keluarga (KK)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </span>
                            <input type="text" id="family_card_number" name="family_card_number" value="{{ old('family_card_number') }}" maxlength="16" placeholder="16 digit angka Nomor KK"
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50/60 border border-slate-300 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                        @error('family_card_number')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kk" class="block text-xs font-bold text-[#123F35] mb-1">Unggah Berkas KK</label>
                        <input type="file" id="kk" name="kk" accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#123F35] file:text-white hover:file:bg-[#28745B] border border-slate-300 rounded-xl bg-slate-50/60 focus:outline-none cursor-pointer transition">
                        <p class="text-[11px] text-slate-400 mt-1">PDF, JPG, JPEG, atau PNG. Maksimal 5MB.</p>
                        @error('kk')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Grid Nomor Ijazah & Berkas Ijazah -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="ijazah_number" class="block text-xs font-bold text-[#123F35] mb-1">Nomor Ijazah Terakhir</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                </svg>
                            </span>
                            <input type="text" id="ijazah_number" name="ijazah_number" value="{{ old('ijazah_number') }}" maxlength="100" placeholder="Masukkan nomor ijazah"
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50/60 border border-slate-300 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">
                        </div>
                        @error('ijazah_number')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ijazah" class="block text-xs font-bold text-[#123F35] mb-1">Unggah Berkas Ijazah</label>
                        <input type="file" id="ijazah" name="ijazah" accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#123F35] file:text-white hover:file:bg-[#28745B] border border-slate-300 rounded-xl bg-slate-50/60 focus:outline-none cursor-pointer transition">
                        <p class="text-[11px] text-slate-400 mt-1">PDF, JPG, JPEG, atau PNG. Maksimal 5MB.</p>
                        @error('ijazah')
                            <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Input Alamat Lengkap -->
                <div>
                    <label for="address" class="block text-xs font-bold text-[#123F35] mb-1">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="3" placeholder="Masukkan alamat lengkap domisili pengguna..."
                              class="w-full p-3 bg-slate-50/60 border border-slate-300 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:bg-white transition">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Submit & Batal -->
                <div class="pt-3 flex items-center justify-end gap-2.5">
                    <a href="{{ route('admin.users.index') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-[#28745B] hover:bg-[#123F35] text-white text-xs font-extrabold px-6 py-3 rounded-xl transition shadow-md cursor-pointer">
                        Simpan Pengguna Baru
                    </button>
                </div>
            </div>
        </div>

    </form>

</div>

<script>
    function toggleCreateUserPassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const eyeOpen = btn.querySelector('.eye-open');
        const eyeClosed = btn.querySelector('.eye-closed');

        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
</script>
@endsection