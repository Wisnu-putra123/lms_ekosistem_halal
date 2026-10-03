<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\SubmissionAttachment;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskSubmissionController extends Controller
{
    /**
     * Tampilkan detail tugas beserta form submission / status jawaban student
     */
    public function show(Assignment $assignment)
    {
        if ($assignment->type !== 'submission') {
            abort(404, 'Halaman ini khusus untuk pengumpulan tugas submission.');
        }

        $assignment->load(['attachments.media', 'meeting', 'course']);

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->with(['attachments.media'])
            ->first();

        return view('student.submissions.show', compact('assignment', 'submission'));
    }

    /**
     * Simpan Submission Jawaban Baru
     */
    public function store(Request $request, Assignment $assignment)
    {
        $this->validateSubmissionMethod($request, $assignment);

        // Cek apakah sudah pernah membuat submission
        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda sudah mengumpulkan tugas ini. Silakan gunakan fitur edit jika ingin mengubah jawaban.');
        }

        DB::beginTransaction();
        try {
            // 1. Simpan data submission utama
            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
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
                ->with('success', 'Tugas berhasil dikumpulkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengumpulkan tugas: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update Submission Jawaban (Resubmit / Edit)
     */
    public function update(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        // Otorisasi pemilik submission
        if ($submission->user_id !== Auth::id() || $submission->assignment_id !== $assignment->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $this->validateSubmissionMethod($request, $assignment);

        DB::beginTransaction();
        try {
            // 1. Update text & waktu pengumpulan
            $submission->update([
                'submission_text' => $request->input('submission_text'),
                'submitted_at' => now(),
                'status' => 'submitted', // Reset status kembali ke submitted jika diedit
            ]);

            // 2. Hapus lampiran yang dicentang untuk dihapus oleh siswa
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
                ->with('success', 'Pengumpulan tugas berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui jawaban: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus Seluruh Submission (Resubmit dari Awal)
     */
    public function destroy(Assignment $assignment, AssignmentSubmission $submission)
    {
        if ($submission->user_id !== Auth::id() || $submission->assignment_id !== $assignment->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        DB::beginTransaction();
        try {
            // Hapus berkas lampiran siswa
            foreach ($submission->attachments as $attachment) {
                if ($attachment->media) {
                    Storage::disk('public')->delete($attachment->media->file_path);
                    $attachment->media->delete();
                }
            }

            $submission->delete();

            DB::commit();

            return redirect()->route('student.submissions.show', $assignment->id)
                ->with('success', 'Pengumpulan tugas berhasil dibatalkan/dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pengumpulan tugas.');
        }
    }

    /**
     * Helper Validasi berdasarkan submission_method (file, text, atau both)
     */
    private function validateSubmissionMethod(Request $request, Assignment $assignment)
    {
        $rules = [
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,ppt,pptx,zip,rar,png,jpg,jpeg', 'max:10240'],
        ];

        if ($assignment->submission_method === 'text') {
            // Metode Teks: Wajib isi teks
            $rules['submission_text'] = ['required', 'string'];
        } elseif ($assignment->submission_method === 'file') {
            // Metode File: Wajib unggah file (jika belum ada file yang tersimpan)
            $rules['submission_text'] = ['nullable', 'string'];
            if (!$request->isMethod('PUT') && !$request->hasFile('attachments')) {
                $rules['attachments'] = ['required', 'array', 'min:1'];
            }
        } else { // 'both'
            // Metode Keduanya: Minimal SALAH SATU terisi (Teks ATAU File)
            
            if ($request->isMethod('PUT')) {
                // Pada mode Edit: Izinkan jika teks diisi ATAU file baru diunggah ATAU masih ada lampiran lama yang tidak dihapus
                $rules['submission_text'] = ['nullable', 'string'];
            } else {
                // Pada pengumpulan baru: Teks wajib jika TIDAK ADA file, dan File wajib jika TIDAK ADA teks
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