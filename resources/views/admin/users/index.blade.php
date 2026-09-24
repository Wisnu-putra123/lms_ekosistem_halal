@extends('layouts.app')

@section('title', 'Kelola User (Admin) - Ekosistem Halal')
@section('header_title', 'Pengelolaan Data User')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center space-x-2">
                <span>Daftar Pengguna</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Kelola pengguna dan ubah role default (<strong class="text-emerald-600">Student</strong>) ke <strong>Teacher</strong> atau <strong>Admin</strong>.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah User Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 my-5">
        <div class="md:col-span-5 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." 
                   class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
        </div>

        <div class="md:col-span-3">
            <select name="role" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                <option value="">-- Semua Role --</option>
                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student (Siswa)</option>
                <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher (Guru)</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div class="md:col-span-4 flex items-center space-x-2">
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Filter
            </button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="border border-slate-300 text-slate-600 hover:bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium transition">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Table of Users -->
    <div class="overflow-x-auto border border-slate-200 rounded-xl">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-800 text-white uppercase text-xs font-semibold tracking-wider">
                <tr>
                    <th scope="col" class="py-3.5 px-4 w-12">#</th>
                    <th scope="col" class="py-3.5 px-4">Nama Lengkap</th>
                    <th scope="col" class="py-3.5 px-4">Email</th>
                    <th scope="col" class="py-3.5 px-4">Role Saat Ini</th>
                    <th scope="col" class="py-3.5 px-4">Ubah Role Cepat</th>
                    <th scope="col" class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-4">{{ $users->firstItem() + $index }}</td>
                        <td class="py-3 px-4">
                            <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                            <div class="text-xs text-slate-400">ID: #{{ $user->id }}</div>
                        </td>
                        <td class="py-3 px-4">{{ $user->email }}</td>
                        <td class="py-3 px-4">
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Admin</span>
                            @elseif($user->role === 'teacher')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Teacher</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Student</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <!-- Quick Role Change Form -->
                            <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="role" onchange="this.form.submit()" class="text-xs py-1 px-2 border border-slate-200 rounded-md bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="teacher" {{ $user->role === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-md transition" title="Edit User">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if(Auth::id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-md transition" title="Hapus User">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="p-1.5 text-slate-300" title="Anda tidak dapat menghapus akun sendiri">
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
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            Tidak ada data pengguna yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-5 text-sm text-slate-500">
        <div>
            Menampilkan {{ $users->firstItem() ?? 0 }} hingga {{ $users->lastItem() ?? 0 }} dari total {{ $users->total() }} user
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection