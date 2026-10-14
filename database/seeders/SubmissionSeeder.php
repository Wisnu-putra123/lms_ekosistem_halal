<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\User;
use App\Helpers\CourseProgressHelper;
use Illuminate\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        $assignment = Assignment::where('type', 'submission')->first();
        $enrollment = Enrollment::first();

        // Buat data pengumpulan tugas menggunakan enrollment_id
        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'enrollment_id' => $enrollment->id, // Terhubung via enrollment_id
            'attempt_number' => 1,
            'submission_text' => 'Berikut adalah dokumen resume regulasi halal saya.',
            'submitted_at' => now(),
            'score' => 85.00,
            'feedback' => 'Penjelasan sangat ringkas dan akurat.',
            'graded_by' => $teacher->id,
            'graded_at' => now(),
            'status' => 'graded',
        ]);

        // Update progres pengerjaan siswa
        CourseProgressHelper::updateStudentProgress($assignment->course_id, $enrollment->id);
    }
}