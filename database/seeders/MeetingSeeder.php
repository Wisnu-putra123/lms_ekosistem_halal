<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Meeting;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::first();

        Meeting::create([
            'course_id' => $course->id,
            'title' => 'Pertemuan 1: Pengantar Sertifikasi Halal',
            'description' => 'Konsep dasar dan regulasi halal.',
            'meeting_number' => 1,
        ]);

        Meeting::create([
            'course_id' => $course->id,
            'title' => 'Pertemuan 2: Evaluasi Bahan & Proses',
            'description' => 'Pemeriksaan titik kritis bahan.',
            'meeting_number' => 2,
        ]);
    }
}