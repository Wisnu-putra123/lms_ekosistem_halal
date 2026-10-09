<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'attempt_number',
        'submission_text',
        'submitted_at',
        'feedback',
        'score',
        'graded_by',
        'graded_at',
        'status',
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * File yang dikumpulkan Student.
     */
    public function attachments()
    {
        return $this->hasMany(
            SubmissionAttachment::class,
            'submission_id'
        )->orderBy('sort_order');
    }

    /**
     * File feedback yang diberikan Teacher.
     */
    public function feedbackAttachments()
    {
        return $this->hasMany(
            SubmissionFeedbackAttachment::class,
            'submission_id'
        )->orderBy('sort_order');
    }

    public function grader()
    {
        return $this->belongsTo(
            User::class,
            'graded_by'
        );
    }

    public function isPassed(): bool
    {
        return $this->score !== null
            && $this->score >= $this->assignment->passing_score;
    }
}