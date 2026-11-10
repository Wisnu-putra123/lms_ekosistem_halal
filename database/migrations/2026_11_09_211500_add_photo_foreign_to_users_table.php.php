<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Membuat kolom profile_photo (BigInteger Unsigned Nullable) + Foreign Key ke lms_ekosistem_halal_media
            $table->foreignId('profile_photo')
                  ->nullable()
                  ->after('role')
                  ->constrained('media')
                  ->nullOnDelete();

            // Membuat kolom ktp_media_id (BigInteger Unsigned Nullable) + Foreign Key ke lms_ekosistem_halal_media
            $table->foreignId('ktp_media_id')
                  ->nullable()
                  ->after('nik')
                  ->constrained('media')
                  ->nullOnDelete();
    
            // #OPSI FOTO KK (JIKA KELAK DIBUTUHKAN)
            // $table->foreignId('kk_media_id')
            //     ->nullable()
            //     ->after('family_card_number')
            //     ->constrained('media')
            //     ->nullOnDelete();
            
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['profile_photo']);
            $table->dropColumn('profile_photo');

            $table->dropForeign(['ktp_media_id']);
            $table->dropColumn('ktp_media_id');

            // $table->dropForeign(['kk_media_id']);
            // $table->dropColumn('kk_media_id');
        });
    }
};