<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_attachments', function (Blueprint $table) {
            $table->id();

            // Submission milik student
            $table->foreignId('submission_id')
                ->constrained('assignment_submissions')
                ->cascadeOnDelete();

            // File dari tabel media
            $table->foreignId('media_id')
                ->constrained('media')
                ->cascadeOnDelete();

            // Urutan file
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            // Media yang sama tidak boleh dilampirkan
            // dua kali pada submission yang sama
            $table->unique([
                'submission_id',
                'media_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_attachments');
    }
};  