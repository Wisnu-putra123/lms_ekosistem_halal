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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
             $table->foreignId('assignment_id')
                ->constrained('assignments')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('media_id')
                ->nullable()
                ->constrained('media')
                ->nullOnDelete();

            $table->timestamp('submitted_at')->nullable();

            $table->text('feedback')->nullable();

            $table->decimal('score', 5, 2)->nullable();

            $table->foreignId('graded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('graded_at')->nullable();

            $table->enum('status', [
                'draft',
                'submitted',
                'graded',
                'late'
            ])->default('draft');

            $table->timestamps();

            $table->unique(['assignment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
