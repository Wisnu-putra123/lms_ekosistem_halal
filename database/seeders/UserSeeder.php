<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        foreach ([
            ['name' => 'Admin LMS', 'email' => 'admin@lms.test', 'role' => 'admin'],
            ['name' => 'Guru Demo', 'email' => 'teacher@lms.test', 'role' => 'teacher'],
            ['name' => 'Siswa Demo', 'email' => 'student@lms.test', 'role' => 'student'],
            ['name' => 'Siswa Kedua', 'email' => 'student2@lms.test', 'role' => 'student'],
        ] as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => $now,
                    'profile_photo' => null,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
