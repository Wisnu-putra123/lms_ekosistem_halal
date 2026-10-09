<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Helpers\CourseProgressHelper;
use App\Models\Assignment;
use App\Models\QuestionOption;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentQuizController extends Controller
{
    /**
     * Tampilkan halaman overview Quiz (instruksi, aturan, & riwayat attempt siswa).
     */
    public function show(Assignment $assignment)
    {
        if ($assignment->type !== 'quiz') {
            abort(404, 'Assignment ini bukan merupakan Quiz.');
        }

        $assignment->load(['quiz.questions', 'meeting', 'course']);
        $quiz = $assignment->quiz;

        // Ambil riwayat attempt milik siswa
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', Auth::id())
            ->orderBy('attempt_number', 'desc')
            ->get();

        // Cari skor tertinggi dari attempt yang sudah disubmit/dikalkulasi
        $bestScore = $attempts->whereIn('status', ['submitted', 'expired'])->max('score');

        // Cek apakah ada attempt yang masih berlangsung (in_progress)
        $activeAttempt = $attempts->firstWhere('status', 'in_progress');

        // Pengecekan sisa kuota attempt
        $hasMaxAttempts = !is_null($assignment->max_attempts);
        $attemptsCount = $attempts->count();
        $canStartNewAttempt = !$activeAttempt && (!$hasMaxAttempts || $attemptsCount < $assignment->max_attempts);

        return view('student.quizzes.show', compact(
            'assignment',
            'quiz',
            'attempts',
            'bestScore',
            'activeAttempt',
            'canStartNewAttempt'
        ));
    }

    /**
     * Inisiasi dan Mulai Attempt Quiz Baru.
     */
    public function start(Assignment $assignment)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        $quiz = $assignment->quiz;
        $userId = Auth::id();

        // 1. Cek jika ada attempt aktif yang belum selesai
        $existingActive = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $userId)
            ->where('status', 'in_progress')
            ->first();

        if ($existingActive) {
            // Jika waktu attempt aktif sudah habis, jalankan auto-submit
            if ($existingActive->expires_at && now()->greaterThan($existingActive->expires_at)) {
                $this->processGradeAttempt($existingActive, 'expired');
            } else {
                return redirect()->route('student.quizzes.attempt', [$assignment->id, $existingActive->id]);
            }
        }

        // 2. Cek Batas Maksimal Attempt
        $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $userId)
            ->count();

        if (!is_null($assignment->max_attempts) && $attemptCount >= $assignment->max_attempts) {
            return back()->with('error', 'Anda telah mencapai batas maksimal percobaan pengerjaan Quiz ini.');
        }

        DB::beginTransaction();
        try {
            // 3. Tentukan Waktu Mulai & Batas Expired
            $startedAt = now();
            $expiresAt = $quiz->duration_minutes ? $startedAt->copy()->addMinutes($quiz->duration_minutes) : null;

            // 4. Buat Record Attempt Baru
            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'user_id' => $userId,
                'attempt_number' => $attemptCount + 1,
                'started_at' => $startedAt,
                'expires_at' => $expiresAt,
                'status' => 'in_progress',
            ]);

            // 5. Generasi Soal untuk Attempt Ini (Randomisasi Soal)
            $query = $quiz->questions()->where('is_active', true);

            if ($quiz->shuffle_questions) {
                $query->inRandomOrder();
            } else {
                $query->orderBy('quiz_questions.sort_order', 'asc');
            }

            // Ambil sejumlah question_count yang ditetapkan
            $selectedQuestions = $query->take($quiz->question_count)->get();

            if ($selectedQuestions->isEmpty()) {
                DB::rollBack();
                return back()->with('error', 'Quiz ini belum memiliki bank soal yang aktif.');
            }

            // 6. Petakan Soal ke Lembar Pengerjaan Attempt Siswa
            foreach ($selectedQuestions as $index => $question) {
                QuizAttemptQuestion::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'question_order' => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()->route('student.quizzes.attempt', [$assignment->id, $attempt->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memulai Quiz: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Lembar Pengerjaan Quiz (Attempt View).
     */
    public function attempt(Assignment $assignment, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->quiz_id !== $assignment->quiz->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        // Cek jika waktu habis saat siswa memuat halaman
        if ($attempt->status === 'in_progress' && $attempt->expires_at && now()->greaterThan($attempt->expires_at)) {
            $this->processGradeAttempt($attempt, 'expired');
            return redirect()->route('student.quizzes.result', [$assignment->id, $attempt->id])
                ->with('error', 'Waktu pengerjaan Quiz telah habis!');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quizzes.result', [$assignment->id, $attempt->id]);
        }

        // Load Soal & Opsi Jawaban (dengan dukungan acak opsi jika diaktifkan)
        $attemptQuestions = QuizAttemptQuestion::where('attempt_id', $attempt->id)
            ->with(['question.media', 'question.options.media', 'selectedOption'])
            ->orderBy('question_order', 'asc')
            ->get();

        $quiz = $assignment->quiz;

        // Acak opsi pilihan jawaban jika shuffle_answers aktif
        if ($quiz->shuffle_answers) {
            foreach ($attemptQuestions as $aq) {
                if ($aq->question) {
                    $aq->question->setRelation('options', $aq->question->options->shuffle());
                }
            }
        }

        return view('student.quizzes.attempt', compact('assignment', 'quiz', 'attempt', 'attemptQuestions'));
    }

    /**
     * Simpan Jawaban Pilihan Siswa secara Pasif / Ajax (Autosave).
     */
    public function saveAnswer(Request $request, Assignment $assignment, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->status !== 'in_progress') {
            return response()->json(['message' => 'Attempt tidak valid atau sudah selesai.'], 403);
        }

        // Cek jika expired
        if ($attempt->expires_at && now()->greaterThan($attempt->expires_at)) {
            $this->processGradeAttempt($attempt, 'expired');
            return response()->json(['message' => 'Waktu pengerjaan telah habis.', 'expired' => true], 400);
        }

        $request->validate([
            'attempt_question_id' => 'required|exists:quiz_attempt_questions,id',
            'selected_option_id' => 'nullable|exists:question_options,id',
        ]);

        $attemptQuestion = QuizAttemptQuestion::where('id', $request->attempt_question_id)
            ->where('attempt_id', $attempt->id)
            ->firstOrFail();

        $attemptQuestion->update([
            'selected_option_id' => $request->selected_option_id,
            'answered_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Jawaban berhasil disimpan.']);
    }

    /**
     * Submit Akhir Attempt Quiz & Hitung Nilai Otomatis.
     */
    public function submit(Request $request, Assignment $assignment, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->quiz_id !== $assignment->quiz->id) {
            abort(403);
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quizzes.result', [$assignment->id, $attempt->id]);
        }

        // Simpan jawaban terakhir jika dikirimkan via form submit utama
        if ($request->has('answers') && is_array($request->answers)) {
            foreach ($request->answers as $aqId => $optionId) {
                QuizAttemptQuestion::where('id', $aqId)
                    ->where('attempt_id', $attempt->id)
                    ->update([
                        'selected_option_id' => $optionId,
                        'answered_at' => now(),
                    ]);
            }
        }

        $status = ($attempt->expires_at && now()->greaterThan($attempt->expires_at)) ? 'expired' : 'submitted';

        $this->processGradeAttempt($attempt, $status);

        return redirect()->route('student.quizzes.result', [$assignment->id, $attempt->id])
            ->with('success', 'Quiz berhasil dikumpulkan dan dinilai!');
    }

    /**
     * Tampilkan Hasil Evaluasi Attempt & Pembahasan.
     */
    public function result(Assignment $assignment, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->quiz_id !== $assignment->quiz->id) {
            abort(403);
        }

        $attempt->load(['attemptQuestions.question.options', 'attemptQuestions.selectedOption']);
        $quiz = $assignment->quiz;

        return view('student.quizzes.result', compact('assignment', 'quiz', 'attempt'));
    }

    /**
     * Helper Private: Kalkulasi Penilaian Otomatis & Pembaruan Progress Belajar.
     */
    private function processGradeAttempt(QuizAttempt $attempt, string $status = 'submitted')
    {
        DB::beginTransaction();
        try {
            $attemptQuestions = QuizAttemptQuestion::where('attempt_id', $attempt->id)
                ->with(['question.options'])
                ->get();

            $totalQuestionsCount = $attemptQuestions->count();
            $correctCount = 0;

            $maxScore = $attempt->quiz->assignment->max_score;
            $pointPerQuestion = $totalQuestionsCount > 0 ? ($maxScore / $totalQuestionsCount) : 0;

            foreach ($attemptQuestions as $aq) {
                $isCorrect = false;
                $pointsEarned = 0;

                if ($aq->selected_option_id) {
                    $selectedOpt = QuestionOption::find($aq->selected_option_id);
                    if ($selectedOpt && $selectedOpt->is_correct) {
                        $isCorrect = true;
                        $pointsEarned = $pointPerQuestion;
                        $correctCount++;
                    }
                }

                $aq->update([
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                ]);
            }

            // Hitung nilai akhir berbasis skor maksimal assignment
            $finalScore = $totalQuestionsCount > 0 ? round(($correctCount / $totalQuestionsCount) * $maxScore, 2) : 0;

            $attempt->update([
                'submitted_at' => now(),
                'score' => $finalScore,
                'status' => $status,
            ]);

            // Update Progress Belajar Siswa jika LULUS KKM
            $courseId = $attempt->quiz->assignment->course_id;
            $userId = $attempt->user_id;

            CourseProgressHelper::updateStudentProgress($courseId, $userId);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}