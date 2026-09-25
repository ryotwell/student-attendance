<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAttendance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Status absensi guru.
     */
    public const STATUS_OPTIONS = [
        'HADIR' => [
            'label' => 'Hadir',
            'icon' => '✓',
            'checkedClass' => 'peer-checked:bg-green-500',
        ],

        'IZIN' => [
            'label' => 'Izin',
            'icon' => '📘',
            'checkedClass' => 'peer-checked:bg-blue-500',
        ],

        'SAKIT' => [
            'label' => 'Sakit',
            'icon' => '🤒',
            'checkedClass' => 'peer-checked:bg-yellow-500',
        ],

        'ALPHA' => [
            'label' => 'Alpha',
            'icon' => '✕',
            'checkedClass' => 'peer-checked:bg-red-500',
        ],
    ];

    /**
     * Guru yang melakukan absensi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sekolah tempat guru melakukan absensi.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Label status.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status]['label']
            ?? $this->status;
    }

    /**
     * Icon status.
     */
    public function getStatusIconAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status]['icon']
            ?? '';
    }

    /**
     * Warna badge status.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'HADIR' => 'success',
            'IZIN' => 'info',
            'SAKIT' => 'warning',
            'ALPHA' => 'error',
            default => 'gray',
        };
    }
}
