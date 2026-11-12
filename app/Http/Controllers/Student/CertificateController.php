<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * Tampilkan daftar seluruh sertifikat milik Student yang sedang login.
     */
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua sertifikat ('draft' maupun 'published') milik student
        $certificates = Certificate::with([
            'enrollment.course.thumbnail',
            'enrollment.course.certificateBackground',
            'media',
            'signedMedia',
        ])
        ->whereHas('enrollment', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->latest('created_at')
        ->get();

        return view('student.certificates.index', compact('certificates'));
    }

    /**
     * Tampilkan detail/pratinjau sertifikat digital tertentu.
     */
    public function show($id)
    {
        $userId = Auth::id();

        $certificate = Certificate::with([
            'enrollment.course.certificateBackground',
            'enrollment.user',
            'media',
            'signedMedia',
        ])
        ->whereHas('enrollment', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->findOrFail($id);

        // Proteksi: Cegah akses halaman pratinjau jika status masih 'draft' (atau ditarik kembali ke 'draft')
        if (!$certificate->isPublished()) {
            return redirect()
                ->route('student.certificates.index')
                ->with('error', 'Sertifikat Anda saat ini berstatus Draft atau sedang dalam proses verifikasi/penandatanganan BPJPH.');
        }

        return view('student.certificates.show', compact('certificate'));
    }

    /**
     * Unduh berkas PDF Sertifikat resmi yang berstatus 'published'.
     */
    public function download($id)
    {
        $userId = Auth::id();

        $certificate = Certificate::with([
            'enrollment.course',
            'enrollment.user',
            'media',
            'signedMedia',
        ])
        ->whereHas('enrollment', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->findOrFail($id);

        // Proteksi: Hanya sertifikat berstatus 'published' yang dapat diunduh
        if (!$certificate->isPublished()) {
            return back()->with('error', 'Sertifikat belum dipublikasikan dan belum dapat diunduh.');
        }

        // Ambil media aktif (memprioritaskan signedMedia dari BPJPH jika tersedia, fallback ke media generated)
        $activeMedia = $certificate->active_media;

        if (!$activeMedia || !Storage::disk('public')->exists($activeMedia->file_path)) {
            return back()->with('error', 'Berkas PDF sertifikat tidak ditemukan di server. Silakan hubungi admin.');
        }

        // Bersihkan cache status file PHP agar selalu membaca byte terbaru
        clearstatcache(true);

        // Buat nama file unduhan bersih + version tag waktu update media agar tidak bentrok dengan cache browser/Downloads
        $studentName  = Str::slug($certificate->enrollment->user->name ?? 'Student', '_');
        $courseCode   = Str::slug($certificate->enrollment->course->code ?? 'COURSE', '_');
        $versionTag   = $activeMedia->updated_at ? $activeMedia->updated_at->format('YmdHis') : now()->format('YmdHis');
        $downloadName = "Sertifikat_{$courseCode}_{$studentName}_{$versionTag}.pdf";

        return Storage::disk('public')->download($activeMedia->file_path, $downloadName, [
            'Content-Type'  => 'application/pdf',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => 'Sat, 01 Jan 2000 00:00:00 GMT',
        ]);
    }
}