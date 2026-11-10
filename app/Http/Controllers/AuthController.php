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
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            session()->flash('success', 'Berhasil masuk! Selamat datang, ' . $user->name);

            if ($user->role === 'admin') {
                return view('admin.index');
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
     * Seluruh field diwajibkan (required) di tingkat controller.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
            'phone'              => ['required', 'string', 'max:20'],
            'nik'                => ['required', 'string', 'size:16'],
            'ktp'                => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'], // Wajib Upload File KTP (Max 2MB)
            'family_card_number' => ['required', 'string', 'size:16'],
            // 'kk'              => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'], // Jika kelak beralih ke wajib upload foto KK
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
            'address.required'            => 'Alamat lengkap wajib diisi.',
            'institution.required'        => 'Asal instansi wajib diisi.',
        ]);

        $ktpMediaId = null;

        /* 
        |--------------------------------------------------------------------------
        | PROSES UNGGAH BERKAS KK (JIKA KELAK DIPERLUKAN KEMBALI)
        |--------------------------------------------------------------------------
        | $kkMediaId = null;
        | if ($request->hasFile('kk')) {
        |     $fileKK = $request->file('kk');
        |     $filePathKK = $fileKK->store('kk', 'public');
        |
        |     $mediaKK = Media::create([
        |         'file_name' => $fileKK->getClientOriginalName(),
        |         'file_path' => $filePathKK,
        |         'file_type' => $fileKK->getClientMimeType(),
        |         'file_size' => $fileKK->getSize(),
        |     ]);
        |
        |     $kkMediaId = $mediaKK->id;
        | }
        */

        $user = User::create([
            'name'               => $validated['name'],
            'email'              => $validated['email'],
            'password'           => Hash::make($validated['password']),
            'role'               => 'student', // Role default student
            'phone'              => $validated['phone'],
            'nik'                => $validated['nik'],
            'ktp_media_id'       => $ktpMediaId,
            'family_card_number' => $validated['family_card_number'],
            // 'kk_media_id'      => $kkMediaId, // Jika kelak memakai upload foto KK
            'address'            => $validated['address'],
            'institution'        => $validated['institution'],
        ]);

        if ($request->hasFile('ktp')) {
        $file = $request->file('ktp');
        $filePath = $file->store('ktp', 'public');

        $media = Media::create([
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $filePath,
            'file_type'   => $file->getClientMimeType(),
            'file_size'   => $file->getSize(),
            'uploaded_by' => $user->id, // Mengisi uploaded_by
        ]);

        
        // Update ktp_media_id pada user
        $user->update([
            'ktp_media_id' => $media->id,
        ]);
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