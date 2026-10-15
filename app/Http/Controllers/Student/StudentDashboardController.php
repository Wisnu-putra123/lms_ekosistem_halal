<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil seluruh Enrollment Aktif Student
        $activeEnrollments = Enrollment::with(['course.thumbnail', 'progress'])
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        $activeEnrollmentIds = $activeEnrollments->pluck('id')->toArray();
        $activeCourseIds = $activeEnrollments->pluck('course_id')->toArray();

        // 2. Ambil Semua Item Tugas & Kuis dari Course Aktif
        $assignments = Assignment::with(['meeting', 'course', 'quiz'])
            ->whereIn('course_id', $activeCourseIds)
            ->where('status', 'published')
            ->orderBy('available_until', 'asc')
            ->get();

        // 3. Query Seluruh Submission & Quiz Attempt Student pada Enrollment Aktif
        $submissions = AssignmentSubmission::whereIn('enrollment_id', $activeEnrollmentIds)->get();
        $quizAttempts = QuizAttempt::whereIn('enrollment_id', $activeEnrollmentIds)->get();

        // 4. Olah Timeline / Pending Tasks (To-Do List Student)
        $pendingTasks = collect();

        foreach ($assignments as $assignment) {
            if ($assignment->type === 'submission') {
                $userSubs = $submissions->where('assignment_id', $assignment->id);
                $bestScore = $userSubs->where('status', 'graded')->max('score');
                $isCompleted = !is_null($bestScore) && $bestScore >= $assignment->passing_score;

                if (!$isCompleted) {
                    $pendingTasks->push([
                        'id' => $assignment->id,
                        'type' => 'submission',
                        'title' => $assignment->title,
                        'course_title' => $assignment->course->title,
                        'meeting_number' => $assignment->meeting->meeting_number ?? '-',
                        'available_until' => $assignment->available_until,
                        'passing_score' => $assignment->passing_score,
                        'status_label' => $userSubs->isNotEmpty() ? 'Menunggu Penilaian' : 'Belum Dikerjakan',
                        'route' => route('student.submissions.show', $assignment->id),
                    ]);
                }
            } else { // 'quiz'
                $quizId = $assignment->quiz->id ?? null;
                $userAttempts = $quizId ? $quizAttempts->where('quiz_id', $quizId) : collect();
                $bestScore = $userAttempts->whereIn('status', ['submitted', 'expired'])->max('score');
                $isCompleted = !is_null($bestScore) && $bestScore >= $assignment->passing_score;

                if (!$isCompleted) {
                    $pendingTasks->push([
                        'id' => $assignment->id,
                        'type' => 'quiz',
                        'title' => $assignment->title,
                        'course_title' => $assignment->course->title,
                        'meeting_number' => $assignment->meeting->meeting_number ?? '-',
                        'available_until' => $assignment->available_until,
                        'passing_score' => $assignment->passing_score,
                        'status_label' => $userAttempts->isNotEmpty() ? 'Belum Lulus KKM' : 'Belum Dikerjakan',
                        'route' => route('student.quizzes.show', $assignment->id),
                    ]);
                }
            }
        }

        // Urutkan To-Do: Yang ada deadline terdekat di atas
        $pendingTasks = $pendingTasks->sortBy(function ($item) {
            return $item['available_until'] ? \Carbon\Carbon::parse($item['available_until'])->timestamp : PHP_INT_MAX;
        })->take(6);

        // 5. Query Recent Graded Feedback (Aktivitas Nilai Baru Masuk)
        $recentFeedbacks = AssignmentSubmission::with(['assignment.course'])
            ->whereIn('enrollment_id', $activeEnrollmentIds)
            ->where('status', 'graded')
            ->orderBy('graded_at', 'desc')
            ->take(4)
            ->get();

        // 6. Quick Stats
        $avgProgress = $activeEnrollments->avg(function ($en) {
            return $en->progress->progress_percentage ?? 0;
        });

        return view('student.dashboard', compact(
            'activeEnrollments',
            'pendingTasks',
            'recentFeedbacks',
            'avgProgress'
        ));
    }
}