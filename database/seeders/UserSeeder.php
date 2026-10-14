<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Teacher
        User::create([
            'name' => 'Dr. Ir. Ahmad Pengajar, M.T.',
            'email' => 'teacher@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // Students
        $students = [
            ['name' => 'Budi Santoso', 'email' => 'budi@gmail.com'],
            ['name' => 'Siti Rahmawati', 'email' => 'siti@gmail.com'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko@gmail.com'],
        ];

        foreach ($students as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);
        }
    }
}