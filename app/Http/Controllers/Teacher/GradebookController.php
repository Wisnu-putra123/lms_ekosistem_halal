<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;

class GradebookController extends Controller
{
    /**
     * Tampilkan Matriks Rekap Nilai Seluruh Siswa
     */
    public function show(Course $course)
    {
        // 1. Ambil semua item Tugas & Kuis resmi yang terbit
        $assignments = Assignment::where('course_id', $course->id)
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('meeting_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 2. Ambil seluruh siswa aktif terdaftar
        $enrollments = Enrollment::with(['user', 'progress'])
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->get();

        $studentIds = $enrollments->pluck('user_id');

        // 3. Query Nilai Tugas (Submissions)
        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignments->where('type', 'submission')->pluck('id'))
            ->whereIn('user_id', $studentIds)
            ->get()
            ->groupBy('user_id');

        // 4. Query Nilai Kuis (Quiz Attempts)
        $quizAttempts = QuizAttempt::join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->whereIn('quizzes.assignment_id', $assignments->where('type', 'quiz')->pluck('id'))
            ->whereIn('quiz_attempts.user_id', $studentIds)
            ->whereIn('quiz_attempts.status', ['submitted', 'expired'])
            ->select('quiz_attempts.*', 'quizzes.assignment_id')
            ->get()
            ->groupBy('user_id');

        // 5. Olah Matriks Rekap Nilai
        $gradebookData = $enrollments->map(function ($enrollment) use ($assignments, $submissions, $quizAttempts) {
            $userId = $enrollment->user_id;
            $userSubmissions = $submissions->get($userId, collect());
            $userQuizAttempts = $quizAttempts->get($userId, collect());

            $scores = [];
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

                $scores[$assignment->id] = [
                    'score' => $bestScore,
                    'is_passed' => $isPassed,
                    'attempt_count' => $attemptCount,
                ];
            }

            $averageScore = $totalEvaluated > 0 ? round($sumScores / $totalEvaluated, 2) : 0;

            return [
                'user' => $enrollment->user,
                'progress' => $enrollment->progress->progress_percentage ?? 0,
                'scores' => $scores,
                'total_passed' => $totalPassed,
                'average_score' => $averageScore,
            ];
        });

        return view('teacher.gradebook.show', compact('course', 'assignments', 'gradebookData'));
    }

    /**
     * Halaman Khusus Detail Seluruh Attempt Siswa pada Satu Tugas/Kuis
     */
    public function studentDetail(Course $course, Assignment $assignment, User $user)
    {
        $attempts = collect();

        if ($assignment->type === 'submission') {
            $attempts = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->where('user_id', $user->id)
                ->orderBy('attempt_number', 'desc')
                ->get();
        } else {
            $quiz = $assignment->quiz;
            if ($quiz) {
                $attempts = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->orderBy('attempt_number', 'desc')
                    ->get();
            }
        }

        return view('teacher.gradebook.student_detail', compact('course', 'assignment', 'user', 'attempts'));
    }

    /**
     * Ekspor Rekap Nilai ke File CSV
     */
    public function exportCsv(Course $course)
    {
        $fileName = 'Rekap_Nilai_' . \Str::slug($course->title) . '_' . date('Ymd_His') . '.csv';

        $assignments = Assignment::where('course_id', $course->id)
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('meeting_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $enrollments = Enrollment::with(['user', 'progress'])
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->get();

        $studentIds = $enrollments->pluck('user_id');

        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignments->where('type', 'submission')->pluck('id'))
            ->whereIn('user_id', $studentIds)
            ->get()
            ->groupBy('user_id');

        $quizAttempts = QuizAttempt::join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->whereIn('quizzes.assignment_id', $assignments->where('type', 'quiz')->pluck('id'))
            ->whereIn('quiz_attempts.user_id', $studentIds)
            ->whereIn('quiz_attempts.status', ['submitted', 'expired'])
            ->select('quiz_attempts.*', 'quizzes.assignment_id')
            ->get()
            ->groupBy('user_id');

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($assignments, $enrollments, $submissions, $quizAttempts) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM UTF-8

            $headerRow = ['Nama Siswa', 'Email', 'Progress (%)'];
            foreach ($assignments as $assignment) {
                $headerRow[] = $assignment->title . ' (' . strtoupper($assignment->type) . ')';
            }
            $headerRow[] = 'Rata-Rata Nilai';
            fputcsv($file, $headerRow);

            foreach ($enrollments as $enrollment) {
                $userId = $enrollment->user_id;
                $userSubmissions = $submissions->get($userId, collect());
                $userQuizAttempts = $quizAttempts->get($userId, collect());

                $row = [
                    $enrollment->user->name,
                    $enrollment->user->email,
                    ($enrollment->progress->progress_percentage ?? 0) . '%'
                ];

                $sumScores = 0;
                $evaluatedCount = 0;

                foreach ($assignments as $assignment) {
                    if ($assignment->type === 'submission') {
                        $bestScore = $userSubmissions->where('assignment_id', $assignment->id)->where('status', 'graded')->max('score');
                    } else {
                        $bestScore = $userQuizAttempts->where('assignment_id', $assignment->id)->max('score');
                    }

                    if (!is_null($bestScore)) {
                        $row[] = $bestScore;
                        $sumScores += $bestScore;
                        $evaluatedCount++;
                    } else {
                        $row[] = '-';
                    }
                }

                $row[] = $evaluatedCount > 0 ? round($sumScores / $evaluatedCount, 2) : '0';
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}