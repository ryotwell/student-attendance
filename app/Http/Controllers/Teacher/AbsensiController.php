<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Schedule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AbsensiController extends Controller
{
    /**
     * Daftar jadwal guru login, dibatasi ke kelas pada tahun ajaran
     * yang sedang aktif.
     */
    public function schedules()
    {
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        $schedules = Auth::user()
            ->schedules()
            ->with(['subject', 'xclass'])
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas('xclass', fn ($q2) => $q2->where('academic_year_id', $activeAcademicYear->id));
            })
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get();

        return view('teacher.absensi.schedules', compact('schedules'));
    }

    /**
     * Riwayat absensi guru, dibatasi ke tahun ajaran yang sedang aktif.
     */
    public function history()
    {
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        $histories = Attendance::with(['schedule.subject', 'studentEnrollment.xclass'])
            ->where('user_id', Auth::id())
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas('studentEnrollment', fn ($q2) => $q2->where('academic_year_id', $activeAcademicYear->id));
            })
            ->orderByDesc('date')
            ->get()
            ->unique(fn ($attendance) => $attendance->date->format('Y-m-d') . '-' . $attendance->schedule_id)
            ->values();

        return view('teacher.absensi.history', compact('histories'));
    }

    /**
     * Detail history. Sekarang abort 422 jika kelas/jadwal yang diminta
     * bukan bagian dari tahun ajaran aktif, alih-alih diam-diam
     * menampilkan halaman kosong.
     *
     * Route: /absensi/history/{date}/{class}/{schedule}
     */
    public function showHistory($date, $class, $schedule)
    {
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        abort_unless($activeAcademicYear, 404, 'Tidak ada tahun ajaran aktif yang dikonfigurasi.');

        $scheduleModel = Schedule::with('xclass')->findOrFail($schedule);

        abort_unless(
            (int) $scheduleModel->xclass_id === (int) $class,
            404
        );

        abort_unless(
            $scheduleModel->xclass && (int) $scheduleModel->xclass->academic_year_id === $activeAcademicYear->id,
            403,
            'Data absensi ini bukan dari tahun ajaran yang sedang aktif.'
        );

        $attendances = Attendance::with(['studentEnrollment.student', 'studentEnrollment.xclass', 'schedule.subject'])
            ->where('user_id', Auth::id())
            ->whereDate('date', $date)
            ->where('schedule_id', $schedule)
            ->whereHas('studentEnrollment', function ($q) use ($class, $activeAcademicYear) {
                $q->where('xclass_id', $class)
                  ->where('academic_year_id', $activeAcademicYear->id);
            })
            ->get();

        return view('teacher.absensi.history-detail', compact('attendances'));
    }

    /**
     * Halaman pilih jadwal untuk rekap, dibatasi ke tahun ajaran aktif.
     */
    public function recapIndex(Request $request)
    {
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        $schedules = $request->user()
            ->mySchedules()
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas('xclass', fn ($q2) => $q2->where('academic_year_id', $activeAcademicYear->id));
            })
            ->get();

        return view('teacher.absensi.recap-index', compact('schedules'));
    }

    /**
     * Tampilan rekap absensi
     */
    public function recapShow(Request $request, Schedule $schedule)
    {
        $data = $this->buildRecap($request, $schedule);

        return view('teacher.absensi.recap-show', $data);
    }

    /**
     * Export PDF
     */
    public function recapExport(Request $request, Schedule $schedule)
    {
        $data = $this->buildRecap($request, $schedule);

        $pdf = Pdf::loadView('teacher.absensi.recap-pdf', $data)
            ->setPaper('a4', 'portrait');

        $fileName = sprintf(
            'rekap-absensi-%s-%s-%s.pdf',
            Str::slug($schedule->subject->name),
            Str::slug($schedule->xclass->name),
            $data['selectedMonth']
        );

        return $pdf->download($fileName);
    }

    /**
     * Logic rekap utama.
     *
     * Sekarang abort 422 jika kelas milik jadwal ini bukan bagian dari
     * tahun ajaran aktif, alih-alih diam-diam mengembalikan rekap kosong.
     */
    private function buildRecap(Request $request, Schedule $schedule): array
    {
        // Pastikan jadwal milik guru login
        abort_unless($schedule->user_id === $request->user()->id, 403);

        $schedule->load(['subject', 'xclass.academicYear']);

        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        abort_unless($activeAcademicYear, 404, 'Tidak ada tahun ajaran aktif yang dikonfigurasi.');

        // Kelas pada jadwal ini harus berada di tahun ajaran yang sedang aktif
        abort_unless(
            $schedule->xclass && $schedule->xclass->academic_year_id === $activeAcademicYear->id,
            422,
            'Jadwal ini bukan bagian dari tahun ajaran yang sedang aktif.'
        );

        $monthOptions = $this->buildMonthOptions();

        $selectedMonth = $request->query('month', $monthOptions->first()['value']);

        if (!$monthOptions->pluck('value')->contains($selectedMonth)) {
            $selectedMonth = $monthOptions->first()['value'];
        }

        $start = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // Siswa yang dihitung: hanya enrollment kelas ini di tahun ajaran aktif
        $enrollments = $schedule->xclass
            ->studentEnrollments()
            ->where('academic_year_id', $activeAcademicYear->id)
            ->with('student')
            ->get();

        $counts = Attendance::query()
            ->where('schedule_id', $schedule->id)
            ->whereIn('student_enrollment_id', $enrollments->pluck('id'))
            ->whereBetween('date', [$start, $end])
            ->selectRaw('student_enrollment_id, status, COUNT(*) as total')
            ->groupBy('student_enrollment_id', 'status')
            ->get()
            ->groupBy('student_enrollment_id');

        $recap = $enrollments
            ->map(function ($enrollment) use ($counts) {
                $status = $counts->get($enrollment->id, collect())->pluck('total', 'status');

                return [
                    'student' => $enrollment->student,
                    'HADIR' => $status->get('HADIR', 0),
                    'IZIN' => $status->get('IZIN', 0),
                    'SAKIT' => $status->get('SAKIT', 0),
                    'ALPHA' => $status->get('ALPHA', 0),
                ];
            })
            ->sortBy(fn ($row) => $row['student']->name)
            ->values();

        return [
            'schedule' => $schedule,
            'academicYear' => $activeAcademicYear,
            'monthOptions' => $monthOptions,
            'selectedMonth' => $selectedMonth,
            'monthLabel' => $monthOptions->firstWhere('value', $selectedMonth)['label'],
            'recap' => $recap,
        ];
    }

    /**
     * Pilihan bulan 6 bulan terakhir.
     */
    private function buildMonthOptions()
    {
        return collect(range(0, 5))->map(function ($i) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();

            return [
                'value' => $date->format('Y-m'),
                'label' => $date->translatedFormat('F Y'),
            ];
        });
    }
}