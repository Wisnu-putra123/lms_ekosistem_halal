<?php

namespace App\Http\Middleware;

use App\Models\Course;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacherCourseAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $course = $this->resolveCourseFromRoute($request);

        if ($course) {
            $userId = Auth::id();
            $isCreator = $course->created_by === $userId;
            $isInstructor = $course->instructors()->where('user_id', $userId)->exists();

            if (!$isCreator && !$isInstructor) {
                abort(403, 'Anda tidak memiliki akses ke pelatihan ini.');
            }

            // Validasi parameter bersarang (Nested Parameter Integrity)
            if ($assignment = $request->route('assignment')) {
                if ((int) $assignment->course_id !== (int) $course->id) {
                    abort(404);
                }

                // Pastikan soal (question) benar-benar bagian dari kuis ini
                if ($question = $request->route('question')) {
                    $belongsToQuiz = $assignment->quiz?->questions()
                        ->where('questions.id', $question->id)
                        ->exists();

                    if (!$belongsToQuiz) {
                        abort(404);
                    }
                }
            }

            if ($enrollment = $request->route('enrollment')) {
                if ((int) $enrollment->course_id !== (int) $course->id) {
                    abort(404);
                }
            }
        }

        return $next($request);
    }

    private function resolveCourseFromRoute(Request $request): ?Course
    {
        if ($course = $request->route('course')) {
            return $course instanceof Course ? $course : Course::find($course);
        }

        if ($meeting = $request->route('meeting')) {
            return $meeting->course;
        }

        if ($material = $request->route('material')) {
            return $material->meeting?->course;
        }

        if ($assignment = $request->route('assignment')) {
            return $assignment->course;
        }

        if ($submission = $request->route('submission')) {
            return $submission->assignment?->course;
        }

        if ($attempt = $request->route('attempt')) {
            return $attempt->quiz?->assignment?->course;
        }

        return null;
    }
}