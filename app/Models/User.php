<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /* --- RELASI BARU --- */

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function taughtCourses()
    {
        return $this->belongsToMany(Course::class, 'course_instructors');
    }

    public function submissions()
    {
        return $this->hasManyThrough(AssignmentSubmission::class, Enrollment::class);
    }

    public function quizAttempts()
    {
        return $this->hasManyThrough(QuizAttempt::class, Enrollment::class);
    }
    /**
     * Relasi Foto Profil ke Tabel Media.
     */
    public function profilePhotoMedia()
    {
        return $this->belongsTo(Media::class, 'profile_photo');
    }

    /**
     * Helper URL Foto Profil (Mengambil dari media atau fallback avatar default).
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profilePhotoMedia && $this->profilePhotoMedia->file_path) {
            return asset('storage/' . $this->profilePhotoMedia->file_path);
        }

        // Fallback jika profile_photo berisi path langsung
        if ($this->profile_photo && filter_var($this->profile_photo, FILTER_VALIDATE_URL)) {
            return $this->profile_photo;
        }

        if ($this->profile_photo && file_exists(storage_path('app/public/' . $this->profile_photo))) {
            return asset('storage/' . $this->profile_photo);
        }

        // Fallback UI Avatar berdasarkan nama pengguna
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=059669&background=E1F5FE';
    }
}