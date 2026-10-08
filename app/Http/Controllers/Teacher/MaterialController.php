<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Material;
use App\Models\Media;
use App\Helpers\EmbedHelper;
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
     * Simpan materi baru.
     */
    public function store(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'media_type' => 'nullable|in:file,embed',
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
        });

        return redirect()->route('teacher.courses.show', $meeting->course_id)
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail materi.
     */
    public function show(Material $material)
    {
        $material->load(['meeting.course', 'media']);
        return view('teacher.materials.show', compact('material'));
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

                // Hapus berkas file lama jika sebelumnya berbentuk file upload
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
                    // Hapus file fisik lama
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
                // Jika tidak memilih media / memilih hapus media
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
     * Hapus materi.
     */
    public function destroy(Material $material)
    {
        $courseId = $material->meeting->course_id;

        DB::transaction(function () use ($material) {
            $media = $material->media;
            $material->delete();

            if ($media) {
                if ($media->file_path) {
                    Storage::disk('public')->delete($media->file_path);
                }
                $media->delete();
            }
        });

        return redirect()->route('teacher.courses.show', $courseId)
            ->with('success', 'Materi berhasil dihapus.');
    }
}