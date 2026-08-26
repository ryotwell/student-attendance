<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Xclass;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalikelasController extends Controller
{
    /**
     * Daftar kelas yang menjadi wali kelas, dibatasi ke tahun ajaran
     * yang sedang aktif dan sekolah yang sama.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->role === 'SUPERADMIN';
        $schoolId = $user->school_id;

        $activeAcademicYear = AcademicYear::where('is_active', true)
            ->when(!$isSuperAdmin, fn($q) => $q->where('school_id', $schoolId))
            ->first();

        $xclasses = $user
            ->classes()
            ->with('academicYear')
            ->withCount('studentEnrollments')
            ->when($activeAcademicYear, fn($q) => $q->where('academic_year_id', $activeAcademicYear->id))
            ->when(!$isSuperAdmin, fn($q) => $q->where('school_id', $schoolId))
            ->get();

        abort_if($xclasses->isEmpty(), 403, 'Anda bukan wali kelas pada tahun ajaran ini.');

        if ($xclasses->count() === 1) {
            return redirect()->route('walikelas.dashboard', $xclasses->first());
        }

        return view('teacher.walikelas.index', compact('xclasses'));
    }

    /**
     * Dashboard kelas
     */
    public function dashboard(Request $request, Xclass $xclass)
    {
        $this->authorizeHomeroom($request, $xclass);

        $xclass->load(['academicYear', 'studentEnrollments.student']);

        $enrollmentIds = $xclass->studentEnrollments->pluck('id');

        // Kehadiran hari ini
        $todayQuery = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->whereDate('date', Carbon::today());

        $this->applyAttendanceSchoolFilter($todayQuery, $xclass);

        $todayCounts = $todayQuery
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Rekap bulan berjalan
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthQuery = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->whereBetween('date', [$monthStart, $monthEnd]);

        $this->applyAttendanceSchoolFilter($monthQuery, $xclass);

        $monthCounts = $monthQuery
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthTotal = $monthCounts->sum();

        $attendanceRate = $monthTotal > 0
            ? round(($monthCounts->get('HADIR', 0) / $monthTotal) * 100, 1)
            : 0;

        // Siswa alpha terbanyak
        $topAlphaQuery = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'ALPHA');

        $this->applyAttendanceSchoolFilter($topAlphaQuery, $xclass);

        $topAlpha = $topAlphaQuery
            ->selectRaw('student_enrollment_id, COUNT(*) as total')
            ->groupBy('student_enrollment_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('studentEnrollment.student')
            ->get();

        return view('teacher.walikelas.dashboard', [
            'xclass'          => $xclass,
            'todayCounts'     => $todayCounts,
            'monthCounts'     => $monthCounts,
            'attendanceRate'  => $attendanceRate,
            'topAlpha'        => $topAlpha,
            'monthLabel'      => Carbon::now()->translatedFormat('F Y'),
        ]);
    }

    /**
     * Rekap absensi
     */
    public function rekap(Request $request, Xclass $xclass)
    {
        $data = $this->buildRekap($request, $xclass);
        return view('teacher.walikelas.rekap', $data);
    }

    /**
     * Export PDF
     */
    public function rekapExport(Request $request, Xclass $xclass)
    {
        $data = $this->buildRekap($request, $xclass);

        $pdf = Pdf::loadView('teacher.walikelas.rekap-pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download(sprintf(
            'rekap-absensi-%s-%s.pdf',
            Str::slug($xclass->name),
            $data['selectedMonth']
        ));
    }

    /**
     * Cek wali kelas + kelas harus di tahun ajaran aktif dan sekolah yang sama.
     */
    private function authorizeHomeroom(Request $request, Xclass $xclass)
    {
        $user = $request->user();
        $isSuperAdmin = $user->role === 'SUPERADMIN';
        $schoolId = $user->school_id;

        abort_unless($xclass->user_id === $user->id, 403);

        // Cek sekolah (kecuali SUPERADMIN)
        if (!$isSuperAdmin) {
            abort_unless(
                $xclass->school_id === $schoolId,
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

        $activeAcademicYear = AcademicYear::where('is_active', true)
            ->when(!$isSuperAdmin, fn($q) => $q->where('school_id', $schoolId))
            ->first();

        abort_unless($activeAcademicYear, 404, 'Tidak ada tahun ajaran aktif yang dikonfigurasi.');

        abort_unless(
            $xclass->academic_year_id === $activeAcademicYear->id,
            403,
            'Kelas ini bukan bagian dari tahun ajaran yang sedang aktif.'
        );
    }

    /**
     * Logic rekap
     */
    private function buildRekap(Request $request, Xclass $xclass): array
    {
        $this->authorizeHomeroom($request, $xclass);

        $xclass->load(['academicYear', 'studentEnrollments.student']);

        $monthOptions = collect(range(0, 5))->map(function ($i) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            return [
                'value' => $date->format('Y-m'),
                'label' => $date->translatedFormat('F Y'),
            ];
        });

        $selectedMonth = $request->query('month', $monthOptions->first()['value']);

        if (!$monthOptions->pluck('value')->contains($selectedMonth)) {
            $selectedMonth = $monthOptions->first()['value'];
        }

        $start = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $enrollmentIds = $xclass->studentEnrollments->pluck('id');

        $countQuery = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->whereBetween('date', [$start, $end]);

        $this->applyAttendanceSchoolFilter($countQuery, $xclass);

        $counts = $countQuery
            ->selectRaw('student_enrollment_id, status, COUNT(*) as total')
            ->groupBy('student_enrollment_id', 'status')
            ->get()
            ->groupBy('student_enrollment_id');

        $recap = $xclass->studentEnrollments
            ->map(function ($enrollment) use ($counts) {
                $status = $counts->get($enrollment->id, collect())->pluck('total', 'status');

                $hadir = $status->get('HADIR', 0);
                $izin  = $status->get('IZIN', 0);
                $sakit = $status->get('SAKIT', 0);
                $alpha = $status->get('ALPHA', 0);
                $total = $hadir + $izin + $sakit + $alpha;

                return [
                    'student' => $enrollment->student,
                    'HADIR'   => $hadir,
                    'IZIN'    => $izin,
                    'SAKIT'   => $sakit,
                    'ALPHA'   => $alpha,
                    'rate'    => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
                ];
            })
            ->sortBy(fn($row) => $row['student']->name)
            ->values();

        return [
            'xclass'        => $xclass,
            'monthOptions'  => $monthOptions,
            'selectedMonth' => $selectedMonth,
            'monthLabel'    => $monthOptions->firstWhere('value', $selectedMonth)['label'],
            'recap'         => $recap,
        ];
    }

    /**
     * Tambahkan filter school_id pada query Attendance jika user bukan SUPERADMIN.
     */
    private function applyAttendanceSchoolFilter($query, Xclass $xclass)
    {
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $xclass->school_id);
        }
        return $query;
    }
}