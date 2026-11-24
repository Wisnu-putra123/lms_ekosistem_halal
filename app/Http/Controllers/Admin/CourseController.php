<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Tampilkan daftar seluruh pelatihan beserta filter pencarian, kategori, status, dan pengajar.
     */
    public function index(Request $request)
    {
        $query = Course::with(['instructors', 'thumbnail'])
            ->withCount([
                'meetings',
                'enrollments as active_enrollments_count' => function ($q) {
                    $q->where('status', 'active');
                },
                'enrollments as completed_enrollments_count' => function ($q) {
                    $q->where('status', 'completed');
                },
            ]);

        // Filter Pencarian (Judul atau Kode Pelatihan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter Kategori ('penyelia_halal', 'juleha', 'auditor_halal')
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter Status ('draft', 'published', 'archived')
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Berdasarkan Pengajar (Teacher)
        if ($request->filled('instructor_id')) {
            $instructorId = $request->instructor_id;
            $query->whereHas('instructors', function ($q) use ($instructorId) {
                $q->where('users.id', $instructorId);
            });
        }

        $courses = $query->latest()->paginate(10)->withQueryString();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'teachers'));
    }

    /**
     * Tampilkan form pembuatan pelatihan baru.
     */
    public function create()
    {
        $generatedKey = Course::generateEnrollmentKey();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('admin.courses.create', compact('generatedKey', 'teachers'));
    }

    /**
     * Simpan data pelatihan baru beserta thumbnail, background sertifikat (wajib), dan assign instruktur.
     */
    public function store(Request $request)
    {
        $bgField = $request->hasFile('certificate_background') ? 'certificate_background' : 'certificate_bg';

        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'code'           => ['required', 'string', 'max:255', 'unique:courses,code'],
            'category'       => ['required', Rule::in(['penyelia_halal', 'juleha', 'auditor_halal'])],
            'description'    => ['nullable', 'string'],
            'enrollment_key' => ['nullable', 'string', 'max:100'],
            'status'         => ['required', Rule::in(['draft', 'published', 'archived'])],
            'instructors'    => ['nullable', 'array'],
            'instructors.*'  => ['exists:users,id'],
            'thumbnail'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            $bgField         => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'title.required'          => 'Judul pelatihan wajib diisi.',
            'code.required'           => 'Kode pelatihan wajib diisi.',
            'code.unique'             => 'Kode pelatihan sudah digunakan.',
            'category.required'       => 'Kategori pelatihan wajib dipilih.',
            'category.in'             => 'Kategori pelatihan tidak valid.',
            'status.required'         => 'Status pelatihan wajib dipilih.',
            'thumbnail.image'         => 'Berkas thumbnail harus berupa gambar.',
            'thumbnail.max'           => 'Ukuran thumbnail maksimal 2MB.',
            "{$bgField}.required"     => 'Template background sertifikat wajib diunggah.',
            "{$bgField}.image"        => 'Background sertifikat harus berupa gambar.',
            "{$bgField}.mimes"        => 'Format background sertifikat harus JPG, JPEG, PNG, atau WEBP.',
            "{$bgField}.max"          => 'Ukuran background sertifikat maksimal 5MB.',
        ]);

        DB::transaction(function () use ($request, $validated, $bgField) {
            $thumbnailMediaId = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailMediaId = $this->uploadCourseMedia(
                    $request->file('thumbnail'),
                    'courses/thumbnails'
                )->id;
            }

            $certificateBgMediaId = null;
            if ($request->hasFile($bgField)) {
                $certificateBgMediaId = $this->uploadCourseMedia(
                    $request->file($bgField),
                    'courses/certificate_backgrounds'
                )->id;
            }

            $course = Course::create([
                'title'                   => $validated['title'],
                'code'                    => strtoupper(trim($validated['code'])),
                'category'                => $validated['category'],
                'description'             => $validated['description'] ?? null,
                'thumbnail_media_id'      => $thumbnailMediaId,
                'certificate_bg_media_id' => $certificateBgMediaId,
                'enrollment_key'          => !empty($validated['enrollment_key']) ? strtoupper(trim($validated['enrollment_key'])) : Course::generateEnrollmentKey(),
                'status'                  => $validated['status'],
                'created_by'              => Auth::id(),
            ]);

            // Assign instruktur ke pivot course_instructors
            if (!empty($validated['instructors'])) {
                $course->instructors()->sync($validated['instructors']);
            }
        });

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Pelatihan baru berhasil dibuat.');
    }

    /**
     * Tampilkan detail pelatihan (Monitoring silabus read-only & daftar enrollment peserta).
     */
    public function show(Course $course)
    {
        $course->load([
            'creator',
            'instructors',
            'thumbnail',
            'certificateBackground',
            'meetings' => function ($q) {
                $q->orderBy('meeting_number')
                  ->withCount(['materials', 'assignments']);
            },
        ]);

        $enrollments = $course->enrollments()
            ->with(['user', 'progress', 'certificate'])
            ->latest('enrolled_at')
            ->paginate(10);

        return view('admin.courses.show', compact('course', 'enrollments'));
    }

    /**
     * Tampilkan form edit master data pelatihan & penugasan instruktur.
     */
    public function edit(Course $course)
    {
        $course->load(['instructors', 'thumbnail', 'certificateBackground']);
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $assignedInstructorIds = $course->instructors->pluck('id')->toArray();

        return view('admin.courses.edit', compact('course', 'teachers', 'assignedInstructorIds'));
    }

    /**
     * Perbarui data pelatihan, media visual (termasuk hapus thumbnail), dan daftar instruktur.
     */
    public function update(Request $request, Course $course)
    {
        $bgField = $request->hasFile('certificate_background') || $request->exists('certificate_background')
            ? 'certificate_background'
            : 'certificate_bg';

        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'code'             => ['required', 'string', 'max:255', Rule::unique('courses', 'code')->ignore($course->id)],
            'category'         => ['required', Rule::in(['penyelia_halal', 'juleha', 'auditor_halal'])],
            'description'      => ['nullable', 'string'],
            'enrollment_key'   => ['nullable', 'string', 'max:100'],
            'status'           => ['required', Rule::in(['draft', 'published', 'archived'])],
            'instructors'      => ['nullable', 'array'],
            'instructors.*'    => ['exists:users,id'],
            'thumbnail'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_thumbnail' => ['nullable', 'boolean'],
            $bgField           => [
                $course->certificate_bg_media_id ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'title.required'          => 'Judul pelatihan wajib diisi.',
            'code.required'           => 'Kode pelatihan wajib diisi.',
            'code.unique'             => 'Kode pelatihan sudah digunakan.',
            'category.required'       => 'Kategori pelatihan wajib dipilih.',
            'category.in'             => 'Kategori pelatihan tidak valid.',
            'status.required'         => 'Status pelatihan wajib dipilih.',
            'thumbnail.image'         => 'Berkas thumbnail harus berupa gambar.',
            'thumbnail.max'           => 'Ukuran thumbnail maksimal 2MB.',
            "{$bgField}.required"     => 'Template background sertifikat wajib diunggah.',
            "{$bgField}.image"        => 'Background sertifikat harus berupa gambar.',
            "{$bgField}.mimes"        => 'Format background sertifikat harus JPG, JPEG, PNG, atau WEBP.',
            "{$bgField}.max"          => 'Ukuran background sertifikat maksimal 5MB.',
        ]);

        DB::transaction(function () use ($request, $validated, $course, $bgField) {
            $thumbnailMediaId = $course->thumbnail_media_id;

            // 1. Jika mengunggah thumbnail baru
            if ($request->hasFile('thumbnail')) {
                $oldThumbnail = $course->thumbnail;
                $thumbnailMediaId = $this->uploadCourseMedia(
                    $request->file('thumbnail'),
                    'courses/thumbnails'
                )->id;

                if ($oldThumbnail) {
                    $course->update(['thumbnail_media_id' => $thumbnailMediaId]);
                    Storage::disk('public')->delete($oldThumbnail->file_path);
                    $oldThumbnail->delete();
                }
            }
            // 2. Jika memilih menghapus thumbnail tanpa mengganti yang baru
            elseif ($request->boolean('remove_thumbnail') && $course->thumbnail) {
                $oldThumbnail = $course->thumbnail;
                $course->update(['thumbnail_media_id' => null]);
                Storage::disk('public')->delete($oldThumbnail->file_path);
                $oldThumbnail->delete();

                $thumbnailMediaId = null;
            }

            // 3. Jika mengganti template background sertifikat
            $certificateBgMediaId = $course->certificate_bg_media_id;
            if ($request->hasFile($bgField)) {
                $oldCertificateBg = $course->certificateBackground;
                $certificateBgMediaId = $this->uploadCourseMedia(
                    $request->file($bgField),
                    'courses/certificate_backgrounds'
                )->id;

                if ($oldCertificateBg) {
                    $course->update(['certificate_bg_media_id' => $certificateBgMediaId]);
                    Storage::disk('public')->delete($oldCertificateBg->file_path);
                    $oldCertificateBg->delete();
                }
            }

            $course->update([
                'title'                   => $validated['title'],
                'code'                    => strtoupper(trim($validated['code'])),
                'category'                => $validated['category'],
                'description'             => $validated['description'] ?? null,
                'thumbnail_media_id'      => $thumbnailMediaId,
                'certificate_bg_media_id' => $certificateBgMediaId,
                'enrollment_key'          => !empty($validated['enrollment_key']) ? strtoupper(trim($validated['enrollment_key'])) : $course->enrollment_key,
                'status'                  => $validated['status'],
            ]);

            // Sinkronisasi instruktur
            $course->instructors()->sync($validated['instructors'] ?? []);
        });

        return redirect()
            ->route('admin.courses.show', $course)
            ->with('success', 'Data pelatihan berhasil diperbarui.');
    }

    /**
     * Hapus pelatihan (beserta pembersihan file media sampul dan background sertifikat).
     */
    public function destroy(Course $course)
    {
        if ($course->enrollments()->exists()) {
            return back()->with('error', 'Pelatihan tidak dapat dihapus karena sudah memiliki riwayat peserta (enrollment). Ubah status menjadi Archived jika ingin menutup kelas.');
        }

        DB::transaction(function () use ($course) {
            $thumbnail = $course->thumbnail;
            $certificateBg = $course->certificateBackground;

            $course->instructors()->detach();
            $course->delete();

            if ($thumbnail) {
                Storage::disk('public')->delete($thumbnail->file_path);
                $thumbnail->delete();
            }

            if ($certificateBg) {
                Storage::disk('public')->delete($certificateBg->file_path);
                $certificateBg->delete();
            }
        });

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Pelatihan berhasil dihapus.');
    }

    public function regenerateKey(Course $course)
    {
        $newKey = Course::generateEnrollmentKey();
        $course->update(['enrollment_key' => $newKey]);

        return back()->with('success', "Enrollment Key baru berhasil dibuat: {$newKey}");
    }

    /**
     * Helper internal untuk menyimpan file gambar ke storage dan tabel media.
     */
    private function uploadCourseMedia($file, string $directory): Media
    {
        $path = $file->store($directory, 'public');

        return Media::create([
            'type'        => 'image',
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'mime_type'   => $file->getClientMimeType(),
            'file_size'   => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);
    }
}