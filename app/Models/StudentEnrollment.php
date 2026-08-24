<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function xclass()
    {
        return $this->belongsTo(Xclass::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

}
