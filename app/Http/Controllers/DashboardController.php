<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\CounselingCase;
use App\Models\Schedule;          // <-- pakai Schedule untuk hitung subject_name unik
use App\Models\Student;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->role === 'SUPERADMIN';
        $schoolId = $user->school_id;

        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // Tahun ajaran aktif
        $activeYearQuery = AcademicYear::where('is_active', true);
        if (! $isSuperAdmin) {
            $activeYearQuery->where('school_id', $schoolId);
        }
        $activeYear = $activeYearQuery->first();

        // Helper filter sekolah
        $applySchoolFilter = function ($query) use ($isSuperAdmin, $schoolId) {
            if (! $isSuperAdmin) {
                $query->where('school_id', $schoolId);
            }
            return $query;
        };

        // ---- TOTALS ----
        // Siswa (hanya yang terdaftar di tahun ajaran aktif)
        $studentsQuery = Student::query();
        if ($activeYear) {
            $studentsQuery->whereHas('enrollments', fn($q) => $q->where('academic_year_id', $activeYear->id));
        } else {
            $studentsQuery->whereRaw('0');
        }
        $applySchoolFilter($studentsQuery);

        // Kelas (hanya tahun ajaran aktif)
        $classesQuery = Xclass::query();
        if ($activeYear) {
            $classesQuery->where('academic_year_id', $activeYear->id);
        } else {
            $classesQuery->whereRaw('0');
        }
        $applySchoolFilter($classesQuery);

        // Jumlah jadwal pelajaran UNIK (distinct subject_name) dari tabel schedules
        $scheduleQuery = Schedule::query();
        $applySchoolFilter($scheduleQuery);
        $totalSchedules = $scheduleQuery->distinct('subject_name')->count('subject_name');

        // Guru, BK, Admin
        $teachersQuery = User::where('role', 'GURU');
        $applySchoolFilter($teachersQuery);

        $counselorsQuery = User::where('role', 'GURU_BK');
        $applySchoolFilter($counselorsQuery);

        $adminsQuery = User::where('role', 'ADMIN');
        $applySchoolFilter($adminsQuery);

        $totals = [
            'students'   => $studentsQuery->count(),
            'classes'    => $classesQuery->count(),
            'subjects'   => $totalSchedules, // sekarang berisi jumlah jadwal pelajaran unik
            'teachers'   => $teachersQuery->count(),
            'counselors' => $counselorsQuery->count(),
            'admins'     => $adminsQuery->count(),
        ];

        // ---- KEHADIRAN HARI INI ----
        $todayAttendanceQuery = Attendance::whereDate('date', $today);
        $applySchoolFilter($todayAttendanceQuery);
        if ($activeYear) {
            $todayAttendanceQuery->whereHas('studentEnrollment', fn($q) => $q->where('academic_year_id', $activeYear->id));
        } else {
            $todayAttendanceQuery->whereRaw('0');
        }
        $todayCounts = $todayAttendanceQuery
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // ---- KEHADIRAN BULAN INI ----
        $monthAttendanceQuery = Attendance::whereBetween('date', [$monthStart, $monthEnd]);
        $applySchoolFilter($monthAttendanceQuery);
        if ($activeYear) {
            $monthAttendanceQuery->whereHas('studentEnrollment', fn($q) => $q->where('academic_year_id', $activeYear->id));
        } else {
            $monthAttendanceQuery->whereRaw('0');
        }
        $monthCounts = $monthAttendanceQuery
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthTotal = $monthCounts->sum();
        $attendanceRate = $monthTotal > 0
            ? round(($monthCounts->get('HADIR', 0) / $monthTotal) * 100, 1)
            : 0;

        // ---- AKTIVITAS TERBARU ----
        $recentUsersQuery = User::latest()->take(5);
        $applySchoolFilter($recentUsersQuery);
        $recentUsers = $recentUsersQuery->get()->map(fn($u) => [
            'type'      => 'user',
            'title'     => "{$u->name} bergabung sebagai " . strtolower(str_replace('_', ' ', $u->role)),
            'timestamp' => $u->created_at,
        ]);

        $recentCasesQuery = CounselingCase::with(['student', 'user'])->latest()->take(5);
        $applySchoolFilter($recentCasesQuery);
        if ($activeYear) {
            $recentCasesQuery->whereHas('studentEnrollment', fn($q) => $q->where('academic_year_id', $activeYear->id));
        } else {
            $recentCasesQuery->whereRaw('0');
        }
        $recentCases = $recentCasesQuery->get()->map(fn($case) => [
            'type'      => 'counseling_case',
            'title'     => "{$case->user->name} mencatat kasus {$case->category_label} untuk {$case->student->name}",
            'timestamp' => $case->created_at,
        ]);

        $recentActivities = $recentUsers->concat($recentCases)
            ->sortByDesc('timestamp')
            ->take(8)
            ->values();

        return view('admin.dashboard', [
            'activeYear'       => $activeYear,
            'totals'           => $totals,
            'todayCounts'      => $todayCounts,
            'monthCounts'      => $monthCounts,
            'attendanceRate'   => $attendanceRate,
            'monthLabel'       => Carbon::now()->translatedFormat('F Y'),
            'recentActivities' => $recentActivities,
        ]);
    }
}