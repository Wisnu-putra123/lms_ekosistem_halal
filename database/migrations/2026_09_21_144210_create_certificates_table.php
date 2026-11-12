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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained('enrollments')->cascadeOnDelete();
            $table->string('certificate_number', 100)->unique();
            $table->string('verification_code', 100)->unique();
            $table->dateTime('issued_at')->nullable();
            
            // Status penerbitan bolak-balik ('draft' <-> 'published'), default 'draft' saat pertama terbuat
            $table->enum('status', ['draft', 'published'])->default('draft');
            
            // File PDF hasil generate otomatis (overlay teks unik di atas template background)
            $table->foreignId('certificate_media_id')->nullable()->constrained('media')->nullOnDelete();
            
            // File PDF resmi balikan dari BPJPH yang sudah ditandatangani (Skenario 2)
            $table->foreignId('signed_media_id')->nullable()->constrained('media')->nullOnDelete();
            
            // Admin yang mempublikasikan sertifikat (null jika dikembalikan ke status 'draft')
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};