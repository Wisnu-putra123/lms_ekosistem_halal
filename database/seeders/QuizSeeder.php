<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        $assignment = Assignment::where('type', 'quiz')->first();

        // 1. Buat Konfigurasi Quiz
        $quiz = Quiz::create([
            'assignment_id' => $assignment->id,
            'duration_minutes' => 15,
            'question_count' => 1,
            'shuffle_questions' => true,
            'shuffle_answers' => true,
        ]);

        // 2. Buat Soal
        $question = Question::create([
            'created_by' => $teacher->id,
            'question_type' => 'multiple_choice',
            'question_text' => 'Manakah di bawah ini yang tergolong bahan kritis halal?',
            'points' => 100.00,
            'is_active' => true,
        ]);

        // 3. Buat Pilihan Jawaban
        QuestionOption::create(['question_id' => $question->id, 'option_text' => 'Air Murni', 'is_correct' => false, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $question->id, 'option_text' => 'Flavor & Perisa Sintetis', 'is_correct' => true, 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $question->id, 'option_text' => 'Garam Dapur', 'is_correct' => false, 'sort_order' => 3]);

        // 4. Hubungkan Soal ke Quiz
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_id' => $question->id,
            'sort_order' => 1,
        ]);
    }
}