<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'media_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Submission yang memiliki attachment.
     */
    public function submission()
    {
        return $this->belongsTo(
            AssignmentSubmission::class,
            'submission_id'
        );
    }

    /**
     * File yang dikumpulkan.
     */
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}