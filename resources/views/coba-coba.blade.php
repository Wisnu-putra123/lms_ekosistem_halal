{{-- New Password + Confirmation --}}
                <div class="log-fade-up d3">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                    <div class="grid grid-cols-1 gap-4">

                        <div class="relative">
                            <input type="password" name="password" id="new_password" placeholder="Password Baru"
                                required autocomplete="new-password"
                                class="w-full h-11 rounded-xl border border-gray-300 px-4 pr-12 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none transition @error('password') border-red-400 @enderror">
                            <button type="button" onclick="togglePassword('new_password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <div class="relative">
                            <input type="password" name="password_confirmation" id="new_password_confirmation"
                                placeholder="Konfirmasi Password" required autocomplete="new-password" disabled
                                class="w-full h-11 rounded-xl border border-gray-300 px-4 pr-12 text-sm
                                focus:ring-2 focus:ring-blue-400 focus:outline-none
                                transition disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <button type="button" onclick="togglePassword('new_password_confirmation', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        @error('password')
                            <p class="text-red-500 text-xs -mt-2">{{ $message }}</p>
                        @enderror

                        <div class="space-y-2">
                            {{-- Strength Bar --}}
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div id="strength-bar" class="h-2 w-0 bg-red-500 transition-all duration-300"></div>
                            </div>

                            {{-- Strength Text --}}
                            <div id="password-strength" class="text-xs font-semibold"></div>

                            {{-- Requirement Checklist --}}
                            <ul id="req-list" class="text-xs space-y-1 mt-2">
                                <li id="length" data-label="Minimal 8 karakter" class="flex items-center gap-2 text-gray-400">
                                    <span class="icon">•</span>
                                    <span>Minimal 8 karakter</span>
                                </li>
                                <li id="uppercase" data-label="Huruf besar (A-Z)" class="flex items-center gap-2 text-gray-400">
                                    <span class="icon">•</span>
                                    <span>Huruf besar (A-Z)</span>
                                </li>
                                <li id="lowercase" data-label="Huruf kecil (a-z)" class="flex items-center gap-2 text-gray-400">
                                    <span class="icon">•</span>
                                    <span>Huruf kecil (a-z)</span>
                                </li>
                                <li id="number" data-label="Angka (0-9)" class="flex items-center gap-2 text-gray-400">
                                    <span class="icon">•</span>
                                    <span>Angka (0-9)</span>
                                </li>
                                <li id="symbol" data-label="Simbol (@$!%*#?&)" class="flex items-center gap-2 text-gray-400">
                                    <span class="icon">•</span>
                                    <span>Simbol (@$!%*#?&)</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary log-btn log-fade-up d5" id="resetBtn" disabled>
                    <span id="resetText">Simpan Password Baru</span>
                    <span id="resetLoading" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                </button>
            </form>

        </div>
    </div>