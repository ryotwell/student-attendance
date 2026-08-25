<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\CounselingCase;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $activeYear = AcademicYear::where('is_active', true)->first();

        // Ringkasan jumlah entitas utama, dibatasi ke tahun ajaran aktif.
        $totals = [
            'students' => $activeYear
                ? Student::whereHas('enrollments', fn ($q) => $q->where('academic_year_id', $activeYear->id))->count()
                : 0,
            'classes' => $activeYear
                ? Xclass::where('academic_year_id', $activeYear->id)->count()
                : 0,
            'subjects' => Subject::count(),
            'teachers' => User::where('role', 'GURU')->count(),
            'counselors' => User::where('role', 'GURU_BK')->count(),
            'admins' => User::where('role', 'ADMIN')->count(),
        ];

        // Kehadiran hari ini, dibatasi ke enrollment tahun ajaran aktif.
        $todayCounts = Attendance::query()
            ->whereDate('date', $today)
            ->whereHas('studentEnrollment', fn ($q) => $q->where('academic_year_id', $activeYear?->id))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Kehadiran bulan berjalan, dibatasi ke enrollment tahun ajaran aktif.
        $monthCounts = Attendance::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->whereHas('studentEnrollment', fn ($q) => $q->where('academic_year_id', $activeYear?->id))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthTotal = $monthCounts->sum();
        $attendanceRate = $monthTotal > 0
            ? round(($monthCounts->get('HADIR', 0) / $monthTotal) * 100, 1)
            : 0;

        // Aktivitas terbaru: user baru daftar + kasus BK baru (tahun ajaran aktif), digabung & diurutkan.
        $recentUsers = User::query()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($user) => [
                'type' => 'user',
                'title' => "{$user->name} bergabung sebagai " . strtolower(str_replace('_', ' ', $user->role)),
                'timestamp' => $user->created_at,
            ]);

        $recentCases = CounselingCase::query()
            ->with(['student', 'user'])
            ->when($activeYear, fn ($q) => $q->whereHas(
                'studentEnrollment',
                fn ($q2) => $q2->where('academic_year_id', $activeYear->id)
            ))
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($case) => [
                'type' => 'counseling_case',
                'title' => "{$case->user->name} mencatat kasus {$case->category_label} untuk {$case->student->name}",
                'timestamp' => $case->created_at,
            ]);

        $recentActivities = $recentUsers->concat($recentCases)
            ->sortByDesc('timestamp')
            ->take(8)
            ->values();

        return view('admin.dashboard', [
            'activeYear' => $activeYear,
            'totals' => $totals,
            'todayCounts' => $todayCounts,
            'monthCounts' => $monthCounts,
            'attendanceRate' => $attendanceRate,
            'monthLabel' => Carbon::now()->translatedFormat('F Y'),
            'recentActivities' => $recentActivities,
        ]);
    }
}