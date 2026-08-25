<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
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

        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        /*
        |--------------------------------------------------------------------------
        | Kehadiran hari ini
        |--------------------------------------------------------------------------
        | Dibatasi ke enrollment kelas pada tahun ajaran aktif, lewat
        | whereHas studentEnrollment.xclass (attendances tidak punya
        | academic_year_id/xclass_id langsung).
        */

        $todayCounts = Attendance::query()
            ->whereDate('date', $today)
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas(
                    'studentEnrollment.xclass',
                    fn ($q2) => $q2->where('academic_year_id', $activeAcademicYear->id)
                );
            })
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
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas(
                    'studentEnrollment.xclass',
                    fn ($q2) => $q2->where('academic_year_id', $activeAcademicYear->id)
                );
            })
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
        | attendances tidak punya kolom xclass_id — groupBy dilakukan lewat
        | join manual ke student_enrollments untuk dapat xclass_id, karena
        | selectRaw/groupBy butuh nama kolom asli di level SQL (tidak bisa
        | pakai relasi Eloquent di sini).
        */

        $attendanceByClass = Attendance::query()
            ->join('student_enrollments', 'student_enrollments.id', '=', 'attendances.student_enrollment_id')
            ->whereBetween('attendances.date', [$monthStart, $monthEnd])
            ->when($activeAcademicYear, fn ($q) => $q->where('student_enrollments.academic_year_id', $activeAcademicYear->id))
            ->selectRaw("
                student_enrollments.xclass_id,
                SUM(attendances.status = 'HADIR') as hadir,
                SUM(attendances.status = 'ALPHA') as alpha,
                COUNT(*) as total
            ")
            ->groupBy('student_enrollments.xclass_id')
            ->get()
            ->keyBy('xclass_id');

        $classBreakdown = Xclass::query()
            ->withCount('students')
            ->when($activeAcademicYear, fn ($q) => $q->where('academic_year_id', $activeAcademicYear->id))
            ->get()
            ->map(function ($xclass) use ($attendanceByClass) {
                $attendance = $attendanceByClass->get($xclass->id);

                $total = $attendance->total ?? 0;

                return [
                    'xclass' => $xclass,

                    'rate' => $total
                        ? round((($attendance->hadir ?? 0) / $total) * 100, 1)
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
        | Attendance tidak punya relasi student()/xclass() langsung
        | (hanya accessor getStudentAttribute() lewat studentEnrollment),
        | dan Student tidak punya kolom/relasi xclass. Group dilakukan
        | berdasarkan student_enrollment_id, lalu eager load
        | studentEnrollment.student + studentEnrollment.xclass.
        */

        $topAlpha = Attendance::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'ALPHA')
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas(
                    'studentEnrollment.xclass',
                    fn ($q2) => $q2->where('academic_year_id', $activeAcademicYear->id)
                );
            })
            ->selectRaw('student_enrollment_id, COUNT(*) as total')
            ->groupBy('student_enrollment_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with(['studentEnrollment.student', 'studentEnrollment.xclass'])
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