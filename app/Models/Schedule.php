<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = [];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function xclass()
    {
        return $this->belongsTo(Xclass::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
