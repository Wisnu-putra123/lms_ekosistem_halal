<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionFeedbackAttachment extends Model
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

    public function submission()
    {
        return $this->belongsTo(
            AssignmentSubmission::class,
            'submission_id'
        );
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}