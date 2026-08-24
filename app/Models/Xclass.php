<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Xclass extends Model
{
    protected $guarded = [];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentEnrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'student_enrollments'
        );
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}