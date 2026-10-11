<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $teacherId = DB::table('users')->where('email', 'teacher@lms.test')->value('id');
        $studentId = DB::table('users')->where('email', 'student@lms.test')->value('id');
        $courseId = DB::table('courses')->where('code', 'HALAL-101')->value('id');
        $meetingId = DB::table('meetings')->where('course_id', $courseId)->where('meeting_number', 2)->value('id');

        if (!$teacherId || !$studentId || !$courseId || !$meetingId) {
            throw new \RuntimeException('Jalankan UserSeeder dan CourseSeeder sebelum QuizSeeder.');
        }

        DB::table('assignments')->updateOrInsert(
            ['course_id' => $courseId, 'title' => 'Quiz 1 - Dasar Sertifikasi Halal'],
            [
                'meeting_id' => $meetingId,
                'created_by' => $teacherId,
                'description' => 'Quiz pilihan ganda tentang dasar sertifikasi halal.',
                'type' => 'quiz',
                'submission_method' => 'text',
                'max_attempts' => 3,
                'available_from' => null,
                'available_until' => null,
                'max_score' => 100,
                'passing_score' => 70,
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $assignmentId = DB::table('assignments')->where('course_id', $courseId)
            ->where('title', 'Quiz 1 - Dasar Sertifikasi Halal')->value('id');

        DB::table('quizzes')->updateOrInsert(
            ['assignment_id' => $assignmentId],
            [
                'duration_minutes' => 20,
                'question_count' => 3,
                'shuffle_questions' => true,
                'shuffle_answers' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $quizId = DB::table('quizzes')->where('assignment_id', $assignmentId)->value('id');

        $questions = [
            [
                'text' => 'Apa tujuan utama sertifikasi halal?',
                'points' => 34,
                'options' => [
                    ['text' => 'Memastikan produk memenuhi persyaratan halal', 'correct' => true],
                    ['text' => 'Menentukan harga jual produk', 'correct' => false],
                    ['text' => 'Menggantikan seluruh izin usaha', 'correct' => false],
                    ['text' => 'Menentukan desain kemasan', 'correct' => false],
                ],
            ],
            [
                'text' => 'Siapa yang bertanggung jawab menjaga konsistensi proses produk halal di perusahaan?',
                'points' => 33,
                'options' => [
                    ['text' => 'Pelaku usaha melalui penerapan sistem jaminan produk halal', 'correct' => true],
                    ['text' => 'Hanya konsumen', 'correct' => false],
                    ['text' => 'Hanya penjual eceran', 'correct' => false],
                    ['text' => 'Pihak pengiriman saja', 'correct' => false],
                ],
            ],
            [
                'text' => 'Apakah bahan dan proses produksi perlu diperhatikan dalam jaminan produk halal?',
                'points' => 33,
                'options' => [
                    ['text' => 'Ya, keduanya perlu diperhatikan', 'correct' => true],
                    ['text' => 'Tidak, hanya warna kemasan', 'correct' => false],
                    ['text' => 'Tidak, hanya harga bahan', 'correct' => false],
                    ['text' => 'Hanya saat produk sudah terjual', 'correct' => false],
                ],
            ],
            [
                'text' => 'Apakah semua soal bank soal harus ditampilkan pada setiap attempt?',
                'points' => 0,
                'options' => [
                    ['text' => 'Tidak, quiz dapat memilih sebagian soal secara acak', 'correct' => true],
                    ['text' => 'Ya, selalu wajib seluruhnya', 'correct' => false],
                ],
            ],
        ];

        $questionIds = [];
        foreach ($questions as $index => $item) {
            DB::table('questions')->updateOrInsert(
                ['question_text' => $item['text'], 'created_by' => $teacherId],
                [
                    'question_type' => 'multiple_choice',
                    'media_id' => null,
                    'points' => $item['points'],
                    'explanation' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $questionId = DB::table('questions')
                ->where('question_text', $item['text'])
                ->where('created_by', $teacherId)->value('id');
            $questionIds[] = $questionId;

            foreach ($item['options'] as $optionIndex => $option) {
                DB::table('question_options')->updateOrInsert(
                    ['question_id' => $questionId, 'option_text' => $option['text']],
                    [
                        'media_id' => null,
                        'is_correct' => $option['correct'],
                        'sort_order' => $optionIndex + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }

            DB::table('quiz_questions')->updateOrInsert(
                ['quiz_id' => $quizId, 'question_id' => $questionId],
                [
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // Simpan contoh histori attempt pertama dengan skor 67.
        DB::table('quiz_attempts')->updateOrInsert(
            ['quiz_id' => $quizId, 'user_id' => $studentId, 'attempt_number' => 1],
            [
                'started_at' => now()->subMinutes(20),
                'expires_at' => now()->subMinutes(1),
                'submitted_at' => now()->subMinutes(1),
                'score' => 67,
                'status' => 'submitted',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $attemptId = DB::table('quiz_attempts')->where('quiz_id', $quizId)
            ->where('user_id', $studentId)->where('attempt_number', 1)->value('id');

        foreach (array_slice($questionIds, 0, 3) as $index => $questionId) {
            $correctOptionId = DB::table('question_options')
                ->where('question_id', $questionId)->where('is_correct', true)->value('id');
            $points = DB::table('questions')->where('id', $questionId)->value('points');

            DB::table('quiz_attempt_questions')->updateOrInsert(
                ['attempt_id' => $attemptId, 'question_id' => $questionId],
                [
                    'question_order' => $index + 1,
                    'selected_option_id' => $index < 2 ? $correctOptionId : null,
                    'is_correct' => $index < 2 ? true : null,
                    'points_earned' => $index < 2 ? $points : null,
                    'answered_at' => $index < 2 ? now()->subMinutes(5) : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
