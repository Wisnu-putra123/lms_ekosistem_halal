<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')
                ->unique()
                ->constrained('assignments')
                ->cascadeOnDelete();

            // NULL = tidak ada batas waktu
            $table->unsignedInteger('duration_minutes')->nullable();

            $table->unsignedInteger('question_count');

            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_answers')->default(false);

            $table->unsignedInteger('max_attempts')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
