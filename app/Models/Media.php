<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'file_name',
        'file_path',
        'external_url',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Course thumbnail.
     */
    public function courses()
    {
        return $this->hasMany(
            Course::class,
            'thumbnail_media_id'
        );
    }

    /**
     * Material yang menggunakan media ini.
     */
    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Questions yang menggunakan media ini.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Options jawaban yang menggunakan media.
     */
    public function questionOptions()
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Submission assignment.
     */
    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Certificate.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}