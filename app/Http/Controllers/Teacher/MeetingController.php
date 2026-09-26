<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    /**
     * Simpan pertemuan baru di bawah suatu kursus.
     */
    public function store(Request $request, Course $course)
    {
        $this->authorizeAccess($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'meeting_number' => ['required', 'integer', 'min:1'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
        ], [
            'title.required' => 'Judul pertemuan wajib diisi.',
            'meeting_number.required' => 'Nomor pertemuan wajib diisi.',
            'available_until.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        $course->meetings()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'meeting_number' => $validated['meeting_number'],
            'available_from' => $validated['available_from'] ?? null,
            'available_until' => $validated['available_until'] ?? null,
        ]);

        return back()->with('success', 'Pertemuan baru berhasil ditambahkan!');
    }

    /**
     * Perbarui data pertemuan.
     */
    public function update(Request $request, Meeting $meeting)
    {
        $this->authorizeAccess($meeting->course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'meeting_number' => ['required', 'integer', 'min:1'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
        ], [
            'title.required' => 'Judul pertemuan wajib diisi.',
            'meeting_number.required' => 'Nomor pertemuan wajib diisi.',
            'available_until.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        $meeting->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'meeting_number' => $validated['meeting_number'],
            'available_from' => $validated['available_from'] ?? null,
            'available_until' => $validated['available_until'] ?? null,
        ]);

        return back()->with('success', 'Pertemuan berhasil diperbarui!');
    }

    /**
     * Hapus pertemuan beserta seluruh materi/tugas di dalamnya.
     */
    public function destroy(Meeting $meeting)
    {
        $this->authorizeAccess($meeting->course);

        $meeting->delete();

        return back()->with('success', 'Pertemuan berhasil dihapus.');
    }

    /**
     * Helper privat untuk mengecek akses pengajar.
     */
    private function authorizeAccess(Course $course): void
    {
        $userId = Auth::id();
        $isCreator = $course->created_by === $userId;
        $isInstructor = $course->instructors()->where('user_id', $userId)->exists();

        if (!$isCreator && !$isInstructor) {
            abort(403, 'Anda tidak memiliki akses ke kursus ini.');
        }
    }
}