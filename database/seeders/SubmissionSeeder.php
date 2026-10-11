<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $teacherId = DB::table('users')->where('email', 'teacher@lms.test')->value('id');
        $studentId = DB::table('users')->where('email', 'student@lms.test')->value('id');
        $courseId = DB::table('courses')->where('code', 'HALAL-101')->value('id');
        $meetingId = DB::table('meetings')->where('course_id', $courseId)->where('meeting_number', 1)->value('id');

        if (!$teacherId || !$studentId || !$courseId || !$meetingId) {
            throw new \RuntimeException('Jalankan UserSeeder dan CourseSeeder sebelum SubmissionSeeder.');
        }

        DB::table('assignments')->updateOrInsert(
            ['course_id' => $courseId, 'title' => 'Tugas 1 - Ringkasan Materi Halal'],
            [
                'meeting_id' => $meetingId,
                'created_by' => $teacherId,
                'description' => 'Buat ringkasan mengenai prinsip dasar sertifikasi halal.',
                'type' => 'submission',
                'submission_method' => 'both',
                'max_attempts' => 2,
                'available_from' => null,
                'available_until' => null,
                'max_score' => 100,
                'passing_score' => 70,
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $assignmentId = DB::table('assignments')
            ->where('course_id', $courseId)
            ->where('title', 'Tugas 1 - Ringkasan Materi Halal')->value('id');

        DB::table('assignment_submissions')->updateOrInsert(
            ['assignment_id' => $assignmentId, 'user_id' => $studentId, 'attempt_number' => 1],
            [
                'submission_text' => 'Sertifikasi halal adalah proses untuk memastikan produk memenuhi persyaratan halal yang berlaku.',
                'submitted_at' => $now,
                'feedback' => 'Contoh feedback: jelaskan juga tahapan pemeriksaan produk.',
                'score' => 85,
                'graded_by' => $teacherId,
                'graded_at' => $now,
                'status' => 'graded',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
