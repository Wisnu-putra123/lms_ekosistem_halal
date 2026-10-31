<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Helpers\CourseProgressHelper;
use App\Models\Assignment;
use App\Models\Meeting;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function create(Meeting $meeting)
    {
        return view('teacher.quizzes.create', compact('meeting'));
    }

    public function store(Request $request, Meeting $meeting)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_score' => 'required|numeric|min:1|max:1000',
            'passing_score' => 'required|numeric|min:0|lte:max_score',
            'max_attempts' => 'nullable|integer|min:1',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
            'status' => 'required|in:draft,published,closed',
            'duration_minutes' => 'nullable|integer|min:1',
            'question_count' => 'required|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $assignment = Assignment::create([
                'course_id' => $meeting->course_id,
                'meeting_id' => $meeting->id,
                'created_by' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'type' => 'quiz',
                'submission_method' => null,
                'max_attempts' => $request->max_attempts,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
                'max_score' => $request->max_score,
                'passing_score' => $request->passing_score,
                'status' => $request->status,
            ]);

            Quiz::create([
                'assignment_id' => $assignment->id,
                'duration_minutes' => $request->duration_minutes,
                'question_count' => $request->question_count,
                'shuffle_questions' => $request->boolean('shuffle_questions', false),
                'shuffle_answers' => $request->boolean('shuffle_answers', false),
            ]);

            CourseProgressHelper::recalculateAllActiveStudentsProgress($meeting->course_id);

            DB::commit();

            return redirect()->route('teacher.quizzes.show', $assignment->id)
                ->with('success', 'Quiz berhasil dibuat! Silakan tambahkan bank soal.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat quiz: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan detail Quiz beserta daftar attempt siswa (Dukungan Filter & Sort)
     */
    public function show(Request $request, Assignment $assignment)
    {
        $quiz = Quiz::with(['questions.options', 'questions.media'])
            ->where('assignment_id', $assignment->id)
            ->firstOrFail();

        // Query attempts terhubung via enrollment.user
        $attemptsQuery = QuizAttempt::with(['enrollment.user'])
            ->where('quiz_id', $quiz->id);

        // 1. Filter Nama / Email Siswa
        if ($request->filled('search')) {
            $search = $request->search;
            $attemptsQuery->whereHas('enrollment.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 2. Filter Status Enrollment Siswa (active / completed)
        if ($request->filled('enrollment_status')) {
            $enrollmentStatus = $request->enrollment_status;
            $attemptsQuery->whereHas('enrollment', function ($q) use ($enrollmentStatus) {
                $q->where('status', $enrollmentStatus);
            });
        }

        // 3. Filter Status Attempt (in_progress / submitted / expired)
        if ($request->filled('status')) {
            $attemptsQuery->where('status', $request->status);
        }

        // 4. Sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $attemptsQuery->orderBy('started_at', 'asc');
                break;
            case 'score_high':
                $attemptsQuery->orderBy('score', 'desc');
                break;
            case 'score_low':
                $attemptsQuery->orderBy('score', 'asc');
                break;
            case 'latest':
            default:
                $attemptsQuery->orderBy('started_at', 'desc');
                break;
        }

        // --- PENYESUAIAN PAGINASI (10 Data per Halaman) ---
        $quizAttempts = $attemptsQuery->paginate(10)->appends($request->query());

        $pointPerQuestion = $quiz->question_count > 0 
            ? round($assignment->max_score / $quiz->question_count, 2) 
            : 0;

        return view('teacher.quizzes.show', compact('assignment', 'quiz', 'quizAttempts', 'pointPerQuestion'));
    }

    public function edit(Assignment $assignment)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        $assignment->load('quiz');
        return view('teacher.quizzes.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_score' => 'required|numeric|min:1|max:1000',
            'passing_score' => 'required|numeric|min:0|lte:max_score',
            'max_attempts' => 'nullable|integer|min:1',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after_or_equal:available_from',
            'status' => 'required|in:draft,published,closed',
            'duration_minutes' => 'nullable|integer|min:1',
            'question_count' => 'required|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $assignment->update([
                'title' => $request->title,
                'description' => $request->description,
                'max_attempts' => $request->max_attempts,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
                'max_score' => $request->max_score,
                'passing_score' => $request->passing_score,
                'status' => $request->status,
            ]);

            if ($assignment->quiz) {
                $assignment->quiz->update([
                    'duration_minutes' => $request->duration_minutes,
                    'question_count' => $request->question_count,
                    'shuffle_questions' => $request->boolean('shuffle_questions', false),
                    'shuffle_answers' => $request->boolean('shuffle_answers', false),
                ]);
            }

            CourseProgressHelper::recalculateAllActiveStudentsProgress($assignment->course_id);

            DB::commit();

            return redirect()->route('teacher.quizzes.show', $assignment->id)
                ->with('success', 'Konfigurasi Quiz berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui Quiz: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Assignment $assignment)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $courseId = $assignment->course_id;
            
            if ($assignment->quiz) {
                $assignment->quiz->delete();
            }
            $assignment->delete();

            CourseProgressHelper::recalculateAllActiveStudentsProgress($courseId);

            DB::commit();

            return redirect()->route('teacher.courses.show', $courseId)
                ->with('success', 'Quiz berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus Quiz.');
        }
    }

    /**
     * Menampilkan detail pengerjaan (attempt) kuis siswa
     */
    public function showAttempt(QuizAttempt $attempt)
    {
        $attempt->load([
            'enrollment.user',
            'quiz.assignment.meeting',
            'attemptQuestions.question.options',
            'attemptQuestions.selectedOption'
        ]);

        $quiz = $attempt->quiz;
        $assignment = $quiz->assignment;

        return view('teacher.quizzes.attempts.show', compact('attempt', 'quiz', 'assignment'));
    }

    public function destroyAttempt(QuizAttempt $attempt)
    {
        $quizId = $attempt->quiz_id;
        $assignment = $attempt->quiz->assignment;

        $attempt->attemptQuestions()->delete();
        $attempt->delete();

        return redirect()->route('teacher.quizzes.show', $assignment->id)
            ->with('success', 'Attempt pengerjaan kuis siswa berhasil dihapus.');
    }
}