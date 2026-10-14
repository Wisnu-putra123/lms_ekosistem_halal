<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();

        Course::create([
            'created_by' => $teacher->id,
            'title' => 'Pelatihan Sistem Jaminan Halal (SJH)',
            'code' => 'SJH-2026',
            'description' => 'Panduan lengkap tata cara implementasi dan sertifikasi halal.',
            'enrollment_key' => 'HALAL123',
            'status' => 'published',
        ]);
    }
}