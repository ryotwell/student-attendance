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
     * Terapkan filter school_id jika user bukan SUPERADMIN.
     */
    private function applySchoolFilter($query)
    {
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }
        return $query;
    }

    /**
     * Cek apakah schedule milik sekolah yang sama dengan user (kecuali SUPERADMIN).
     */
    private function canAccessSchedule(Schedule $schedule): bool
    {
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            return true;
        }
        return $schedule->xclass && $schedule->xclass->school_id === $user->school_id;
    }

    /**
     * Daftar jadwal guru login, dibatasi ke kelas pada tahun ajaran
     * yang sedang aktif dan sekolah yang sama.
     * Sekarang tanpa relasi subject, langsung pakai subject_name.
     */
    public function schedules()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->role === 'SUPERADMIN';
        $schoolId = $user->school_id;

        $activeAcademicYear = AcademicYear::where('is_active', true)
            ->when(!$isSuperAdmin, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->first();

        $schedules = $user
            ->schedules()
            ->with(['xclass']) // subject dihapus
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas('xclass', fn ($q2) => $q2->where('academic_year_id', $activeAcademicYear->id));
            })
            ->when(!$isSuperAdmin, function ($q) use ($schoolId) {
                $q->whereHas('xclass', fn ($q2) => $q2->where('school_id', $schoolId));
            })
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get();

        return view('teacher.absensi.schedules', compact('schedules'));
    }

    /**
     * Riwayat absensi guru, dibatasi ke tahun ajaran yang sedang aktif
     * dan sekolah yang sama.
     */
    public function history()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->role === 'SUPERADMIN';
        $schoolId = $user->school_id;

        $activeAcademicYear = AcademicYear::where('is_active', true)
            ->when(!$isSuperAdmin, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->first();

        $query = Attendance::with(['schedule', 'studentEnrollment.xclass']) // subject dihapus
            ->where('user_id', Auth::id());

        if (!$isSuperAdmin) {
            $query->where('school_id', $schoolId);
        }

        if ($activeAcademicYear) {
            $query->whereHas('studentEnrollment', fn ($q2) => $q2->where('academic_year_id', $activeAcademicYear->id));
        }

        $histories = $query
            ->orderByDesc('date')
            ->get()
            ->unique(fn ($attendance) => $attendance->date->format('Y-m-d') . '-' . $attendance->schedule_id)
            ->values();

        return view('teacher.absensi.history', compact('histories'));
    }

    /**
     * Detail history.
     */
    public function showHistory($date, $class, $schedule)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->role === 'SUPERADMIN';
        $schoolId = $user->school_id;

        $activeAcademicYear = AcademicYear::where('is_active', true)
            ->when(!$isSuperAdmin, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->first();

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

        // Pastikan schedule berada di sekolah yang sama
        if (!$isSuperAdmin) {
            abort_unless(
                $scheduleModel->xclass && $scheduleModel->xclass->school_id === $schoolId,
                403,
                'Anda tidak memiliki akses ke data ini.'
            );
        }

        $query = Attendance::with(['studentEnrollment.student', 'studentEnrollment.xclass', 'schedule']) // subject dihapus
            ->where('user_id', Auth::id())
            ->whereDate('date', $date)
            ->where('schedule_id', $schedule)
            ->whereHas('studentEnrollment', function ($q) use ($class, $activeAcademicYear) {
                $q->where('xclass_id', $class)
                  ->where('academic_year_id', $activeAcademicYear->id);
            });

        if (!$isSuperAdmin) {
            $query->where('school_id', $schoolId);
        }

        $attendances = $query->get();

        return view('teacher.absensi.history-detail', compact('attendances'));
    }

    /**
     * Halaman pilih jadwal untuk rekap, dibatasi ke tahun ajaran aktif
     * dan sekolah yang sama.
     */
    public function recapIndex(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->role === 'SUPERADMIN';
        $schoolId = $user->school_id;

        $activeAcademicYear = AcademicYear::where('is_active', true)
            ->when(!$isSuperAdmin, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->first();

        // Ambil jadwal milik user, tanpa relasi subject
        $schedules = $user
            ->schedules()
            ->with(['xclass'])
            ->when($activeAcademicYear, function ($q) use ($activeAcademicYear) {
                $q->whereHas('xclass', fn ($q2) => $q2->where('academic_year_id', $activeAcademicYear->id));
            })
            ->when(!$isSuperAdmin, function ($q) use ($schoolId) {
                $q->whereHas('xclass', fn ($q2) => $q2->where('school_id', $schoolId));
            })
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
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
            Str::slug($schedule->subject_name),
            Str::slug($schedule->xclass->name),
            $data['selectedMonth']
        );

        return $pdf->download($fileName);
    }

    /**
     * Logic rekap utama.
     */
    private function buildRecap(Request $request, Schedule $schedule): array
    {
        $user = $request->user();
        $isSuperAdmin = $user->role === 'SUPERADMIN';
        $schoolId = $user->school_id;

        // Pastikan jadwal milik guru login
        abort_unless($schedule->user_id === $user->id, 403);

        $schedule->load(['xclass.academicYear']); // subject dihapus

        // Cek akses sekolah
        if (!$isSuperAdmin) {
            abort_unless(
                $schedule->xclass && $schedule->xclass->school_id === $schoolId,
                403,
                'Anda tidak memiliki akses ke jadwal ini.'
            );
        }

        $activeAcademicYear = AcademicYear::where('is_active', true)
            ->when(!$isSuperAdmin, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->first();

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

        $attendanceQuery = Attendance::query()
            ->where('schedule_id', $schedule->id)
            ->whereIn('student_enrollment_id', $enrollments->pluck('id'))
            ->whereBetween('date', [$start, $end]);

        if (!$isSuperAdmin) {
            $attendanceQuery->where('school_id', $schoolId);
        }

        $counts = $attendanceQuery
            ->selectRaw('student_enrollment_id, status, COUNT(*) as total')
            ->groupBy('student_enrollment_id', 'status')
            ->get()
            ->groupBy('student_enrollment_id');

        $recap = $enrollments
            ->map(function ($enrollment) use ($counts) {
                $status = $counts->get($enrollment->id, collect())->pluck('total', 'status');

                return [
                    'student' => $enrollment->student,
                    'HADIR'   => $status->get('HADIR', 0),
                    'IZIN'    => $status->get('IZIN', 0),
                    'SAKIT'   => $status->get('SAKIT', 0),
                    'ALPHA'   => $status->get('ALPHA', 0),
                ];
            })
            ->sortBy(fn ($row) => $row['student']->name)
            ->values();

        return [
            'schedule'       => $schedule,
            'academicYear'   => $activeAcademicYear,
            'monthOptions'   => $monthOptions,
            'selectedMonth'  => $selectedMonth,
            'monthLabel'     => $monthOptions->firstWhere('value', $selectedMonth)['label'],
            'recap'          => $recap,
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