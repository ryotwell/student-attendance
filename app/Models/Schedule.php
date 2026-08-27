<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $guarded = [];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Casting waktu jadwal
     */
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];


    /**
     * Guru pengajar
     *
     * schedules
     *      |
     *      +--- users
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Kelas yang diajar
     *
     * schedules
     *      |
     *      +--- xclasses
     */
    public function xclass(): BelongsTo
    {
        return $this->belongsTo(Xclass::class);
    }


    /**
     * Data absensi berdasarkan jadwal
     *
     * schedules
     *      |
     *      +--- attendances
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}