<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Meeting;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Tampilkan form pembuatan Quiz baru pada meeting tertentu.
     */
    public function create(Meeting $meeting)
    {
        return view('teacher.quizzes.create', compact('meeting'));
    }

    /**
     * Simpan data Quiz & Assignment baru.
     */
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
            // Field khusus Quiz
            'duration_minutes' => 'nullable|integer|min:1',
            'question_count' => 'required|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat Assignment bertipe 'quiz'
            $assignment = Assignment::create([
                'course_id' => $meeting->course_id,
                'meeting_id' => $meeting->id,
                'created_by' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'type' => 'quiz',
                // 'submission_method' => null, // Khusus submission, untuk quiz di-null-kan
                'max_attempts' => $request->max_attempts,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
                'max_score' => $request->max_score,
                'passing_score' => $request->passing_score,
                'status' => $request->status,
            ]);

            // 2. Buat konfigurasi Quiz terkait
            Quiz::create([
                'assignment_id' => $assignment->id,
                'duration_minutes' => $request->duration_minutes,
                'question_count' => $request->question_count,
                'shuffle_questions' => $request->boolean('shuffle_questions', false),
                'shuffle_answers' => $request->boolean('shuffle_answers', false),
            ]);

            DB::commit();

            return redirect()->route('teacher.quizzes.show', $assignment->id)
                ->with('success', 'Quiz berhasil dibuat! Silakan tambahkan bank soal.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat quiz: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan detail Quiz beserta daftar bank soalnya.
     */
    public function show(Assignment $assignment)
    {
        if ($assignment->type !== 'quiz') {
            abort(404, 'Assignment ini bukan merupakan Quiz.');
        }

        $assignment->load(['quiz.questions.options', 'quiz.questions.media', 'meeting', 'course']);
        $quiz = $assignment->quiz;

        // Hitung poin per soal secara otomatis
        $pointPerQuestion = ($quiz && $quiz->question_count > 0) 
            ? round($assignment->max_score / $quiz->question_count, 2) 
            : 0;

        return view('teacher.quizzes.show', compact('assignment', 'quiz', 'pointPerQuestion'));
    }

    /**
     * Tampilkan form edit konfigurasi Quiz.
     */
    public function edit(Assignment $assignment)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        $assignment->load('quiz');
        return view('teacher.quizzes.edit', compact('assignment'));
    }

    /**
     * Perbarui konfigurasi Quiz & Assignment.
     */
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
            // Field khusus Quiz
            'duration_minutes' => 'nullable|integer|min:1',
            'question_count' => 'required|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Update Assignment
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

            // Update Konfigurasi Quiz
            if ($assignment->quiz) {
                $assignment->quiz->update([
                    'duration_minutes' => $request->duration_minutes,
                    'question_count' => $request->question_count,
                    'shuffle_questions' => $request->boolean('shuffle_questions', false),
                    'shuffle_answers' => $request->boolean('shuffle_answers', false),
                ]);
            }

            DB::commit();

            return redirect()->route('teacher.quizzes.show', $assignment->id)
                ->with('success', 'Konfigurasi Quiz berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui Quiz: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus Quiz.
     */
    public function destroy(Assignment $assignment)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $courseId = $assignment->course_id;
            
            // Hapus Quiz & Assignment (Relasi DB cascading akan menangani sisanya)
            if ($assignment->quiz) {
                $assignment->quiz->delete();
            }
            $assignment->delete();

            DB::commit();

            return redirect()->route('teacher.courses.show', $courseId)
                ->with('success', 'Quiz berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus Quiz.');
        }
    }
}