<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\SubmissionFeedbackAttachment;
use App\Models\Media;
use App\Helpers\CourseProgressHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubmissionGradingController extends Controller
{
    /**
     * Tampilkan Halaman Review & Penilaian
     */
    public function edit(AssignmentSubmission $submission)
    {
        // Otorisasi Akses Pengajar
        // $this->authorizeAccess($submission->assignment->course);

        $submission->load([
            'assignment.course', 
            'assignment.meeting', 
            'enrollment.user', 
            'attachments.media', 
            'feedbackAttachments.media',
            'grader'
        ]);

        // Ambil seluruh attempt submission dari siswa (enrollment) yang sama pada tugas ini
        $allAttempts = AssignmentSubmission::with('grader')
            ->where('assignment_id', $submission->assignment_id)
            ->where('enrollment_id', $submission->enrollment_id)
            ->orderBy('attempt_number', 'asc')
            ->get();

        // Kunci form penilaian jika status enrollment sudah completed atau dropped
        $isLocked = in_array($submission->enrollment?->status, ['completed', 'dropped'], true);

        return view('teacher.submissions.grade', compact('submission', 'allAttempts', 'isLocked'));
    }

    /**
     * Simpan Nilai & Multiple File Feedback dari Teacher
     */
    public function update(Request $request, AssignmentSubmission $submission)
    {
        // $this->authorizeAccess($submission->assignment->course);

        $submission->load(['assignment.course', 'enrollment']);

        // Cegah perubahan nilai & feedback jika status enrollment sudah completed atau dropped
        if (in_array($submission->enrollment?->status, ['completed', 'dropped'], true)) {
            return back()->with('error', 'Penilaian dan feedback tidak dapat diubah karena status enrollment siswa sudah ' . strtoupper($submission->enrollment->status) . '.');
        }

        $maxScore = $submission->assignment->max_score;

        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:' . $maxScore],
            'feedback' => ['nullable', 'string'],
            'feedback_attachments' => ['nullable', 'array'],
            'feedback_attachments.*' => ['file', 'mimes:pdf,doc,docx,ppt,pptx,zip,rar,png,jpg,jpeg', 'max:10240'],
            'delete_feedback_attachments' => ['nullable', 'array'],
        ], [
            'score.required' => 'Nilai wajib diisi.',
            'score.numeric' => 'Nilai harus berupa angka.',
            'score.min' => 'Nilai tidak boleh kurang dari 0.',
            'score.max' => 'Nilai tidak boleh melebihi nilai maksimal (' . $maxScore . ').',
            'feedback_attachments.*.max' => 'Ukuran berkas feedback maksimal 10MB per file.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update data submission
            $submission->update([
                'score' => $validated['score'],
                'feedback' => $validated['feedback'] ?? null,
                'graded_by' => Auth::id(),
                'graded_at' => now(),
                'status' => 'graded',
            ]);

            // 2. Hapus file lampiran feedback yang dicentang
            if (!empty($validated['delete_feedback_attachments'])) {
                $attachmentsToDelete = SubmissionFeedbackAttachment::where('submission_id', $submission->id)
                    ->whereIn('id', $validated['delete_feedback_attachments'])
                    ->get();

                foreach ($attachmentsToDelete as $att) {
                    if ($att->media) {
                        Storage::disk('public')->delete($att->media->file_path);
                        $att->media->delete();
                    }
                    $att->delete();
                }
            }

            // 3. Simpan file lampiran feedback baru dari Teacher
            if ($request->hasFile('feedback_attachments')) {
                $lastOrder = $submission->feedbackAttachments()->max('sort_order') ?? 0;
                foreach ($request->file('feedback_attachments') as $index => $file) {
                    $path = $file->store('submission_feedbacks', 'public');
                    $media = Media::create([
                        'type' => 'document',
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);

                    SubmissionFeedbackAttachment::create([
                        'submission_id' => $submission->id,
                        'media_id' => $media->id,
                        'sort_order' => $lastOrder + $index + 1,
                    ]);
                }
            }

            // 4. Hitung ulang progress pengerjaan siswa berdasarkan enrollment_id
            CourseProgressHelper::updateStudentProgress($submission->assignment->course_id, $submission->enrollment_id);

            DB::commit();

            return redirect()->route('teacher.assignments.show', $submission->assignment_id)
                ->with('success', 'Nilai dan feedback berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Unduh Berkas Lampiran Submission / Feedback dengan Nama File Asli
     */
    public function downloadMedia(AssignmentSubmission $submission, Media $media)
    {
        // $this->authorizeAccess($submission->assignment->course);

        $submission->load(['assignment.course', 'attachments', 'feedbackAttachments']);

        $isAttached = $submission->attachments->contains('media_id', $media->id)
            || $submission->feedbackAttachments->contains('media_id', $media->id);

        if (!$isAttached) {
            abort(404, 'Berkas tidak ditemukan pada submission ini.');
        }

        if (!Storage::disk('public')->exists($media->file_path)) {
            abort(404, 'File fisik tidak ditemukan di penyimpanan.');
        }

        return Storage::disk('public')->download($media->file_path, $media->file_name);
    }

    /**
     * Hapus Attempt Submission Siswa
     */
    public function destroy(AssignmentSubmission $submission)
    {
        // $this->authorizeAccess($submission->assignment->course);

        $submission->load(['assignment.course', 'enrollment', 'attachments.media', 'feedbackAttachments.media']);

        if (in_array($submission->enrollment?->status, ['completed', 'dropped'], true)) {
            return back()->with('error', 'Attempt submission tidak dapat dihapus karena status enrollment siswa sudah ' . strtoupper($submission->enrollment->status) . '.');
        }

        DB::beginTransaction();
        try {
            $assignmentId = $submission->assignment_id;
            $courseId = $submission->assignment->course_id;
            $enrollmentId = $submission->enrollment_id;

            // Hapus file lampiran jawaban siswa
            foreach ($submission->attachments as $att) {
                if ($att->media) {
                    Storage::disk('public')->delete($att->media->file_path);
                    $att->media->delete();
                }
            }

            // Hapus file lampiran feedback pengajar
            foreach ($submission->feedbackAttachments as $att) {
                if ($att->media) {
                    Storage::disk('public')->delete($att->media->file_path);
                    $att->media->delete();
                }
            }

            $submission->delete();

            // Hitung ulang progress pengerjaan siswa menggunakan Helper
            CourseProgressHelper::updateStudentProgress($courseId, $enrollmentId);

            DB::commit();

            return redirect()->route('teacher.assignments.show', $assignmentId)
                ->with('success', 'Attempt submission siswa berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus submission: ' . $e->getMessage());
        }
    }

    // private function authorizeAccess($course): void
    // {
    //     $userId = Auth::id();
    //     $isCreator = $course->created_by === $userId;
    //     $isInstructor = $course->instructors()->where('user_id', $userId)->exists();

    //     if (!$isCreator && !$isInstructor) {
    //         abort(403, 'Anda tidak memiliki akses ke kelas ini.');
    //     }
    // }
}