<?php

namespace App\Helpers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseProgress;
use App\Models\Enrollment;

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
        // 1. Hitung total assignment resmi di course ini
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

        // 2. Hitung jumlah assignment di mana NILAI MAKSIMAL (MAX SCORE) siswa >= passing_score
        $passedAssignmentsCount = AssignmentSubmission::join('assignments', 'assignments.id', '=', 'assignment_submissions.assignment_id')
            ->where('assignments.course_id', $courseId)
            ->whereIn('assignments.status', ['published', 'closed'])
            ->where('assignment_submissions.user_id', $userId)
            ->where('assignment_submissions.status', 'graded')
            ->selectRaw('assignment_submissions.assignment_id, MAX(assignment_submissions.score) as max_score, assignments.passing_score')
            ->groupBy('assignment_submissions.assignment_id', 'assignments.passing_score')
            ->havingRaw('MAX(assignment_submissions.score) >= assignments.passing_score')
            ->get()
            ->count();

        // 3. Persentase progres
        $progressPercentage = min(100, round(($passedAssignmentsCount / $totalAssignments) * 100, 2));

        // 4. Simpan ke database
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