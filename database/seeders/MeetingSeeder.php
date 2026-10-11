<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $courseId = DB::table('courses')
            ->where('code', 'HALAL-101')
            ->value('id');

        if (!$courseId) {
            throw new \RuntimeException(
                'Course HALAL-101 tidak ditemukan. Jalankan UserSeeder dan CourseSeeder terlebih dahulu.'
            );
        }

        $meetings = [
            [
                'meeting_number' => 1,
                'title' => 'Pertemuan 1 - Pengenalan Sertifikasi Halal',
                'description' => 'Pengenalan konsep, tujuan, dan alur sertifikasi halal.',
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'meeting_number' => 2,
                'title' => 'Pertemuan 2 - Penerapan Jaminan Produk Halal',
                'description' => 'Pembahasan penerapan jaminan produk halal dalam proses produksi.',
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'meeting_number' => 3,
                'title' => 'Pertemuan 3 - Bahan dan Proses Produk Halal',
                'description' => 'Pembahasan pemeriksaan bahan dan proses produksi yang relevan dengan produk halal.',
                'available_from' => null,
                'available_until' => null,
            ],
            [
                'meeting_number' => 4,
                'title' => 'Pertemuan 4 - Evaluasi Pemahaman',
                'description' => 'Pertemuan untuk evaluasi dan penguatan pemahaman materi.',
                'available_from' => null,
                'available_until' => null,
            ],
        ];

        foreach ($meetings as $meeting) {
            DB::table('meetings')->updateOrInsert(
                [
                    'course_id' => $courseId,
                    'meeting_number' => $meeting['meeting_number'],
                ],
                [
                    'title' => $meeting['title'],
                    'description' => $meeting['description'],
                    'available_from' => $meeting['available_from'],
                    'available_until' => $meeting['available_until'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
