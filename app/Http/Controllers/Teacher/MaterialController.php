<?php

namespace App\Http\Controllers\Teacher;

use App\Helpers\CourseProgressHelper;
use App\Helpers\EmbedHelper;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\Media;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Tampilkan form tambah materi untuk meeting tertentu.
     */
    public function create(Meeting $meeting)
    {
        return view('teacher.materials.create', compact('meeting'));
    }

    /**
     * Simpan materi baru dan hitung ulang progres seluruh peserta aktif.
     */
    public function store(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'media_type' => 'nullable|in:file,embed,none',
            'external_url' => 'nullable|required_if:media_type,embed|url',
            'file' => 'nullable|required_if:media_type,file|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar,png,jpg,jpeg|max:20480',
            'sort_order' => 'nullable|integer',
        ], [
            'title.required' => 'Judul materi wajib diisi.',
            'external_url.required_if' => 'URL eksternal wajib diisi jika memilih jenis Embedded Link.',
            'external_url.url' => 'Format URL eksternal tidak valid.',
            'file.required_if' => 'Berkas lampiran wajib diunggah jika memilih jenis File Upload.',
            'file.max' => 'Ukuran berkas tidak boleh melebihi 20MB.',
        ]);

        DB::transaction(function () use ($validated, $meeting, $request) {
            $mediaId = null;

            if ($request->input('media_type') === 'embed' && !empty($validated['external_url'])) {
                $embedUrl = EmbedHelper::formatToEmbedUrl($validated['external_url']);

                $media = Media::create([
                    'type' => 'embed',
                    'file_name' => 'Embedded Content: ' . $validated['title'],
                    'external_url' => $embedUrl,
                    'uploaded_by' => Auth::id(),
                ]);

                $mediaId = $media->id;
            } elseif ($request->input('media_type') === 'file' && $request->hasFile('file')) {
                $uploadedFile = $request->file('file');
                $filePath = $uploadedFile->store('materials', 'public');

                $media = Media::create([
                    'type' => 'document',
                    'file_name' => $uploadedFile->getClientOriginalName(),
                    'file_path' => $filePath,
                    'mime_type' => $uploadedFile->getMimeType(),
                    'file_size' => $uploadedFile->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);

                $mediaId = $media->id;
            }

            $sortOrder = $validated['sort_order'] ?? ($meeting->materials()->max('sort_order') + 1);

            Material::create([
                'meeting_id' => $meeting->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'content' => $validated['content'] ?? null,
                'media_id' => $mediaId,
                'sort_order' => $sortOrder,
                'created_by' => Auth::id(),
            ]);

            // Hitung ulang progres seluruh peserta aktif karena total item wajib bertambah
            CourseProgressHelper::recalculateAllActiveStudentsProgress($meeting->course_id);
        });

        return redirect()->route('teacher.courses.show', $meeting->course_id)
            ->with('success', 'Materi berhasil ditambahkan dan progres peserta telah disesuaikan!');
    }

    /**
     * Tampilkan detail materi beserta tabel aktivitas belajar peserta (dengan filter & sortir).
     */
    public function show(Request $request, Material $material)
    {
        $material->load(['meeting.course', 'media']);
        $courseId = $material->meeting->course_id;

        // Parameter filter & sortir
        $search           = trim((string) $request->input('search', ''));
        $status           = $request->input('status', 'all');            // all | viewed | not_viewed
        $enrollmentStatus = $request->input('enrollment_status', 'all'); // all | active | completed | dropped
        $sort             = $request->input('sort', 'name');             // name | enrollment_status | viewed_at | status
        $direction        = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (!in_array($status, ['all', 'viewed', 'not_viewed'], true)) {
            $status = 'all';
        }

        if (!in_array($enrollmentStatus, ['all', 'active', 'completed', 'dropped'], true)) {
            $enrollmentStatus = 'all';
        }

        if (!in_array($sort, ['name', 'enrollment_status', 'viewed_at', 'status'], true)) {
            $sort = 'name';
        }

        // Ambil seluruh enrollment peserta pada course ini (termasuk active, completed, dropped)
        $allEnrollments = Enrollment::with([
            'user:id,name,email,nik,institution',
            'materialViews' => function ($query) use ($material) {
                $query->where('material_id', $material->id);
            },
        ])
            ->where('course_id', $courseId)
            ->get()
            ->map(function ($enrollment) {
                $viewRecord = $enrollment->materialViews->first();
                $enrollment->has_viewed = !is_null($viewRecord);
                $enrollment->viewed_at  = $viewRecord?->viewed_at;

                return $enrollment;
            });

        // Statistik keseluruhan sebelum filter diterapkan
        $totalStudents  = $allEnrollments->count();
        $viewedCount    = $allEnrollments->where('has_viewed', true)->count();
        $notViewedCount = $totalStudents - $viewedCount;
        $completionRate = $totalStudents > 0 ? round(($viewedCount / $totalStudents) * 100, 1) : 0;

        $stats = [
            'total'           => $totalStudents,
            'viewed'          => $viewedCount,
            'not_viewed'      => $notViewedCount,
            'completion_rate' => $completionRate,
        ];

        // Terapkan filter pencarian, status baca materi, & status enrollment
        $filteredEnrollments = $allEnrollments->filter(function ($enrollment) use ($search, $status, $enrollmentStatus) {
            if ($status === 'viewed' && !$enrollment->has_viewed) {
                return false;
            }

            if ($status === 'not_viewed' && $enrollment->has_viewed) {
                return false;
            }

            if ($enrollmentStatus !== 'all' && $enrollment->status !== $enrollmentStatus) {
                return false;
            }

            if ($search !== '') {
                $keyword     = mb_strtolower($search);
                $name        = mb_strtolower($enrollment->user->name ?? '');
                $email       = mb_strtolower($enrollment->user->email ?? '');
                $nik         = mb_strtolower($enrollment->user->nik ?? '');
                $institution = mb_strtolower($enrollment->user->institution ?? '');

                return str_contains($name, $keyword)
                    || str_contains($email, $keyword)
                    || str_contains($nik, $keyword)
                    || str_contains($institution, $keyword);
            }

            return true;
        });

        // Terapkan sortir
        $sortedEnrollments = $filteredEnrollments->sortBy(function ($enrollment) use ($sort) {
            return match ($sort) {
                'enrollment_status' => $enrollment->status ?? '',
                'viewed_at'         => $enrollment->viewed_at ? $enrollment->viewed_at->timestamp : 0,
                'status'            => $enrollment->has_viewed ? 1 : 0,
                default             => mb_strtolower($enrollment->user->name ?? ''),
            };
        }, SORT_REGULAR, $direction === 'desc')->values();

        return view('teacher.materials.show', compact(
            'material',
            'sortedEnrollments',
            'stats',
            'search',
            'status',
            'enrollmentStatus',
            'sort',
            'direction'
        ));
    }

    /**
     * Form edit materi.
     */
    public function edit(Material $material)
    {
        $material->load(['meeting.course', 'media']);
        return view('teacher.materials.edit', compact('material'));
    }

    /**
     * Update data materi.
     */
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'media_type' => 'nullable|in:file,embed,none',
            'external_url' => 'nullable|required_if:media_type,embed|url',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar,png,jpg,jpeg|max:20480',
            'sort_order' => 'nullable|integer',
        ], [
            'title.required' => 'Judul materi wajib diisi.',
            'external_url.required_if' => 'URL eksternal wajib diisi jika memilih Embedded Link.',
            'file.max' => 'Ukuran berkas tidak boleh melebihi 20MB.',
        ]);

        DB::transaction(function () use ($validated, $material, $request) {
            $mediaId = $material->media_id;
            $mediaTypeChoice = $request->input('media_type', 'none');

            if ($mediaTypeChoice === 'embed' && !empty($validated['external_url'])) {
                $embedUrl = EmbedHelper::formatToEmbedUrl($validated['external_url']);

                if ($material->media && $material->media->type !== 'embed' && $material->media->file_path) {
                    Storage::disk('public')->delete($material->media->file_path);
                }

                if ($material->media) {
                    $material->media->update([
                        'type' => 'embed',
                        'external_url' => $embedUrl,
                        'file_path' => null,
                        'file_name' => 'Embedded Content: ' . $validated['title'],
                    ]);
                } else {
                    $media = Media::create([
                        'type' => 'embed',
                        'file_name' => 'Embedded Content: ' . $validated['title'],
                        'external_url' => $embedUrl,
                        'uploaded_by' => Auth::id(),
                    ]);
                    $mediaId = $media->id;
                }
            } elseif ($mediaTypeChoice === 'file') {
                if ($request->hasFile('file')) {
                    if ($material->media && $material->media->file_path) {
                        Storage::disk('public')->delete($material->media->file_path);
                    }

                    $uploadedFile = $request->file('file');
                    $filePath = $uploadedFile->store('materials', 'public');

                    if ($material->media) {
                        $material->media->update([
                            'type' => 'document',
                            'file_name' => $uploadedFile->getClientOriginalName(),
                            'file_path' => $filePath,
                            'external_url' => null,
                            'mime_type' => $uploadedFile->getMimeType(),
                            'file_size' => $uploadedFile->getSize(),
                        ]);
                    } else {
                        $media = Media::create([
                            'type' => 'document',
                            'file_name' => $uploadedFile->getClientOriginalName(),
                            'file_path' => $filePath,
                            'mime_type' => $uploadedFile->getMimeType(),
                            'file_size' => $uploadedFile->getSize(),
                            'uploaded_by' => Auth::id(),
                        ]);
                        $mediaId = $media->id;
                    }
                }
            } else {
                if ($material->media) {
                    $oldMedia = $material->media;
                    if ($oldMedia->file_path) {
                        Storage::disk('public')->delete($oldMedia->file_path);
                    }
                    $mediaId = null;
                    $material->update(['media_id' => null]);
                    $oldMedia->delete();
                }
            }

            $material->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'content' => $validated['content'] ?? null,
                'media_id' => $mediaId,
                'sort_order' => $validated['sort_order'] ?? $material->sort_order,
            ]);
        });

        return redirect()->route('teacher.courses.show', $material->meeting->course_id)
            ->with('success', 'Materi berhasil diperbarui!');
    }

    /**
     * Hapus materi dan hitung ulang progres seluruh peserta aktif.
     */
    public function destroy(Material $material)
    {
        $courseId = $material->meeting->course_id;

        DB::transaction(function () use ($material, $courseId) {
            $media = $material->media;
            $material->delete();

            if ($media) {
                if ($media->file_path) {
                    Storage::disk('public')->delete($media->file_path);
                }
                $media->delete();
            }

            // Hitung ulang progres seluruh peserta aktif setelah materi dihapus
            CourseProgressHelper::recalculateAllActiveStudentsProgress($courseId);
        });

        return redirect()->route('teacher.courses.show', $courseId)
            ->with('success', 'Materi berhasil dihapus dan progres peserta telah disesuaikan.');
    }
}