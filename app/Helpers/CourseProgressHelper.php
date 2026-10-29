<?php

namespace App\Helpers;

use App\Helpers\CertificateHelper;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\QuizAttempt;

class CourseProgressHelper
{
    /**
     * Hitung ulang progres satu siswa berdasarkan enrollment_id
     */
    public static function updateStudentProgress(int $courseId, int $enrollmentId): void
    {
        $enrollment = Enrollment::find($enrollmentId);

        if (!$enrollment) return;

        self::calculateAndSaveProgress($enrollment, $courseId);
    }

    /**
     * Hitung ulang progres seluruh siswa di kelas (aktif & selesai)
     */
    public static function recalculateAllActiveStudentsProgress(int $courseId): void
    {
        // PERBAIKAN: Hitung juga siswa yang statusnya completed jika ada perubahan nilai dari pengajar
        $enrollments = Enrollment::where('course_id', $courseId)
            ->whereIn('status', ['active', 'completed'])
            ->get();

        foreach ($enrollments as $enrollment) {
            self::calculateAndSaveProgress($enrollment, $courseId);
        }
    }

    private static function calculateAndSaveProgress(Enrollment $enrollment, int $courseId): void
    {
        // 1. Hitung total seluruh assignment terbit di course ini
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

        // 2. Hitung jumlah item SUBMISSION yang lulus KKM
        $passedSubmissionsCount = AssignmentSubmission::join('assignments', 'assignments.id', '=', 'assignment_submissions.assignment_id')
            ->where('assignments.course_id', $courseId)
            ->where('assignments.type', 'submission')
            ->whereIn('assignments.status', ['published', 'closed'])
            ->where('assignment_submissions.enrollment_id', $enrollment->id)
            ->where('assignment_submissions.status', 'graded')
            ->selectRaw('assignment_submissions.assignment_id')
            ->groupBy('assignment_submissions.assignment_id', 'assignments.passing_score')
            ->havingRaw('MAX(assignment_submissions.score) >= assignments.passing_score')
            ->get()
            ->count();

        // 3. Hitung jumlah item QUIZ yang lulus KKM
        $passedQuizzesCount = QuizAttempt::join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->join('assignments', 'assignments.id', '=', 'quizzes.assignment_id')
            ->where('assignments.course_id', $courseId)
            ->where('assignments.type', 'quiz')
            ->whereIn('assignments.status', ['published', 'closed'])
            ->where('quiz_attempts.enrollment_id', $enrollment->id)
            ->whereIn('quiz_attempts.status', ['submitted', 'expired'])
            ->selectRaw('quizzes.assignment_id')
            ->groupBy('quizzes.assignment_id', 'assignments.passing_score')
            ->havingRaw('MAX(quiz_attempts.score) >= assignments.passing_score')
            ->get()
            ->count();

        // Total item yang lulus KKM
        $totalPassedCount = $passedSubmissionsCount + $passedQuizzesCount;

        // 4. Persentase progres
        $progressPercentage = min(100, round(($totalPassedCount / $totalAssignments) * 100, 2));

        $isCompleted = $progressPercentage >= 100;
        $now = now();

        // 5. Simpan / perbarui ke database `course_progress`
        CourseProgress::updateOrCreate(
            ['enrollment_id' => $enrollment->id],
            [
                'progress_percentage' => $progressPercentage,
                'last_activity_at'    => $now,
                'completed_at'        => $isCompleted ? $now : null,
            ]
        );

        // 6. Jika progres mencapai 100%, perbarui status enrollment dan otomatis terbitkan sertifikat
        if ($isCompleted) {
            if ($enrollment->status !== 'completed') {
                $expiryDays = 30; // Kedaluwarsa akses 30 hari dari tanggal lulus
                
                $enrollment->update([
                    'status'            => 'completed',
                    'completed_at'      => $now,
                    'access_expires_at' => $now->copy()->addDays($expiryDays),
                ]);
            }

            // Pemicu otomatisasi penerbitan sertifikat
            CertificateHelper::issueCertificate($enrollment->id);
        }
    }
}