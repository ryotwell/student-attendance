<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Xclass;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // Ringkasan hari ini, sekolah-wide.
        $todayCounts = Attendance::query()
            ->whereDate('date', $today)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Ringkasan bulan berjalan, sekolah-wide.
        $monthCounts = Attendance::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthTotal = $monthCounts->sum();
        $attendanceRate = $monthTotal > 0
            ? round(($monthCounts->get('HADIR', 0) / $monthTotal) * 100, 1)
            : 0;

        // Breakdown per kelas: skor rata-rata kehadiran bulan ini.
        $classBreakdown = Xclass::query()
            ->withCount('students')
            ->with('academicYear')
            ->get()
            ->map(function ($xclass) use ($monthStart, $monthEnd) {
                $counts = Attendance::query()
                    ->where('xclass_id', $xclass->id)
                    ->whereBetween('date', [$monthStart, $monthEnd])
                    ->selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status');

                $total = $counts->sum();
                $rate = $total > 0 ? round(($counts->get('HADIR', 0) / $total) * 100, 1) : null;

                return [
                    'xclass' => $xclass,
                    'rate' => $rate,
                    'alpha' => $counts->get('ALPHA', 0),
                ];
            })
            ->sortBy('rate') // kelas dengan kehadiran terendah muncul duluan
            ->values();

        // Top siswa dengan Alpha terbanyak, sekolah-wide, bulan ini.
        $topAlpha = Attendance::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'ALPHA')
            ->selectRaw('student_id, COUNT(*) as total')
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->take(10)
            ->with('student.xclass')
            ->get();

        return view('teacher.bk.dashboard', [
            'todayCounts' => $todayCounts,
            'monthCounts' => $monthCounts,
            'attendanceRate' => $attendanceRate,
            'classBreakdown' => $classBreakdown,
            'topAlpha' => $topAlpha,
            'monthLabel' => Carbon::now()->translatedFormat('F Y'),
        ]);
    }
}