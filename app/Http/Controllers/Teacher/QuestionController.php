<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Media;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
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
            'question_type' => 'required|in:multiple_choice,true_false',
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'question_media' => 'nullable|file|mimes:jpg,jpeg,png,mp3,wav,pdf|max:10240',
            // Validasi Opsi
            'options' => 'required|array|min:2',
            'options.*.text' => 'nullable|string',
            'options.*.media' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'correct_option' => 'required|integer', // Index opsi yang benar
        ]);

        DB::beginTransaction();
        try {
            // 1. Upload Media Soal (jika ada)
            $questionMediaId = null;
            if ($request->hasFile('question_media')) {
                $file = $request->file('question_media');
                $path = $file->store('quiz_questions', 'public');

                $media = Media::create([
                    'type' => str_contains($file->getMimeType(), 'image') ? 'image' : (str_contains($file->getMimeType(), 'audio') ? 'audio' : 'document'),
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);
                $questionMediaId = $media->id;
            }

            // Hitung kalkulasi bobot default berdasarkan kkm / jumlah soal
            $quiz = $assignment->quiz;
            $defaultPoints = ($quiz && $quiz->question_count > 0) ? ($assignment->max_score / $quiz->question_count) : 0;

            // 2. Simpan Data Soal
            $question = Question::create([
                'created_by' => Auth::id(),
                'question_type' => $request->question_type,
                'question_text' => $request->question_text,
                'media_id' => $questionMediaId,
                'points' => $defaultPoints,
                'explanation' => $request->explanation,
                'is_active' => true,
            ]);

            // 3. Hubungkan Soal dengan Quiz via Pivot
            $lastSortOrder = QuizQuestion::where('quiz_id', $quiz->id)->max('sort_order') ?? 0;
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_id' => $question->id,
                'sort_order' => $lastSortOrder + 1,
            ]);

            // 4. Simpan Pilihan Jawaban (Options)
            foreach ($request->options as $index => $optionData) {
                $optionMediaId = null;

                // Upload Media Opsi Jawaban (jika ada)
                if (isset($optionData['media']) && $request->hasFile("options.{$index}.media")) {
                    $optFile = $request->file("options.{$index}.media");
                    $optPath = $optFile->store('quiz_options', 'public');

                    $optMedia = Media::create([
                        'type' => 'image',
                        'file_name' => $optFile->getClientOriginalName(),
                        'file_path' => $optPath,
                        'mime_type' => $optFile->getMimeType(),
                        'file_size' => $optFile->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);
                    $optionMediaId = $optMedia->id;
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['text'] ?? '',
                    'media_id' => $optionMediaId,
                    'is_correct' => ($index == $request->correct_option),
                    'sort_order' => $index + 1,
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
            'question_type' => 'required|in:multiple_choice,true_false',
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'question_media' => 'nullable|file|mimes:jpg,jpeg,png,mp3,wav,pdf|max:10240',
            'options' => 'required|array|min:2',
            'options.*.text' => 'nullable|string',
            'options.*.media' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'correct_option' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            // Update / Replace Media Soal
            if ($request->hasFile('question_media')) {
                // Hapus media lama jika ada
                if ($question->media) {
                    Storage::disk('public')->delete($question->media->file_path);
                    $question->media->delete();
                }

                $file = $request->file('question_media');
                $path = $file->store('quiz_questions', 'public');

                $media = Media::create([
                    'type' => str_contains($file->getMimeType(), 'image') ? 'image' : (str_contains($file->getMimeType(), 'audio') ? 'audio' : 'document'),
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);
                $question->media_id = $media->id;
            }

            // Update Soal utama
            $question->update([
                'question_type' => $request->question_type,
                'question_text' => $request->question_text,
                'explanation' => $request->explanation,
                'media_id' => $question->media_id,
            ]);

            // Hapus opsi lama & ganti dengan opsi baru
            foreach ($question->options as $oldOpt) {
                if ($oldOpt->media) {
                    Storage::disk('public')->delete($oldOpt->media->file_path);
                    $oldOpt->media->delete();
                }
                $oldOpt->delete();
            }

            // Simpan Opsi Jawaban Baru
            foreach ($request->options as $index => $optionData) {
                $optionMediaId = null;

                if (isset($optionData['media']) && $request->hasFile("options.{$index}.media")) {
                    $optFile = $request->file("options.{$index}.media");
                    $optPath = $optFile->store('quiz_options', 'public');

                    $optMedia = Media::create([
                        'type' => 'image',
                        'file_name' => $optFile->getClientOriginalName(),
                        'file_path' => $optPath,
                        'mime_type' => $optFile->getMimeType(),
                        'file_size' => $optFile->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);
                    $optionMediaId = $optMedia->id;
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['text'] ?? '',
                    'media_id' => $optionMediaId,
                    'is_correct' => ($index == $request->correct_option),
                    'sort_order' => $index + 1,
                ]);
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
}