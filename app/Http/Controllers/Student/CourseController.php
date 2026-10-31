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

        // 1. Ambil seluruh enrollment milik student (active & completed)
        $allEnrollments = Enrollment::with(['course.thumbnail', 'course.creator'])
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'completed'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('course', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // Filter enrollment yang MASIH BERLAKU AKSESNYA
        $myEnrollments = $allEnrollments->filter(function ($enrollment) {
            return $enrollment->isAccessValid();
        });

        // Ambil ID course yang aksesnya masih aktif agar TIDAK muncul di katalog
        $activeCourseIds = $myEnrollments->pluck('course_id')->toArray();

        // 2. Katalog Course yang tersedia untuk diambil
        // (Course yang aksesnya sudah kedaluwarsa akan otomatis muncul kembali di sini)
        $availableCourses = Course::with(['thumbnail', 'creator'])
            ->where('status', 'published')
            ->whereNotIn('id', $activeCourseIds)
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

        // Cek enrollment terbaru milik user pada course ini
        $latestEnrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        // Jika aksesnya masih berlaku, langsung arahkan ke kelas tanpa buat enrollment baru
        if ($latestEnrollment && $latestEnrollment->isAccessValid()) {
            return redirect()->route('student.courses.show', $course->id)
                ->with('info', 'Anda masih memiliki akses aktif dalam pelatihan ini.');
        }

        // Jika tidak ada enrollment atau aksesnya sudah kedaluwarsa, buat enrollment baru
        Enrollment::create([
            'course_id'   => $course->id,
            'user_id'     => Auth::id(),
            'enrolled_at' => now(),
            'status'      => 'active',
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

        // Ambil enrollment terbaru
        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', $userId)
            ->latest()
            ->first();

        // Proteksi: Pastikan student sudah enroll dan masa aksesnya belum kedaluwarsa
        if (!$enrollment || !$enrollment->isAccessValid()) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Masa akses Anda untuk pelatihan ini telah berakhir. Silakan mendaftar ulang.');
        }

        // Load relasi lengkap
        $course->load([
            'meetings.materials',
            'meetings.assignments.quiz',
            'thumbnail',
            'creator',
            'instructors'
        ]);

        return view('student.courses.show', compact('course', 'enrollment'));
    }
}