<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskSubmissionController extends Controller
{
    /**
     * Menampilkan instruksi Tugas Upload dan status pengumpulan siswa dari tabel assignment_submissions.
     */
    public function show(Assignment $assignment)
    {
        // Pastikan tipe evaluasi adalah 'submission'
        if ($assignment->type !== 'submission') {
            abort(404, 'Modul evaluasi ini bukan merupakan Tugas Upload berkas.');
        }

        $this->authorizeEnrollment($assignment->meeting->course_id);

        // Ambil data pengumpulan berkas milik student
        $submission = AssignmentSubmission::with(['media', 'grader'])
            ->where('assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->first();

        return view('student.submissions.show', compact('assignment', 'submission'));
    }

    /**
     * Mengirimkan / mengunggah berkas Tugas Upload ke tabel assignment_submissions.
     */
    public function submit(Request $request, Assignment $assignment)
    {
        if ($assignment->type !== 'submission') {
            abort(400, 'Jenis evaluasi tidak valid.');
        }

        $this->authorizeEnrollment($assignment->meeting->course_id);

        // Cek batas waktu pengumpulan jika ada
        if ($assignment->available_until && now()->greaterThan($assignment->available_until)) {
            return back()->with('error', 'Batas waktu pengumpulan Tugas Upload telah berakhir.');
        }

        $validated = $request->validate([
            'file' => ['required_without:submission_id', 'nullable', 'file', 'mimes:pdf,doc,docx,zip,rar,png,jpg', 'max:10240'],
            'feedback' => ['nullable', 'string'],
        ], [
            'file.required_without' => 'Berkas tugas wajib diunggah.',
            'file.mimes' => 'Format berkas yang diperbolehkan: PDF, DOC, DOCX, ZIP, RAR, PNG, JPG.',
            'file.max' => 'Ukuran berkas maksimal 10MB.',
        ]);

        $mediaId = null;

        // 1. Simpan berkas ke tabel media jika ada file baru diunggah
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('submissions', 'public');

            $media = Media::create([
                'type' => 'document',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);

            $mediaId = $media->id;
        }

        // 2. Simpan / perbarui catatan pengumpulan di tabel assignment_submissions
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($submission) {
            // Hapus berkas media lama jika mengunggah berkas pengganti
            if ($mediaId && $submission->media) {
                Storage::disk('public')->delete($submission->media->file_path);
                $submission->media->delete();
            }

            $submission->update([
                'media_id' => $mediaId ?? $submission->media_id,
                'feedback' => $validated['feedback'] ?? $submission->feedback,
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);
        } else {
            AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
                'media_id' => $mediaId,
                'feedback' => $validated['feedback'] ?? null,
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);
        }

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }

    private function authorizeEnrollment(int $courseId): void
    {
        $isEnrolled = Enrollment::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }
}