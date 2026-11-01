<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Material;
use Symfony\Component\HttpFoundation\Response;

class EnsureEnrollmentIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== 'student') {
            return $next($request);
        }

        $courseId = null;

        if ($request->route('course')) {
            $course = $request->route('course');
            $courseId = $course instanceof Course ? $course->id : $course;
        } elseif ($request->route('assignment')) {
            $assignment = $request->route('assignment');
            $courseId = $assignment instanceof Assignment ? $assignment->course_id : Assignment::where('id', $assignment)->value('course_id');
        } elseif ($request->route('material')) {
            $material = $request->route('material');
            if ($material instanceof Material) {
                $courseId = $material->meeting->course_id ?? null;
            } else {
                $mat = Material::with('meeting')->find($material);
                $courseId = $mat->meeting->course_id ?? null;
            }
        }

        if ($courseId) {
        // Gunakan latest() agar mengambil pendaftaran paling baru jika ada multiple records
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->latest()
            ->first();

        // Pengecekan Tunggal: Terdaftar DAN Akses Masih Valid
        if (!$enrollment || !$enrollment->isAccessValid()) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Masa akses pendaftaran Anda untuk pelatihan ini telah berakhir atau tidak berlaku.');
            }
        }

        return $next($request);
    }
}