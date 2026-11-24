<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Media;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    /**
     * Tampilkan form pembuatan Soal Baru untuk Quiz tertentu.
     */
    public function create(Assignment $assignment)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        return view('teacher.quizzes.questions.create', compact('assignment'));
    }

    /**
     * Simpan Soal Baru & Pilihan Jawabannya ke Bank Soal.
     */
    public function store(Request $request, Assignment $assignment)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        $request->validate([
            'question_type'    => 'required|in:multiple_choice,true_false',
            'question_text'    => 'required|string',
            'explanation'      => 'nullable|string',
            'question_media'   => 'nullable|file|mimes:jpg,jpeg,png,webp,mp3,wav,ogg,mp4,webm,mov,pdf|max:20480',
            'options'          => 'required|array|min:2',
            'options.*.text'   => 'nullable|string',
            'options.*.media'  => 'nullable|file|mimes:jpg,jpeg,png,webp,mp3,wav,ogg,mp4,webm,mov|max:15360',
            'correct_option'   => 'required|integer',
        ], [
            'question_text.required'   => 'Pertanyaan soal wajib diisi.',
            'question_media.mimes'     => 'Media soal harus berupa gambar (JPG/PNG/WEBP), audio (MP3/WAV/OGG), video (MP4/WEBM/MOV), atau PDF.',
            'question_media.max'       => 'Ukuran media soal maksimal 20MB.',
            'options.required'         => 'Opsi pilihan jawaban wajib diisi.',
            'options.*.media.mimes'    => 'Media jawaban harus berupa gambar (JPG/PNG/WEBP), audio (MP3/WAV/OGG), atau video (MP4/WEBM/MOV).',
            'options.*.media.max'      => 'Ukuran media jawaban maksimal 15MB.',
            'correct_option.required'  => 'Kunci jawaban yang benar wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Upload Media Soal (jika ada)
            $questionMediaId = null;
            if ($request->hasFile('question_media')) {
                $media = $this->storeMediaFile($request->file('question_media'), 'quiz_questions');
                $questionMediaId = $media->id;
            }

            // Hitung kalkulasi bobot default berdasarkan max_score / jumlah soal
            $quiz = $assignment->quiz;
            $defaultPoints = ($quiz && $quiz->question_count > 0) ? ($assignment->max_score / $quiz->question_count) : 0;

            // 2. Simpan Data Soal
            $question = Question::create([
                'created_by'    => Auth::id(),
                'question_type' => $request->question_type,
                'question_text' => $request->question_text,
                'media_id'      => $questionMediaId,
                'points'        => $defaultPoints,
                'explanation'   => $request->explanation,
                'is_active'     => true,
            ]);

            // 3. Hubungkan Soal dengan Quiz via Pivot
            $lastSortOrder = QuizQuestion::where('quiz_id', $quiz->id)->max('sort_order') ?? 0;
            QuizQuestion::create([
                'quiz_id'     => $quiz->id,
                'question_id' => $question->id,
                'sort_order'  => $lastSortOrder + 1,
            ]);

            // 4. Simpan Pilihan Jawaban (Options)
            foreach ($request->options as $index => $optionData) {
                $optionMediaId = null;

                if ($request->hasFile("options.{$index}.media")) {
                    $optMedia = $this->storeMediaFile($request->file("options.{$index}.media"), 'quiz_options');
                    $optionMediaId = $optMedia->id;
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['text'] ?? '',
                    'media_id'    => $optionMediaId,
                    'is_correct'  => ($index == $request->correct_option),
                    'sort_order'  => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()->route('teacher.quizzes.show', $assignment->id)
                ->with('success', 'Soal berhasil ditambahkan ke bank soal Quiz!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan soal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan form edit Soal.
     */
    public function edit(Assignment $assignment, Question $question)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        $question->load(['options.media', 'media']);
        return view('teacher.quizzes.questions.edit', compact('assignment', 'question'));
    }

    /**
     * Perbarui Soal & Pilihan Jawaban.
     */
    public function update(Request $request, Assignment $assignment, Question $question)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        $request->validate([
            'question_type'           => 'required|in:multiple_choice,true_false',
            'question_text'           => 'required|string',
            'explanation'             => 'nullable|string',
            'question_media'          => 'nullable|file|mimes:jpg,jpeg,png,webp,mp3,wav,ogg,mp4,webm,mov,pdf|max:20480',
            'remove_question_media'   => 'nullable|boolean',
            'options'                 => 'required|array|min:2',
            'options.*.id'            => 'nullable|integer',
            'options.*.text'          => 'nullable|string',
            'options.*.media'         => 'nullable|file|mimes:jpg,jpeg,png,webp,mp3,wav,ogg,mp4,webm,mov|max:15360',
            'options.*.remove_media'  => 'nullable|boolean',
            'correct_option'          => 'required|integer',
        ], [
            'question_text.required'   => 'Pertanyaan soal wajib diisi.',
            'question_media.mimes'     => 'Media soal harus berupa gambar (JPG/PNG/WEBP), audio (MP3/WAV/OGG), video (MP4/WEBM/MOV), atau PDF.',
            'question_media.max'       => 'Ukuran media soal maksimal 20MB.',
            'options.required'         => 'Opsi pilihan jawaban wajib diisi.',
            'options.*.media.mimes'    => 'Media jawaban harus berupa gambar (JPG/PNG/WEBP), audio (MP3/WAV/OGG), atau video (MP4/WEBM/MOV).',
            'options.*.media.max'      => 'Ukuran media jawaban maksimal 15MB.',
            'correct_option.required'  => 'Kunci jawaban yang benar wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update / Replace / Remove Media Soal
            if ($request->hasFile('question_media')) {
                if ($question->media) {
                    Storage::disk('public')->delete($question->media->file_path);
                    $oldQMedia = $question->media;
                    $question->update(['media_id' => null]);
                    $oldQMedia->delete();
                }

                $media = $this->storeMediaFile($request->file('question_media'), 'quiz_questions');
                $question->media_id = $media->id;
            } elseif ($request->boolean('remove_question_media') && $question->media) {
                Storage::disk('public')->delete($question->media->file_path);
                $oldQMedia = $question->media;
                $question->update(['media_id' => null]);
                $oldQMedia->delete();
                $question->media_id = null;
            }

            // 2. Update Soal utama
            $question->update([
                'question_type' => $request->question_type,
                'question_text' => $request->question_text,
                'explanation'   => $request->explanation,
                'media_id'      => $question->media_id,
            ]);

            // 3. Petakan opsi lama berdasarkan ID agar media yang tidak diubah tetap tersimpan
            $existingOptions = $question->options()->with('media')->get()->keyBy('id');
            $keptMediaIds = [];

            foreach ($question->options as $oldOpt) {
                $oldOpt->delete();
            }

            // 4. Simpan Opsi Jawaban Baru / Diperbarui
            foreach ($request->options as $index => $optionData) {
                $optionMediaId = null;
                $oldOption = isset($optionData['id']) ? $existingOptions->get((int) $optionData['id']) : null;
                $shouldRemoveMedia = filter_var($optionData['remove_media'] ?? false, FILTER_VALIDATE_BOOLEAN);

                if ($request->hasFile("options.{$index}.media")) {
                    // Hapus media lama pada opsi ini jika diganti file baru
                    if ($oldOption && $oldOption->media) {
                        Storage::disk('public')->delete($oldOption->media->file_path);
                        $oldOption->media->delete();
                    }

                    $optMedia = $this->storeMediaFile($request->file("options.{$index}.media"), 'quiz_options');
                    $optionMediaId = $optMedia->id;
                } elseif ($oldOption && $oldOption->media_id && !$shouldRemoveMedia) {
                    // Pertahankan media lama jika tidak diganti maupun dihapus
                    $optionMediaId = $oldOption->media_id;
                    $keptMediaIds[] = $optionMediaId;
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['text'] ?? '',
                    'media_id'    => $optionMediaId,
                    'is_correct'  => ($index == $request->correct_option),
                    'sort_order'  => $index + 1,
                ]);
            }

            // 5. Bersihkan file media dari opsi yang dihapus atau media opsi yang ditandai hapus
            foreach ($existingOptions as $oldOpt) {
                if ($oldOpt->media && !in_array($oldOpt->media_id, $keptMediaIds)) {
                    Storage::disk('public')->delete($oldOpt->media->file_path);
                    $oldOpt->media->delete();
                }
            }

            DB::commit();

            return redirect()->route('teacher.quizzes.show', $assignment->id)
                ->with('success', 'Soal berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui soal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus Soal dari Quiz.
     */
    public function destroy(Assignment $assignment, Question $question)
    {
        if ($assignment->type !== 'quiz') {
            abort(404);
        }

        DB::beginTransaction();
        try {
            // Unlink dari Quiz
            QuizQuestion::where('quiz_id', $assignment->quiz->id)
                ->where('question_id', $question->id)
                ->delete();

            // Hapus Media Soal & Opsi
            if ($question->media) {
                Storage::disk('public')->delete($question->media->file_path);
                $question->media->delete();
            }

            foreach ($question->options as $opt) {
                if ($opt->media) {
                    Storage::disk('public')->delete($opt->media->file_path);
                    $opt->media->delete();
                }
            }

            $question->delete();

            DB::commit();

            return redirect()->route('teacher.quizzes.show', $assignment->id)
                ->with('success', 'Soal berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus soal.');
        }
    }

    /**
     * Helper internal untuk menyimpan berkas media (gambar, audio, video, atau dokumen) ke storage & tabel Media.
     */
    private function storeMediaFile(UploadedFile $file, string $directory): Media
    {
        $path = $file->store($directory, 'public');
        $mime = $file->getMimeType() ?? 'application/octet-stream';

        $type = match (true) {
            str_contains($mime, 'image') => 'image',
            str_contains($mime, 'audio') => 'audio',
            str_contains($mime, 'video') => 'video',
            default                      => 'document',
        };

        return Media::create([
            'type'        => $type,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'mime_type'   => $mime,
            'file_size'   => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);
    }
}