<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Ekosistem Halal LMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#123F35] min-h-screen flex flex-col justify-center items-center p-4 py-8 sm:py-12 relative overflow-y-auto">

    <div class="fixed -left-20 -top-20 -z-0 h-96 w-96 rounded-full bg-[#28745B]/10 blur-3xl pointer-events-none"></div>
    <div class="fixed -right-20 -bottom-20 -z-0 h-96 w-96 rounded-full bg-[#E7C982]/20 blur-3xl pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-[#28745B]/15 p-6 sm:p-10 my-auto">
        
        <!-- Header & Logo Brand -->
        <div class="flex flex-col items-center text-center mb-6 sm:mb-8">
            <a href="{{ url('/') }}" class="group flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl text-[#E7C982] flex items-center justify-center shadow-md border border-[#E7C982]/30 transition group-hover:scale-105">
                     <img src="{{ asset('images/logo_telkom_transparan2.png') }}" alt="Logo Telkom" class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl text-[#E7C982] flex items-center justify-center shadow-md border border-[#E7C982]/30 transition group-hover:scale-105">
                     <img src="{{ asset('images/logo_lms_lppjph_transparan2.png') }}" alt="Logo LPP-JPH" class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
                </div>
            </a>

            <h2 class="text-xl sm:text-2xl font-bold text-[#123F35]">Daftar Akun Baru</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi seluruh data diri dan dokumen persyaratan untuk mengakses platform LMS</p>
        </div>

        <!-- Form Register -->
        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-5">
            @csrf

            <!-- Nama Lengkap & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required autofocus
                               class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                    </div>
                    @error('name')
                        <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required
                               class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                    </div>
                    @error('email')
                        <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kata Sandi & Konfirmasi (Dengan Tombol Visibility) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Kata Sandi <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" placeholder="Min. 8 karakter" required
                               class="w-full pl-11 pr-11 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                        <button type="button" onclick="togglePasswordVisibility('password', this)" aria-label="Tampilkan kata sandi"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-[#123F35] transition cursor-pointer">
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </span>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi" required
                               class="w-full pl-11 pr-11 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Tampilkan konfirmasi kata sandi"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-[#123F35] transition cursor-pointer">
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- No. Telepon & Asal Instansi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </span>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" required
                               class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                    </div>
                    @error('phone')
                        <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="institution" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Asal Instansi <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12"></path>
                            </svg>
                        </span>
                        <input type="text" id="institution" name="institution" value="{{ old('institution', 'Telkom University') }}" placeholder="Masukkan asal instansi" required
                               class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                    </div>
                    @error('institution')
                        <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Grid NIK KTP & Upload Berkas KTP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nik" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Nomor NIK KTP <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3 3 0 00-3 3h6a3 3 0 00-3-3z"></path>
                            </svg>
                        </span>
                        <input type="text" id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="16 Digit NIK" required
                               class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                    </div>
                    @error('nik')
                        <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ktp" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Upload Berkas KTP <span class="text-red-500">*</span></label>
                    <input type="file" id="ktp" name="ktp" accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf" required
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#123F35] file:text-white hover:file:bg-[#28745B] border border-slate-300 rounded-xl bg-[#F7F5EE]/60 focus:outline-none cursor-pointer transition">
                    <p class="text-[10px] text-slate-400 mt-1">Format: PDF, JPG, JPEG, PNG (Max 2MB)</p>
                    @error('ktp')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Grid Nomor KK & Upload Berkas KK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="family_card_number" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Nomor Kartu Keluarga (KK) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </span>
                        <input type="text" id="family_card_number" name="family_card_number" value="{{ old('family_card_number') }}" maxlength="16" placeholder="16 Digit Nomor KK" required
                               class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                    </div>
                    @error('family_card_number')
                        <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kk" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Upload Berkas KK <span class="text-red-500">*</span></label>
                    <input type="file" id="kk" name="kk" accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf" required
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#123F35] file:text-white hover:file:bg-[#28745B] border border-slate-300 rounded-xl bg-[#F7F5EE]/60 focus:outline-none cursor-pointer transition">
                    <p class="text-[10px] text-slate-400 mt-1">Format: PDF, JPG, JPEG, PNG (Max 2MB)</p>
                    @error('kk')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Grid Nomor Ijazah & Upload Berkas Ijazah -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="ijazah_number" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Nomor Ijazah Terakhir <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                        </span>
                        <input type="text" id="ijazah_number" name="ijazah_number" value="{{ old('ijazah_number') }}" maxlength="100" placeholder="Masukkan nomor seri ijazah" required
                               class="w-full pl-11 pr-4 py-2.5 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition">
                    </div>
                    @error('ijazah_number')
                        <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ijazah" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Upload Berkas Ijazah <span class="text-red-500">*</span></label>
                    <input type="file" id="ijazah" name="ijazah" accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf" required
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#123F35] file:text-white hover:file:bg-[#28745B] border border-slate-300 rounded-xl bg-[#F7F5EE]/60 focus:outline-none cursor-pointer transition">
                    <p class="text-[10px] text-slate-400 mt-1">Format: PDF, JPG, JPEG, PNG (Max 2MB)</p>
                    @error('ijazah')
                        <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Alamat Lengkap -->
            <div>
                <label for="address" class="block text-xs font-bold text-[#123F35] uppercase tracking-wider mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea id="address" name="address" rows="3" placeholder="Masukkan alamat domisili lengkap..." required
                          class="w-full p-3 bg-[#F7F5EE]/60 border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#28745B] focus:border-transparent focus:bg-white transition sm:text-sm">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-xs text-red-600 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 px-4 bg-[#123F35] hover:bg-[#28745B] text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition duration-200 mt-4 cursor-pointer">
                Daftar Sekarang
            </button>

            <!-- Footer Login Link -->
            <div class="text-center pt-4 border-t border-slate-100">
                <p class="text-xs sm:text-sm text-slate-600">
                    Sudah memiliki akun? <a href="{{ route('login') }}" class="font-bold text-[#28745B] hover:text-[#123F35] underline underline-offset-2 transition">Login ke Akun Anda</a>
                </p>
            </div>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(inputId, btn) {
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
</body>
</html>