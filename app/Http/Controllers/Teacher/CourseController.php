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
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->input('search');
        $category = $request->input('category');

        $validCategories = ['penyelia_halal', 'juleha', 'auditor_halal'];
        if (!in_array($category, $validCategories)) {
            $category = null;
        }

        // 1. SECTION 1: Course yang diajar oleh Teacher saat ini (Pembuat atau Instruktur Tambahan)
        $myCourses = Course::with(['thumbnail', 'certificateBackground', 'creator', 'instructors'])
            ->where(function ($query) use ($userId) {
                $query->where('created_by', $userId)
                    ->orWhereHas('instructors', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    });
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // Ambil list ID course yang sudah diajar agar tidak muncul lagi di Section 2
        $myCourseIds = $myCourses->pluck('id')->toArray();

        // 2. SECTION 2: Course lainnya (Published) yang TIDAK diajar oleh Teacher ini
        $availableCourses = Course::with(['thumbnail', 'creator', 'instructors'])
            ->whereNotIn('id', $myCourseIds)
            ->where('status', 'published')
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $categoryLabels = [
            'penyelia_halal' => 'Penyelia Halal',
            'juleha'         => 'Juleha (Juru Sembelih Halal)',
            'auditor_halal'  => 'Auditor Halal',
        ];

        return view('teacher.courses.index', compact(
            'myCourses', 
            'availableCourses', 
            'search', 
            'category', 
            'categoryLabels'
        ));
    }

    public function create()
    {
        $generatedKey = Course::generateEnrollmentKey();
        
        $teachers = User::where('role', 'teacher')
            ->where('id', '!=', Auth::id())
            ->orderBy('name', 'asc')
            ->get();

        return view('teacher.courses.create', compact('generatedKey', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:courses,code'],
            'category' => ['required', Rule::in(['penyelia_halal', 'juleha', 'auditor_halal'])],
            'description' => ['nullable', 'string'],
            'enrollment_key' => ['required', 'string', 'max:100', 'unique:courses,enrollment_key'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'certificate_background' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'instructors' => ['nullable', 'array'],
            'instructors.*' => ['exists:users,id'],
        ], [
            'title.required' => 'Judul kursus wajib diisi.',
            'code.required' => 'Kode kursus wajib diisi.',
            'code.unique' => 'Kode kursus sudah digunakan.',
            'category.required' => 'Kategori kursus wajib dipilih.',
            'category.in' => 'Kategori kursus tidak valid.',
            'enrollment_key.required' => 'Enrollment key wajib diisi.',
            'enrollment_key.unique' => 'Enrollment key sudah digunakan.',
            'status.required' => 'Status kursus wajib dipilih.',
            'status.in' => 'Status kursus tidak valid.',
            'thumbnail.image' => 'Berkas thumbnail harus berupa gambar.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB.',
            'certificate_background.image' => 'Background sertifikat harus berupa gambar.',
            'certificate_background.max' => 'Ukuran background sertifikat maksimal 3MB.',
        ]);

        $thumbnailMediaId = null;
        $certificateBgMediaId = null;

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

        if ($request->hasFile('certificate_background')) {
            $file = $request->file('certificate_background');
            $path = $file->store('certificate_backgrounds', 'public');

            $media = Media::create([
                'type' => 'image',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);

            $certificateBgMediaId = $media->id;
        }

        $course = Course::create([
            'title' => $validated['title'],
            'code' => strtoupper($validated['code']),
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'thumbnail_media_id' => $thumbnailMediaId,
            'certificate_bg_media_id' => $certificateBgMediaId,
            'enrollment_key' => strtoupper($validated['enrollment_key']),
            'status' => $validated['status'],
            'created_by' => Auth::id(),
        ]);

        $instructorIds = $validated['instructors'] ?? [];
        if (!in_array(Auth::id(), $instructorIds)) {
            $instructorIds[] = Auth::id();
        }

        $course->instructors()->attach($instructorIds);

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Pelatihan baru berhasil ditambahkan!');
    }

    public function show(Course $course)
    {
        $this->authorizeAccess($course);

        $course->load(['meetings', 'thumbnail', 'certificateBackground', 'instructors']);
        return view('teacher.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->authorizeAccess($course);

        $teachers = User::where('role', 'teacher')
            ->where('id', '!=', $course->created_by)
            ->orderBy('name', 'asc')
            ->get();

        $assignedInstructorIds = $course->instructors()
            ->where('user_id', '!=', $course->created_by)
            ->pluck('users.id')
            ->toArray();

        return view('teacher.courses.edit', compact('course', 'teachers', 'assignedInstructorIds'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeAccess($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('courses', 'code')->ignore($course->id)],
            'category' => ['required', Rule::in(['penyelia_halal', 'juleha', 'auditor_halal'])],
            'description' => ['nullable', 'string'],
            'enrollment_key' => ['required', 'string', 'max:100', Rule::unique('courses', 'enrollment_key')->ignore($course->id)],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'certificate_background' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'instructors' => ['nullable', 'array'],
            'instructors.*' => ['exists:users,id'],
        ], [
            'title.required' => 'Judul kursus wajib diisi.',
            'code.required' => 'Kode kursus wajib diisi.',
            'code.unique' => 'Kode kursus sudah digunakan.',
            'category.required' => 'Kategori kursus wajib dipilih.',
            'category.in' => 'Kategori kursus tidak valid.',
            'enrollment_key.required' => 'Enrollment key wajib diisi.',
            'enrollment_key.unique' => 'Enrollment key sudah digunakan.',
            'status.required' => 'Status kursus wajib dipilih.',
            'status.in' => 'Status kursus tidak valid.',
            'thumbnail.image' => 'Berkas thumbnail harus berupa gambar.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB.',
            'certificate_background.image' => 'Background sertifikat harus berupa gambar.',
            'certificate_background.max' => 'Ukuran background sertifikat maksimal 3MB.',
        ]);

        $thumbnailMediaId = $course->thumbnail_media_id;
        $certificateBgMediaId = $course->certificate_bg_media_id;

        if ($request->hasFile('thumbnail')) {
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

        if ($request->hasFile('certificate_background')) {
            if ($course->certificateBackground) {
                Storage::disk('public')->delete($course->certificateBackground->file_path);
                $course->certificateBackground->delete();
            }

            $file = $request->file('certificate_background');
            $path = $file->store('certificate_backgrounds', 'public');

            $media = Media::create([
                'type' => 'image',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);

            $certificateBgMediaId = $media->id;
        }

        $course->update([
            'title' => $validated['title'],
            'code' => strtoupper($validated['code']),
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'thumbnail_media_id' => $thumbnailMediaId,
            'certificate_bg_media_id' => $certificateBgMediaId,
            'enrollment_key' => strtoupper($validated['enrollment_key']),
            'status' => $validated['status'],
        ]);

        $instructorIds = $validated['instructors'] ?? [];
        if (!in_array($course->created_by, $instructorIds)) {
            $instructorIds[] = $course->created_by;
        }

        $course->instructors()->sync($instructorIds);

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Data pelatihan berhasil diperbarui!');
    }

    public function regenerateKey(Course $course)
    {
        $this->authorizeAccess($course);

        $newKey = Course::generateEnrollmentKey();
        $course->update(['enrollment_key' => $newKey]);

        return back()->with('success', "Enrollment Key baru berhasil dibuat: {$newKey}");
    }

    public function destroy(Course $course)
    {
        $this->authorizeAccess($course);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail->file_path);
            $course->thumbnail->delete();
        }

        if ($course->certificateBackground) {
            Storage::disk('public')->delete($course->certificateBackground->file_path);
            $course->certificateBackground->delete();
        }

        $course->delete();

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Pelatihan berhasil dihapus.');
    }

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