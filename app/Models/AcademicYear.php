<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $guarded = [];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Daftar kelas pada tahun ajaran ini
     *
     * academic_years
     *          |
     *          +--- xclasses
     */
    public function xclasses(): HasMany
    {
        return $this->hasMany(Xclass::class);
    }


    /**
     * Daftar enrollment siswa pada tahun ajaran ini
     *
     * academic_years
     *          |
     *          +--- student_enrollments
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }
}