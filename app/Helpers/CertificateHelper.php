<?php

namespace App\Helpers;

use App\Models\Certificate;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Media;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateHelper
{
    /**
     * Otomatis membuat draf sertifikat saat enrollment selesai (progress = 100%).
     * Sertifikat awal berstatus 'draft' dan PDF overlay langsung di-generate.
     */
    public static function issueCertificate(int $enrollmentId): ?Certificate
    {
        $enrollment = Enrollment::with(['course.certificateBackground', 'user', 'certificate'])->find($enrollmentId);

        if (!$enrollment) {
            return null;
        }

        // Cek jika sertifikat sudah pernah dibuat sebelumnya
        if ($enrollment->certificate) {
            return $enrollment->certificate;
        }

        // Pastikan progres belajar siswa benar-benar sudah >= 100%
        $progressRecord = CourseProgress::where('enrollment_id', $enrollmentId)->first();
        $progress = $progressRecord ? (float) $progressRecord->progress_percentage : 0.0;

        if ($progress < 100.0) {
            return null;
        }

        $certificateNumber = static::generateCertificateNumber($enrollment);
        $verificationCode  = static::generateVerificationCode();

        // Buat record sertifikat sesuai skema migration baru (tanpa user_id & course_id langsung)
        $certificate = Certificate::create([
            'enrollment_id'      => $enrollment->id,
            'certificate_number' => $certificateNumber,
            'verification_code'  => $verificationCode,
            'issued_at'          => now(),
            'status'             => 'draft',
        ]);

        // Render PDF sertifikat awal
        static::renderCertificatePdf($certificate);

        return $certificate;
    }

    /**
     * Ubah status sertifikat menjadi 'published' (Terbit ke Student).
     */
    public static function publishCertificate(Certificate $certificate, int $approvedById): bool
    {
        return $certificate->update([
            'status'      => 'published',
            'approved_by' => $approvedById,
            'issued_at'   => $certificate->issued_at ?? now(),
        ]);
    }

    /**
     * Kembalikan status sertifikat menjadi 'draft' (Unpublish / Tarik Kembali).
     */
    public static function setToDraft(Certificate $certificate): bool
    {
        return $certificate->update([
            'status'      => 'draft',
            'approved_by' => null,
        ]);
    }

    /**
     * Regenerate file PDF sertifikat dari ulang (menghapus media lama & membuat file baru).
     */
    public static function regenerateCertificate(Certificate $certificate): Certificate
    {
        static::renderCertificatePdf($certificate);

        return $certificate->fresh();
    }

    /**
     * Render PDF sertifikat dengan overlay teks di atas gambar template background.
     */
    public static function renderCertificatePdf(Certificate $certificate): void
    {
        $certificate->load([
            'enrollment.user',
            'enrollment.course.certificateBackground',
            'media',
            'signedMedia',
        ]);

        $enrollment = $certificate->enrollment;
        $user       = $enrollment->user;
        $course     = $enrollment->course;

        // 1. Bersihkan file & record media lama (baik generated maupun signed) sebelum membuat yang baru
        if ($certificate->certificate_media_id && $certificate->media) {
            if (Storage::disk('public')->exists($certificate->media->file_path)) {
                Storage::disk('public')->delete($certificate->media->file_path);
            }
            $oldMedia = $certificate->media;
            $certificate->update(['certificate_media_id' => null]);
            $oldMedia->delete();
        }

        if ($certificate->signed_media_id && $certificate->signedMedia) {
            if (Storage::disk('public')->exists($certificate->signedMedia->file_path)) {
                Storage::disk('public')->delete($certificate->signedMedia->file_path);
            }
            $oldSignedMedia = $certificate->signedMedia;
            $certificate->update(['signed_media_id' => null]);
            $oldSignedMedia->delete();
        }

        // 2. Ambil path fisik background template secara akurat menggunakan Storage::disk('public')->path()
        $bgPath = null;
        if ($course->certificateBackground && Storage::disk('public')->exists($course->certificateBackground->file_path)) {
            $bgPath = Storage::disk('public')->path($course->certificateBackground->file_path);
        }

        // 3. Siapkan data teks overlay untuk view PDF
        $data = [
            'certificateNumber' => $certificate->certificate_number,
            'studentName'       => $user->name,
            'courseTitle'       => $course->title,
            'issuedDate'        => $certificate->issued_at ? $certificate->issued_at->format('d F Y') : now()->format('d F Y'),
            'verificationCode'  => $certificate->verification_code,
            'bgPath'            => $bgPath,
        ];

        // 4. Render HTML ke PDF (A4 Landscape)
        $pdf = Pdf::loadView('certificates.pdf_template', $data)
            ->setPaper('a4', 'landscape')
            ->setOption(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        // 5. Penamaan file PDF unik dengan akhiran timestamp agar bebas dari cache browser/OS
        $cleanStudentName = Str::slug($user->name, '_');
        $cleanCourseCode  = Str::slug($course->code ?? 'COURSE', '_');
        $timestamp        = now()->format('YmdHis');
        $fileName         = "Sertifikat_{$cleanCourseCode}_{$cleanStudentName}_{$certificate->id}_{$timestamp}.pdf";
        $storagePath      = "certificates/{$fileName}";

        // 6. Simpan ke disk 'public'
        $pdfOutput = $pdf->output();
        Storage::disk('public')->put($storagePath, $pdfOutput);

        $fileSize = Storage::disk('public')->exists($storagePath)
            ? Storage::disk('public')->size($storagePath)
            : strlen($pdfOutput);

        // 7. Catat file ke tabel media
        $media = Media::create([
            'type'        => 'document',
            'file_name'   => $fileName,
            'file_path'   => $storagePath,
            'mime_type'   => 'application/pdf',
            'file_size'   => $fileSize,
            'uploaded_by' => $user->id,
        ]);

        // 8. Tautkan media baru ke sertifikat
        $certificate->update([
            'certificate_media_id' => $media->id,
        ]);
    }

    /**
     * Generate nomor sertifikat resmi berurutan per bulan.
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
     * Generate kode verifikasi unik 12 karakter (XXXX-XXXX-XXXX).
     */
    public static function generateVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (Certificate::where('verification_code', $code)->exists());

        return $code;
    }
}