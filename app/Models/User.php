<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'profile_photo',
        'phone',
        'nik',
        'ktp_media_id',
        'family_card_number',
        // 'kk_media_id', // Jika kelak beralih ke upload foto KK
        'address',
        'institution',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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

    /* --- RELASI LMS & REGISTRASI --- */

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
     * Relasi Foto Profil ke Tabel Media Pusat.
     */
    public function profilePhotoMedia()
    {
        return $this->belongsTo(Media::class, 'profile_photo');
    }

    /**
     * Relasi Berkas/Foto KTP ke Tabel Media Pusat.
     */
    public function ktpMedia()
    {
        return $this->belongsTo(Media::class, 'ktp_media_id');
    }

    /* 
    |--------------------------------------------------------------------------
    | RELASI FOTO KK (DIKOMENTAR KARENA MEMAKAI TEKS NOMOR KK)
    |--------------------------------------------------------------------------
    | public function kkMedia()
    | {
    |     return $this->belongsTo(Media::class, 'kk_media_id');
    | }
    */

    /* --- HELPER ACCESSORS / URL ACCESSIBLE --- */

    /**
     * Helper URL Foto Profil.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profilePhotoMedia && $this->profilePhotoMedia->file_path) {
            return asset('storage/' . $this->profilePhotoMedia->file_path);
        }

        if ($this->profile_photo && filter_var($this->profile_photo, FILTER_VALIDATE_URL)) {
            return $this->profile_photo;
        }

        if ($this->profile_photo && file_exists(storage_path('app/public/' . $this->profile_photo))) {
            return asset('storage/' . $this->profile_photo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=059669&background=E1F5FE';
    }

    /**
     * Helper URL Berkas / Foto KTP.
     */
    public function getKtpPhotoUrlAttribute(): ?string
    {
        if ($this->ktpMedia && $this->ktpMedia->file_path) {
            return asset('storage/' . $this->ktpMedia->file_path);
        }

        return null;
    }
}