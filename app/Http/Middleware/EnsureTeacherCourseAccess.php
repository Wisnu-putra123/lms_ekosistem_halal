<?php

namespace App\Http\Middleware;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\Meeting;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\AssignmentSubmission;
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
            if ($assignmentParam = $request->route('assignment')) {
                $assignment = $assignmentParam instanceof Assignment
                    ? $assignmentParam
                    : Assignment::find($assignmentParam);

                if (!$assignment || (int) $assignment->course_id !== (int) $course->id) {
                    abort(404);
                }

                // Pastikan soal (question) benar-benar bagian dari kuis ini
                if ($questionParam = $request->route('question')) {
                    $questionId = $questionParam instanceof Question ? $questionParam->id : $questionParam;
                    $belongsToQuiz = $assignment->quiz?->questions()
                        ->where('questions.id', $questionId)
                        ->exists();

                    if (!$belongsToQuiz) {
                        abort(404);
                    }
                }
            }

            if ($enrollmentParam = $request->route('enrollment')) {
                $enrollment = $enrollmentParam instanceof Enrollment
                    ? $enrollmentParam
                    : Enrollment::find($enrollmentParam);

                if (!$enrollment || (int) $enrollment->course_id !== (int) $course->id) {
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
            $model = $meeting instanceof Meeting ? $meeting : Meeting::find($meeting);
            return $model?->course;
        }

        if ($material = $request->route('material')) {
            $model = $material instanceof Material ? $material : Material::find($material);
            return $model?->meeting?->course;
        }

        if ($assignment = $request->route('assignment')) {
            $model = $assignment instanceof Assignment ? $assignment : Assignment::find($assignment);
            return $model?->course;
        }

        if ($submission = $request->route('submission')) {
            $model = $submission instanceof AssignmentSubmission ? $submission : AssignmentSubmission::find($submission);
            return $model?->assignment?->course;
        }

        if ($attempt = $request->route('attempt')) {
            $model = $attempt instanceof QuizAttempt ? $attempt : QuizAttempt::find($attempt);
            return $model?->quiz?->assignment?->course;
        }

        return null;
    }
}