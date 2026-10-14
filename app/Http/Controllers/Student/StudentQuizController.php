<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptQuestion;
use App\Helpers\CourseProgressHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentQuizController extends Controller
{
    /**
     * Menampilkan detail kuis & riwayat pengerjaan (attempts) siswa.
     */
    public function show(Assignment $assignment)
    {
        $userId = Auth::id();

        // 1. Ambil Enrollment terbaru siswa pada kelas ini
        $enrollment = Enrollment::where('course_id', $assignment->course_id)
            ->where('user_id', $userId)
            ->latest('id')
            ->firstOrFail();

        $assignment->load(['meeting', 'quiz.questions.options']);
        $quiz = $assignment->quiz;

        // 2. Ambil seluruh riwayat attempt siswa berdasarkan enrollment_id
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('enrollment_id', $enrollment->id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        $attemptCount = $attempts->count();
        $bestScore = $attempts->whereIn('status', ['submitted', 'expired'])->max('score');
        
        // Cek apakah ada attempt yang sedang berlangsung/mengerjakan
        $activeAttempt = $attempts->where('status', 'in_progress')->first();

        // 3. Hitung apakah siswa diperbolehkan memulai attempt baru
        $hasMaxAttempts = !is_null($assignment->max_attempts);
        $canStartNewAttempt = $enrollment->isAccessValid() 
            && !$activeAttempt 
            && (!$hasMaxAttempts || ($attemptCount < $assignment->max_attempts));

        // 4. Pass semua variabel (termasuk $canStartNewAttempt) ke view
        return view('student.quizzes.show', compact(
            'assignment',
            'quiz',
            'enrollment',
            'attempts',
            'attemptCount',
            'bestScore',
            'activeAttempt',
            'canStartNewAttempt' // <-- Variabel ini wajib dipass
        ));
    }

    /**
     * Mulai pengerjaan Kuis baru / Lanjutkan pengerjaan.
     */
    public function start(Assignment $assignment)
    {
        $userId = Auth::id();

        $enrollment = Enrollment::where('course_id', $assignment->course_id)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if (!$enrollment || !$enrollment->isAccessValid()) {
            return back()->with('error', 'Akses Anda ke kelas ini telah selesai atau kedaluwarsa.');
        }

        $quiz = Quiz::with('questions.options')->where('assignment_id', $assignment->id)->firstOrFail();

        DB::beginTransaction();
        try {
            // 1. Cek apakah ada attempt yang sedang berjalan
            $attempt = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('enrollment_id', $enrollment->id)
                ->where('status', 'in_progress')
                ->first();

            // Jika belum ada, buat attempt baru
            if (!$attempt) {
                $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('enrollment_id', $enrollment->id)
                    ->count();

                if (!is_null($assignment->max_attempts) && $attemptCount >= $assignment->max_attempts) {
                    return back()->with('error', 'Anda telah mencapai batas maksimal percobaan kuis.');
                }

                $expiresAt = $quiz->duration_minutes ? now()->addMinutes($quiz->duration_minutes) : null;

                $attempt = QuizAttempt::create([
                    'quiz_id' => $quiz->id,
                    'enrollment_id' => $enrollment->id, // Menggunakan enrollment_id
                    'attempt_number' => $attemptCount + 1,
                    'started_at' => now(),
                    'expires_at' => $expiresAt,
                    'status' => 'in_progress',
                ]);

                // Acak / pilih soal
                $questions = $quiz->questions;
                if ($quiz->shuffle_questions) {
                    $questions = $questions->shuffle();
                }

                $selectedQuestions = $questions->take($quiz->question_count);

                foreach ($selectedQuestions as $index => $question) {
                    QuizAttemptQuestion::create([
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'question_order' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('student.quizzes.attempt', [$assignment->id, $attempt->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memulai kuis: ' . $e->getMessage());
        }
    }

/**
     * Lembar Soal Kuis (Attempt).
     */
    public function attempt(Assignment $assignment, QuizAttempt $attempt)
    {
        // Pastikan attempt milik enrollment user saat ini
        if ($attempt->enrollment->user_id !== Auth::id()) {
            abort(403);
        }

        // Cek Durasi Waktu Kuis
        if ($attempt->expires_at && now()->greaterThan($attempt->expires_at) && $attempt->status === 'in_progress') {
            $this->finalizeAttempt($attempt);
            return redirect()->route('student.quizzes.result', [$assignment->id, $attempt->id])
                ->with('warning', 'Waktu pengerjaan kuis telah habis.');
        }

        // 1. Load relasi attemptQuestions beserta detail soal & opsinya
        $attempt->load(['attemptQuestions.question.options', 'quiz']);

        // 2. Definisikan variabel $attemptQuestions agar dapat dipass ke Blade
        $attemptQuestions = $attempt->attemptQuestions;

        // 3. Pass $attemptQuestions ke view
        return view('student.quizzes.attempt', compact('assignment', 'attempt', 'attemptQuestions'));
    }

    /**
     * Simpan / Submit Jawaban Kuis.
     */
    public function submit(Request $request, Assignment $assignment, QuizAttempt $attempt)
    {
        if ($attempt->enrollment->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quizzes.result', [$assignment->id, $attempt->id]);
        }

        $answers = $request->input('answers', []);

        DB::beginTransaction();
        try {
            foreach ($attempt->attemptQuestions as $attemptQuestion) {
                $selectedOptionId = $answers[$attemptQuestion->id] ?? null;

                if ($selectedOptionId) {
                    $option = $attemptQuestion->question->options->where('id', $selectedOptionId)->first();
                    $isCorrect = $option ? $option->is_correct : false;

                    // Hitung poin per soal
                    $pointPerQuestion = $attempt->quiz->question_count > 0 
                        ? ($assignment->max_score / $attempt->quiz->question_count) 
                        : 0;

                    $pointsEarned = $isCorrect ? $pointPerQuestion : 0;

                    $attemptQuestion->update([
                        'selected_option_id' => $selectedOptionId,
                        'is_correct' => $isCorrect,
                        'points_earned' => $pointsEarned,
                        'answered_at' => now(),
                    ]);
                }
            }

            $this->finalizeAttempt($attempt);

            DB::commit();

            return redirect()->route('student.quizzes.result', [$assignment->id, $attempt->id])
                ->with('success', 'Kuis berhasil diselesaikan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan jawaban: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Hasil Kuis.
     */
    public function result(Assignment $assignment, QuizAttempt $attempt)
    {
        if ($attempt->enrollment->user_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load(['attemptQuestions.question.options', 'attemptQuestions.selectedOption']);

        return view('student.quizzes.result', compact('assignment', 'attempt'));
    }

    /**
     * Helper internal untuk mengkalkulasi total skor akhir attempt kuis.
     */
    private function finalizeAttempt(QuizAttempt $attempt)
    {
        $totalScore = $attempt->attemptQuestions()->sum('points_earned');

        $attempt->update([
            'score' => round($totalScore, 2),
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Hitung ulang progress siswa
        CourseProgressHelper::updateStudentProgress($attempt->quiz->assignment->course_id, $attempt->enrollment_id);
    }
}