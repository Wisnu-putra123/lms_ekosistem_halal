<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman formulir profil dan ubah kata sandi pengguna.
     */
    public function edit(Request $request)
    {
        $user = $request->user()->load('profilePhotoMedia');

        return view('profile.edit', compact('user'));
    }

    /**
     * Perbarui data informasi profil pengguna (Nama, Email, & Foto Profil).
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maksimal 2MB
        ], [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'name.max'             => 'Nama lengkap maksimal 255 karakter.',
            'email.required'       => 'Email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email sudah digunakan oleh pengguna lain.',
            'profile_photo.image'  => 'Berkas foto profil harus berupa gambar.',
            'profile_photo.mimes'  => 'Format foto profil harus jpeg, png, jpg, atau webp.',
            'profile_photo.max'    => 'Ukuran foto profil maksimal 2MB.',
        ]);

        $mediaId = $user->profile_photo;

        // Proses unggah foto profil jika ada berkas baru
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('profile-photos', $fileName, 'public');

            // Hapus media lama jika ada
            if ($user->profilePhotoMedia) {
                Storage::disk('public')->delete($user->profilePhotoMedia->file_path);
                $user->profilePhotoMedia->delete();
            }

            // Buat record media baru di database
            $media = Media::create([
                'type'        => 'image',
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => $filePath,
                'file_size'   => $file->getSize(),
                'mime_type'   => $file->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

            $mediaId = $media->id;
        }

        $user->update([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'profile_photo' => $mediaId,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Informasi profil Anda berhasil diperbarui!');
    }

    /**
     * Perbarui kata sandi (password) pengguna.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.min'              => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini tidak cocok dengan data kami.',
            ])->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Kata sandi Anda berhasil diperbarui!');
    }

    /**
     * Hapus foto profil pengguna dan kembalikan ke avatar bawaan.
     */
    public function destroyPhoto(Request $request)
    {
        $user = $request->user();

        if ($user->profile_photo) {
            // Ambil data media
            $media = Media::find($user->profile_photo);

            if ($media) {
                // 1. Hapus berkas fisik dari storage
                if (Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                }
                // 2. Hapus record dari tabel media
                $media->delete();
            }

            // 3. Set kolom profile_photo di tabel users menjadi null
            $user->update([
                'profile_photo' => null,
            ]);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Foto profil berhasil dihapus!');
    }
}