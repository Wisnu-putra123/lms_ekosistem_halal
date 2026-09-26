<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'progress_percentage',
        'last_activity_at',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'last_activity_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}