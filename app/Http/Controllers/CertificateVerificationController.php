<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    /**
     * Tampilkan halaman formulir verifikasi publik atau hasil verifikasi berdasarkan kode.
     */
    public function verify(Request $request, $code = null)
    {
        // Ambil kode dari parameter URL atau input query string
        $searchCode = trim($code ?? $request->query('code', ''));

        $certificate = null;
        $searched = false;

        if (!empty($searchCode)) {
            $searched = true;

            // Cari sertifikat berdasarkan verification_code atau certificate_number
            $certificate = Certificate::with([
                'enrollment.course',
                'enrollment.user',
            ])
            ->where('verification_code', $searchCode)
            ->orWhere('certificate_number', $searchCode)
            ->first();
        }

        return view('certificates.verify', compact('certificate', 'searchCode', 'searched'));
    }
}