<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentEnrollment extends Model
{
    protected $guarded = [];


    /**
     * Siswa yang terdaftar
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }


    /**
     * Kelas siswa pada tahun ajaran tersebut
     */
    public function xclass(): BelongsTo
    {
        return $this->belongsTo(Xclass::class);
    }


    /**
     * Tahun ajaran
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }


    /**
     * Data absensi siswa
     *
     * Relasi:
     * student_enrollments
     *          |
     *          +--- attendances
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}