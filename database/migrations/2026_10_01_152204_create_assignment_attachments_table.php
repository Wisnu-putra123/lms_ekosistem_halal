<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_attachments', function (Blueprint $table) {
            $table->id();

            // Assignment yang memiliki lampiran
            $table->foreignId('assignment_id')
                ->constrained('assignments')
                ->cascadeOnDelete();

            // File dari tabel media
            $table->foreignId('media_id')
                ->constrained('media')
                ->cascadeOnDelete();

            // Urutan file ketika ditampilkan
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            // Satu media tidak boleh dilampirkan
            // dua kali pada assignment yang sama
            $table->unique([
                'assignment_id',
                'media_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_attachments');
    }
};