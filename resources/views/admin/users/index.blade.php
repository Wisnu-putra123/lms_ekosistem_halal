@extends('layouts.app')

@section('title', 'Kelola User (Admin) - Ekosistem Halal')
@section('header_title', 'Pengelolaan Data User')

@section('content')
<div class="max-w-6xl mx-auto space-y-5 sm:space-y-6">

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center sm:justify-start space-x-2 text-xs font-semibold text-slate-700 hover:text-[#123F35] transition bg-white px-3.5 py-2 rounded-xl border border-slate-300 shadow-2xs self-start">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Dashboard</span>
        </a>

        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center space-x-1.5 bg-[#28745B] hover:bg-[#123F35] text-white border border-[#123F35]/20 px-4 py-2 rounded-xl text-xs font-extrabold shadow-2xs transition self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah User Baru</span>
        </a>
    </div>

    <!-- Alert Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center space-x-2 shadow-2xs">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-300 text-red-800 rounded-2xl text-xs font-semibold flex items-center space-x-2 shadow-2xs">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- 1. TOP CARD FULL GRADASI HIJAU TUA -->
    <div class="bg-gradient-to-r from-[#123F35] via-[#123F35]/95 to-[#195e4f] rounded-2xl border border-[#28745B]/40 shadow-md p-5 sm:p-8 text-white space-y-6">
        <div class="border-b border-white/20 pb-4 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-1.5">
                <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold tracking-wider bg-white/10 text-[#E7C982] border border-white/20 uppercase backdrop-blur-xs">
                    Administrasi Sistem &bull; Manajemen Akun
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-white leading-snug">Daftar Pengguna Platform</h1>
                <p class="text-xs text-emerald-100/85">
                    Kelola profil, berkas persyaratan, dan hak akses pengguna (Student, Teacher, maupun Admin).
                </p>
            </div>

            <div class="flex items-center space-x-2 flex-shrink-0">
                <span class="px-3 py-1.5 rounded-xl text-xs font-extrabold bg-[#E7C982] text-[#123F35] shadow-2xs">
                    Total: {{ $users->total() }} Akun
                </span>
            </div>
        </div>

        <!-- Metric Cards Ringkasan Role -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Hasil Filter</span>
                <div class="text-base sm:text-lg font-extrabold text-white mt-0.5">
                    {{ $users->total() }} <span class="text-[11px] font-semibold text-emerald-100/80">User</span>
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Peserta (Student)</span>
                <div class="text-base sm:text-lg font-extrabold text-white mt-0.5">
                    {{ \App\Models\User::where('role', 'student')->count() }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Pengajar (Teacher)</span>
                <div class="text-base sm:text-lg font-extrabold text-white mt-0.5">
                    {{ \App\Models\User::where('role', 'teacher')->count() }}
                </div>
            </div>

            <div class="p-3.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-xs">
                <span class="text-[10px] font-bold text-emerald-100/80 uppercase tracking-wider">Administrator</span>
                <div class="text-base sm:text-lg font-extrabold text-white mt-0.5">
                    {{ \App\Models\User::where('role', 'admin')->count() }}
                </div>
            </div>
        </div>
    </div>

    <!-- 2. CONTAINER SEARCH & FILTER WARNA CREAM (#F7F5EE) -->
    <div class="bg-[#F7F5EE] rounded-2xl border border-slate-300 p-4 sm:p-5 shadow-2xs">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            <!-- Input Pencarian -->
            <div class="lg:col-span-6">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Cari Pengguna</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIK, atau asal instansi..." 
                           class="w-full border border-slate-300 rounded-xl px-3 py-2 pl-9 text-xs text-slate-700 outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 bg-white transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Filter Role -->
            <div class="lg:col-span-3">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#123F35] mb-1">Filter Role</label>
                <select name="role" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 bg-white transition cursor-pointer">
                    <option value="">Semua Role</option>
                    <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student (Peserta)</option>
                    <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher (Pengajar)</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="lg:col-span-3 flex items-end gap-2">
                <button type="submit" class="w-full bg-[#28745B] hover:bg-[#123F35] text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="w-full bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-100 transition flex items-center justify-center shadow-2xs">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- 3. TABEL PENGGUNA DENGAN HEADING HIJAU MUDA (#28745B) -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#28745B] text-white border-b border-slate-300">
                    <tr class="text-[11px] font-bold uppercase tracking-wider whitespace-nowrap">
                        <th scope="col" class="py-3.5 px-4 sm:px-6 w-12">#</th>
                        <th scope="col" class="py-3.5 px-4 min-w-[220px]">Pengguna</th>
                        <th scope="col" class="py-3.5 px-4 min-w-[180px]">Kontak &amp; Instansi</th>
                        <th scope="col" class="py-3.5 px-4 min-w-[130px]">Role Saat Ini</th>
                        <th scope="col" class="py-3.5 px-4 min-w-[140px]">Ubah Role Cepat</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-right min-w-[110px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300 text-xs text-slate-700">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-[#F7F5EE]/40 transition">
                            <td class="py-3.5 px-4 sm:px-6 font-semibold text-slate-500 whitespace-nowrap">
                                {{ $users->firstItem() + $index }}
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover border border-[#28745B]/40 flex-shrink-0">
                                    <div>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="font-bold text-[#123F35] hover:text-[#28745B] transition">
                                            {{ $user->name }}
                                        </a>
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            ID #{{ $user->id }} @if($user->nik) &bull; NIK: {{ $user->nik }} @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-700">{{ $user->email }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    {{ $user->institution ?: 'Instansi belum diisi' }}
                                    @if($user->phone) &bull; {{ $user->phone }} @endif
                                </div>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-red-300 bg-red-100 text-red-700 text-[10px] font-extrabold">
                                        Administrator
                                    </span>
                                @elseif($user->role === 'teacher')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-[#E7C982] bg-[#E7C982]/40 text-[#123F35] text-[10px] font-extrabold">
                                        Teacher
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border border-[#28745B]/40 bg-[#28745B]/15 text-[#28745B] text-[10px] font-extrabold">
                                        Student
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="text-xs py-1.5 px-2.5 border border-slate-300 rounded-xl bg-slate-50/70 text-[#123F35] font-semibold focus:border-[#28745B] focus:ring-2 focus:ring-[#28745B]/20 focus:outline-none cursor-pointer transition">
                                        <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="teacher" {{ $user->role === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </td>

                            <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="p-1.5 bg-[#E7C982]/40 text-[#123F35] border border-[#E7C982] hover:bg-[#123F35] hover:text-white rounded-lg transition shadow-2xs" 
                                       title="Lihat Detail & Edit Profil">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    @if(Auth::id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }} beserta seluruh berkasnya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-red-50 text-red-600 border border-red-300 hover:bg-red-600 hover:text-white rounded-lg transition shadow-2xs cursor-pointer" title="Hapus User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="p-1.5 bg-slate-100 text-slate-400 border border-slate-200 rounded-lg" title="Anda tidak dapat menghapus akun sendiri">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-xs text-slate-400 italic">
                                Tidak ada data pengguna yang cocok dengan pencarian atau filter tersebut.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 sm:px-6 border-t border-slate-300 bg-[#F7F5EE]/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div>
                    Menampilkan <span class="font-bold text-[#123F35]">{{ $users->firstItem() }}</span> - 
                    <span class="font-bold text-[#123F35]">{{ $users->lastItem() }}</span> dari 
                    <span class="font-bold text-[#123F35]">{{ $users->total() }}</span> pengguna
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection