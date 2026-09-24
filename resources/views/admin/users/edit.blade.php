@extends('layouts.app')

@section('title', 'Edit User - Admin Panel')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-pencil-square text-warning me-2"></i> Edit Data User: {{ $user->name }}
                </h5>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label font-medium">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label font-medium">Alamat Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label font-medium">Role Pengguna</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student (Siswa)</option>
                        <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher (Guru / Pengajar)</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Administrator)</option>
                    </select>
                    @error('role')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label font-medium">Kata Sandi Baru <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••">
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-warning px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Perbarui User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
