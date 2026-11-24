<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'meeting_number',
        'available_from',
        'available_until',
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Mengecek apakah pertemuan dapat diakses saat ini berdasarkan available_from & available_until.
     */
    public function isAccessible(): bool
    {
        $now = now();

        if ($this->available_from && $now->lt($this->available_from)) {
            return false;
        }

        if ($this->available_until && $now->gt($this->available_until)) {
            return false;
        }

        return true;
    }

    /**
     * Mengembalikan keterangan mengapa pertemuan terkunci.
     */
    public function lockReason(): ?string
    {
        $now = now();

        if ($this->available_from && $now->lt($this->available_from)) {
            return 'belum dibuka (dapat diakses mulai ' . $this->available_from->translatedFormat('d M Y, H:i') . ').';
        }

        if ($this->available_until && $now->gt($this->available_until)) {
            return 'sudah ditutup pada ' . $this->available_until->translatedFormat('d M Y, H:i') . '.';
        }

        return null;
    }

    /**
     * Scope Eloquent untuk memfilter pertemuan yang sedang terbuka.
     */
    public function scopeAccessible(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('available_from')
                    ->orWhere('available_from', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('available_until')
                    ->orWhere('available_until', '>=', $now);
            });
    }

}