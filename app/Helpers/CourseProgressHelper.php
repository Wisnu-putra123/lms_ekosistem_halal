<?php

namespace App\Helpers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseProgress;
use App\Models\Enrollment;

class CourseProgressHelper
{
    /**
     * Hitung & perbarui persentase progress siswa di kelas
     */
    public static function updateStudentProgress(int $courseId, int $userId): void
    {
        // 1. Cari data enrollment siswa
        $enrollment = Enrollment::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->first();

        if (!$enrollment) {
            return;
        }

        // 2. Hitung total assignment yang ada di course ini (published)
        $totalAssignments = Assignment::where('course_id', $courseId)
            ->where('status', 'published')
            ->count();

        if ($totalAssignments === 0) {
            return;
        }

        // 3. Hitung berapa assignment yang sudah dikerjakan dan LULUS (score >= passing_score)
        $passedAssignmentsCount = AssignmentSubmission::whereHas('assignment', function ($query) use ($courseId) {
                $query->where('course_id', $courseId)->where('status', 'published');
            })
            ->where('user_id', $userId)
            ->where('status', 'graded')
            ->whereRaw('score >= (SELECT passing_score FROM assignments WHERE assignments.id = assignment_submissions.assignment_id)')
            ->count();

        // 4. Hitung persentase kelulusan
        $progressPercentage = min(100, round(($passedAssignmentsCount / $totalAssignments) * 100, 2));

        // 5. Update atau buat record di tabel course_progress
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