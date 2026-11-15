<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
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
        // Load seluruh relasi dokumen media (Foto Profil, KTP, KK, dan Ijazah)
        $user = $request->user()->load(['profilePhotoMedia', 'ktpMedia', 'kkMedia', 'ijazahMedia']);

        return view('profile.edit', compact('user'));
    }

    /**
     * Perbarui data informasi profil pengguna.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_photo'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'phone'              => ['required', 'string', 'max:20'],
            'nik'                => ['required', 'string', 'size:16'],
            'ktp'                => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
            'family_card_number' => ['required', 'string', 'size:16'],
            'kk'                 => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
            'ijazah_number'      => ['required', 'string', 'max:100'],
            'ijazah'             => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
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
            'kk.mimes'                    => 'Format berkas KK harus berupa JPEG, PNG, JPG, atau PDF.',
            'kk.max'                      => 'Ukuran berkas KK maksimal 2MB.',
            'ijazah_number.required'      => 'Nomor Ijazah wajib diisi.',
            'ijazah_number.max'           => 'Nomor Ijazah maksimal 100 karakter.',
            'ijazah.mimes'                => 'Format berkas Ijazah harus berupa JPEG, PNG, JPG, atau PDF.',
            'ijazah.max'                  => 'Ukuran berkas Ijazah maksimal 2MB.',
            'address.required'            => 'Alamat lengkap wajib diisi.',
            'institution.required'        => 'Asal instansi wajib diisi.',
        ]);

        $profileMediaId = $user->profile_photo;
        $ktpMediaId     = $user->ktp_media_id;
        $kkMediaId      = $user->kk_media_id;
        $ijazahMediaId  = $user->ijazah_media_id;

        // 1. Proses unggah foto profil jika ada berkas baru
        if ($request->hasFile('profile_photo')) {
            $fileProfile     = $request->file('profile_photo');
            $fileNameProfile = time() . '_' . $fileProfile->getClientOriginalName();
            $filePathProfile = $fileProfile->storeAs('profile-photos', $fileNameProfile, 'public');

            if ($user->profilePhotoMedia) {
                if (Storage::disk('public')->exists($user->profilePhotoMedia->file_path)) {
                    Storage::disk('public')->delete($user->profilePhotoMedia->file_path);
                }
                $user->profilePhotoMedia->delete();
            }

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
            $fileKtp     = $request->file('ktp');
            $fileNameKtp = time() . '_ktp_' . $fileKtp->getClientOriginalName();
            $filePathKtp = $fileKtp->storeAs('ktp', $fileNameKtp, 'public');

            if ($user->ktpMedia) {
                if (Storage::disk('public')->exists($user->ktpMedia->file_path)) {
                    Storage::disk('public')->delete($user->ktpMedia->file_path);
                }
                $user->ktpMedia->delete();
            }

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

        // 3. Proses unggah/replace berkas KK baru jika ada
        if ($request->hasFile('kk')) {
            $fileKk     = $request->file('kk');
            $fileNameKk = time() . '_kk_' . $fileKk->getClientOriginalName();
            $filePathKk = $fileKk->storeAs('kk', $fileNameKk, 'public');

            if ($user->kkMedia) {
                if (Storage::disk('public')->exists($user->kkMedia->file_path)) {
                    Storage::disk('public')->delete($user->kkMedia->file_path);
                }
                $user->kkMedia->delete();
            }

            $mediaKk = Media::create([
                'type'        => str_contains($fileKk->getMimeType(), 'pdf') ? 'document' : 'image',
                'file_name'   => $fileKk->getClientOriginalName(),
                'file_path'   => $filePathKk,
                'file_size'   => $fileKk->getSize(),
                'mime_type'   => $fileKk->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

            $kkMediaId = $mediaKk->id;
        }

        // 4. Proses unggah/replace berkas Ijazah baru jika ada
        if ($request->hasFile('ijazah')) {
            $fileIjazah     = $request->file('ijazah');
            $fileNameIjazah = time() . '_ijazah_' . $fileIjazah->getClientOriginalName();
            $filePathIjazah = $fileIjazah->storeAs('ijazah', $fileNameIjazah, 'public');

            if ($user->ijazahMedia) {
                if (Storage::disk('public')->exists($user->ijazahMedia->file_path)) {
                    Storage::disk('public')->delete($user->ijazahMedia->file_path);
                }
                $user->ijazahMedia->delete();
            }

            $mediaIjazah = Media::create([
                'type'        => str_contains($fileIjazah->getMimeType(), 'pdf') ? 'document' : 'image',
                'file_name'   => $fileIjazah->getClientOriginalName(),
                'file_path'   => $filePathIjazah,
                'file_size'   => $fileIjazah->getSize(),
                'mime_type'   => $fileIjazah->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

            $ijazahMediaId = $mediaIjazah->id;
        }

        // 5. Update data user
        $user->update([
            'name'               => $validated['name'],
            'email'              => $validated['email'],
            'profile_photo'      => $profileMediaId,
            'phone'              => $validated['phone'],
            'nik'                => $validated['nik'],
            'ktp_media_id'       => $ktpMediaId,
            'family_card_number' => $validated['family_card_number'],
            'kk_media_id'        => $kkMediaId,
            'ijazah_number'      => $validated['ijazah_number'],
            'ijazah_media_id'    => $ijazahMediaId,
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
            $media = Media::find($user->profile_photo);

            if ($media) {
                if (Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                }
                $media->delete();
            }

            $user->update([
                'profile_photo' => null,
            ]);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Foto profil berhasil dihapus!');
    }
}