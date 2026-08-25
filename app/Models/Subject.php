<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $guarded = [];


    /**
     * Jadwal pelajaran berdasarkan mata pelajaran
     *
     * subjects
     *      |
     *      +--- schedules
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}