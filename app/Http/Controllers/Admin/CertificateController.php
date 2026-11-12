<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CertificateHelper;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class CertificateController extends Controller
{
    /**
     * Halaman Utama Manajemen Sertifikat Admin.
     */
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $courseId = $request->input('course_id');
        $status   = $request->input('status');

        $courses = Course::orderBy('title', 'asc')->get();

        $certificates = Certificate::with([
            'enrollment.user',
            'enrollment.course',
            'media',
            'signedMedia',
            'approver',
        ])
        ->when($courseId, function ($query) use ($courseId) {
            $query->whereHas('enrollment', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        })
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                  ->orWhere('verification_code', 'like', "%{$search}%")
                  ->orWhereHas('enrollment.user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        })
        ->latest()
        ->paginate(15)
        ->withQueryString();

        return view('admin.certificates.index', compact('certificates', 'courses', 'search', 'courseId', 'status'));
    }

    /**
     * Toggle Status Bolak-Balik: 'draft' <-> 'published'.
     */
    public function toggleStatus(Certificate $certificate)
    {
        if ($certificate->status === 'draft') {
            CertificateHelper::publishCertificate($certificate, Auth::id());
            return back()->with('success', "Sertifikat {$certificate->certificate_number} berhasil dipublikasikan ke Student.");
        }

        CertificateHelper::setToDraft($certificate);
        return back()->with('success', "Status sertifikat {$certificate->certificate_number} dikembalikan menjadi Draft.");
    }

    /**
     * Perubahan Status Massal ('publish' atau 'draft').
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'certificate_ids'   => ['required', 'array', 'min:1'],
            'certificate_ids.*' => ['exists:certificates,id'],
            'target_status'     => ['required', 'in:draft,published'],
        ]);

        $certificates = Certificate::whereIn('id', $request->certificate_ids)->get();
        $count = 0;

        foreach ($certificates as $cert) {
            if ($request->target_status === 'published' && $cert->status !== 'published') {
                CertificateHelper::publishCertificate($cert, Auth::id());
                $count++;
            } elseif ($request->target_status === 'draft' && $cert->status !== 'draft') {
                CertificateHelper::setToDraft($cert);
                $count++;
            }
        }

        $label = $request->target_status === 'published' ? 'dipublikasikan' : 'dikembalikan ke Draft';
        return back()->with('success', "Sebanyak {$count} sertifikat berhasil {$label}.");
    }

    /**
     * Download Batch ZIP:
     * Mendukung download berdasarkan checkbox pilihan (selected_ids)
     * ATAU seluruh sertifikat draft dalam 1 course (course_id).
     */
    public function downloadZip(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);
        $courseId    = $request->input('course_id');

        $query = Certificate::with(['enrollment.user', 'enrollment.course', 'media', 'signedMedia']);

        if (!empty($selectedIds)) {
            $query->whereIn('id', $selectedIds);
            $zipPrefix = 'Selected_Sertifikat';
        } elseif (!empty($courseId)) {
            $course = Course::find($courseId);
            $query->whereHas('enrollment', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })->where('status', 'draft');
            $zipPrefix = 'Draft_Sertifikat_' . Str::slug($course->code ?? 'Course', '_');
        } else {
            return back()->with('error', 'Pilih minimal satu sertifikat atau pilih filter kelas terlebih dahulu untuk mengunduh ZIP.');
        }

        $certificates = $query->get();

        if ($certificates->isEmpty()) {
            return back()->with('error', 'Tidak ditemukan berkas sertifikat yang sesuai untuk diunduh.');
        }

        $zipFileName = "{$zipPrefix}_" . now()->format('Ymd_His') . '.zip';
        $tempDir     = storage_path('app/temp');
        $zipPath     = "{$tempDir}/{$zipFileName}";

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zip = new ZipArchive();
        $addedFiles = 0;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($certificates as $cert) {
                $activeMedia = $cert->active_media;

                if ($activeMedia && Storage::disk('public')->exists($activeMedia->file_path)) {
                    $fileAbsolute = Storage::disk('public')->path($activeMedia->file_path);

                    // Format nama file: Sertifikat_[KODE]_[ID]_[NamaStudent].pdf
                    // ID diletakkan di depan agar memudahkan pencocokan otomatis saat Bulk Upload
                    $courseCode  = Str::slug($cert->enrollment->course->code ?? 'CERT', '_');
                    $studentName = Str::slug($cert->enrollment->user->name ?? 'Student', '_');
                    $insideName  = "Sertifikat_{$courseCode}_{$cert->id}_{$studentName}.pdf";

                    $zip->addFile($fileAbsolute, $insideName);
                    $addedFiles++;
                }
            }
            $zip->close();
        } else {
            return back()->with('error', 'Gagal membuat arsip ZIP.');
        }

        if ($addedFiles === 0) {
            @unlink($zipPath);
            return back()->with('error', 'Berkas fisik PDF tidak ditemukan di storage.');
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Upload Satuan Sertifikat Ber-TTD BPJPH untuk 1 siswa.
     */
    public function uploadSigned(Request $request, Certificate $certificate)
    {
        $request->validate([
            'signed_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'signed_pdf.required' => 'Berkas PDF bertanda tangan wajib dipilih.',
            'signed_pdf.mimes'    => 'Format file harus berupa PDF.',
            'signed_pdf.max'      => 'Ukuran maksimal file adalah 10MB.',
        ]);

        $autoPublish = $request->boolean('auto_publish', true);
        $this->storeSignedPdfFile($certificate, $request->file('signed_pdf')->getRealPath(), $request->file('signed_pdf')->getClientOriginalName(), $autoPublish);

        $statusMsg = $autoPublish ? 'dan langsung dipublikasikan ke Student' : 'dengan status Draft';
        return back()->with('success', "Sertifikat ber-TTD untuk {$certificate->enrollment->user->name} berhasil diunggah {$statusMsg}.");
    }

    /**
     * Bulk Upload Sertifikat Ber-TTD BPJPH:
     * Mendukung unggah banyak file PDF sekaligus ATAU 1 file arsip ZIP.
     */
    public function bulkUploadSigned(Request $request)
    {
        $request->validate([
            'signed_files'   => ['required', 'array', 'min:1'],
            'signed_files.*' => ['file', 'mimes:pdf,zip', 'max:51200'], // Max 50MB per file/ZIP
        ], [
            'signed_files.required' => 'Pilih minimal satu file PDF atau arsip ZIP.',
            'signed_files.*.mimes'  => 'Hanya file berformat PDF atau ZIP yang diizinkan.',
        ]);

        $autoPublish   = $request->boolean('auto_publish', true);
        $matchedCount  = 0;
        $unmatchedList = [];

        foreach ($request->file('signed_files') as $uploadedFile) {
            $ext = strtolower($uploadedFile->getClientOriginalExtension());

            if ($ext === 'pdf') {
                $originalName = $uploadedFile->getClientOriginalName();
                $cert = $this->matchCertificateFromFilename($originalName);

                if ($cert) {
                    $this->storeSignedPdfFile($cert, $uploadedFile->getRealPath(), $originalName, $autoPublish);
                    $matchedCount++;
                } else {
                    $unmatchedList[] = $originalName;
                }
            } elseif ($ext === 'zip') {
                $zip = new ZipArchive();
                if ($zip->open($uploadedFile->getRealPath()) === true) {
                    $extractDir = storage_path('app/temp/extract_' . Str::random(10));
                    mkdir($extractDir, 0755, true);
                    $zip->extractTo($extractDir);
                    $zip->close();

                    // Telusuri seluruh file PDF di dalam hasil ekstrak ZIP
                    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($extractDir));
                    foreach ($iterator as $fileInfo) {
                        if ($fileInfo->isFile() && strtolower($fileInfo->getExtension()) === 'pdf') {
                            $pdfName = $fileInfo->getFilename();
                            // Abaikan file metadata macOS (__MACOSX / ._)
                            if (str_starts_with($pdfName, '._')) {
                                continue;
                            }

                            $cert = $this->matchCertificateFromFilename($pdfName);
                            if ($cert) {
                                $this->storeSignedPdfFile($cert, $fileInfo->getPathname(), $pdfName, $autoPublish);
                                $matchedCount++;
                            } else {
                                $unmatchedList[] = $pdfName;
                            }
                        }
                    }

                    // Bersihkan direktori temp
                    $this->deleteDirectory($extractDir);
                }
            }
        }

        $message = "Berhasil memproses dan menautkan {$matchedCount} sertifikat bertanda tangan.";
        if (!empty($unmatchedList)) {
            $unmatchedStr = implode(', ', array_slice($unmatchedList, 0, 5));
            $moreCount    = count($unmatchedList) > 5 ? ' dan ' . (count($unmatchedList) - 5) . ' file lainnya' : '';
            $message     .= " File tidak cocok dengan data sertifikat: [{$unmatchedStr}{$moreCount}]. Pastikan nama file mengandung ID atau Nomor Sertifikat.";
        }

        return back()->with($matchedCount > 0 ? 'success' : 'error', $message);
    }

    /**
     * Regenerate ulang PDF Sertifikat dari template.
     */
    public function regenerate(Certificate $certificate)
    {
        CertificateHelper::regenerateCertificate($certificate);

        return back()->with('success', "File PDF Sertifikat {$certificate->certificate_number} berhasil di-regenerate.");
    }

    /**
     * Helper internal: Mencocokkan nama file PDF dengan record Certificate di database.
     * Mendukung pola:
     * 1. Sertifikat_[KODE]_[ID]_[Nama].pdf (format bawaan Download ZIP)
     * 2. Nama file yang mengandung Nomor Sertifikat (misal: CERT-SJH-2026-202611-0001)
     */
    private function matchCertificateFromFilename(string $filename): ?Certificate
    {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);

        // Pola 1: Format bawaan downloadZip -> Sertifikat_{courseCode}_{id}_{studentName}
        if (preg_match('/^Sertifikat_[a-zA-Z0-9\-]+_(\d+)_/i', $baseName, $matches)) {
            $cert = Certificate::find((int) $matches[1]);
            if ($cert) {
                return $cert;
            }
        }

        // Pola 2: Cocokkan berdasarkan Nomor Sertifikat (normalisasi / menjadi - atau _)
        $normalizedFile = strtoupper(str_replace(['/', '\\', '_', ' '], '-', $baseName));
        $allCertificates = Certificate::all(['id', 'certificate_number']);

        foreach ($allCertificates as $item) {
            $normalizedNumber = strtoupper(str_replace(['/', '\\', '_', ' '], '-', $item->certificate_number));
            if (str_contains($normalizedFile, $normalizedNumber)) {
                return Certificate::find($item->id);
            }
        }

        return null;
    }

    /**
     * Helper internal: Menyimpan file PDF bertanda tangan ke storage & tabel media.
     */
    private function storeSignedPdfFile(Certificate $certificate, string $sourceFilePath, string $originalName, bool $autoPublish): void
    {
        $certificate->load(['enrollment.user', 'signedMedia']);

        // Hapus file signed lama jika ada
        if ($certificate->signed_media_id && $certificate->signedMedia) {
            if (Storage::disk('public')->exists($certificate->signedMedia->file_path)) {
                Storage::disk('public')->delete($certificate->signedMedia->file_path);
            }
            $oldSigned = $certificate->signedMedia;
            $certificate->update(['signed_media_id' => null]);
            $oldSigned->delete();
        }

        $studentName = Str::slug($certificate->enrollment->user->name ?? 'Student', '_');
        $timestamp   = now()->format('YmdHis');
        $fileName    = "Signed_Sertifikat_{$certificate->id}_{$studentName}_{$timestamp}.pdf";
        $storagePath = "certificates/signed/{$fileName}";

        $fileContent = file_get_contents($sourceFilePath);
        Storage::disk('public')->put($storagePath, $fileContent);

        $fileSize = Storage::disk('public')->exists($storagePath)
            ? Storage::disk('public')->size($storagePath)
            : strlen($fileContent);

        $media = Media::create([
            'type'        => 'document',
            'file_name'   => $fileName,
            'file_path'   => $storagePath,
            'mime_type'   => 'application/pdf',
            'file_size'   => $fileSize,
            'uploaded_by' => Auth::id(),
        ]);

        $updateData = [
            'signed_media_id' => $media->id,
        ];

        if ($autoPublish) {
            $updateData['status']      = 'published';
            $updateData['approved_by'] = Auth::id();
            $updateData['issued_at']   = $certificate->issued_at ?? now();
        }

        $certificate->update($updateData);
    }

    /**
     * Helper internal: Hapus folder sementara hasil ekstrak ZIP secara rekursif.
     */
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "{$dir}/{$file}";
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }
        @rmdir($dir);
    }
}