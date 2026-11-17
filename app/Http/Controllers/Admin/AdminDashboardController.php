<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Tampilkan halaman utama Dashboard Admin beserta ringkasan KPI,
     * antrean tindakan prioritas, performa pelatihan, dan aktivitas terbaru.
     */
    public function index(Request $request)
    {
        // 1. KPI Ringkasan Pengguna (tabel: users)
        $userStats = [
            'total'    => User::count(),
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'admins'   => User::where('role', 'admin')->count(),
        ];

        // 2. KPI Ringkasan Pelatihan (tabel: courses)
        $courseStats = [
            'total'     => Course::count(),
            'published' => Course::where('status', 'published')->count(),
            'draft'     => Course::where('status', 'draft')->count(),
        ];

        // 3. KPI Ringkasan Enrollment Peserta (tabel: enrollments)
        $enrollmentStats = [
            'total'     => Enrollment::count(),
            'active'    => Enrollment::where('status', 'active')->count(),
            'completed' => Enrollment::where('status', 'completed')->count(),
        ];

        // 4. KPI Ringkasan Sertifikat (tabel: certificates)
        $certificateStats = [
            'total'     => Certificate::count(),
            'published' => Certificate::where('status', 'published')->count(),
            'draft'     => Certificate::where('status', 'draft')->count(),
            'signed'    => Certificate::whereNotNull('signed_media_id')->count(),
        ];

        // 5. Antrean Tindakan Prioritas (Actionable Alerts)
        // A. Peserta sudah lulus (status = 'completed') tetapi record sertifikat belum ada
        $pendingCertificates = Enrollment::with(['user', 'course'])
            ->where('status', 'completed')
            ->whereDoesntHave('certificate')
            ->orderByDesc('completed_at')
            ->limit(5)
            ->get();

        // B. Sertifikat sudah dibuat tetapi masih berstatus 'draft'
        $draftCertificates = Certificate::with(['enrollment.user', 'enrollment.course'])
            ->where('status', 'draft')
            ->latest()
            ->limit(5)
            ->get();

        // C. Peserta aktif yang data kependudukan / berkas persyaratannya belum lengkap
        $incompleteProfilesCount = User::where('role', 'student')
            ->where(function ($q) {
                $q->whereNull('nik')
                  ->orWhereNull('family_card_number')
                  ->orWhereNull('ijazah_number')
                  ->orWhereNull('ktp_media_id')
                  ->orWhereNull('kk_media_id')
                  ->orWhereNull('ijazah_media_id');
            })
            ->count();

        // D. Pelatihan yang belum memiliki pengajar (pivot: course_instructors)
        $coursesWithoutInstructor = Course::whereDoesntHave('instructors')
            ->latest()
            ->limit(5)
            ->get();

        // 6. Performa Pelatihan (Progres diambil dari tabel course_progress melalui enrollments.progress)
        $courseOverviews = Course::with(['instructors', 'enrollments.progress'])
            ->withCount([
                'enrollments as active_students_count' => function ($q) {
                    $q->where('status', 'active');
                },
                'enrollments as completed_students_count' => function ($q) {
                    $q->where('status', 'completed');
                },
            ])
            ->orderByDesc('active_students_count')
            ->limit(6)
            ->get()
            ->map(function ($course) {
                $course->avg_progress = $course->enrollments->avg(function ($enrollment) {
                    return $enrollment->progress->progress_percentage ?? 0;
                }) ?? 0;

                return $course;
            });

        // 7. Data Grafik Pendaftaran & Kelulusan 6 Bulan Terakhir
        $chartLabels = [];
        $chartEnrollments = [];
        $chartCompletions = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('M Y');

            $chartEnrollments[] = Enrollment::whereYear('enrolled_at', $month->year)
                ->whereMonth('enrolled_at', $month->month)
                ->count();

            $chartCompletions[] = Enrollment::where('status', 'completed')
                ->whereYear('completed_at', $month->year)
                ->whereMonth('completed_at', $month->month)
                ->count();
        }

        // 8. Log Aktivitas Terbaru
        $recentUsers = User::latest()
            ->limit(5)
            ->get();

        // tabel: assignment_submissions (kolom waktu: submitted_at)
        $recentSubmissions = AssignmentSubmission::with([
                'enrollment.user',
                'assignment.course',
            ])
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        // tabel: quiz_attempts (kolom waktu: submitted_at, relasi: quiz.assignment.course)
        $recentQuizAttempts = QuizAttempt::with([
                'enrollment.user',
                'quiz.assignment.course',
            ])
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'userStats',
            'courseStats',
            'enrollmentStats',
            'certificateStats',
            'pendingCertificates',
            'draftCertificates',
            'incompleteProfilesCount',
            'coursesWithoutInstructor',
            'courseOverviews',
            'chartLabels',
            'chartEnrollments',
            'chartCompletions',
            'recentUsers',
            'recentSubmissions',
            'recentQuizAttempts'
        ));
    }
}