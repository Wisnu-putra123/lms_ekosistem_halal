<?php

namespace App\Helpers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseProgress;
use App\Models\Enrollment;

class CourseProgressHelper
{
    /**
     * Hitung & perbarui persentase progress untuk SATU siswa di suatu course.
     */
    public static function updateStudentProgress(int $courseId, int $userId): void
    {
        // 1. Cari data enrollment siswa
        $enrollment = Enrollment::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return;
        }

        self::calculateAndSaveProgress($enrollment, $courseId, $userId);
    }

    /**
     * Hitung & perbarui persentase progress untuk SEMUA siswa aktif di suatu course.
     * Dipanggil saat Teacher menambah, mengubah status, atau menghapus assignment.
     */
    public static function recalculateAllActiveStudentsProgress(int $courseId): void
    {
        // Ambil seluruh siswa aktif di kelas ini
        $activeEnrollments = Enrollment::where('course_id', $courseId)
            ->where('status', 'active')
            ->get();

        foreach ($activeEnrollments as $enrollment) {
            self::calculateAndSaveProgress($enrollment, $courseId, $enrollment->user_id);
        }
    }

    /**
     * Internal logic kalkulasi persentase dan penulisan ke database.
     */
    private static function calculateAndSaveProgress(Enrollment $enrollment, int $courseId, int $userId): void
    {
        // 1. Hitung total assignment published di course ini
        $totalAssignments = Assignment::where('course_id', $courseId)
            ->where('status', 'published')
            ->count();

        // Jika tidak ada assignment published, reset progress ke 0%
        if ($totalAssignments === 0) {
            CourseProgress::updateOrCreate(
                ['enrollment_id' => $enrollment->id],
                [
                    'progress_percentage' => 0.00,
                    'last_activity_at' => now(),
                    'completed_at' => null,
                ]
            );
            return;
        }

        // 2. Hitung berapa assignment published yang sudah dikerjakan dan LULUS (score >= passing_score)
        $passedAssignmentsCount = AssignmentSubmission::whereHas('assignment', function ($query) use ($courseId) {
                $query->where('course_id', $courseId)->where('status', 'published');
            })
            ->where('user_id', $userId)
            ->where('status', 'graded')
            ->whereRaw('score >= (SELECT passing_score FROM assignments WHERE assignments.id = assignment_submissions.assignment_id)')
            ->count();

        // 3. Hitung persentase progres
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