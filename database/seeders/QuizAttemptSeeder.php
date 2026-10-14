<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Helpers\CourseProgressHelper;
use Illuminate\Database\Seeder;

class QuizAttemptSeeder extends Seeder
{
    public function run(): void
    {
        $assignment = Assignment::where('type', 'quiz')->first();
        $quiz = $assignment->quiz;
        $enrollment = Enrollment::first();

        // Buat pengerjaan kuis menggunakan enrollment_id
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'enrollment_id' => $enrollment->id, // Terhubung via enrollment_id
            'attempt_number' => 1,
            'started_at' => now()->subMinutes(10),
            'expires_at' => now()->addMinutes(5),
            'submitted_at' => now(),
            'score' => 100.00,
            'status' => 'submitted',
        ]);

        // Update progres pengerjaan siswa
        CourseProgressHelper::updateStudentProgress($assignment->course_id, $enrollment->id);
    }
}