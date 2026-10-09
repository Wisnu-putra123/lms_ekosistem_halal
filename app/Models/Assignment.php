<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'meeting_id',
        'created_by',
        'title',
        'description',
        'type',
        'submission_method',
        'max_attempts',
        'available_from',
        'available_until',
        'max_score',
        'passing_score',
        'status',
    ];

    protected $casts = [
        'max_attempts' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'max_score' => 'decimal:2',
        'passing_score' => 'decimal:2',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function attachments()
    {
        return $this->hasMany(AssignmentAttachment::class)
            ->orderBy('sort_order');
    }

    public function allowsTextSubmission(): bool
    {
        return in_array($this->submission_method, [
            'text',
            'both'
        ]);
    }

    public function allowsFileSubmission(): bool
    {
        return in_array($this->submission_method, [
            'file',
            'both'
        ]);
    }
}