<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Xclass;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Total counts
        $totalStudents = Student::count();
        $totalClasses = Xclass::count();
        $totalSubjects = Subject::count();

        // Today's attendance stats
        $todaysAttendances = Attendance::whereDate('date', $today)->get();
        
        $attendanceStats = [
            'hadir' => $todaysAttendances->where('status', 'HADIR')->count(),
            'izin' => $todaysAttendances->where('status', 'IZIN')->count(),
            'sakit' => $todaysAttendances->where('status', 'SAKIT')->count(),
            'alpha' => $todaysAttendances->where('status', 'ALPHA')->count(),
            'total' => $todaysAttendances->count(),
        ];

        // Attendance rate (percentage of students present today)
        $attendanceRate = $totalStudents > 0 
            ? round(($attendanceStats['hadir'] / $totalStudents) * 100, 1)
            : 0;

        // Recent attendance records (last 5)
        $recentAttendances = Attendance::with(['student', 'schedule.subject', 'xclass'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pages.dashboard', [
            'title' => 'Dashboard',
            'totalStudents' => $totalStudents,
            'totalClasses' => $totalClasses,
            'totalSubjects' => $totalSubjects,
            'attendanceStats' => $attendanceStats,
            'attendanceRate' => $attendanceRate,
            'recentAttendances' => $recentAttendances,
            'today' => $today,
        ]);
    }
}
