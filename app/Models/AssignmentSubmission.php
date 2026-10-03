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
        'submission_text',
        'submitted_at',
        'feedback',
        'score',
        'graded_by',
        'graded_at',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    /**
     * Assignment yang dikerjakan.
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Student yang melakukan submission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * File-file yang dikumpulkan Student.
     */
    public function attachments()
    {
        // Tambahkan 'submission_id' sebagai parameter kedua
        return $this->hasMany(SubmissionAttachment::class, 'submission_id')->orderBy('sort_order');
    }

    /**
     * Teacher yang memberikan nilai.
     */
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}