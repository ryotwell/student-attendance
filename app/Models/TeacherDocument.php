<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherDocument extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

}
