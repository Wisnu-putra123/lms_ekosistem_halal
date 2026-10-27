<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Tampilkan daftar seluruh sertifikat milik Student yang sedang login.
     */
    public function index()
    {
        $userId = Auth::id();

        // Ambil semua sertifikat milik student melalui relasi enrollment
        $certificates = Certificate::with(['enrollment.course.thumbnail', 'enrollment.course.certificateBackground'])
            ->whereHas('enrollment', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest('issued_at')
            ->get();

        return view('student.certificates.index', compact('certificates'));
    }

    /**
     * Tampilkan detail/pratinjau sertifikat digital tertentu.
     */
    public function show($id)
    {
        $userId = Auth::id();

        // Cari sertifikat dan pastikan hanya pemilik sertifikat yang dapat mengaksesnya
        $certificate = Certificate::with([
            'enrollment.course.certificateBackground',
            'enrollment.user',
        ])
        ->whereHas('enrollment', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->findOrFail($id);

        return view('student.certificates.show', compact('certificate'));
    }
}