<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Xclass;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $totalSchools = School::count();
        $activeSchools = School::where('is_active', true)->count();
        $inactiveSchools = $totalSchools - $activeSchools;

        $totalAdmins = User::where('role', 'ADMIN')->count();
        $totalTeachers = User::where('role', 'GURU')->count();
        $totalBk = User::where('role', 'GURU_BK')->count();
        $totalStudents = Student::count();

        $totalAcademicYears = AcademicYear::count();
        $activeAcademicYears = AcademicYear::where('is_active', true)->count();
        $totalClasses = Xclass::count();

        // Data untuk grafik: 10 sekolah dengan siswa terbanyak
        $schoolStudentStats = School::withCount('students')
            ->orderBy('students_count', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'students_count']);

        // Data untuk grafik: 10 sekolah dengan guru terbanyak
        $schoolTeacherStats = School::withCount(['users' => function ($query) {
            $query->where('role', 'GURU');
        }])->orderBy('users_count', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'users_count']);

        // Statistik berdasarkan level sekolah (SD, SMP, SMA)
        $levelStats = School::select('level', DB::raw('count(*) as total'))
            ->groupBy('level')
            ->get();

        // Aktivitas terbaru (contoh: 5 sekolah terakhir dibuat)
        $recentSchools = School::latest()->limit(5)->get(['name', 'created_at']);

        return view('superadmin.dashboard', compact(
            'totalSchools',
            'activeSchools',
            'inactiveSchools',
            'totalAdmins',
            'totalTeachers',
            'totalBk',
            'totalStudents',
            'totalAcademicYears',
            'activeAcademicYears',
            'totalClasses',
            'schoolStudentStats',
            'schoolTeacherStats',
            'levelStats',
            'recentSchools'
        ));
    }
}