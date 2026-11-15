<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login pengguna.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna umum (Student, Teacher, Admin).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            session()->flash('success', 'Berhasil masuk! Selamat datang, ' . $user->name);

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'teacher') {
                return redirect()->route('teacher.dashboard');
            } else {
                return redirect()->route('student.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan halaman registrasi.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi pengguna baru (default role: student).
     * Seluruh field data diri dan berkas (KTP, KK, Ijazah) diwajibkan.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
            'phone'              => ['required', 'string', 'max:20'],
            'nik'                => ['required', 'string', 'size:16'],
            'ktp'                => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
            'family_card_number' => ['required', 'string', 'size:16'],
            'kk'                 => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
            'ijazah_number'      => ['required', 'string', 'max:100'],
            'ijazah'             => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
            'address'            => ['required', 'string'],
            'institution'        => ['required', 'string', 'max:255'],
        ], [
            'name.required'               => 'Nama lengkap wajib diisi.',
            'email.required'              => 'Email wajib diisi.',
            'email.email'                 => 'Format email tidak valid.',
            'email.unique'                => 'Email sudah terdaftar.',
            'password.required'           => 'Kata sandi wajib diisi.',
            'password.min'                => 'Kata sandi minimal 8 karakter.',
            'password.confirmed'          => 'Konfirmasi kata sandi tidak cocok.',
            'phone.required'              => 'Nomor telepon wajib diisi.',
            'nik.required'                => 'Nomor NIK KTP wajib diisi.',
            'nik.size'                    => 'Nomor NIK KTP harus terdiri dari 16 digit.',
            'ktp.required'                => 'File foto/berkas KTP wajib diunggah.',
            'ktp.mimes'                   => 'Format file KTP harus berupa JPEG, PNG, JPG, atau PDF.',
            'ktp.max'                     => 'Ukuran file KTP maksimal 2MB.',
            'family_card_number.required' => 'Nomor Kartu Keluarga (KK) wajib diisi.',
            'family_card_number.size'     => 'Nomor Kartu Keluarga (KK) harus terdiri dari 16 digit.',
            'kk.required'                 => 'File foto/berkas Kartu Keluarga (KK) wajib diunggah.',
            'kk.mimes'                    => 'Format file KK harus berupa JPEG, PNG, JPG, atau PDF.',
            'kk.max'                      => 'Ukuran file KK maksimal 2MB.',
            'ijazah_number.required'      => 'Nomor Ijazah wajib diisi.',
            'ijazah_number.max'           => 'Nomor Ijazah maksimal 100 karakter.',
            'ijazah.required'             => 'File scan/berkas Ijazah wajib diunggah.',
            'ijazah.mimes'                => 'Format file Ijazah harus berupa JPEG, PNG, JPG, atau PDF.',
            'ijazah.max'                  => 'Ukuran file Ijazah maksimal 2MB.',
            'address.required'            => 'Alamat lengkap wajib diisi.',
            'institution.required'        => 'Asal instansi wajib diisi.',
        ]);

        $user = User::create([
            'name'               => $validated['name'],
            'email'              => $validated['email'],
            'password'           => Hash::make($validated['password']),
            'role'               => 'student',
            'phone'              => $validated['phone'],
            'nik'                => $validated['nik'],
            'ktp_media_id'       => null,
            'family_card_number' => $validated['family_card_number'],
            'kk_media_id'        => null,
            'ijazah_number'      => $validated['ijazah_number'],
            'ijazah_media_id'    => null,
            'address'            => $validated['address'],
            'institution'        => $validated['institution'],
        ]);

        $updateMedia = [];

        // 1. Simpan berkas KTP ke storage & tabel media
        if ($request->hasFile('ktp')) {
            $fileKtp     = $request->file('ktp');
            $fileNameKtp = time() . '_ktp_' . $fileKtp->getClientOriginalName();
            $filePathKtp = $fileKtp->storeAs('ktp', $fileNameKtp, 'public');

            $mediaKtp = Media::create([
                'type'        => str_contains($fileKtp->getMimeType(), 'pdf') ? 'document' : 'image',
                'file_name'   => $fileKtp->getClientOriginalName(),
                'file_path'   => $filePathKtp,
                'file_size'   => $fileKtp->getSize(),
                'mime_type'   => $fileKtp->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

            $updateMedia['ktp_media_id'] = $mediaKtp->id;
        }

        // 2. Simpan berkas KK ke storage & tabel media
        if ($request->hasFile('kk')) {
            $fileKk     = $request->file('kk');
            $fileNameKk = time() . '_kk_' . $fileKk->getClientOriginalName();
            $filePathKk = $fileKk->storeAs('kk', $fileNameKk, 'public');

            $mediaKk = Media::create([
                'type'        => str_contains($fileKk->getMimeType(), 'pdf') ? 'document' : 'image',
                'file_name'   => $fileKk->getClientOriginalName(),
                'file_path'   => $filePathKk,
                'file_size'   => $fileKk->getSize(),
                'mime_type'   => $fileKk->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

            $updateMedia['kk_media_id'] = $mediaKk->id;
        }

        // 3. Simpan berkas Ijazah ke storage & tabel media
        if ($request->hasFile('ijazah')) {
            $fileIjazah     = $request->file('ijazah');
            $fileNameIjazah = time() . '_ijazah_' . $fileIjazah->getClientOriginalName();
            $filePathIjazah = $fileIjazah->storeAs('ijazah', $fileNameIjazah, 'public');

            $mediaIjazah = Media::create([
                'type'        => str_contains($fileIjazah->getMimeType(), 'pdf') ? 'document' : 'image',
                'file_name'   => $fileIjazah->getClientOriginalName(),
                'file_path'   => $filePathIjazah,
                'file_size'   => $fileIjazah->getSize(),
                'mime_type'   => $fileIjazah->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

            $updateMedia['ijazah_media_id'] = $mediaIjazah->id;
        }

        if (!empty($updateMedia)) {
            $user->update($updateMedia);
        }

        // Pengguna otomatis login setelah registrasi
        Auth::login($user);
        $request->session()->regenerate();

        session()->flash('success', 'Registrasi berhasil! Anda telah otomatis masuk sebagai Siswa (Student).');

        return redirect()->route('student.dashboard');
    }

    /**
     * Proses logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}