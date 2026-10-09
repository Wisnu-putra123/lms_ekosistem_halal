<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\SubmissionAttachment;
use App\Models\Media;
use App\Helpers\CourseProgressHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskSubmissionController extends Controller
{
    /**
     * Tampilkan detail tugas beserta seluruh riwayat submission/attempt siswa.
     */
    public function show(Assignment $assignment)
    {
        if ($assignment->type !== 'submission') {
            abort(404, 'Halaman ini khusus untuk pengumpulan tugas submission.');
        }

        $assignment->load(['attachments.media', 'meeting', 'course']);

        // Ambil seluruh riwayat attempt milik siswa (diurutkan dari attempt terbaru)
        $submissions = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->with(['attachments.media', 'feedbackAttachments.media'])
            ->orderBy('attempt_number', 'desc')
            ->get();

        // Attempt terakhir
        $latestSubmission = $submissions->first();

        // Cari skor tertinggi dari seluruh attempt yang sudah dinilai
        $bestScore = $submissions->where('status', 'graded')->max('score');

        // Pengecekan apakah siswa masih bisa mengirim attempt baru
        $hasMaxAttempts = !is_null($assignment->max_attempts);
        $attemptsCount = $submissions->count();
        $canSubmitNewAttempt = !$hasMaxAttempts || ($attemptsCount < $assignment->max_attempts);

        return view('student.submissions.show', compact(
            'assignment', 
            'submissions', 
            'latestSubmission', 
            'bestScore', 
            'canSubmitNewAttempt'
        ));
    }

    /**
     * Simpan Submission Jawaban Baru (Attempt Baru)
     */
    public function store(Request $request, Assignment $assignment)
    {
        // 1. Cek Batas Attempt
        $existingSubmissions = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->get();

        $currentAttemptCount = $existingSubmissions->count();

        if (!is_null($assignment->max_attempts) && $currentAttemptCount >= $assignment->max_attempts) {
            return back()->with('error', 'Anda telah mencapai batas maksimal percobaan pengerjaan tugas ini (' . $assignment->max_attempts . 'x).');
        }

        // Cek apakah ada submission terakhir yang masih berstatus 'submitted' (belum dinilai)
        $lastSubmission = $existingSubmissions->sortByDesc('attempt_number')->first();
        if ($lastSubmission && $lastSubmission->status === 'submitted') {
            return back()->with('error', 'Pengumpulan sebelumnya masih menunggu penilaian dari pengajar. Silakan gunakan fitur edit jika ingin memperbarui jawaban attempt ini.');
        }

        // Validasi format pengumpulan
        $this->validateSubmissionMethod($request, $assignment);

        $nextAttemptNumber = $currentAttemptCount + 1;

        DB::beginTransaction();
        try {
            // 1. Simpan data submission attempt baru
            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
                'attempt_number' => $nextAttemptNumber,
                'submission_text' => $request->input('submission_text'),
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);

            // 2. Simpan Lampiran File jika ada
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $file) {
                    $path = $file->store('student_submissions', 'public');

                    $media = Media::create([
                        'type' => 'document',
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);

                    SubmissionAttachment::create([
                        'submission_id' => $submission->id,
                        'media_id' => $media->id,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('student.submissions.show', $assignment->id)
                ->with('success', 'Tugas attempt #' . $nextAttemptNumber . ' berhasil dikumpulkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengumpulkan tugas: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update Submission Jawaban pada Attempt Tertentu (Edit/Re-upload sebelum dinilai)
     */
    public function update(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        // Otorisasi pemilik submission
        if ($submission->user_id !== Auth::id() || $submission->assignment_id !== $assignment->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if ($submission->status === 'graded') {
            return back()->with('error', 'Jawaban pada attempt ini sudah dinilai dan tidak dapat diubah lagi.');
        }

        $this->validateSubmissionMethod($request, $assignment);

        DB::beginTransaction();
        try {
            // 1. Update text & waktu pengumpulan
            $submission->update([
                'submission_text' => $request->input('submission_text'),
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);

            // 2. Hapus lampiran yang dicentang oleh siswa
            if ($request->filled('delete_attachments')) {
                $attachmentsToDelete = SubmissionAttachment::whereIn('id', $request->input('delete_attachments'))
                    ->where('submission_id', $submission->id)
                    ->get();

                foreach ($attachmentsToDelete as $att) {
                    if ($att->media) {
                        Storage::disk('public')->delete($att->media->file_path);
                        $att->media->delete();
                    }
                    $att->delete();
                }
            }

            // 3. Tambahkan lampiran berkas baru jika ada
            if ($request->hasFile('attachments')) {
                $lastOrder = $submission->attachments()->max('sort_order') ?? 0;

                foreach ($request->file('attachments') as $index => $file) {
                    $path = $file->store('student_submissions', 'public');

                    $media = Media::create([
                        'type' => 'document',
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);

                    SubmissionAttachment::create([
                        'submission_id' => $submission->id,
                        'media_id' => $media->id,
                        'sort_order' => $lastOrder + $index + 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('student.submissions.show', $assignment->id)
                ->with('success', 'Pengumpulan tugas attempt #' . $submission->attempt_number . ' berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui jawaban: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus Attempt Submission Tertentu
     */
    public function destroy(Assignment $assignment, AssignmentSubmission $submission)
    {
        if ($submission->user_id !== Auth::id() || $submission->assignment_id !== $assignment->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if ($submission->status === 'graded') {
            return back()->with('error', 'Attempt ini sudah dinilai dan tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            $courseId = $assignment->course_id;
            $studentId = Auth::id();

            // Hapus berkas lampiran siswa
            foreach ($submission->attachments as $attachment) {
                if ($attachment->media) {
                    Storage::disk('public')->delete($attachment->media->file_path);
                    $attachment->media->delete();
                }
            }

            $submission->delete();

            // Hitung ulang progress pengerjaan siswa
            CourseProgressHelper::updateStudentProgress($courseId, $studentId);

            DB::commit();

            return redirect()->route('student.submissions.show', $assignment->id)
                ->with('success', 'Attempt pengumpulan tugas berhasil dibatalkan/dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pengumpulan tugas.');
        }
    }

    /**
     * Helper Validasi berdasarkan submission_method
     */
    private function validateSubmissionMethod(Request $request, Assignment $assignment)
    {
        $rules = [
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,ppt,pptx,zip,rar,png,jpg,jpeg', 'max:10240'],
        ];

        if ($assignment->submission_method === 'text') {
            $rules['submission_text'] = ['required', 'string'];
        } elseif ($assignment->submission_method === 'file') {
            $rules['submission_text'] = ['nullable', 'string'];
            if (!$request->isMethod('PUT') && !$request->hasFile('attachments')) {
                $rules['attachments'] = ['required', 'array', 'min:1'];
            }
        } else { // 'both'
            if ($request->isMethod('PUT')) {
                $rules['submission_text'] = ['nullable', 'string'];
            } else {
                $rules['submission_text'] = ['nullable', 'required_without:attachments', 'string'];
                $rules['attachments'] = ['nullable', 'required_without:submission_text', 'array', 'min:1'];
            }
        }

        $messages = [
            'submission_text.required' => 'Jawaban teks wajib diisi.',
            'submission_text.required_without' => 'Mohon isi teks jawaban atau unggah minimal satu berkas lampiran.',
            'attachments.required' => 'Wajib melampirkan berkas jawaban.',
            'attachments.required_without' => 'Mohon unggah minimal satu berkas lampiran atau isi teks jawaban.',
            'attachments.*.max' => 'Ukuran berkas maksimal adalah 10MB per file.',
        ];

        $request->validate($rules, $messages);
    }
}