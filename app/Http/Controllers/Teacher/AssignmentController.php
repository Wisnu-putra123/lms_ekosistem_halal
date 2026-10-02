<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentAttachment;
use App\Models\Meeting;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function create(Meeting $meeting)
    {
        $this->authorizeAccess($meeting->course);
        return view('teacher.assignments.create', compact('meeting'));
    }

    public function store(Request $request, Meeting $meeting)
    {
        $this->authorizeAccess($meeting->course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'submission_method' => ['required', 'in:file,text,both'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
            'max_score' => ['required', 'numeric', 'min:1', 'max:1000'],
            'passing_score' => ['required', 'numeric', 'min:0', 'lte:max_score'],
            'status' => ['required', 'in:draft,published,closed'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,ppt,pptx,zip,rar,png,jpg,jpeg', 'max:10240'],
        ], [
            'title.required' => 'Judul tugas wajib diisi.',
            'submission_method.required' => 'Metode pengumpulan wajib dipilih.',
            'passing_score.lte' => 'Nilai kelulusan tidak boleh melebihi nilai maksimal.',
        ]);

        DB::beginTransaction();
        try {
            $assignment = Assignment::create([
                'course_id' => $meeting->course_id,
                'meeting_id' => $meeting->id,
                'created_by' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => 'submission',
                'submission_method' => $validated['submission_method'],
                'available_from' => $validated['available_from'] ?? null,
                'available_until' => $validated['available_until'] ?? null,
                'max_score' => $validated['max_score'] ?? 100.00,
                'passing_score' => $validated['passing_score'] ?? 75.00,
                'status' => $validated['status'],
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $file) {
                    $path = $file->store('assignment_attachments', 'public');
                    $media = Media::create([
                        'type' => 'document',
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);

                    AssignmentAttachment::create([
                        'assignment_id' => $assignment->id,
                        'media_id' => $media->id,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('teacher.courses.show', $meeting->course_id)->with('success', 'Tempat submission tugas berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat submission: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Assignment $assignment)
    {
        $this->authorizeAccess($assignment->course);
        $assignment->load(['attachments.media', 'submissions.user', 'meeting']);
        return view('teacher.assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        $this->authorizeAccess($assignment->course);
        $assignment->load(['attachments.media']);
        return view('teacher.assignments.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorizeAccess($assignment->course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'submission_method' => ['required', 'in:file,text,both'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
            'max_score' => ['required', 'numeric', 'min:1', 'max:1000'],
            'passing_score' => ['required', 'numeric', 'min:0', 'lte:max_score'],
            'status' => ['required', 'in:draft,published,closed'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,ppt,pptx,zip,rar,png,jpg,jpeg', 'max:10240'],
            'delete_attachments' => ['nullable', 'array'],
        ]);

        DB::beginTransaction();
        try {
            $assignment->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'submission_method' => $validated['submission_method'],
                'available_from' => $validated['available_from'] ?? null,
                'available_until' => $validated['available_until'] ?? null,
                'max_score' => $validated['max_score'],
                'passing_score' => $validated['passing_score'],
                'status' => $validated['status'],
            ]);

            // Hapus lampiran yang dicentang
            if (!empty($validated['delete_attachments'])) {
                $attachmentsToDelete = AssignmentAttachment::whereIn('id', $validated['delete_attachments'])->get();
                foreach ($attachmentsToDelete as $att) {
                    if ($att->media) {
                        Storage::disk('public')->delete($att->media->file_path);
                        $att->media->delete();
                    }
                    $att->delete();
                }
            }

            // Tambah lampiran baru
            if ($request->hasFile('attachments')) {
                $lastOrder = $assignment->attachments()->max('sort_order') ?? 0;
                foreach ($request->file('attachments') as $index => $file) {
                    $path = $file->store('assignment_attachments', 'public');
                    $media = Media::create([
                        'type' => 'document',
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);

                    AssignmentAttachment::create([
                        'assignment_id' => $assignment->id,
                        'media_id' => $media->id,
                        'sort_order' => $lastOrder + $index + 1,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('teacher.assignments.show', $assignment->id)->with('success', 'Tempat submission berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui submission: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorizeAccess($assignment->course);

        DB::beginTransaction();
        try {
            foreach ($assignment->attachments as $attachment) {
                if ($attachment->media) {
                    Storage::disk('public')->delete($attachment->media->file_path);
                    $attachment->media->delete();
                }
            }

            $courseId = $assignment->course_id;
            $assignment->delete();

            DB::commit();

            return redirect()->route('teacher.courses.show', $courseId)->with('success', 'Tempat submission berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus tugas.');
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