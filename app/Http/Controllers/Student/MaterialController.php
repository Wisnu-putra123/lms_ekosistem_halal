<?php

namespace App\Http\Controllers\Student;

use App\Helpers\CourseProgressHelper;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\MaterialView;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    /**
     * Tampilkan detail materi pembelajaran untuk Student & catat progres belajar.
     */
    public function show(Material $material)
    {
        // 1. Load relasi meeting, course, dan media (lampiran berkas)
        $material->load(['meeting.course', 'media']);

        $course = $material->meeting?->course;
        if (!$course) {
            abort(404, 'Pelatihan untuk materi ini tidak ditemukan.');
        }

        // 2. Pastikan Student memiliki enrollment yang aksesnya masih berlaku
        $enrollment = $this->getValidEnrollment($course->id);

        // 3. Catat aktivitas membaca materi (sekali per pasangan enrollment_id & material_id)
        $materialView = MaterialView::firstOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'material_id'   => $material->id,
            ],
            [
                'viewed_at' => now(),
            ]
        );

        // 4. Perbarui progres belajar jika materi ini baru pertama kali dikunjungi
        if ($materialView->wasRecentlyCreated) {
            CourseProgressHelper::updateStudentProgress($course->id, $enrollment->id);
        } elseif ($enrollment->progress) {
            $enrollment->progress->update(['last_activity_at' => now()]);
        }

        return view('student.materials.show', compact('material', 'enrollment'));
    }

    /**
     * Helper privat untuk mengambil enrollment student yang aksesnya masih valid.
     */
    private function getValidEnrollment(int $courseId): Enrollment
    {
        $enrollment = Enrollment::with('progress')
            ->where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['active', 'completed'])
            ->latest()
            ->first();

        if (!$enrollment || !$enrollment->isAccessValid()) {
            abort(403, 'Anda tidak memiliki akses aktif ke materi pembelajaran ini.');
        }

        return $enrollment;
    }
}