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
            ['email' => 'admin@email.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('12345678'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'student@email.com'],
            [
                'name' => 'Siswa Example',
                'password' => bcrypt('12345678'),
                'role' => 'student',
            ]
        );

        User::updateOrCreate(
            ['email' => 'teacher@email.com'],
            [
                'name' => 'Guru Example',
                'password' => bcrypt('12345678'),
                'role' => 'teacher',
            ]
        );
        User::updateOrCreate(
            ['email' => 'teacher1@email.com'],
            [
                'name' => 'Guru Example',
                'password' => bcrypt('12345678'),
                'role' => 'teacher',
            ]
        );
        User::updateOrCreate(
            ['email' => 'teacher2@email.com'],
            [
                'name' => 'Guru Example',
                'password' => bcrypt('12345678'),
                'role' => 'teacher',
            ]
        );
    }
}
