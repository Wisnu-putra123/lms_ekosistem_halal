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
     * Hitung ulang progres seluruh siswa yang masih aktif di kelas
     */
    public static function recalculateAllActiveStudentsProgress(int $courseId): void
    {
        // PERBAIKAN: Hanya ambil siswa dengan status 'active' saja
        $enrollments = Enrollment::where('course_id', $courseId)
            ->where('status', 'active')
            ->get();

        foreach ($enrollments as $enrollment) {
            self::calculateAndSaveProgress($enrollment, $courseId);
        }
    }

    private static function calculateAndSaveProgress(Enrollment $enrollment, int $courseId): void
    {
        // PROTEKSI: Jika enrollment sudah 'completed', jangan ubah progres atau datanya sama sekali
        if ($enrollment->status === 'completed') {
            return;
        }

        // 1. Hitung total assignment yang HANYA berstatus 'published'
        $totalAssignments = Assignment::where('course_id', $courseId)
            ->where('status', 'published')
            ->count();

        if ($totalAssignments === 0) {
            CourseProgress::updateOrCreate(
                ['enrollment_id' => $enrollment->id],
                ['progress_percentage' => 0.00, 'last_activity_at' => now(), 'completed_at' => null]
            );
            return;
        }

        // 2. Hitung SUBMISSION lulus KKM pada assignment yang HANYA berstatus 'published'
        $passedSubmissionsCount = AssignmentSubmission::join('assignments', 'assignments.id', '=', 'assignment_submissions.assignment_id')
            ->where('assignments.course_id', $courseId)
            ->where('assignments.type', 'submission')
            ->where('assignments.status', 'published')
            ->where('assignment_submissions.enrollment_id', $enrollment->id)
            ->where('assignment_submissions.status', 'graded')
            ->selectRaw('assignment_submissions.assignment_id')
            ->groupBy('assignment_submissions.assignment_id', 'assignments.passing_score')
            ->havingRaw('MAX(assignment_submissions.score) >= assignments.passing_score')
            ->get()
            ->count();

        // 3. Hitung QUIZ lulus KKM pada assignment yang HANYA berstatus 'published'
        $passedQuizzesCount = QuizAttempt::join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->join('assignments', 'assignments.id', '=', 'quizzes.assignment_id')
            ->where('assignments.course_id', $courseId)
            ->where('assignments.type', 'quiz')
            ->where('assignments.status', 'published')
            ->where('quiz_attempts.enrollment_id', $enrollment->id)
            ->whereIn('quiz_attempts.status', ['submitted', 'expired'])
            ->selectRaw('quizzes.assignment_id')
            ->groupBy('quizzes.assignment_id', 'assignments.passing_score')
            ->havingRaw('MAX(quiz_attempts.score) >= assignments.passing_score')
            ->get()
            ->count();

        $totalPassedCount = $passedSubmissionsCount + $passedQuizzesCount;

        // 4. Hitung persentase progres
        $progressPercentage = min(100, round(($totalPassedCount / $totalAssignments) * 100, 2));

        $isCompleted = $progressPercentage >= 100;
        $now = now();

        // 5. Simpan / perbarui progres ke database
        CourseProgress::updateOrCreate(
            ['enrollment_id' => $enrollment->id],
            [
                'progress_percentage' => $progressPercentage,
                'last_activity_at'    => $now,
                'completed_at'        => $isCompleted ? $now : null,
            ]
        );

        // 6. Apabila mencapai 100%, tandai completed dan terbitkan sertifikat
        if ($isCompleted) {
            $expiryDays = 30;
            
            $enrollment->update([
                'status'            => 'completed',
                'completed_at'      => $now,
                'access_expires_at' => $now->copy()->addDays($expiryDays),
            ]);

            CertificateHelper::issueCertificate($enrollment->id);
        }
    }
}