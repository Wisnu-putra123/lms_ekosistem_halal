<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class GradebookController extends Controller
{
    /**
     * Tampilkan Matriks Rekap Nilai Seluruh Siswa (Dukungan Filter, Search & Sorting Waktu Enrollment)
     */
    public function show(Request $request, Course $course)
    {
        // 1. Ambil semua item Tugas & Kuis resmi yang terbit
        $assignments = Assignment::where('course_id', $course->id)
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('meeting_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 2. Query Enrollment Base
        $enrollmentsQuery = Enrollment::with(['user', 'progress'])
            ->where('course_id', $course->id);

        // Filter status enrollment
        if ($request->filled('enrollment_status') && $request->enrollment_status !== 'all') {
            $enrollmentsQuery->where('status', $request->enrollment_status);
        } else {
            $enrollmentsQuery->whereIn('status', ['active', 'completed']);
        }

        // Filter pencarian nama / email / ID Enrollment
        if ($request->filled('search')) {
            $search = $request->search;
            $enrollmentsQuery->where(function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // 3. Paginasi Database (10 Data per Halaman)
        // Menyimpan parameter request agar link paginasi tidak mereset filter
        $enrollments = $enrollmentsQuery->paginate(10)->appends($request->query());
        $enrollmentIds = $enrollments->pluck('id');

        // 4. Query Nilai Tugas & Kuis (Hanya untuk ID enrollment pada halaman ini)
        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignments->where('type', 'submission')->pluck('id'))
            ->whereIn('enrollment_id', $enrollmentIds)
            ->get()
            ->groupBy('enrollment_id');

        $quizAttempts = QuizAttempt::join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->whereIn('quizzes.assignment_id', $assignments->where('type', 'quiz')->pluck('id'))
            ->whereIn('quiz_attempts.enrollment_id', $enrollmentIds)
            ->whereIn('quiz_attempts.status', ['submitted', 'expired'])
            ->select('quiz_attempts.*', 'quizzes.assignment_id')
            ->get()
            ->groupBy('enrollment_id');

        // 5. Olah Matriks Rekap Nilai untuk Halaman Ini
        $gradebookCollection = $enrollments->getCollection()->map(function ($enrollment) use ($assignments, $submissions, $quizAttempts) {
            $enrollmentId = $enrollment->id;
            $userSubmissions = $submissions->get($enrollmentId, collect());
            $userQuizAttempts = $quizAttempts->get($enrollmentId, collect());

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
                'enrollment' => $enrollment,
                'user' => $enrollment->user,
                'progress' => $enrollment->progress->progress_percentage ?? 0,
                'scores' => $scores,
                'total_passed' => $totalPassed,
                'average_score' => $averageScore,
            ];
        });

        // 6. Fitur Sorting pada Halaman
        $sort = $request->get('sort', 'enrolled_at');
        $direction = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        if ($sort === 'progress') {
            $sortedCollection = $direction === 'desc' 
                ? $gradebookCollection->sortByDesc('progress') 
                : $gradebookCollection->sortBy('progress');
        } elseif ($sort === 'average') {
            $sortedCollection = $direction === 'desc' 
                ? $gradebookCollection->sortByDesc('average_score') 
                : $gradebookCollection->sortBy('average_score');
        } elseif ($sort === 'name') {
            $sortedCollection = $direction === 'desc' 
                ? $gradebookCollection->sortByDesc(fn($item) => strtolower($item['user']->name)) 
                : $gradebookCollection->sortBy(fn($item) => strtolower($item['user']->name));
        } else {
            $sortedCollection = $direction === 'desc' 
                ? $gradebookCollection->sortByDesc(fn($item) => $item['enrollment']->enrolled_at) 
                : $gradebookCollection->sortBy(fn($item) => $item['enrollment']->enrolled_at);
        }

        // Set kembali collection yang diurutkan ke Paginator
        $gradebookData = $enrollments->setCollection($sortedCollection);

        return view('teacher.gradebook.show', compact('course', 'assignments', 'gradebookData'));
    }

    /**
     * Halaman Khusus Detail Seluruh Attempt Siswa pada Satu Enrollment & Item Tugas/Kuis
     */
    public function studentDetail(Course $course, Assignment $assignment, $enrollmentId)
    {
        // 1. Cari Enrollment berdasarkan ID Enrollment, jika tidak ketemu cari berdasarkan User ID pada course ini
        $enrollment = Enrollment::with('user')
            ->where('id', $enrollmentId)
            ->first();

        if (!$enrollment) {
            // Fallback jika yang terpassing adalah user_id
            $enrollment = Enrollment::with('user')
                ->where('course_id', $course->id)
                ->where('user_id', $enrollmentId)
                ->latest('enrolled_at')
                ->firstOrFail();
        }

        $user = $enrollment->user;
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

        return view('teacher.gradebook.student_detail', compact('course', 'assignment', 'enrollment', 'user', 'attempts'));
    }

    /**
     * Ekspor Rekap Nilai ke File CSV (Mencakup Status Enrollment)
     */
    public function exportCsv(Request $request, Course $course)
    {
        $fileName = 'Rekap_Nilai_' . \Str::slug($course->title) . '_' . date('Ymd_His') . '.csv';

        $assignments = Assignment::where('course_id', $course->id)
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('meeting_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $enrollmentsQuery = Enrollment::with(['user', 'progress'])
            ->where('course_id', $course->id);

        if ($request->filled('enrollment_status') && $request->enrollment_status !== 'all') {
            $enrollmentsQuery->where('status', $request->enrollment_status);
        } else {
            $enrollmentsQuery->whereIn('status', ['active', 'completed']);
        }

        $enrollments = $enrollmentsQuery->get();
        $enrollmentIds = $enrollments->pluck('id');

        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignments->where('type', 'submission')->pluck('id'))
            ->whereIn('enrollment_id', $enrollmentIds)
            ->get()
            ->groupBy('enrollment_id');

        $quizAttempts = QuizAttempt::join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->whereIn('quizzes.assignment_id', $assignments->where('type', 'quiz')->pluck('id'))
            ->whereIn('quiz_attempts.enrollment_id', $enrollmentIds)
            ->whereIn('quiz_attempts.status', ['submitted', 'expired'])
            ->select('quiz_attempts.*', 'quizzes.assignment_id')
            ->get()
            ->groupBy('enrollment_id');

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($assignments, $enrollments, $submissions, $quizAttempts) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            $headerRow = ['Enrollment ID', 'Nama Siswa', 'Email', 'Status Enrollment', 'Progress (%)'];
            foreach ($assignments as $assignment) {
                $headerRow[] = $assignment->title . ' (' . strtoupper($assignment->type) . ')';
            }
            $headerRow[] = 'Rata-Rata Nilai';
            fputcsv($file, $headerRow);

            foreach ($enrollments as $enrollment) {
                $enrollmentId = $enrollment->id;
                $userSubmissions = $submissions->get($enrollmentId, collect());
                $userQuizAttempts = $quizAttempts->get($enrollmentId, collect());

                $row = [
                    '#' . $enrollment->id,
                    $enrollment->user->name,
                    $enrollment->user->email,
                    strtoupper($enrollment->status),
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