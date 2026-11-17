<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Mapping field input file sesuai dengan ProfileController & User Model.
     */
    private const FILE_FIELDS = [
        'profile_photo' => [
            'column' => 'profile_photo',
            'folder' => 'users/photos',
            'type'   => 'image',
        ],
        'ktp' => [
            'column' => 'ktp_media_id',
            'folder' => 'users/documents/ktp',
            'type'   => 'document',
        ],
        'kk' => [
            'column' => 'kk_media_id',
            'folder' => 'users/documents/kk',
            'type'   => 'document',
        ],
        'ijazah' => [
            'column' => 'ijazah_media_id',
            'folder' => 'users/documents/ijazah',
            'type'   => 'document',
        ],
    ];

    /**
     * Tampilkan daftar seluruh pengguna untuk dikelola Admin.
     */
    public function index(Request $request)
    {
        $query = User::with(['profilePhotoMedia']);

        // Filter berdasarkan pencarian nama, email, NIK, atau institusi
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role') && in_array($request->input('role'), ['admin', 'student', 'teacher'])) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Tampilkan formulir tambah pengguna baru oleh Admin.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Simpan pengguna baru yang ditambahkan Admin.
     * Hanya name, email, role, dan password yang wajib; sisanya nullable.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Wajib
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', Rule::in(['admin', 'student', 'teacher'])],

            // Nullable
            'phone'              => ['nullable', 'string', 'max:255'],
            'nik'                => ['nullable', 'digits:16', 'unique:users,nik'],
            'family_card_number' => ['nullable', 'digits:16'],
            'ijazah_number'      => ['nullable', 'string', 'max:100'],
            'address'            => ['nullable', 'string'],
            'institution'        => ['nullable', 'string', 'max:255'],

            // File Uploads (Nullable)
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'ktp'           => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'kk'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'ijazah'        => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'name.required'             => 'Nama lengkap wajib diisi.',
            'email.required'            => 'Email wajib diisi.',
            'email.unique'              => 'Email sudah terdaftar.',
            'password.required'         => 'Kata sandi wajib diisi.',
            'password.min'              => 'Kata sandi minimal 8 karakter.',
            'role.required'             => 'Role pengguna wajib dipilih.',
            'role.in'                   => 'Role tidak valid.',
            'nik.digits'                => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'                => 'NIK sudah terdaftar pada pengguna lain.',
            'family_card_number.digits' => 'Nomor Kartu Keluarga harus terdiri dari 16 digit angka.',
            'profile_photo.image'       => 'Foto profil harus berupa gambar.',
            'profile_photo.max'         => 'Ukuran foto profil maksimal 2MB.',
            'ktp.mimes'                 => 'Berkas KTP harus berformat PDF, JPG, JPEG, atau PNG.',
            'kk.mimes'                  => 'Berkas Kartu Keluarga harus berformat PDF, JPG, JPEG, atau PNG.',
            'ijazah.mimes'              => 'Berkas Ijazah harus berformat PDF, JPG, JPEG, atau PNG.',
        ]);

        $user = DB::transaction(function () use ($request, $validated) {
            $userData = [
                'name'               => $validated['name'],
                'email'              => $validated['email'],
                'password'           => Hash::make($validated['password']),
                'role'               => $validated['role'],
                'phone'              => $validated['phone'] ?? null,
                'nik'                => $validated['nik'] ?? null,
                'family_card_number' => $validated['family_card_number'] ?? null,
                'ijazah_number'      => $validated['ijazah_number'] ?? null,
                'address'            => $validated['address'] ?? null,
                'institution'        => $validated['institution'] ?? null,
            ];

            foreach (self::FILE_FIELDS as $inputName => $config) {
                if ($request->hasFile($inputName)) {
                    $media = $this->storeMediaFile(
                        $request->file($inputName),
                        $config['folder'],
                        $config['type']
                    );
                    $userData[$config['column']] = $media->id;
                }
            }

            return User::create($userData);
        });

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna baru '{$user->name}' berhasil ditambahkan.");
    }

    /**
     * Arahkan show ke halaman detail & edit terpadu (seperti halaman profile).
     */
    public function show(User $user)
    {
        return $this->edit($user);
    }

    /**
     * Tampilkan halaman detail lengkap sekaligus form edit pengguna (mirip halaman profile).
     */
    public function edit(User $user)
    {
        $user->load([
            'profilePhotoMedia',
            'ktpMedia',
            'kkMedia',
            'ijazahMedia',
        ]);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Perbarui data profil & berkas pengguna oleh Admin.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            // Wajib
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin', 'student', 'teacher'])],

            // Nullable
            'phone'              => ['nullable', 'string', 'max:255'],
            'nik'                => ['nullable', 'digits:16', Rule::unique('users', 'nik')->ignore($user->id)],
            'family_card_number' => ['nullable', 'digits:16'],
            'ijazah_number'      => ['nullable', 'string', 'max:100'],
            'address'            => ['nullable', 'string'],
            'institution'        => ['nullable', 'string', 'max:255'],

            // File Uploads (Nullable)
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'ktp'           => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'kk'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'ijazah'        => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'name.required'             => 'Nama lengkap wajib diisi.',
            'email.required'            => 'Email wajib diisi.',
            'email.unique'              => 'Email sudah digunakan pengguna lain.',
            'role.required'             => 'Role pengguna wajib dipilih.',
            'role.in'                   => 'Role tidak valid.',
            'nik.digits'                => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'                => 'NIK sudah terdaftar pada pengguna lain.',
            'family_card_number.digits' => 'Nomor Kartu Keluarga harus terdiri dari 16 digit angka.',
            'profile_photo.image'       => 'Foto profil harus berupa gambar.',
            'profile_photo.max'         => 'Ukuran foto profil maksimal 2MB.',
            'ktp.mimes'                 => 'Berkas KTP harus berformat PDF, JPG, JPEG, atau PNG.',
            'kk.mimes'                  => 'Berkas Kartu Keluarga harus berformat PDF, JPG, JPEG, atau PNG.',
            'ijazah.mimes'              => 'Berkas Ijazah harus berformat PDF, JPG, JPEG, atau PNG.',
        ]);

        DB::transaction(function () use ($request, $validated, $user) {
            $data = [
                'name'               => $validated['name'],
                'email'              => $validated['email'],
                'role'               => $validated['role'],
                'phone'              => $validated['phone'] ?? null,
                'nik'                => $validated['nik'] ?? null,
                'family_card_number' => $validated['family_card_number'] ?? null,
                'ijazah_number'      => $validated['ijazah_number'] ?? null,
                'address'            => $validated['address'] ?? null,
                'institution'        => $validated['institution'] ?? null,
            ];

            foreach (self::FILE_FIELDS as $inputName => $config) {
                if ($request->hasFile($inputName)) {
                    $column = $config['column'];
                    $oldMediaId = $user->{$column};

                    $newMedia = $this->storeMediaFile(
                        $request->file($inputName),
                        $config['folder'],
                        $config['type']
                    );
                    $data[$column] = $newMedia->id;

                    if ($oldMediaId) {
                        $this->deleteMediaById($oldMediaId);
                    }
                }
            }

            $user->update($data);
        });

        return redirect()->route('admin.users.edit', $user->id)
            ->with('success', "Informasi profil pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Perbarui kata sandi pengguna oleh Admin (terpisah di Section 2 seperti halaman profile).
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.edit', $user->id)
            ->with('success', "Kata sandi untuk '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Hapus foto profil pengguna oleh Admin (mirip route profile.photo.destroy).
     */
    public function destroyPhoto(User $user)
    {
        if ($user->profile_photo) {
            $oldMediaId = $user->profile_photo;
            $user->update(['profile_photo' => null]);
            $this->deleteMediaById($oldMediaId);
        }

        return redirect()->route('admin.users.edit', $user->id)
            ->with('success', "Foto profil '{$user->name}' berhasil dihapus.");
    }

    /**
     * Ubah role pengguna secara cepat dari halaman index.
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'student', 'teacher'])],
        ], [
            'role.required' => 'Role wajib dipilih.',
            'role.in'       => 'Role tidak valid.',
        ]);

        $oldRole = ucfirst($user->role);
        $user->update(['role' => $validated['role']]);
        $newRole = ucfirst($user->role);

        return redirect()->back()
            ->with('success', "Role '{$user->name}' berhasil diubah dari {$oldRole} menjadi {$newRole}.");
    }

    /**
     * Hapus pengguna beserta seluruh berkas media terkait dari storage & database.
     */
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;

        DB::transaction(function () use ($user) {
            $mediaIds = array_filter([
                $user->profile_photo,
                $user->ktp_media_id,
                $user->kk_media_id,
                $user->ijazah_media_id,
            ]);

            $user->delete();

            foreach ($mediaIds as $mediaId) {
                $this->deleteMediaById($mediaId);
            }
        });

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna '{$userName}' berhasil dihapus.");
    }

    /**
     * Simpan file ke storage/app/public dan buat record pada tabel media.
     */
    private function storeMediaFile(UploadedFile $file, string $folder, string $mediaType): Media
    {
        $path = $file->store($folder, 'public');

        // Samakan tipe 'image' vs 'document' dengan accessor di model User
        $detectedType = str_starts_with($file->getMimeType() ?? '', 'image/') ? 'image' : $mediaType;

        return Media::create([
            'type'        => $detectedType,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'mime_type'   => $file->getMimeType(),
            'file_size'   => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);
    }

    /**
     * Hapus file fisik dan record tabel media berdasarkan ID.
     */
    private function deleteMediaById(int $mediaId): void
    {
        $media = Media::find($mediaId);
        if (!$media) {
            return;
        }

        if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();
    }
}