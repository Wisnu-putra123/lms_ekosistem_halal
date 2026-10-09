<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->foreignId('meeting_id')
                ->nullable()
                ->constrained('meetings')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('title');

            $table->longText('description')->nullable();

            $table->enum('type', [
                'quiz',
                'submission'
            ]);

            /*
             * Cara Student mengerjakan submission.
             *
             * text  = text editor saja
             * file  = upload file saja
             * both  = text + file
             *
             * Tidak terlalu berpengaruh untuk type = quiz.
             */
            $table->enum('submission_method', [
                'text',
                'file',
                'both'
            ])->default('file');

            /*
             * Jumlah maksimum pengerjaan.
             *
             * Berlaku untuk quiz maupun submission.
             */
            $table->unsignedInteger('max_attempts')
                ->nullable();

            /*
             * Waktu assignment tersedia.
             */
            $table->dateTime('available_from')
                ->nullable();

            $table->dateTime('available_until')
                ->nullable();

            /*
             * Sistem penilaian.
             */
            $table->decimal('max_score', 5, 2)
                ->default(100);

            $table->decimal('passing_score', 5, 2)
                ->default(70);

            $table->enum('status', [
                'draft',
                'published',
                'closed'
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};