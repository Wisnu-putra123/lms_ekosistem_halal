<?php

namespace App\Http\Middleware;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Material;
use App\Models\Meeting;
use App\Models\Quiz;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMeetingIsAccessible
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $meeting = $this->resolveMeeting($request);

        if ($meeting && ! $meeting->isAccessible()) {
            $reason = $meeting->lockReason() ?? 'Pertemuan ini sedang terkunci dan tidak dapat diakses saat ini.';
            $message = "Akses ditolak: Pertemuan {$meeting->meeting_number} ({$meeting->title}) {$reason}";

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 403);
            }

            if ($meeting->course_id) {
                return redirect()
                    ->route('student.courses.show', $meeting->course_id)
                    ->with('error', $message);
            }

            return redirect()
                ->route('student.dashboard')
                ->with('error', $message);
        }

        return $next($request);
    }

    /**
     * Resolve model Meeting dari parameter route yang tersedia.
     */
    protected function resolveMeeting(Request $request): ?Meeting
    {
        // 1. Jika parameter route langsung berupa meeting
        if ($meetingParam = $request->route('meeting')) {
            return $meetingParam instanceof Meeting
                ? $meetingParam
                : Meeting::find($meetingParam);
        }

        // 2. Jika route mengakses Material (student.materials.*)
        if ($materialParam = $request->route('material')) {
            $material = $materialParam instanceof Material
                ? $materialParam->loadMissing('meeting')
                : Material::with('meeting')->find($materialParam);

            return $material?->meeting;
        }

        // 3. Jika route mengakses Assignment / Task Submission (student.submissions.*)
        if ($assignmentParam = $request->route('assignment')) {
            $assignment = $assignmentParam instanceof Assignment
                ? $assignmentParam->loadMissing('meeting')
                : Assignment::with('meeting')->find($assignmentParam);

            return $assignment?->meeting;
        }

        // 4. Jika parameter route berupa submission
        if ($submissionParam = $request->route('submission')) {
            $submission = $submissionParam instanceof AssignmentSubmission
                ? $submissionParam->loadMissing('assignment.meeting')
                : AssignmentSubmission::with('assignment.meeting')->find($submissionParam);

            return $submission?->assignment?->meeting;
        }

        // 5. Jika route mengakses Quiz (student.quizzes.*)
        if ($quizParam = $request->route('quiz')) {
            $quiz = $quizParam instanceof Quiz
                ? $quizParam->loadMissing('assignment.meeting')
                : Quiz::with('assignment.meeting')->find($quizParam);

            return $quiz?->assignment?->meeting;
        }

        return null;
    }
}