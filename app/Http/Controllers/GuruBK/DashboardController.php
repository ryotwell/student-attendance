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


        /*
        |--------------------------------------------------------------------------
        | Kehadiran hari ini
        |--------------------------------------------------------------------------
        */

        $todayCounts = Attendance::query()
            ->whereDate('date', $today)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');


        /*
        |--------------------------------------------------------------------------
        | Kehadiran bulan ini
        |--------------------------------------------------------------------------
        */

        $monthCounts = Attendance::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');


        $monthTotal = $monthCounts->sum();

        $attendanceRate = $monthTotal
            ? round(($monthCounts->get('HADIR', 0) / $monthTotal) * 100, 1)
            : 0;



        /*
        |--------------------------------------------------------------------------
        | Breakdown kelas (NO N+1)
        |--------------------------------------------------------------------------
        */

        $attendanceByClass = Attendance::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->selectRaw("
                xclass_id,
                SUM(status = 'HADIR') as hadir,
                SUM(status = 'ALPHA') as alpha,
                COUNT(*) as total
            ")
            ->groupBy('xclass_id')
            ->get()
            ->keyBy('xclass_id');


        $classBreakdown = Xclass::query()
            ->withCount('students')
            ->get()
            ->map(function ($xclass) use ($attendanceByClass) {

                $attendance = $attendanceByClass
                    ->get($xclass->id);


                $total = $attendance->total ?? 0;

                return [
                    'xclass' => $xclass,

                    'rate' => $total
                        ? round(
                            (($attendance->hadir ?? 0) / $total) * 100,
                            1
                        )
                        : null,

                    'alpha' => $attendance->alpha ?? 0,
                ];

            })
            ->sortBy('rate')
            ->values();



        /*
        |--------------------------------------------------------------------------
        | Top Alpha
        |--------------------------------------------------------------------------
        */

        $topAlpha = Attendance::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'ALPHA')
            ->selectRaw('student_id, COUNT(*) as total')
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with([
                'student:id,name,xclass_id',
                'student.xclass:id,name'
            ])
            ->get();



        return view('teacher.bk.dashboard', [

            'todayCounts' => $todayCounts,

            'monthCounts' => $monthCounts,

            'attendanceRate' => $attendanceRate,

            'classBreakdown' => $classBreakdown,

            'topAlpha' => $topAlpha,

            'monthLabel' => Carbon::now()
                ->translatedFormat('F Y'),

        ]);
    }
}