<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
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


    protected $guarded = [];


    protected $casts = [
        'date' => 'date',
    ];


    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }


    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function getStudentAttribute()
    {
        return $this->studentEnrollment?->student;
    }


    public function getXclassAttribute()
    {
        return $this->studentEnrollment?->xclass;
    }


    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status]['label']
            ?? $this->status;
    }


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