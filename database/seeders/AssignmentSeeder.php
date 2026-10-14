<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        $meeting1 = Meeting::where('meeting_number', 1)->first();
        $meeting2 = Meeting::where('meeting_number', 2)->first();

        // 1. Tugas Upload File
        Assignment::create([
            'course_id' => $meeting1->course_id,
            'meeting_id' => $meeting1->id,
            'created_by' => $teacher->id,
            'title' => 'Tugas 1: Resume Regulasi Halal',
            'description' => 'Buatlah dokumen ringkasan regulasi halal terbaru.',
            'type' => 'submission',
            'submission_method' => 'file',
            'max_attempts' => 2,
            'max_score' => 100.00,
            'passing_score' => 75.00,
            'status' => 'published',
        ]);

        // 2. Kuis Multiple Choice
        Assignment::create([
            'course_id' => $meeting2->course_id,
            'meeting_id' => $meeting2->id,
            'created_by' => $teacher->id,
            'title' => 'Kuis 1: Pemahaman Titik Kritis',
            'description' => 'Uji pemahaman materi kriteria bahan halal.',
            'type' => 'quiz',
            'max_attempts' => 3,
            'max_score' => 100.00,
            'passing_score' => 70.00,
            'status' => 'published',
        ]);
    }
}