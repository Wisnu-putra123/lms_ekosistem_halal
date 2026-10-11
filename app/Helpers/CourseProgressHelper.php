<?php

namespace App\Helpers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\QuizAttempt;

class CourseProgressHelper
{
    public static function updateStudentProgress(int $courseId, int $userId): void
    {
        $enrollment = Enrollment::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) return;

        self::calculateAndSaveProgress($enrollment, $courseId, $userId);
    }

    public static function recalculateAllActiveStudentsProgress(int $courseId): void
    {
        $activeEnrollments = Enrollment::where('course_id', $courseId)
            ->where('status', 'active')
            ->get();

        foreach ($activeEnrollments as $enrollment) {
            self::calculateAndSaveProgress($enrollment, $courseId, $enrollment->user_id);
        }
    }

    private static function calculateAndSaveProgress(Enrollment $enrollment, int $courseId, int $userId): void
    {
        // 1. Hitung total seluruh assignment resmi di course ini (baik tipe 'submission' maupun 'quiz')
        $totalAssignments = Assignment::where('course_id', $courseId)
            ->whereIn('status', ['published', 'closed'])
            ->count();

        if ($totalAssignments === 0) {
            CourseProgress::updateOrCreate(
                ['enrollment_id' => $enrollment->id],
                ['progress_percentage' => 0.00, 'last_activity_at' => now(), 'completed_at' => null]
            );
            return;
        }

        // 2. Hitung jumlah assignment tipe SUBMISSION yang lulus KKM
        $passedSubmissionsCount = AssignmentSubmission::join('assignments', 'assignments.id', '=', 'assignment_submissions.assignment_id')
            ->where('assignments.course_id', $courseId)
            ->where('assignments.type', 'submission')
            ->whereIn('assignments.status', ['published', 'closed'])
            ->where('assignment_submissions.user_id', $userId)
            ->where('assignment_submissions.status', 'graded')
            ->selectRaw('assignment_submissions.assignment_id')
            ->groupBy('assignment_submissions.assignment_id', 'assignments.passing_score')
            ->havingRaw('MAX(assignment_submissions.score) >= assignments.passing_score')
            ->get()
            ->count();

        // 3. Hitung jumlah assignment tipe QUIZ yang lulus KKM (mengambil skor tertinggi dari seluruh attempt)
        $passedQuizzesCount = QuizAttempt::join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->join('assignments', 'assignments.id', '=', 'quizzes.assignment_id')
            ->where('assignments.course_id', $courseId)
            ->where('assignments.type', 'quiz')
            ->whereIn('assignments.status', ['published', 'closed'])
            ->where('quiz_attempts.user_id', $userId)
            ->whereIn('quiz_attempts.status', ['submitted', 'expired'])
            ->selectRaw('quizzes.assignment_id')
            ->groupBy('quizzes.assignment_id', 'assignments.passing_score')
            ->havingRaw('MAX(quiz_attempts.score) >= assignments.passing_score')
            ->get()
            ->count();

        // Total item yang lulus KKM
        $totalPassedCount = $passedSubmissionsCount + $passedQuizzesCount;

        // 4. Persentase progres akhir
        $progressPercentage = min(100, round(($totalPassedCount / $totalAssignments) * 100, 2));

        // 5. Simpan ke database
        CourseProgress::updateOrCreate(
            ['enrollment_id' => $enrollment->id],
            [
                'progress_percentage' => $progressPercentage,
                'last_activity_at' => now(),
                'completed_at' => $progressPercentage >= 100 ? now() : null,
            ]
        );
    }
}