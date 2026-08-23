<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Xclass extends Model
{
    public function students()
    {
        return $this->hasMany(Student::class);
    }


    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }


    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
