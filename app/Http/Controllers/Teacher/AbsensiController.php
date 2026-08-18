<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function schedules()
    {
        $schedules = Auth::user()->schedules()->with(['subject', 'xclass'])
            ->where('user_id', auth()->id())
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get();

        return view('teacher.absensi.schedules', compact('schedules'));
    }
}
