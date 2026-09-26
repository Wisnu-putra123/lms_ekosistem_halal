<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Tampilkan daftar kursus yang dibuat oleh Teacher yang sedang login
     * atau di mana Teacher terdaftar sebagai pengajar tambahan.
     */
    public function index()
    {
        $userId = Auth::id();

        $courses = Course::with(['thumbnail'])
            ->where('created_by', $userId)
            ->orWhereHas('instructors', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->paginate(9);

        return view('teacher.courses.index', compact('courses'));
    }

    /**
     * Form tambah kursus baru.
     */
    public function create()
    {
        $generatedKey = Course::generateEnrollmentKey();
        
        // Ambil daftar pengajar lain (selain pengajar yang sedang login) untuk dipilih
        $teachers = User::where('role', 'teacher')
            ->where('id', '!=', Auth::id())
            ->orderBy('name', 'asc')
            ->get();

        return view('teacher.courses.create', compact('generatedKey', 'teachers'));
    }

    /**
     * Simpan kursus baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:courses,code'],
            'description' => ['nullable', 'string'],
            'enrollment_key' => ['required', 'string', 'max:100', 'unique:courses,enrollment_key'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'instructors' => ['nullable', 'array'],
            'instructors.*' => ['exists:users,id'],
        ], [
            'title.required' => 'Judul kursus wajib diisi.',
            'code.required' => 'Kode kursus wajib diisi.',
            'code.unique' => 'Kode kursus sudah digunakan.',
            'enrollment_key.required' => 'Enrollment key wajib diisi.',
            'enrollment_key.unique' => 'Enrollment key sudah digunakan.',
            'status.required' => 'Status kursus wajib dipilih.',
            'status.in' => 'Status kursus tidak valid.',
            'thumbnail.image' => 'Berkas harus berupa gambar.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $thumbnailMediaId = null;

        // 1. Proses Upload Thumbnail ke Tabel Media
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $path = $file->store('thumbnails', 'public');

            $media = Media::create([
                'type' => 'image',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);

            $thumbnailMediaId = $media->id;
        }

        // 2. Simpan Data Utama Kursus
        $course = Course::create([
            'title' => $validated['title'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'thumbnail_media_id' => $thumbnailMediaId,
            'enrollment_key' => strtoupper($validated['enrollment_key']),
            'status' => $validated['status'],
            'created_by' => Auth::id(),
        ]);

        // 3. Gabungkan Creator dan Pengajar Tambahan ke Tabel course_instructors
        $instructorIds = $validated['instructors'] ?? [];
        
        // Pastikan ID pembuat (creator) dimasukkan ke dalam daftar instructor jika belum ada
        if (!in_array(Auth::id(), $instructorIds)) {
            $instructorIds[] = Auth::id();
        }

        $course->instructors()->attach($instructorIds);

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Pelatihan baru berhasil ditambahkan!');
    }

    /**
     * Detail kursus beserta pertemuan di dalamnya.
     */
    public function show(Course $course)
    {
        $this->authorizeAccess($course);

        $course->load(['meetings', 'thumbnail', 'instructors']);
        return view('teacher.courses.show', compact('course'));
    }

    /**
     * Form edit kursus.
     */
    public function edit(Course $course)
    {
        $this->authorizeAccess($course);

        // Ambil daftar pengajar lain (selain pengajar pembuat/creator)
        $teachers = User::where('role', 'teacher')
            ->where('id', '!=', $course->created_by)
            ->orderBy('name', 'asc')
            ->get();

        // Ambil ID pengajar tambahan yang terikat (tanpa pembuat/creator)
        $assignedInstructorIds = $course->instructors()
            ->where('user_id', '!=', $course->created_by)
            ->pluck('users.id')
            ->toArray();

        return view('teacher.courses.edit', compact('course', 'teachers', 'assignedInstructorIds'));
    }

    /**
     * Perbarui data kursus.
     */
    public function update(Request $request, Course $course)
    {
        $this->authorizeAccess($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('courses', 'code')->ignore($course->id)],
            'description' => ['nullable', 'string'],
            'enrollment_key' => ['required', 'string', 'max:100', Rule::unique('courses', 'enrollment_key')->ignore($course->id)],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'instructors' => ['nullable', 'array'],
            'instructors.*' => ['exists:users,id'],
        ], [
            'title.required' => 'Judul kursus wajib diisi.',
            'code.required' => 'Kode kursus wajib diisi.',
            'code.unique' => 'Kode kursus sudah digunakan.',
            'enrollment_key.required' => 'Enrollment key wajib diisi.',
            'enrollment_key.unique' => 'Enrollment key sudah digunakan.',
            'status.required' => 'Status kursus wajib dipilih.',
            'status.in' => 'Status kursus tidak valid.',
            'thumbnail.image' => 'Berkas harus berupa gambar.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $thumbnailMediaId = $course->thumbnail_media_id;

        // 1. Proses Update / Ganti Thumbnail
        if ($request->hasFile('thumbnail')) {
            // Hapus berkas gambar lama jika ada
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail->file_path);
                $course->thumbnail->delete();
            }

            $file = $request->file('thumbnail');
            $path = $file->store('thumbnails', 'public');

            $media = Media::create([
                'type' => 'image',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);

            $thumbnailMediaId = $media->id;
        }

        // 2. Update Data Utama Kursus
        $course->update([
            'title' => $validated['title'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'thumbnail_media_id' => $thumbnailMediaId,
            'enrollment_key' => strtoupper($validated['enrollment_key']),
            'status' => $validated['status'],
        ]);

        // 3. Sync Pengajar Tambahan (Selalu pertahankan ID pembuat/creator)
        $instructorIds = $validated['instructors'] ?? [];
        if (!in_array($course->created_by, $instructorIds)) {
            $instructorIds[] = $course->created_by;
        }

        $course->instructors()->sync($instructorIds);

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Data pelatihan berhasil diperbarui!');
    }

    /**
     * Regenerate Enrollment Key secara cepat.
     */
    public function regenerateKey(Course $course)
    {
        $this->authorizeAccess($course);

        $newKey = Course::generateEnrollmentKey();
        $course->update(['enrollment_key' => $newKey]);

        return back()->with('success', "Enrollment Key baru berhasil dibuat: {$newKey}");
    }

    /**
     * Hapus kursus.
     */
    public function destroy(Course $course)
    {
        $this->authorizeAccess($course);

        // Hapus media thumbnail jika ada
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail->file_path);
            $course->thumbnail->delete();
        }

        $course->delete();

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Pelatihan berhasil dihapus.');
    }

    /**
     * Helper privat untuk mengecek otoritas pengajar terhadap kursus.
     */
    private function authorizeAccess(Course $course): void
    {
        $userId = Auth::id();
        $isCreator = $course->created_by === $userId;
        $isInstructor = $course->instructors()->where('user_id', $userId)->exists();

        if (!$isCreator && !$isInstructor) {
            abort(403, 'Anda tidak memiliki akses ke kursus ini.');
        }
    }
}