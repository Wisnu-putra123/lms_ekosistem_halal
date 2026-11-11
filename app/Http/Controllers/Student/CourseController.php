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
     * Menampilkan daftar course yang diikuti & katalog course yang tersedia berdasarkan kategori.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->input('search');
        $category = $request->input('category');

        // Validasi daftar kategori yang diizinkan
        $validCategories = ['penyelia_halal', 'juleha', 'auditor_halal'];
        if (!in_array($category, $validCategories)) {
            $category = null;
        }

        // 1. Ambil seluruh enrollment milik student (active & completed) HANYA untuk course yang berstatus PUBLISHED
        $allEnrollments = Enrollment::with(['course.thumbnail', 'course.creator'])
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'completed'])
            ->whereHas('course', function ($q) use ($search, $category) {
                // Filter course yang statusnya published
                $q->where('status', 'published');

                // Filter berdasarkan kategori jika ada
                if ($category) {
                    $q->where('category', $category);
                }

                // Filter pencarian berdasarkan judul atau kode
                if ($search) {
                    $q->where(function ($querySearch) use ($search) {
                        $querySearch->where('title', 'like', "%{$search}%")
                                    ->orWhere('code', 'like', "%{$search}%");
                    });
                }
            })
            ->latest()
            ->get();

        // Filter enrollment yang MASIH BERLAKU AKSESNYA
        $myEnrollments = $allEnrollments->filter(function ($enrollment) {
            return $enrollment->isAccessValid();
        });

        // Ambil ID course yang aksesnya masih aktif agar TIDAK muncul di katalog
        $activeCourseIds = $myEnrollments->pluck('course_id')->toArray();

        // 2. Katalog Course yang tersedia untuk diambil (hanya yang berstatus published)
        $availableCourses = Course::with(['thumbnail', 'creator'])
            ->where('status', 'published')
            ->whereNotIn('id', $activeCourseIds)
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
            ->withQueryString(); // Mempertahankan query param ?category=... & ?search=... pada pagination

        // Array label kategori untuk keperluan UI/Breadcrumb/Header
        $categoryLabels = [
            'penyelia_halal' => 'Penyelia Halal',
            'juleha'         => 'Juleha (Juru Sembelih Halal)',
            'auditor_halal'  => 'Auditor Halal',
        ];

        return view('student.courses.index', compact(
            'myEnrollments',
            'availableCourses',
            'search',
            'category',
            'categoryLabels'
        ));
    }

    /**
     * Proses pendaftaran/enrollment course menggunakan enrollment key.
     */
    public function enroll(Request $request, Course $course)
    {
        // Proteksi: Cek apakah course yang akan di-enroll berstatus published
        if ($course->status !== 'published') {
            return redirect()->route('student.courses.index')
                ->with('error', 'Pelatihan ini sedang diarsipkan dan tidak menerima pendaftaran baru.');
        }

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
        // Proteksi: Cek apakah course berstatus published
        if ($course->status !== 'published') {
            return redirect()->route('student.courses.index')
                ->with('error', 'Pelatihan ini sedang diarsipkan atau tidak tersedia untuk diakses.');
        }

        $userId = Auth::id();

        // Ambil enrollment terbaru LENGKAP dengan relasi progress
        $enrollment = Enrollment::with('progress')
            ->where('course_id', $course->id)
            ->where('user_id', $userId)
            ->latest()
            ->first();

        // Load relasi course lainnya
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