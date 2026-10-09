<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_feedback_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('submission_id')
                ->constrained('assignment_submissions')
                ->cascadeOnDelete();

            $table->foreignId('media_id')
                ->constrained('media')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')
                ->default(1);

            $table->timestamps();

            $table->unique([
                'submission_id',
                'media_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_feedback_attachments');
    }
};