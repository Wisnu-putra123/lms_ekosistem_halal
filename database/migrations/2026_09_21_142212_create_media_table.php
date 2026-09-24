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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
             $table->enum('type', [
                'image',
                'audio',
                'video',
                'document',
                'youtube',
                'drive',
                'embed'
            ]);

            // Digunakan untuk file yang di-upload
            $table->string('file_name')->nullable();
            $table->string('file_path', 500)->nullable();

            // Digunakan untuk YouTube, Google Drive, embed, dll.
            $table->text('external_url')->nullable();

            $table->string('mime_type', 100)->nullable();

            // Ukuran file dalam bytes
            $table->unsignedBigInteger('file_size')->nullable();

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
