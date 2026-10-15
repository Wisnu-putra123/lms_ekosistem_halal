<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentAttachment;
use App\Models\Meeting;
use App\Models\Media;
use App\Helpers\CourseProgressHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function create(Meeting $meeting)
    {
        $this->authorizeAccess($meeting->course);
        return view('teacher.submissions.create', compact('meeting'));
    }

    public function store(Request $request, Meeting $meeting)
    {
        $this->authorizeAccess($meeting->course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'submission_method' => ['required', 'in:file,text,both'],
            'max_attempts' => ['nullable', 'integer', 'min:1'],
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
            'max_attempts.integer' => 'Batas percobaan pengerjaan harus berupa angka.',
            'max_attempts.min' => 'Batas percobaan pengerjaan minimal 1.',
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
                'max_attempts' => $validated['max_attempts'] ?? null,
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

            CourseProgressHelper::recalculateAllActiveStudentsProgress($meeting->course_id);

            DB::commit();
            return redirect()->route('teacher.courses.show', $meeting->course_id)
                ->with('success', 'Tempat submission tugas berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat submission: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan Detail Tugas beserta Daftar Pengumpulan Siswa (Dukungan Filter, Sort, & Status Enrollment)
     */
    public function show(Request $request, Assignment $assignment)
    {
        $this->authorizeAccess($assignment->course);

        $assignment->load(['attachments.media', 'meeting']);

        // Query submissions dengan eger loading enrollment & user
        $submissionsQuery = $assignment->submissions()
            ->with(['enrollment.user', 'attachments.media']);

        // 1. Filter Nama / Email Siswa
        if ($request->filled('search')) {
            $search = $request->search;
            $submissionsQuery->whereHas('enrollment.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 2. Filter Status Enrollment Siswa (active / completed / dropped)
        if ($request->filled('enrollment_status')) {
            $enrollmentStatus = $request->enrollment_status;
            $submissionsQuery->whereHas('enrollment', function ($q) use ($enrollmentStatus) {
                $q->where('status', $enrollmentStatus);
            });
        }

        // 3. Filter Status Submission (submitted / graded / late / draft)
        if ($request->filled('status')) {
            $submissionsQuery->where('status', $request->status);
        }

        // 4. Sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $submissionsQuery->orderBy('submitted_at', 'asc');
                break;
            case 'score_high':
                $submissionsQuery->orderBy('score', 'desc');
                break;
            case 'score_low':
                $submissionsQuery->orderBy('score', 'asc');
                break;
            case 'latest':
            default:
                $submissionsQuery->orderBy('submitted_at', 'desc');
                break;
        }

        $submissions = $submissionsQuery->get();

        return view('teacher.submissions.show', compact('assignment', 'submissions'));
    }

    public function edit(Assignment $assignment)
    {
        $this->authorizeAccess($assignment->course);
        $assignment->load(['attachments.media']);
        return view('teacher.submissions.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorizeAccess($assignment->course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'submission_method' => ['required', 'in:file,text,both'],
            'max_attempts' => ['nullable', 'integer', 'min:1'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
            'max_score' => ['required', 'numeric', 'min:1', 'max:1000'],
            'passing_score' => ['required', 'numeric', 'min:0', 'lte:max_score'],
            'status' => ['required', 'in:draft,published,closed'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,ppt,pptx,zip,rar,png,jpg,jpeg', 'max:10240'],
            'delete_attachments' => ['nullable', 'array'],
        ], [
            'title.required' => 'Judul tugas wajib diisi.',
            'submission_method.required' => 'Metode pengumpulan wajib dipilih.',
            'passing_score.lte' => 'Nilai kelulusan tidak boleh melebihi nilai maksimal.',
            'max_attempts.integer' => 'Batas percobaan pengerjaan harus berupa angka.',
            'max_attempts.min' => 'Batas percobaan pengerjaan minimal 1.',
        ]);

        DB::beginTransaction();
        try {
            $assignment->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'submission_method' => $validated['submission_method'],
                'max_attempts' => $validated['max_attempts'] ?? null,
                'available_from' => $validated['available_from'] ?? null,
                'available_until' => $validated['available_until'] ?? null,
                'max_score' => $validated['max_score'],
                'passing_score' => $validated['passing_score'],
                'status' => $validated['status'],
            ]);

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

            CourseProgressHelper::recalculateAllActiveStudentsProgress($assignment->course_id);

            DB::commit();
            return redirect()->route('teacher.submissions.show', $assignment->id)
                ->with('success', 'Tempat submission berhasil diperbarui!');
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
            $courseId = $assignment->course_id;

            foreach ($assignment->attachments as $attachment) {
                if ($attachment->media) {
                    Storage::disk('public')->delete($attachment->media->file_path);
                    $attachment->media->delete();
                }
            }

            $assignment->delete();

            CourseProgressHelper::recalculateAllActiveStudentsProgress($courseId);

            DB::commit();

            return redirect()->route('teacher.courses.show', $courseId)
                ->with('success', 'Tempat submission berhasil dihapus.');
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