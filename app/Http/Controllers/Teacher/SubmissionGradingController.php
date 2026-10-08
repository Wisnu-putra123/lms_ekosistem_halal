<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Helpers\CourseProgressHelper; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubmissionGradingController extends Controller
{
    /**
     * Tampilkan Halaman Review & Penilaian (Method edit)
     */
    public function edit(AssignmentSubmission $submission)
    {
        $submission->load(['assignment.course', 'assignment.meeting', 'user', 'attachments.media']);

        // Otorisasi Akses Pengajar
        $this->authorizeAccess($submission->assignment->course);

        return view('teacher.submissions.grade', compact('submission'));
    }

    /**
     * Simpan Nilai & Feedback dari Teacher
     */
    public function update(Request $request, AssignmentSubmission $submission)
    {
        $submission->load('assignment.course');
        $this->authorizeAccess($submission->assignment->course);

        $maxScore = $submission->assignment->max_score;

        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:' . $maxScore],
            'feedback' => ['nullable', 'string'],
        ], [
            'score.required' => 'Nilai wajib diisi.',
            'score.max' => 'Nilai tidak boleh melebihi nilai maksimal (' . $maxScore . ').',
        ]);

        DB::beginTransaction();
        try {
            // Update data submission
            $submission->update([
                'score' => $validated['score'],
                'feedback' => $validated['feedback'] ?? null,
                'graded_by' => Auth::id(),
                'graded_at' => now(),
                'status' => 'graded',
            ]);

            // Hitung ulang progress pengerjaan siswa menggunakan Helper
            CourseProgressHelper::updateStudentProgress($submission->assignment->course_id, $submission->user_id);

            DB::commit();

            return redirect()->route('teacher.assignments.show', $submission->assignment_id)
                ->with('success', 'Nilai dan feedback berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus Submission Siswa
     */
    public function destroy(AssignmentSubmission $submission)
    {
        $submission->load('assignment.course');
        $this->authorizeAccess($submission->assignment->course);

        DB::beginTransaction();
        try {
            $assignmentId = $submission->assignment_id;
            $courseId = $submission->assignment->course_id;
            $studentId = $submission->user_id;

            // Hapus file lampiran siswa dari storage & DB
            foreach ($submission->attachments as $att) {
                if ($att->media) {
                    Storage::disk('public')->delete($att->media->file_path);
                    $att->media->delete();
                }
            }

            $submission->delete();

            // Hitung ulang progress pengerjaan siswa menggunakan Helper
            CourseProgressHelper::updateStudentProgress($courseId, $studentId);

            DB::commit();

            return redirect()->route('teacher.assignments.show', $assignmentId)
                ->with('success', 'Submission siswa berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus submission.');
        }
    }

    private function authorizeAccess($course): void
    {
        $userId = Auth::id();
        $isCreator = $course->created_by === $userId;
        $isInstructor = $course->instructors()->where('user_id', $userId)->exists();

        if (!$isCreator && !$isInstructor) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }
    }
}