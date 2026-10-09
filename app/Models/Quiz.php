<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'duration_minutes',
        'question_count',
        'shuffle_questions',
        'shuffle_answers',
    ];

    protected $casts = [
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id')->orderBy('sort_order');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'quiz_questions', 'quiz_id', 'question_id')
                    ->withPivot('sort_order')
                    ->withTimestamps();
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }
}