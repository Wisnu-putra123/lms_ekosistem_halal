<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first() ?? User::first();

        if (!$teacher) {
            return;
        }

        $courses = [
            [
                'created_by' => $teacher->id,
                'title' => 'Pelatihan Sistem Jaminan Produk Halal (SJPH)',
                'code' => 'SJH-2026',
                'category' => 'penyelia_halal',
                'description' => 'Panduan komprehensif mengenai tata cara penyusunan, implementasi, dan pengawasan Sistem Jaminan Produk Halal untuk Penyelia Halal.',
                'enrollment_key' => 'PENYELIA123',
                'status' => 'published',
            ],
            [
                'created_by' => $teacher->id,
                'title' => 'Sertifikasi Juru Sembelih Halal (Juleha) Tingkat Dasar',
                'code' => 'JLH-2026',
                'category' => 'juleha',
                'description' => 'Bimbingan teknis tata cara penyembelihan hewan sesuai syariat Islam dan standar kompetensi kerja nasional.',
                'enrollment_key' => 'JULEHA123',
                'status' => 'published',
            ],
            [
                'created_by' => $teacher->id,
                'title' => 'Teknik Audit & Pemeriksaan Produk Halal',
                'code' => 'AUD-2026',
                'category' => 'auditor_halal',
                'description' => 'Pelatihan intensif pemeriksaan bahan, proses verifikasi kecukupan dokumen, dan audit lapangan bagi calon Auditor Halal LPH.',
                'enrollment_key' => 'AUDITOR123',
                'status' => 'published',
            ],
        ];

        foreach ($courses as $courseData) {
            Course::updateOrCreate(
                ['code' => $courseData['code']],
                $courseData
            );
        }
    }
}