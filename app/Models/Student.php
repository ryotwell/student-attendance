<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function xclass()
    {
        return $this->belongsTo(Xclass::class);
    }

    public function counselingCases()
    {
        return $this->hasMany(CounselingCase::class);
    }
}
