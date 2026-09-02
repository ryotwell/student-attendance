<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
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

        // Ambil tahun ajaran aktif, filter berdasarkan sekolah (kecuali SUPERADMIN)
        $activeAcademicYearQuery = AcademicYear::where('is_active', true);
        if (!$isSuperAdmin) {
            $activeAcademicYearQuery->where('school_id', $schoolId);
        }
        $activeAcademicYear = $activeAcademicYearQuery->first();

        /*
        |--------------------------------------------------------------------------
        | Kehadiran hari ini
        |--------------------------------------------------------------------------
        */
        $todayQuery = Attendance::query()
            ->whereDate('date', $today)
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas('studentEnrollment', function ($q2) use ($activeAcademicYear) {
                    $q2->where('academic_year_id', $activeAcademicYear->id);
                });
            });

        if (!$isSuperAdmin) {
            $todayQuery->where('school_id', $schoolId);
        }

        $todayCounts = $todayQuery
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        /*
        |--------------------------------------------------------------------------
        | Kehadiran bulan ini
        |--------------------------------------------------------------------------
        */
        $monthQuery = Attendance::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas('studentEnrollment', function ($q2) use ($activeAcademicYear) {
                    $q2->where('academic_year_id', $activeAcademicYear->id);
                });
            });

        if (!$isSuperAdmin) {
            $monthQuery->where('school_id', $schoolId);
        }

        $monthCounts = $monthQuery
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthTotal = $monthCounts->sum();
        $attendanceRate = $monthTotal
            ? round(($monthCounts->get('HADIR', 0) / $monthTotal) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Breakdown kelas (join dengan student_enrollments)
        |--------------------------------------------------------------------------
        */
        $attendanceByClassQuery = Attendance::query()
            ->join('student_enrollments', 'student_enrollments.id', '=', 'attendances.student_enrollment_id')
            ->whereBetween('attendances.date', [$monthStart, $monthEnd])
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->where('student_enrollments.academic_year_id', $activeAcademicYear->id);
            });

        if (!$isSuperAdmin) {
            $attendanceByClassQuery->where('attendances.school_id', $schoolId);
        }

        $attendanceByClass = $attendanceByClassQuery
            ->selectRaw("
                student_enrollments.xclass_id,
                SUM(attendances.status = 'HADIR') as hadir,
                SUM(attendances.status = 'ALPHA') as alpha,
                COUNT(*) as total
            ")
            ->groupBy('student_enrollments.xclass_id')
            ->get()
            ->keyBy('xclass_id');

        // Ambil daftar kelas, filter sekolah dan tahun ajaran aktif
        $classQuery = Xclass::withCount('students')
            ->when($activeAcademicYear, fn($q) => $q->where('academic_year_id', $activeAcademicYear->id));

        if (!$isSuperAdmin) {
            $classQuery->where('school_id', $schoolId);
        }

        $classBreakdown = $classQuery
            ->get()
            ->map(function ($xclass) use ($attendanceByClass) {
                $attendance = $attendanceByClass->get($xclass->id);
                $total = $attendance->total ?? 0;
                return [
                    'xclass' => $xclass,
                    'rate' => $total ? round((($attendance->hadir ?? 0) / $total) * 100, 1) : null,
                    'alpha' => $attendance->alpha ?? 0,
                ];
            })
            ->sortBy('rate')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Top Alpha (siswa terbanyak alpha)
        |--------------------------------------------------------------------------
        */
        $topAlphaQuery = Attendance::query()
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'ALPHA')
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas('studentEnrollment', function ($q2) use ($activeAcademicYear) {
                    $q2->where('academic_year_id', $activeAcademicYear->id);
                });
            });

        if (!$isSuperAdmin) {
            $topAlphaQuery->where('school_id', $schoolId);
        }

        $topAlpha = $topAlphaQuery
            ->selectRaw('student_enrollment_id, COUNT(*) as total')
            ->groupBy('student_enrollment_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with(['studentEnrollment.student', 'studentEnrollment.xclass'])
            ->get();

        return view('teacher.bk.dashboard', [
            'todayCounts'      => $todayCounts,
            'monthCounts'      => $monthCounts,
            'attendanceRate'   => $attendanceRate,
            'classBreakdown'   => $classBreakdown,
            'topAlpha'         => $topAlpha,
            'monthLabel'       => Carbon::now()->translatedFormat('F Y'),
        ]);
    }
}