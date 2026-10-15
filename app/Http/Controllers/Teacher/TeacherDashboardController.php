<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil seluruh Course yang dibuat oleh Teacher ini
        $myCourses = Course::with(['thumbnail', 'enrollments'])
            ->where('created_by', $userId)
            ->latest()
            ->get();

        $courseIds = $myCourses->pluck('id')->toArray();

        // 2. Query Submission Tugas Siswa yang MEMBUTUHKAN PENILAIAN (status = 'submitted')
        $pendingSubmissions = AssignmentSubmission::with(['assignment.course', 'enrollment.user', 'attachments.media'])
            ->whereHas('assignment', function ($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'asc')
            ->get();

        // 3. Query Pengerjaan Kuis Siswa Terbaru (Attempt Submitted/Expired)
        $recentQuizAttempts = QuizAttempt::with(['quiz.assignment.course', 'enrollment.user'])
            ->whereHas('quiz.assignment', function ($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })
            ->whereIn('status', ['submitted', 'expired'])
            ->orderBy('submitted_at', 'desc')
            ->take(5)
            ->get();

        // 4. Hitung Statistik Ringkas
        $totalActiveStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->distinct('user_id')
            ->count('user_id');

        $pendingGradingCount = $pendingSubmissions->count();

        return view('teacher.dashboard', compact(
            'myCourses',
            'pendingSubmissions',
            'recentQuizAttempts',
            'totalActiveStudents',
            'pendingGradingCount'
        ));
    }
}