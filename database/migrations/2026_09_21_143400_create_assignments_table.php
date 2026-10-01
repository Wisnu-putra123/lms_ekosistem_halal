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
             * Waktu assignment dapat dikerjakan.
             */
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_until')->nullable();

            /*
             * Sistem penilaian.
             *
             * max_score:
             * Nilai maksimum yang dapat diperoleh Student.
             *
             * passing_score:
             * Nilai minimum/KKM agar assignment dianggap lulus.
             */
            $table->decimal('max_score', 5, 2)->default(100);
            $table->decimal('passing_score', 5, 2)->default(70);

            $table->enum('status', [
                'draft',
                'published',
                'closed'
            ])->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};