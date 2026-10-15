<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Menampilkan daftar course yang diikuti & katalog course yang tersedia.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->input('search');

        // 1. Course yang sedang diikuti oleh student
        $myEnrollments = Enrollment::with(['course.thumbnail', 'course.creator'])
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('course', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // Ambil ID course yang sudah di-enroll agar tidak muncul di katalog
        $enrolledCourseIds = $myEnrollments->pluck('course_id')->toArray();

        // 2. Katalog Course yang tersedia untuk diambil (status = 'published')
        $availableCourses = Course::with(['thumbnail', 'creator'])
            ->where('status', 'published')
            ->whereNotIn('id', $enrolledCourseIds)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(9);

        return view('student.courses.index', compact('myEnrollments', 'availableCourses', 'search'));
    }

    /**
     * Proses pendaftaran/enrollment course menggunakan enrollment key.
     */
    public function enroll(Request $request, Course $course)
    {
        $request->validate([
            'enrollment_key' => ['required', 'string'],
        ], [
            'enrollment_key.required' => 'Enrollment Key wajib diisi.',
        ]);

        // Verifikasi Enrollment Key
        if (strtoupper($request->enrollment_key) !== strtoupper($course->enrollment_key)) {
            return back()->withErrors(['enrollment_key' => 'Enrollment Key yang Anda masukkan salah.'])->withInput();
        }

        // Cek apakah sudah terdaftar sebelumnya
        $existingEnrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingEnrollment) {
            return back()->with('info', 'Anda sudah terdaftar dalam pelatihan ini.');
        }

        // Simpan ke tabel enrollments
        Enrollment::create([
            'course_id' => $course->id,
            'user_id' => Auth::id(),
            'enrolled_at' => now(),
            'status' => 'active',
        ]);

        return redirect()->route('student.courses.show', $course->id)
            ->with('success', 'Berhasil mendaftar ke pelatihan ' . $course->title);
    }

    /**
     * Menampilkan isi materi & pertemuan dari course yang di-enroll.
     */
    public function show(Course $course)
    {
        $userId = Auth::id();

        // Proteksi: Pastikan student sudah enroll di course ini
        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', $userId)
            ->latest()
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda belum terdaftar dalam pelatihan ini. Silakan masukkan Enrollment Key terlebih dahulu.');
        }

        // Load relasi lengkap: meetings, materials, assignments, creator, serta pengajar tambahan (teachers)
        $course->load([
            'meetings.materials',
            'meetings.assignments.quiz',
            'thumbnail',
            'creator',
            'instructors' // Mengambil seluruh pengajar course
        ]);

        return view('student.courses.show', compact('course', 'enrollment'));
    }
}