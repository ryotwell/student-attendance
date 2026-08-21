<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Xclass;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class WalikelasController extends Controller
{
    /**
     * Daftar kelas yang diwalikan oleh guru yang login.
     * Kalau cuma 1 kelas, langsung redirect ke dashboard kelas itu.
     */
    public function index(Request $request)
    {
        $xclasses = $request->user()->classes()
            ->with('academicYear')
            ->withCount('students')
            ->get();

        abort_if($xclasses->isEmpty(), 403, 'Anda bukan wali kelas.');

        if ($xclasses->count() === 1) {
            return redirect()->route('walikelas.dashboard', $xclasses->first());
        }

        return view('teacher.walikelas.index', [
            'xclasses' => $xclasses,
        ]);
    }

    /**
     * Dashboard ringkasan untuk satu kelas perwalian.
     */
    public function dashboard(Request $request, Xclass $xclass)
    {
        $this->authorizeHomeroom($request, $xclass);

        $xclass->load('academicYear');
        $studentIds = $xclass->students()->pluck('id');

        // Kehadiran hari ini: semua attendance yang DIINPUT hari ini, lintas mapel.
        $todayCounts = Attendance::query()
            ->whereIn('student_id', $studentIds)
            ->whereDate('date', Carbon::today())
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Ringkasan bulan berjalan.
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthCounts = Attendance::query()
            ->whereIn('student_id', $studentIds)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthTotal = $monthCounts->sum();
        $attendanceRate = $monthTotal > 0
            ? round(($monthCounts->get('HADIR', 0) / $monthTotal) * 100, 1)
            : 0;

        // Siswa dengan Alpha terbanyak bulan ini (top 5).
        $topAlpha = Attendance::query()
            ->whereIn('student_id', $studentIds)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'ALPHA')
            ->selectRaw('student_id, COUNT(*) as total')
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('student')
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
     * Rekap absensi bulanan, lintas semua mapel, untuk satu kelas.
     */
    public function rekap(Request $request, Xclass $xclass)
    {
        $data = $this->buildRekap($request, $xclass);

        return view('teacher.walikelas.rekap', $data);
    }

    public function rekapExport(Request $request, Xclass $xclass)
    {
        $data = $this->buildRekap($request, $xclass);

        $pdf = Pdf::loadView('teacher.walikelas.rekap-pdf', $data)
            ->setPaper('a4', 'portrait');

        $fileName = sprintf(
            'rekap-absensi-kelas-%s-%s.pdf',
            \Str::slug($xclass->name),
            $data['selectedMonth']
        );

        return $pdf->download($fileName);
    }

    private function authorizeHomeroom(Request $request, Xclass $xclass): void
    {
        abort_unless($xclass->user_id === $request->user()->id, 403);
    }

    private function buildRekap(Request $request, Xclass $xclass): array
    {
        $this->authorizeHomeroom($request, $xclass);

        $xclass->load(['students', 'academicYear']);

        $monthOptions = collect(range(0, 5))->map(function ($i) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();

            return [
                'value' => $date->format('Y-m'),
                'label' => $date->translatedFormat('F Y'),
            ];
        });

        $selectedMonth = $request->query('month', $monthOptions->first()['value']);

        if (! $monthOptions->pluck('value')->contains($selectedMonth)) {
            $selectedMonth = $monthOptions->first()['value'];
        }

        $periodStart = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        // Rekap lintas SEMUA mapel di kelas ini (tidak difilter schedule_id).
        $counts = Attendance::query()
            ->where('xclass_id', $xclass->id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->selectRaw('student_id, status, COUNT(*) as total')
            ->groupBy('student_id', 'status')
            ->get()
            ->groupBy('student_id');

        $recap = $xclass->students->map(function ($student) use ($counts) {
            $statusCounts = $counts->get($student->id, collect())->pluck('total', 'status');

            $hadir = $statusCounts->get('HADIR', 0);
            $izin = $statusCounts->get('IZIN', 0);
            $sakit = $statusCounts->get('SAKIT', 0);
            $alpha = $statusCounts->get('ALPHA', 0);
            $total = $hadir + $izin + $sakit + $alpha;

            return [
                'student' => $student,
                'HADIR' => $hadir,
                'IZIN' => $izin,
                'SAKIT' => $sakit,
                'ALPHA' => $alpha,
                'rate' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
            ];
        })->sortBy(fn ($row) => $row['student']->name)->values();

        return [
            'xclass' => $xclass,
            'monthOptions' => $monthOptions,
            'selectedMonth' => $selectedMonth,
            'monthLabel' => $monthOptions->firstWhere('value', $selectedMonth)['label'],
            'recap' => $recap,
        ];
    }
}