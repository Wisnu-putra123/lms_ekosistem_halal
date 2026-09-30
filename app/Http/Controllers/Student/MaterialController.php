<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    /**
     * Tampilkan detail materi pembelajaran untuk Student.
     */
    public function show(Material $material)
    {
        // 1. Pastikan Student terdaftar di kursus terkait
        $this->authorizeEnrollment($material->meeting->course_id);

        // 2. Load relasi meeting, course, dan media (lampiran berkas)
        $material->load(['meeting.course', 'media']);

        return view('student.materials.show', compact('material'));
    }

    /**
     * Helper privat untuk mengecek status pendaftaran student.
     */
    private function authorizeEnrollment(int $courseId): void
    {
        $isEnrolled = Enrollment::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak memiliki akses ke materi pembelajaran ini.');
        }
    }
}