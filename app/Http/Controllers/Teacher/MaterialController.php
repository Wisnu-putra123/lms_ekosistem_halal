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
            'content' => 'nullable|string', // Isi Teks / Trix / CKEditor
            'external_url' => 'nullable|url', // Link Drive, YouTube, dll.
            'sort_order' => 'nullable|integer',
        ], [
            'title.required' => 'Judul materi wajib diisi.',
            'external_url.url' => 'Format URL eksternal tidak valid.',
        ]);

        DB::transaction(function () use ($validated, $meeting, $request) {
            $mediaId = null;

            // Jika menginput URL Media / Embedded Link
            if (!empty($validated['external_url'])) {
                $embedUrl = EmbedHelper::formatToEmbedUrl($validated['external_url']);

                $media = Media::create([
                    'type' => 'embed',
                    'file_name' => 'Embedded Content: ' . $validated['title'],
                    'external_url' => $embedUrl,
                    'uploaded_by' => Auth::id(),
                ]);

                $mediaId = $media->id;
            }

            // Hitung urutan materi jika tidak diisi
            $sortOrder = $validated['sort_order'] ?? ($meeting->materials()->max('sort_order') + 1);

            // Simpan ke tabel materials
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
        $material->load(['meeting', 'media']);
        return view('teacher.materials.show', compact('material'));
    }

    /**
     * Form edit materi.
     */
    public function edit(Material $material)
    {
        $material->load('media');
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
            'external_url' => 'nullable|url',
            'sort_order' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($validated, $material) {
            $mediaId = $material->media_id;

            if (!empty($validated['external_url'])) {
                $embedUrl = EmbedHelper::formatToEmbedUrl($validated['external_url']);

                if ($material->media) {
                    // Update media lama
                    $material->media->update([
                        'external_url' => $embedUrl,
                        'file_name' => 'Embedded Content: ' . $validated['title'],
                    ]);
                } else {
                    // Buat record media baru
                    $media = Media::create([
                        'type' => 'embed',
                        'file_name' => 'Embedded Content: ' . $validated['title'],
                        'external_url' => $embedUrl,
                        'uploaded_by' => Auth::id(),
                    ]);
                    $mediaId = $media->id;
                }
            } else {
                // Jika URL dihapus, hapus relasi media-nya
                if ($material->media) {
                    $oldMedia = $material->media;
                    $mediaId = null;
                    $material->update(['media_id' => null]);
                    $oldMedia->delete();
                }
            }

            // Update tabel materials
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
                $media->delete();
            }
        });

        return redirect()->route('teacher.courses.show', $courseId)
            ->with('success', 'Materi berhasil dihapus.');
    }
}