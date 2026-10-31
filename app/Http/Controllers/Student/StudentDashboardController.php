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

        // 1. Ketentuan 3: Ambil Enrollment Aktif DAN Enrollment Completed yang Belum Expired
        $enrollments = Enrollment::with(['course.thumbnail', 'progress'])
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', 'active')
                      ->orWhere(function ($q) {
                          $q->where('status', 'completed')
                            ->where(function ($subQ) {
                                $subQ->whereNull('access_expires_at')
                                     ->orWhere('access_expires_at', '>', now());
                            });
                      });
            })
            ->get();

        $enrollmentIds = $enrollments->pluck('id')->toArray();
        $courseIds     = $enrollments->pluck('course_id')->toArray();

        // 2. Ambil Semua Tugas & Kuis Terpublikasi dari Course Terkait
        $assignments = Assignment::with(['meeting', 'course', 'quiz'])
            ->whereIn('course_id', $courseIds)
            ->where('status', 'published')
            ->orderBy('available_until', 'asc')
            ->get();

        // 3. Query Seluruh Submission & Quiz Attempt Student
        $submissions  = AssignmentSubmission::whereIn('enrollment_id', $enrollmentIds)->get();
        $quizAttempts = QuizAttempt::whereIn('enrollment_id', $enrollmentIds)->get();

        // 4. Olah Timeline / Pending Tasks
        $pendingTasks = collect();

        foreach ($assignments as $assignment) {
            if ($assignment->type === 'submission') {
                $userSubs = $submissions->where('assignment_id', $assignment->id);
                $bestScore = $userSubs->where('status', 'graded')->max('score');
                $isCompleted = !is_null($bestScore) && $bestScore >= $assignment->passing_score;

                if (!$isCompleted) {
                    $hasSubmittedUngraded = $userSubs->whereIn('status', ['submitted', 'late'])->isNotEmpty();
                    $hasFailedGraded      = !is_null($bestScore) && $bestScore < $assignment->passing_score;

                    // Ketentuan 1 & 2: Tentukan label status & badge
                    if ($hasSubmittedUngraded) {
                        $statusLabel = 'Dalam Penilaian';
                        $statusBadge = 'unassessed'; // Belum dinilai pengajar
                    } elseif ($hasFailedGraded) {
                        $statusLabel = 'Belum Memenuhi KKM (Nilai: ' . number_format($bestScore, 0) . ')';
                        $statusBadge = 'failed';     // Sudah dinilai tapi tidak lulus KKM
                    } else {
                        $statusLabel = 'Belum Dikerjakan';
                        $statusBadge = 'pending';    // Belum kirim berkas
                    }

                    $pendingTasks->push([
                        'id'              => $assignment->id,
                        'type'            => 'submission',
                        'title'           => $assignment->title,
                        'course_title'    => $assignment->course->title,
                        'meeting_number'  => $assignment->meeting->meeting_number ?? '-',
                        'available_until' => $assignment->available_until,
                        'passing_score'   => $assignment->passing_score,
                        'status_label'    => $statusLabel,
                        'status_badge'    => $statusBadge,
                        'route'           => route('student.submissions.show', $assignment->id),
                    ]);
                }
            } else { // 'quiz'
                $quizId = $assignment->quiz->id ?? null;
                $userAttempts = $quizId ? $quizAttempts->where('quiz_id', $quizId) : collect();
                $bestScore = $userAttempts->whereIn('status', ['submitted', 'expired'])->max('score');
                $isCompleted = !is_null($bestScore) && $bestScore >= $assignment->passing_score;

                if (!$isCompleted) {
                    $hasAttempted = $userAttempts->isNotEmpty();

                    // Ketentuan 2: Status kuis belum lulus KKM
                    if ($hasAttempted) {
                        $statusLabel = 'Belum Memenuhi KKM (Nilai: ' . number_format($bestScore ?? 0, 0) . ')';
                        $statusBadge = 'failed';
                    } else {
                        $statusLabel = 'Belum Dikerjakan';
                        $statusBadge = 'pending';
                    }

                    $pendingTasks->push([
                        'id'              => $assignment->id,
                        'type'            => 'quiz',
                        'title'           => $assignment->title,
                        'course_title'    => $assignment->course->title,
                        'meeting_number'  => $assignment->meeting->meeting_number ?? '-',
                        'available_until' => $assignment->available_until,
                        'passing_score'   => $assignment->passing_score,
                        'status_label'    => $statusLabel,
                        'status_badge'    => $statusBadge,
                        'route'           => route('student.quizzes.show', $assignment->id),
                    ]);
                }
            }
        }

        // Urutkan To-Do: Deadline terdekat di atas
        $pendingTasks = $pendingTasks->sortBy(function ($item) {
            return $item['available_until'] ? \Carbon\Carbon::parse($item['available_until'])->timestamp : PHP_INT_MAX;
        })->take(6);

        // 5. Query Recent Graded Feedback
        $recentFeedbacks = AssignmentSubmission::with(['assignment.course'])
            ->whereIn('enrollment_id', $enrollmentIds)
            ->where('status', 'graded')
            ->orderBy('graded_at', 'desc')
            ->take(4)
            ->get();

        // 6. Ketentuan 3: Hitung Rata-Rata Progres Pelatihan (Active & Complete)
        $avgProgress = $enrollments->avg(function ($en) {
            return $en->progress->progress_percentage ?? 0;
        });

        // Mengirimkan $enrollments (Aktif + Completed) sebagai variabel $activeEnrollments ke view
        $activeEnrollments = $enrollments;

        return view('student.dashboard', compact(
            'activeEnrollments',
            'pendingTasks',
            'recentFeedbacks',
            'avgProgress'
        ));
    }
}