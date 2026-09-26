<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'title',
        'description',
        'content',
        'media_id',
        'sort_order',
        'created_by',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}