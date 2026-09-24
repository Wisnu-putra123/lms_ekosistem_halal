<?php

namespace App\Http\Controllers;

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
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'admin') {
                return view('admin.index');
            } elseif ($user->role === 'teacher') {
                return view('teacher.index');
            } else {
                return view('student.index');
            }
        }

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
                return view('teacher.index');
            } else {
                return view('student.index');
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
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'admin') {
                return view('admin.index');
            } elseif ($user->role === 'teacher') {
                return view('teacher.index');
            } else {
                return view('student.index');
            }
        }

        return view('auth.register');
    }

    /**
     * Proses registrasi pengguna baru (default role: student).
     * Setelah registrasi berhasil, pengguna otomatis ter-login dan ditampilkan view student/index.blade.php.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student', // Role default student
        ]);

        // Pengguna otomatis login setelah registrasi
        Auth::login($user);
        $request->session()->regenerate();

        session()->flash('success', 'Registrasi berhasil! Anda telah otomatis masuk sebagai Siswa (Student).');

        return view('student.index');
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