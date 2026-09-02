<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function counselingCases()
    {
        return $this->hasMany(CounselingCase::class);
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function currentEnrollment()
    {
        return $this->hasOne(StudentEnrollment::class)
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->latestOfMany();
    }
}
