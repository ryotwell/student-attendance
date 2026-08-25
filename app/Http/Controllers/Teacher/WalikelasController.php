<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Xclass;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class WalikelasController extends Controller
{
    /**
     * Daftar kelas yang menjadi wali kelas, dibatasi ke tahun ajaran
     * yang sedang aktif. Xclass terikat tetap ke satu academic_year_id,
     * jadi tanpa filter ini kelas lama (tahun ajaran sebelumnya) tetap
     * muncul selama user masih tercatat sebagai wali kelasnya — termasuk
     * auto-redirect ke kelas lama kalau itu satu-satunya yang cocok.
     */
    public function index(Request $request)
    {
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        $xclasses = $request->user()
            ->classes()
            ->with('academicYear')
            ->withCount('studentEnrollments')
            ->when($activeAcademicYear, fn ($q) => $q->where('academic_year_id', $activeAcademicYear->id))
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
        $todayCounts = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->whereDate('date', Carbon::today())
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Rekap bulan berjalan
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthCounts = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthTotal = $monthCounts->sum();

        $attendanceRate = $monthTotal > 0
            ? round(($monthCounts->get('HADIR', 0) / $monthTotal) * 100, 1)
            : 0;

        // Siswa alpha terbanyak
        $topAlpha = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'ALPHA')
            ->selectRaw('student_enrollment_id, COUNT(*) as total')
            ->groupBy('student_enrollment_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('studentEnrollment.student')
            ->get();

        return view('teacher.walikelas.dashboard', [
            'xclass' => $xclass,
            'todayCounts' => $todayCounts,
            'monthCounts' => $monthCounts,
            'attendanceRate' => $attendanceRate,
            'topAlpha' => $topAlpha,
            'monthLabel' => Carbon::now()->translatedFormat('F Y'),
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
     * Cek wali kelas + kelas harus di tahun ajaran yang sedang aktif.
     *
     * Xclass terikat tetap ke satu academic_year_id. Tanpa cek ini,
     * wali kelas masih bisa mengakses dashboard/rekap kelas dari tahun
     * ajaran lama lewat URL langsung selama dia tercatat sebagai wali
     * kelasnya, walau kelas itu sudah tidak aktif.
     */
    private function authorizeHomeroom(Request $request, Xclass $xclass)
    {
        abort_unless($xclass->user_id === $request->user()->id, 403);

        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

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

        $counts = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentIds)
            ->whereBetween('date', [$start, $end])
            ->selectRaw('student_enrollment_id, status, COUNT(*) as total')
            ->groupBy('student_enrollment_id', 'status')
            ->get()
            ->groupBy('student_enrollment_id');

        $recap = $xclass->studentEnrollments
            ->map(function ($enrollment) use ($counts) {
                $status = $counts->get($enrollment->id, collect())->pluck('total', 'status');

                $hadir = $status->get('HADIR', 0);
                $izin = $status->get('IZIN', 0);
                $sakit = $status->get('SAKIT', 0);
                $alpha = $status->get('ALPHA', 0);
                $total = $hadir + $izin + $sakit + $alpha;

                return [
                    'student' => $enrollment->student,
                    'HADIR' => $hadir,
                    'IZIN' => $izin,
                    'SAKIT' => $sakit,
                    'ALPHA' => $alpha,
                    'rate' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
                ];
            })
            ->sortBy(fn ($row) => $row['student']->name)
            ->values();

        return [
            'xclass' => $xclass,
            'monthOptions' => $monthOptions,
            'selectedMonth' => $selectedMonth,
            'monthLabel' => $monthOptions->firstWhere('value', $selectedMonth)['label'],
            'recap' => $recap,
        ];
    }
}