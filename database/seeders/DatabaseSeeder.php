<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin1@email.com'],
            [
                'name' => 'Administrator 1',
                'password' => bcrypt('12345678'),
                'role' => 'admin',
            ]
        );
        User::updateOrCreate(
            ['email' => 'admin2@email.com'],
            [
                'name' => 'Administrator 2',
                'password' => bcrypt('12345678'),
                'role' => 'admin',
            ]
        );
        User::updateOrCreate(
            ['email' => 'admin3@email.com'],
            [
                'name' => 'Administrator 3',
                'password' => bcrypt('12345678'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'student1@email.com'],
            [
                'name' => 'Siswa Example 1',
                'password' => bcrypt('12345678'),
                'role' => 'student',
            ]
        );

        User::updateOrCreate(
            ['email' => 'student2@email.com'],
            [
                'name' => 'Siswa2 Example 2',
                'password' => bcrypt('12345678'),
                'role' => 'student',
            ]
        );

        User::updateOrCreate(
            ['email' => 'student3@email.com'],
            [
                'name' => 'Siswa2 Example 3',
                'password' => bcrypt('12345678'),
                'role' => 'student',
            ]
        );

        User::updateOrCreate(
            ['email' => 'teacher1@email.com'],
            [
                'name' => 'Guru Example 1',
                'password' => bcrypt('12345678'),
                'role' => 'teacher',
            ]
        );
        User::updateOrCreate(
            ['email' => 'teacher2@email.com'],
            [
                'name' => 'Guru Example 2',
                'password' => bcrypt('12345678'),
                'role' => 'teacher',
            ]
        );
        User::updateOrCreate(
            ['email' => 'teacher3@email.com'],
            [
                'name' => 'Guru Example 3',
                'password' => bcrypt('12345678'),
                'role' => 'teacher',
            ]
        );

        $this->call([
            UserSeeder::class,
            CourseSeeder::class,
            EnrollmentSeeder::class,
            MeetingSeeder::class,
            // AssignmentSeeder::class,
            // QuizSeeder::class,
            // SubmissionSeeder::class,
            // QuizAttemptSeeder::class,
            ]);

    }
}
