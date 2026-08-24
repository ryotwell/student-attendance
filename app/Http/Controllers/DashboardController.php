<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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

        // Ringkasan jumlah entitas utama.
        $totals = [
            'students' => Student::count(),
            'classes' => Xclass::count(),
            'subjects' => Subject::count(),
            'teachers' => User::where('role', 'GURU')->count(),
            'counselors' => User::where('role', 'GURU_BK')->count(),
            'admins' => User::where('role', 'ADMIN')->count(),
        ];

        // Kehadiran hari ini, sekolah-wide.
        $todayCounts = Attendance::query()
            ->whereDate('date', $today)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Kehadiran bulan berjalan, sekolah-wide.
        $monthCounts = Attendance::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthTotal = $monthCounts->sum();
        $attendanceRate = $monthTotal > 0
            ? round(($monthCounts->get('HADIR', 0) / $monthTotal) * 100, 1)
            : 0;

        // Aktivitas terbaru: user baru daftar + kasus BK baru, digabung & diurutkan.
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
            'totals' => $totals,
            'todayCounts' => $todayCounts,
            'monthCounts' => $monthCounts,
            'attendanceRate' => $attendanceRate,
            'monthLabel' => Carbon::now()->translatedFormat('F Y'),
            'recentActivities' => $recentActivities,
        ]);
    }
}