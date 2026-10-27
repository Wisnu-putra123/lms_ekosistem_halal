<?php

namespace App\Helpers;

use App\Models\Certificate;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use Illuminate\Support\Str;

class CertificateHelper
{
    /**
     * Terbitkan sertifikat secara otomatis jika enrollment telah selesai ( progress = 100% ).
     *
     * @param int $enrollmentId
     * @return Certificate|null
     */
    public static function issueCertificate(int $enrollmentId): ?Certificate
    {
        // Load relasi course, user, certificate, DAN courseProgress
        $enrollment = Enrollment::with(['course', 'user', 'certificate', 'courseProgress'])->find($enrollmentId);

        if (!$enrollment) {
            return null;
        }

        // 1. Cek jika sertifikat sudah pernah diterbitkan sebelumnya
        if ($enrollment->certificate) {
            return $enrollment->certificate;
        }

        // 2. Ambil persentase dari relasi courseProgress (atau query langsung)
        $progress = $enrollment->courseProgress 
            ? (float) $enrollment->courseProgress->progress_percentage 
            : 0.0;

        // Pastikan progres belajar siswa benar-benar sudah >= 100%
        if ($progress < 100.0) {
            return null;
        }

        // Generate Nomor Sertifikat & Kode Verifikasi Unik
        $certificateNumber = static::generateCertificateNumber($enrollment);
        $verificationCode  = static::generateVerificationCode();

        // Buat record sertifikat baru di database
        $certificate = Certificate::create([
            'enrollment_id'      => $enrollment->id,
            'user_id'            => $enrollment->user_id,
            'course_id'          => $enrollment->course_id,
            'certificate_number' => $certificateNumber,
            'verification_code'  => $verificationCode,
            'issued_at'          => now(),
            'status'             => 'issued',
        ]);

        return $certificate;
    }

    /**
     * Generate format nomor sertifikat unik.
     */
    public static function generateCertificateNumber(Enrollment $enrollment): string
    {
        $courseCode = strtoupper($enrollment->course->code ?? 'GEN');
        $datePrefix = now()->format('Ym');
        
        $latestCount = Certificate::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;

        $sequence = str_pad((string) $latestCount, 4, '0', STR_PAD_LEFT);

        return "CERT/{$courseCode}/{$datePrefix}/{$sequence}";
    }

    /**
     * Generate kode unik acak untuk verifikasi keabsahan sertifikat publik.
     */
    public static function generateVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (Certificate::where('verification_code', $code)->exists());

        return $code;
    }
}