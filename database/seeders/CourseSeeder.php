<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $teacherId = DB::table('users')->where('email', 'teacher@email.com')->value('id');
        $studentIds = DB::table('users')->whereIn('email', ['student@lms.test', 'student2@lms.test'])->pluck('id');

        if (!$teacherId || $studentIds->count() < 2) {
            throw new \RuntimeException('Jalankan UserSeeder sebelum CourseSeeder.');
        }

        DB::table('courses')->updateOrInsert(
            ['code' => 'HALAL-101'],
            [
                'title' => 'Dasar Sertifikasi Halal',
                'description' => 'Course demo untuk pembelajaran dasar sertifikasi halal.',
                'thumbnail_media_id' => null,
                'enrollment_key' => 'HALAL-8A2B9C',
                'status' => 'published',
                'created_by' => $teacherId,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $courseId = DB::table('courses')->where('code', 'HALAL-101')->value('id');

        DB::table('course_instructors')->updateOrInsert(
            ['course_id' => $courseId, 'user_id' => $teacherId],
            ['created_at' => $now, 'updated_at' => $now]
        );

        foreach ($studentIds as $studentId) {
            DB::table('enrollments')->updateOrInsert(
                ['course_id' => $courseId, 'user_id' => $studentId],
                [
                    'enrolled_at' => $now,
                    'status' => 'active',
                    'completed_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $enrollmentId = DB::table('enrollments')
                ->where('course_id', $courseId)->where('user_id', $studentId)->value('id');

            DB::table('course_progress')->updateOrInsert(
                ['enrollment_id' => $enrollmentId],
                [
                    'progress_percentage' => 0,
                    'last_activity_at' => null,
                    'completed_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        foreach ([
            [1, 'Pertemuan 1 - Pengenalan Sertifikasi Halal', 'Pengenalan konsep dan alur sertifikasi halal.'],
            [2, 'Pertemuan 2 - Penerapan Jaminan Produk Halal', 'Materi tentang penerapan jaminan produk halal.'],
        ] as [$number, $title, $description]) {
            DB::table('meetings')->updateOrInsert(
                ['course_id' => $courseId, 'meeting_number' => $number],
                [
                    'title' => $title,
                    'description' => $description,
                    'available_from' => null,
                    'available_until' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
