<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'started_at',
        'expires_at',
        'submitted_at',
        'score',
        'status',
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(
            QuizAttemptQuestion::class,
            'attempt_id'
        )->orderBy('question_order');
    }

    public function isPassed(): bool
    {
        $passingScore = $this->quiz
            ->assignment
            ->passing_score;

        return $this->score !== null
            && $this->score >= $passingScore;
    }
}