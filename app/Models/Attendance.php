<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function xclass(): BelongsTo
    {
        return $this->belongsTo(Xclass::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'HADIR' => 'Hadir',
            'IZIN' => 'Izin',
            'SAKIT' => 'Sakit',
            'ALPHA' => 'Alpha',
            default => $this->status,
        };
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

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'HADIR' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'IZIN' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'SAKIT' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'ALPHA' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}
