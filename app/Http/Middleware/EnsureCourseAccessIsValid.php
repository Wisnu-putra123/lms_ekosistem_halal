<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Material;
use Symfony\Component\HttpFoundation\Response;

class EnsureCourseAccessIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya proses proteksi untuk user dengan role 'student'
        if (!$user || $user->role !== 'student') {
            return $next($request);
        }

        $course = null;

        // 1. Deteksi dan Ambil Model Course berdasarkan parameter Route
        if ($request->route('course')) {
            $courseParam = $request->route('course');
            $course = $courseParam instanceof Course 
                ? $courseParam 
                : Course::find($courseParam);

        } elseif ($request->route('assignment')) {
            $assignmentParam = $request->route('assignment');
            $assignment = $assignmentParam instanceof Assignment 
                ? $assignmentParam 
                : Assignment::find($assignmentParam);
            
            $course = $assignment ? $assignment->course : null;

        } elseif ($request->route('material')) {
            $materialParam = $request->route('material');
            $material = $materialParam instanceof Material 
                ? $materialParam 
                : Material::with('meeting.course')->find($materialParam);

            $course = $material && $material->meeting ? $material->meeting->course : null;
        }

        // 2. Jika konteks Course ditemukan pada Route
        if ($course) {
            // PROTEKSI 1: Cek Status Course (Arsip/Draft)
            if ($course->status !== 'published') {
                return redirect()->route('student.courses.index')
                    ->with('error', 'Pelatihan ini sedang diarsipkan atau tidak tersedia untuk diakses.');
            }

            // PROTEKSI 2: Cek Validitas Enrollment Siswa
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->latest()
                ->first();

            if (!$enrollment || !$enrollment->isAccessValid()) {
                return redirect()->route('student.courses.index')
                    ->with('error', 'Masa akses pendaftaran Anda untuk pelatihan ini telah berakhir atau tidak berlaku.');
            }
        }

        return $next($request);
    }
}