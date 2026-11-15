<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Membuat kolom profile_photo (BigInteger Unsigned Nullable) + Foreign Key ke media
            $table->foreignId('profile_photo')
                  ->nullable()
                  ->after('role')
                  ->constrained('media')
                  ->nullOnDelete();

            // Membuat kolom ktp_media_id (BigInteger Unsigned Nullable) + Foreign Key ke media
            $table->foreignId('ktp_media_id')
                  ->nullable()
                  ->after('nik')
                  ->constrained('media')
                  ->nullOnDelete();

            // Mengaktifkan kolom kk_media_id untuk berkas Kartu Keluarga (KK)
            $table->foreignId('kk_media_id')
                  ->nullable()
                  ->after('family_card_number')
                  ->constrained('media')
                  ->nullOnDelete();

            // Menambahkan kolom nomor ijazah (nullable)
            $table->string('ijazah_number', 100)
                  ->nullable()
                  ->after('kk_media_id');

            // Menambahkan kolom file ijazah (nullable) + Foreign Key ke media
            $table->foreignId('ijazah_media_id')
                  ->nullable()
                  ->after('ijazah_number')
                  ->constrained('media')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['profile_photo']);
            $table->dropColumn('profile_photo');

            $table->dropForeign(['ktp_media_id']);
            $table->dropColumn('ktp_media_id');

            $table->dropForeign(['kk_media_id']);
            $table->dropColumn('kk_media_id');

            $table->dropForeign(['ijazah_media_id']);
            $table->dropColumn(['ijazah_number', 'ijazah_media_id']);
        });
    }
};