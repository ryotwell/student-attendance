<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Xclass extends Model
{
    protected $guarded = [];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Tahun ajaran kelas
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Wali kelas
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Daftar enrollment siswa
     *
     * xclasses
     *      |
     *      +--- student_enrollments
     */
    public function studentEnrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * Daftar siswa dalam kelas
     *
     * Relasi melalui tabel pivot:
     * student_enrollments
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'student_enrollments'
        )
        ->withPivot('academic_year_id');
    }

    /**
     * Jadwal pelajaran kelas
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}