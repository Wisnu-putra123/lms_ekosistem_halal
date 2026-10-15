<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentGradebookController extends Controller
{
    /**
     * Tampilkan Matriks Rekap Nilai Student untuk Course tertentu.
     */
    public function show(Request $request, Course $course)
    {
        $userId = Auth::id();

        // 1. Ambil seluruh riwayat Enrollment milik student di course ini
        $allEnrollments = Enrollment::where('course_id', $course->id)
            ->where('user_id', $userId)
            ->orderBy('enrolled_at', 'desc')
            ->get();

        if ($allEnrollments->isEmpty()) {
            abort(403, 'Anda belum pernah terdaftar dalam course ini.');
        }

        // 2. Tentukan Enrollment yang dipilih (Default: Enrollment Aktif / Terbaru)
        $selectedEnrollmentId = $request->input('enrollment_id');
        
        if ($selectedEnrollmentId) {
            $currentEnrollment = $allEnrollments->firstWhere('id', $selectedEnrollmentId);
        }

        if (!$selectedEnrollmentId || !isset($currentEnrollment)) {
            $currentEnrollment = $allEnrollments->firstWhere('status', 'active') ?? $allEnrollments->first();
        }

        // 3. Ambil semua item Tugas & Kuis resmi yang dipublikasikan pada course ini
        $assignments = Assignment::where('course_id', $course->id)
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('meeting_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 4. Query Nilai Tugas berdasarkan enrollment_id yang dipilih
        $userSubmissions = AssignmentSubmission::whereIn('assignment_id', $assignments->where('type', 'submission')->pluck('id'))
            ->where('enrollment_id', $currentEnrollment->id)
            ->get();

        // 5. Query Nilai Kuis berdasarkan enrollment_id yang dipilih
        $userQuizAttempts = QuizAttempt::join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->whereIn('quizzes.assignment_id', $assignments->where('type', 'quiz')->pluck('id'))
            ->where('quiz_attempts.enrollment_id', $currentEnrollment->id)
            ->whereIn('quiz_attempts.status', ['submitted', 'expired'])
            ->select('quiz_attempts.*', 'quizzes.assignment_id')
            ->get();

        // 6. Olah Matriks Nilai untuk Student
        $scoresData = [];
        $totalPassed = 0;
        $totalEvaluated = 0;
        $sumScores = 0;

        foreach ($assignments as $assignment) {
            if ($assignment->type === 'submission') {
                $itemSubmissions = $userSubmissions->where('assignment_id', $assignment->id);
                $bestScore = $itemSubmissions->where('status', 'graded')->max('score');
                $attemptCount = $itemSubmissions->count();
            } else {
                $itemAttempts = $userQuizAttempts->where('assignment_id', $assignment->id);
                $bestScore = $itemAttempts->max('score');
                $attemptCount = $itemAttempts->count();
            }

            $isPassed = !is_null($bestScore) && $bestScore >= $assignment->passing_score;

            if ($isPassed) {
                $totalPassed++;
            }

            if (!is_null($bestScore)) {
                $sumScores += $bestScore;
                $totalEvaluated++;
            }

            $scoresData[$assignment->id] = [
                'score' => $bestScore,
                'is_passed' => $isPassed,
                'attempt_count' => $attemptCount,
            ];
        }

        $averageScore = $totalEvaluated > 0 ? round($sumScores / $totalEvaluated, 2) : 0;

        return view('student.gradebook.show', compact(
            'course',
            'assignments',
            'allEnrollments',
            'currentEnrollment',
            'scoresData',
            'totalPassed',
            'averageScore'
        ));
    }

    /**
     * Tampilkan Rincian Seluruh Attempt Student pada Item Tugas / Kuis Tertentu.
     */
    public function detail(Request $request, Course $course, Assignment $assignment)
    {
        $userId = Auth::id();

        // 1. Tentukan Enrollment ID yang digunakan
        $enrollmentId = $request->input('enrollment_id');
        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', $userId)
            ->when($enrollmentId, function ($q) use ($enrollmentId) {
                $q->where('id', $enrollmentId);
            })
            ->firstOrFail();

        $attempts = collect();

        // 2. Query Attempt berdasarkan Enrollment ID
        if ($assignment->type === 'submission') {
            $attempts = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->where('enrollment_id', $enrollment->id)
                ->orderBy('attempt_number', 'desc')
                ->get();
        } else {
            $quiz = $assignment->quiz;
            if ($quiz) {
                $attempts = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('enrollment_id', $enrollment->id)
                    ->orderBy('attempt_number', 'desc')
                    ->get();
            }
        }

        return view('student.gradebook.detail', compact('course', 'assignment', 'enrollment', 'attempts'));
    }
}