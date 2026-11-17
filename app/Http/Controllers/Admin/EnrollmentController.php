<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CourseProgressHelper;
use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    /**
     * Menampilkan daftar seluruh enrollment peserta beserta filter & modal pendaftaran manual.
     */
    public function index(Request $request)
    {
        $query = Enrollment::with([
            'user:id,name,email,nik,institution',
            'course:id,title,code,category,status',
            'progress',
            'certificate:id,enrollment_id,certificate_number,status',
        ]);

        // Filter berdasarkan pelatihan
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter berdasarkan status enrollment (active, completed, dropped)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan status masa akses (access_expires_at)
        if ($request->filled('access_status')) {
            if ($request->access_status === 'expired') {
                $query->whereNotNull('access_expires_at')
                      ->where('access_expires_at', '<', now());
            } elseif ($request->access_status === 'active_limited') {
                $query->whereNotNull('access_expires_at')
                      ->where('access_expires_at', '>=', now());
            } elseif ($request->access_status === 'unlimited') {
                $query->whereNull('access_expires_at');
            }
        }

        // Pencarian berdasarkan nama, email, NIK peserta, atau judul/kode pelatihan
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('nik', 'like', "%{$search}%");
                })->orWhereHas('course', function ($cq) use ($search) {
                    $cq->where('title', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        $enrollments = $query->latest('enrolled_at')
            ->paginate(15)
            ->withQueryString();

        // Data pendukung untuk filter & form manual enrollment
        $courses = Course::orderBy('title')->get(['id', 'title', 'code', 'status']);
        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'nik']);

        // Statistik ringkas
        $stats = [
            'total' => Enrollment::count(),
            'active' => Enrollment::where('status', 'active')->count(),
            'completed' => Enrollment::where('status', 'completed')->count(),
            'expired' => Enrollment::whereNotNull('access_expires_at')
                ->where('access_expires_at', '<', now())
                ->count(),
        ];

        return view('admin.enrollments.index', compact(
            'enrollments',
            'courses',
            'students',
            'stats'
        ));
    }

    /**
     * Mendaftarkan peserta secara manual oleh Admin ke dalam pelatihan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
            'access_expires_at' => ['nullable', 'date'],
            'allow_re_enroll' => ['nullable', 'boolean'],
        ], [
            'course_id.required' => 'Pelatihan wajib dipilih.',
            'user_ids.required' => 'Pilih minimal satu peserta.',
            'access_expires_at.date' => 'Format tanggal batas akses tidak valid.',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $allowReEnroll = (bool) ($validated['allow_re_enroll'] ?? false);

        $enrolledCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($validated, $course, $allowReEnroll, &$enrolledCount, &$skippedCount) {
            foreach ($validated['user_ids'] as $userId) {
                // Cek apakah user masih memiliki enrollment aktif pada course yang sama
                $hasActiveEnrollment = Enrollment::where('course_id', $course->id)
                    ->where('user_id', $userId)
                    ->where('status', 'active')
                    ->exists();

                if ($hasActiveEnrollment && !$allowReEnroll) {
                    $skippedCount++;
                    continue;
                }

                $enrollment = Enrollment::create([
                    'course_id' => $course->id,
                    'user_id' => $userId,
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'completed_at' => null,
                    'access_expires_at' => $validated['access_expires_at'] ?? null,
                ]);

                CourseProgress::create([
                    'enrollment_id' => $enrollment->id,
                    'progress_percentage' => 0,
                    'last_activity_at' => now(),
                ]);

                $enrolledCount++;
            }
        });

        if ($enrolledCount === 0) {
            return back()->with('error', 'Tidak ada peserta baru yang didaftarkan (peserta terpilih sudah berstatus aktif di pelatihan ini).');
        }

        $message = "{$enrolledCount} peserta berhasil didaftarkan ke pelatihan \"{$course->title}\".";
        if ($skippedCount > 0) {
            $message .= " ({$skippedCount} peserta dilewati karena sudah terdaftar aktif.)";
        }

        return back()->with('success', $message);
    }

    /**
     * Menampilkan detail enrollment, pengaturan waktu akses, file submission, & feedback.
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load([
            'user',
            'course.meetings' => function ($query) {
                $query->orderBy('meeting_number');
            },
            'course.meetings.materials' => function ($query) {
                $query->orderBy('sort_order');
            },
            'course.meetings.assignments' => function ($query) {
                $query->orderBy('id');
            },
            'course.meetings.assignments.attachments.media',
            'course.meetings.assignments.quiz',
            'progress',
            'certificate',
        ]);

        // Ambil seluruh submission tugas milik enrollment ini beserta lampiran jawaban & feedback
        $submissions = AssignmentSubmission::with([
            'assignment',
            'grader:id,name',
            'attachments.media',
            'feedbackAttachments.media',
        ])
            ->where('enrollment_id', $enrollment->id)
            ->orderByDesc('attempt_number')
            ->get()
            ->groupBy('assignment_id');

        // Ambil seluruh quiz attempts milik enrollment ini
        $quizAttempts = QuizAttempt::with('quiz.assignment')
            ->where('enrollment_id', $enrollment->id)
            ->orderByDesc('attempt_number')
            ->get()
            ->groupBy('quiz_id');

        return view('admin.enrollments.show', compact(
            'enrollment',
            'submissions',
            'quizAttempts'
        ));
    }

    /**
     * Memperbarui status enrollment dan batas waktu akses (access_expires_at).
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'completed', 'dropped'])],
            'access_mode' => ['required', Rule::in(['unlimited', 'limited'])],
            'access_expires_at' => ['required_if:access_mode,limited', 'nullable', 'date'],
        ], [
            'access_expires_at.required_if' => 'Tanggal & waktu berakhir akses wajib diisi jika mode akses berbatas waktu dipilih.',
        ]);

        $expiresAt = $validated['access_mode'] === 'limited'
            ? $validated['access_expires_at']
            : null;

        $completedAt = $enrollment->completed_at;
        if ($validated['status'] === 'completed' && !$completedAt) {
            $completedAt = now();
        } elseif ($validated['status'] !== 'completed') {
            $completedAt = null;
        }

        $enrollment->update([
            'status' => $validated['status'],
            'completed_at' => $completedAt,
            'access_expires_at' => $expiresAt,
        ]);

        // Sinkronkan progres jika ditandai completed
        if ($validated['status'] === 'completed' && $enrollment->progress) {
            $enrollment->progress->update([
                'progress_percentage' => 100,
                'completed_at' => $completedAt,
            ]);
        }

        return back()->with('success', 'Status kepesertaan dan waktu akses berhasil diperbarui.');
    }

    /**
     * Mengatur atau memperpanjang waktu akses (access_expires_at) secara massal per pelatihan.
     */
    public function bulkAccess(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'access_mode' => ['required', Rule::in(['unlimited', 'limited'])],
            'access_expires_at' => ['required_if:access_mode,limited', 'nullable', 'date'],
            'only_active' => ['nullable', 'boolean'],
        ], [
            'course_id.required' => 'Pelatihan wajib dipilih.',
            'access_expires_at.required_if' => 'Tanggal batas akses wajib diisi.',
        ]);

        $query = Enrollment::where('course_id', $validated['course_id']);

        if ($request->boolean('only_active', true)) {
            $query->where('status', 'active');
        }

        $expiresAt = $validated['access_mode'] === 'limited'
            ? $validated['access_expires_at']
            : null;

        $affected = $query->update([
            'access_expires_at' => $expiresAt,
        ]);

        return back()->with('success', "Waktu akses berhasil diperbarui untuk {$affected} peserta.");
    }

    /**
     * Menghitung ulang persentase progres belajar peserta.
     */
    public function recalculateProgress(Enrollment $enrollment)
    {
        if (class_exists(CourseProgressHelper::class) && method_exists(CourseProgressHelper::class, 'recalculate')) {
            CourseProgressHelper::recalculate($enrollment);
        } elseif (class_exists(CourseProgressHelper::class) && method_exists(CourseProgressHelper::class, 'updateProgress')) {
            CourseProgressHelper::updateProgress($enrollment);
        }

        return back()->with('success', 'Progres belajar peserta berhasil dihitung ulang.');
    }

    /**
     * Menghapus / mereset attempt submission tugas tertentu (Admin Override).
     */
    public function resetSubmission(Enrollment $enrollment, AssignmentSubmission $submission)
    {
        abort_if($submission->enrollment_id !== $enrollment->id, 404);

        DB::transaction(function () use ($submission, $enrollment) {
            // Hapus lampiran jawaban peserta
            foreach ($submission->attachments as $attachment) {
                $media = $attachment->media;
                $attachment->delete();
                if ($media && $media->file_path && Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                    $media->delete();
                }
            }

            // Hapus lampiran feedback instruktur
            foreach ($submission->feedbackAttachments as $fbAttachment) {
                $media = $fbAttachment->media;
                $fbAttachment->delete();
                if ($media && $media->file_path && Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                    $media->delete();
                }
            }

            $submission->delete();

            if (class_exists(CourseProgressHelper::class) && method_exists(CourseProgressHelper::class, 'recalculate')) {
                CourseProgressHelper::recalculate($enrollment);
            }
        });

        return back()->with('success', 'Submission tugas berhasil direset sehingga peserta dapat mengumpulkan ulang.');
    }

    /**
     * Menghapus / mereset attempt kuis tertentu (Admin Override).
     */
    public function resetQuizAttempt(Enrollment $enrollment, QuizAttempt $attempt)
    {
        abort_if($attempt->enrollment_id !== $enrollment->id, 404);

        DB::transaction(function () use ($attempt, $enrollment) {
            $attempt->attemptQuestions()->delete();
            $attempt->delete();

            if (class_exists(CourseProgressHelper::class) && method_exists(CourseProgressHelper::class, 'recalculate')) {
                CourseProgressHelper::recalculate($enrollment);
            }
        });

        return back()->with('success', 'Percobaan kuis berhasil direset sehingga peserta dapat mengerjakan ulang.');
    }

    /**
     * Menghapus seluruh file fisik submission & feedback milik enrollment tertentu
     * (tanpa menghapus data nilai / riwayat enrollment).
     */
    public function purgeFiles(Enrollment $enrollment)
    {
        $deletedCount = $this->deleteEnrollmentFiles($enrollment);

        if ($deletedCount === 0) {
            return back()->with('error', 'Tidak ada berkas submission atau feedback yang tersimpan untuk peserta ini.');
        }

        return back()->with('success', "Berhasil membersihkan {$deletedCount} berkas submission & feedback dari penyimpanan.");
    }

    /**
     * Membersihkan file fisik submission & feedback secara massal untuk enrollment
     * yang masa aksesnya (access_expires_at) sudah kedaluwarsa.
     */
    public function purgeExpiredFiles(Request $request)
    {
        $query = Enrollment::whereNotNull('access_expires_at')
            ->where('access_expires_at', '<', now());

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $totalFilesDeleted = 0;
        $affectedEnrollments = 0;

        $query->chunkById(100, function ($enrollments) use (&$totalFilesDeleted, &$affectedEnrollments) {
            foreach ($enrollments as $enrollment) {
                $count = $this->deleteEnrollmentFiles($enrollment);
                if ($count > 0) {
                    $totalFilesDeleted += $count;
                    $affectedEnrollments++;
                }
            }
        });

        if ($totalFilesDeleted === 0) {
            return back()->with('error', 'Tidak ditemukan berkas tersisa pada kepesertaan yang sudah kedaluwarsa.');
        }

        return back()->with('success', "Pembersihan selesai: {$totalFilesDeleted} berkas dihapus dari {$affectedEnrollments} kepesertaan yang kedaluwarsa.");
    }

    /**
     * Menghapus data enrollment beserta seluruh file fisiknya (jika belum punya sertifikat resmi).
     */
    public function destroy(Enrollment $enrollment)
    {
        if ($enrollment->certificate && $enrollment->certificate->status === 'published') {
            return back()->with('error', 'Enrollment tidak dapat dihapus karena sertifikat resmi sudah diterbitkan.');
        }

        DB::transaction(function () use ($enrollment) {
            $this->deleteEnrollmentFiles($enrollment);
            $enrollment->delete();
        });

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Data enrollment dan seluruh berkas terkait berhasil dihapus.');
    }

    /**
     * Helper internal: menghapus seluruh file fisik & record lampiran submission/feedback
     * milik sebuah enrollment dengan aman (cek referensi media agar tidak menghapus file yang dipakai record lain).
     */
    private function deleteEnrollmentFiles(Enrollment $enrollment): int
    {
        $deletedFiles = 0;

        $submissions = AssignmentSubmission::with(['attachments.media', 'feedbackAttachments.media'])
            ->where('enrollment_id', $enrollment->id)
            ->get();

        DB::transaction(function () use ($submissions, &$deletedFiles) {
            foreach ($submissions as $submission) {
                // 1. Hapus lampiran jawaban peserta
                foreach ($submission->attachments as $attachment) {
                    $media = $attachment->media;
                    $attachment->delete();

                    if ($media && $this->deleteMediaIfUnused($media)) {
                        $deletedFiles++;
                    }
                }

                // 2. Hapus lampiran feedback instruktur
                foreach ($submission->feedbackAttachments as $fbAttachment) {
                    $media = $fbAttachment->media;
                    $fbAttachment->delete();

                    if ($media && $this->deleteMediaIfUnused($media)) {
                        $deletedFiles++;
                    }
                }
            }
        });

        return $deletedFiles;
    }

    /**
     * Menghapus file dari storage & tabel media hanya jika tidak lagi dirujuk lampiran lain.
     */
    private function deleteMediaIfUnused($media): bool
    {
        $stillUsedInSubmissions = \App\Models\SubmissionAttachment::where('media_id', $media->id)->exists();
        $stillUsedInFeedback = \App\Models\SubmissionFeedbackAttachment::where('media_id', $media->id)->exists();

        if ($stillUsedInSubmissions || $stillUsedInFeedback) {
            return false;
        }

        if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return true;
    }
}