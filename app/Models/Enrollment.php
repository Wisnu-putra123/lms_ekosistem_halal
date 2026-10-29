<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'enrolled_at',
        'status',
        'completed_at',
        'access_expires_at', // Pastikan kolom ini masuk fillable
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'access_expires_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function progress()
    {
        return $this->hasOne(CourseProgress::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    // /**
    //  * Relasi ke Course Progress.
    //  */
    // public function courseProgress()
    // {
    //     return $this->hasOne(CourseProgress::class, 'enrollment_id');
    // }


    /**
     * Helper untuk mengecek apakah akses enrollment masih berlaku
     */
    public function isAccessValid(): bool
    {
        if ($this->status === 'active') {
            return true;
        }

        if ($this->status === 'completed') {
            // Jika tidak ada batas kedaluwarsa, anggap berlaku selamanya
            if (!$this->access_expires_at) {
                return true;
            }

            // Cek apakah tanggal sekarang belum melewati access_expires_at
            return now()->lessThanOrEqualTo($this->access_expires_at);
        }

        return false;
    }
}   