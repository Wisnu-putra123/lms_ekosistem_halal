<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assignment_id')
                ->constrained('assignments')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * Nomor pengerjaan submission.
             */
            $table->unsignedInteger('attempt_number');

            /*
             * Jawaban berupa teks.
             *
             * Bisa menyimpan HTML dari Rich Text Editor.
             */
            $table->longText('submission_text')
                ->nullable();

            $table->timestamp('submitted_at')
                ->nullable();

            /*
             * Feedback dari Teacher.
             */
            $table->longText('feedback')
                ->nullable();

            /*
             * Nilai attempt ini.
             */
            $table->decimal('score', 5, 2)
                ->nullable();

            $table->foreignId('graded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('graded_at')
                ->nullable();

            $table->enum('status', [
                'draft',
                'submitted',
                'graded',
                'late'
            ])->default('draft');

            $table->timestamps();

            /*
             * Satu Student dapat mempunyai banyak attempt,
             * tetapi nomor attempt tidak boleh duplikat.
             */
            $table->unique(
                [
                    'assignment_id',
                    'user_id',
                    'attempt_number'
                ],
                'assignment_attempt_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};