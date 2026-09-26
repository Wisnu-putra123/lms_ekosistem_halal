<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'description',
        'thumbnail_media_id',
        'enrollment_key',
        'status',
        'created_by',
    ];

    /**
     * Helper untuk membuat enrollment key acak (contoh: HALAL-8A2B9C)
     */
    public static function generateEnrollmentKey(): string
    {
        do {
            $key = 'HALAL-' . strtoupper(Str::random(6));
        } while (self::where('enrollment_key', $key)->exists());

        return $key;
    }

    /**
     * Relasi ke Pengajar yang membuat kursus
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke Media (Thumbnail)
     */
    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_media_id');
    }

    /**
     * Relasi ke Pertemuan (Meetings)
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Relasi ke Pendaftaran Siswa (Enrollments)
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Relasi ke Pengajar Tambahan (Bisa ditulis tanpa type hint)
     */
    public function instructors()
    {
        return $this->belongsToMany(User::class, 'course_instructors', 'course_id', 'user_id')
                    ->withTimestamps();
    }
}