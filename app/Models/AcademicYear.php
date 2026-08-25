<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $guarded = [];


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