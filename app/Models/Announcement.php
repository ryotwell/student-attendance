<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $guarded = [];

    protected $casts = [];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
