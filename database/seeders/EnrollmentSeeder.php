<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::first();
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            Enrollment::create([
                'course_id' => $course->id,
                'user_id' => $student->id,
                'enrolled_at' => now(),
                'status' => 'active',
            ]);
        }
    }
}