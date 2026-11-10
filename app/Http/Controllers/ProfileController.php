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
        // Load relasi foto profil dan berkas KTP dari tabel media
        $user = $request->user()->load(['profilePhotoMedia', 'ktpMedia']);

        return view('profile.edit', compact('user'));
    }

    /**
     * Perbarui data informasi profil pengguna (Nama, Email, Foto Profil, Telepon, NIK, Berkas KTP, KK, Alamat, Instansi).
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_photo'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Maksimal 2MB
            'phone'              => ['required', 'string', 'max:20'],
            'nik'                => ['required', 'string', 'size:16'],
            'ktp'                => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'], // Opsional saat update profil
            'family_card_number' => ['required', 'string', 'size:16'],
            // 'kk'              => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'], // Jika kelak beralih ke upload foto KK
            'address'            => ['required', 'string'],
            'institution'        => ['required', 'string', 'max:255'],
        ], [
            'name.required'               => 'Nama lengkap wajib diisi.',
            'name.max'                    => 'Nama lengkap maksimal 255 karakter.',
            'email.required'              => 'Email wajib diisi.',
            'email.email'                 => 'Format email tidak valid.',
            'email.unique'                => 'Email sudah digunakan oleh pengguna lain.',
            'profile_photo.image'         => 'Berkas foto profil harus berupa gambar.',
            'profile_photo.mimes'         => 'Format foto profil harus jpeg, png, jpg, atau webp.',
            'profile_photo.max'           => 'Ukuran foto profil maksimal 2MB.',
            'phone.required'              => 'Nomor telepon wajib diisi.',
            'phone.max'                   => 'Nomor telepon maksimal 20 karakter.',
            'nik.required'                => 'Nomor NIK KTP wajib diisi.',
            'nik.size'                    => 'Nomor NIK KTP harus terdiri dari 16 digit.',
            'ktp.mimes'                   => 'Format berkas KTP harus berupa JPEG, PNG, JPG, atau PDF.',
            'ktp.max'                     => 'Ukuran berkas KTP maksimal 2MB.',
            'family_card_number.required' => 'Nomor Kartu Keluarga (KK) wajib diisi.',
            'family_card_number.size'     => 'Nomor Kartu Keluarga (KK) harus terdiri dari 16 digit.',
            'address.required'            => 'Alamat lengkap wajib diisi.',
            'institution.required'        => 'Asal instansi wajib diisi.',
        ]);

        $profileMediaId = $user->profile_photo;
        $ktpMediaId = $user->ktp_media_id;

        // 1. Proses unggah foto profil jika ada berkas baru
        if ($request->hasFile('profile_photo')) {
            $fileProfile = $request->file('profile_photo');
            $fileNameProfile = time() . '_' . $fileProfile->getClientOriginalName();
            $filePathProfile = $fileProfile->storeAs('profile-photos', $fileNameProfile, 'public');

            // Hapus foto profil lama jika ada
            if ($user->profilePhotoMedia) {
                if (Storage::disk('public')->exists($user->profilePhotoMedia->file_path)) {
                    Storage::disk('public')->delete($user->profilePhotoMedia->file_path);
                }
                $user->profilePhotoMedia->delete();
            }

            // Buat record media baru untuk foto profil
            $mediaProfile = Media::create([
                'type'        => 'image',
                'file_name'   => $fileProfile->getClientOriginalName(),
                'file_path'   => $filePathProfile,
                'file_size'   => $fileProfile->getSize(),
                'mime_type'   => $fileProfile->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

            $profileMediaId = $mediaProfile->id;
        }

        // 2. Proses unggah/replace berkas KTP baru jika ada
        if ($request->hasFile('ktp')) {
            $fileKtp = $request->file('ktp');
            $fileNameKtp = time() . '_ktp_' . $fileKtp->getClientOriginalName();
            $filePathKtp = $fileKtp->storeAs('ktp', $fileNameKtp, 'public');

            // Hapus berkas KTP lama dari storage & database jika ada
            if ($user->ktpMedia) {
                if (Storage::disk('public')->exists($user->ktpMedia->file_path)) {
                    Storage::disk('public')->delete($user->ktpMedia->file_path);
                }
                $user->ktpMedia->delete();
            }

            // Buat record media baru untuk KTP
            $mediaKtp = Media::create([
                'type'        => str_contains($fileKtp->getMimeType(), 'pdf') ? 'document' : 'image',
                'file_name'   => $fileKtp->getClientOriginalName(),
                'file_path'   => $filePathKtp,
                'file_size'   => $fileKtp->getSize(),
                'mime_type'   => $fileKtp->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

            $ktpMediaId = $mediaKtp->id;
        }

        /* 
        |--------------------------------------------------------------------------
        | PROSES UPDATE BERKAS KK (JIKA KELAK DIPERLUKAN KEMBALI)
        |--------------------------------------------------------------------------
        | $kkMediaId = $user->kk_media_id;
        | if ($request->hasFile('kk')) {
        |     $fileKk = $request->file('kk');
        |     $filePathKk = $fileKk->store('kk', 'public');
        |     if ($user->kkMedia) {
        |         Storage::disk('public')->delete($user->kkMedia->file_path);
        |         $user->kkMedia->delete();
        |     }
        |     $mediaKk = Media::create([
        |         'type'        => 'image',
        |         'file_name'   => $fileKk->getClientOriginalName(),
        |         'file_path'   => $filePathKk,
        |         'file_size'   => $fileKk->getSize(),
        |         'mime_type'   => $fileKk->getMimeType(),
        |         'uploaded_by' => $user->id,
        |     ]);
        |     $kkMediaId = $mediaKk->id;
        | }
        */

        // 3. Update data user
        $user->update([
            'name'               => $validated['name'],
            'email'              => $validated['email'],
            'profile_photo'      => $profileMediaId,
            'phone'              => $validated['phone'],
            'nik'                => $validated['nik'],
            'ktp_media_id'       => $ktpMediaId,
            'family_card_number' => $validated['family_card_number'],
            // 'kk_media_id'      => $kkMediaId,
            'address'            => $validated['address'],
            'institution'        => $validated['institution'],
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