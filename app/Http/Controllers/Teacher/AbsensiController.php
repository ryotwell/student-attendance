<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiController extends Controller
{
    public function schedules()
    {
        $schedules = Auth::user()->schedules()->with(['subject', 'xclass'])
            ->where('user_id', auth()->id())
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get();

        return view('teacher.absensi.schedules', compact('schedules'));
    }

    public function history()
    {
        $histories = Attendance::with([
            'xclass',
            'schedule.subject'
        ])
        ->where('user_id', Auth::id())
        ->select(
            'date',
            'xclass_id',
            'schedule_id'
        )
        ->groupBy(
            'date',
            'xclass_id',
            'schedule_id'
        )
        ->orderByDesc('date')
        ->get();

        return view('teacher.absensi.history', compact('histories'));
    }

    public function showHistory($date, $class, $schedule)
    {
        $attendances = Attendance::with([
                'student',
                'xclass',
                'schedule.subject'
            ])
            ->where('user_id', Auth::id())
            ->whereDate('date', $date)
            ->where('xclass_id', $class)
            ->where('schedule_id', $schedule)
            ->get();

        return view('teacher.absensi.history-detail', compact('attendances'));
    }

    /**
     * Step 1: Tampilkan semua jadwal milik guru yang login.
     */
    public function recapIndex(Request $request)
    {
        $schedules = $request->user()->mySchedules()->get();

        return view('teacher.absensi.recap-index', [
            'schedules' => $schedules,
        ]);
    }

    /**
     * Step 2: Pilih bulan (3 bulan kalender terakhir) & tampilkan
     * ringkasan total status kehadiran per siswa.
     */
    public function recapShow(Request $request, Schedule $schedule)
    {
        // Pastikan jadwal ini memang milik guru yang login.
        abort_unless($schedule->user_id === $request->user()->id, 403);

        $schedule->load(['subject', 'xclass.students']);

        // Bangun daftar 3 bulan kalender terakhir (termasuk bulan ini).
        $monthOptions = collect(range(0, 5))->map(function ($i) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();

            return [
                'value' => $date->format('Y-m'),
                'label' => $date->translatedFormat('F Y'),
            ];
        });

        // Bulan yang dipilih, default bulan berjalan.
        $selectedMonth = $request->query('month', $monthOptions->first()['value']);

        // Validasi: hanya boleh salah satu dari 3 opsi bulan yang tersedia.
        if (! $monthOptions->pluck('value')->contains($selectedMonth)) {
            $selectedMonth = $monthOptions->first()['value'];
        }

        $periodStart = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        // Ambil rekap: total per status, dikelompokkan per siswa.
        $counts = Attendance::query()
            ->where('schedule_id', $schedule->id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->selectRaw('student_id, status, COUNT(*) as total')
            ->groupBy('student_id', 'status')
            ->get()
            ->groupBy('student_id');

        $recap = $schedule->xclass->students->map(function ($student) use ($counts) {
            $statusCounts = $counts->get($student->id, collect())
                ->pluck('total', 'status');

            return [
                'student' => $student,
                'HADIR' => $statusCounts->get('HADIR', 0),
                'IZIN' => $statusCounts->get('IZIN', 0),
                'SAKIT' => $statusCounts->get('SAKIT', 0),
                'ALPHA' => $statusCounts->get('ALPHA', 0),
            ];
        })->sortBy(fn ($row) => $row['student']->name)->values();

        return view('teacher.absensi.recap-show', [
            'schedule' => $schedule,
            'monthOptions' => $monthOptions,
            'selectedMonth' => $selectedMonth,
            'recap' => $recap,
        ]);
    }

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
     * Logika inti pengambilan data rekap, dipakai bersama oleh
     * recapShow (tampilan web) dan recapExport (PDF).
     */
    private function buildRecap(Request $request, Schedule $schedule): array
    {
        // Pastikan jadwal ini memang milik guru yang login.
        abort_unless($schedule->user_id === $request->user()->id, 403);

        $schedule->load(['subject', 'xclass.students']);

        // Bangun daftar 6 bulan kalender terakhir (termasuk bulan ini).
        $monthOptions = collect(range(0, 5))->map(function ($i) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();

            return [
                'value' => $date->format('Y-m'),
                'label' => $date->translatedFormat('F Y'),
            ];
        });

        // Bulan yang dipilih, default bulan berjalan.
        $selectedMonth = $request->query('month', $monthOptions->first()['value']);

        // Validasi: hanya boleh salah satu dari opsi bulan yang tersedia.
        if (! $monthOptions->pluck('value')->contains($selectedMonth)) {
            $selectedMonth = $monthOptions->first()['value'];
        }

        $periodStart = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        // Ambil rekap: total per status, dikelompokkan per siswa.
        $counts = Attendance::query()
            ->where('schedule_id', $schedule->id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->selectRaw('student_id, status, COUNT(*) as total')
            ->groupBy('student_id', 'status')
            ->get()
            ->groupBy('student_id');

        $recap = $schedule->xclass->students->map(function ($student) use ($counts) {
            $statusCounts = $counts->get($student->id, collect())
                ->pluck('total', 'status');

            return [
                'student' => $student,
                'HADIR' => $statusCounts->get('HADIR', 0),
                'IZIN' => $statusCounts->get('IZIN', 0),
                'SAKIT' => $statusCounts->get('SAKIT', 0),
                'ALPHA' => $statusCounts->get('ALPHA', 0),
            ];
        })->sortBy(fn ($row) => $row['student']->name)->values();

        return [
            'schedule' => $schedule,
            'monthOptions' => $monthOptions,
            'selectedMonth' => $selectedMonth,
            'monthLabel' => $monthOptions->firstWhere('value', $selectedMonth)['label'],
            'recap' => $recap,
        ];
    }
}
