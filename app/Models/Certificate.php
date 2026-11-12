<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'certificate_number',
        'verification_code',
        'issued_at',
        'status',
        'certificate_media_id',
        'signed_media_id',
        'approved_by',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Media PDF hasil generate otomatis oleh sistem.
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'certificate_media_id');
    }

    /**
     * Media PDF resmi yang sudah ditandatangani BPJPH (Skenario 2).
     */
    public function signedMedia()
    {
        return $this->belongsTo(Media::class, 'signed_media_id');
    }

    /**
     * Admin yang menyetujui / mempublikasikan sertifikat.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Accessor: Mengambil media PDF yang aktif (memprioritaskan signedMedia jika ada).
     */
    public function getActiveMediaAttribute()
    {
        return $this->signedMedia ?? $this->media;
    }

    /**
     * Helper pengecekan status published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}