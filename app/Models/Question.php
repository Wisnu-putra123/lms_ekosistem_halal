<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'created_by',
        'question_type',
        'question_text',
        'media_id',
        'points',
        'explanation',
        'is_active',
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(
            Quiz::class,
            'quiz_questions',
            'question_id',
            'quiz_id'
        )->withPivot('sort_order')
         ->withTimestamps();
    }

    public function attemptQuestions()
    {
        return $this->hasMany(QuizAttemptQuestion::class);
    }
}