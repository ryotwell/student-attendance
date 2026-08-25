<?php

namespace App\Http\Controllers;

use App\Jobs\SendAlphaWhatsAppNotification;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Xclass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Check teacher role.
     */
    private function isTeacher(): bool
    {
        return in_array(auth()->user()->role, [
            'GURU',
            'GURU_BK'
        ]);
    }

    /**
     * Check class access.
     */
    private function canAccessClass($classId): bool
    {
        $user = auth()->user();

        if ($user->role === 'ADMIN') {
            return true;
        }

        return Schedule::where('user_id', $user->id)
            ->where('xclass_id', $classId)
            ->exists();
    }

    /**
     * Pastikan kelas yang diakses adalah kelas dari tahun ajaran yang
     * sedang aktif. Absensi tidak boleh diinput/diedit untuk kelas dari
     * tahun ajaran lama (kelas lama tetap ada di DB untuk histori, tapi
     * read-only lewat report/recap, bukan lewat endpoint ini).
     */
    private function ensureActiveAcademicYear(Xclass $class): void
    {
        $isActive = $class->academicYear && $class->academicYear->is_active;

        abort_if(
            ! $isActive,
            403,
            'Kelas ini bukan bagian dari tahun ajaran yang sedang aktif. Absensi hanya bisa diinput/diedit untuk tahun ajaran aktif.'
        );
    }

    /**
     * Base query attendance untuk 1 kelas.
     */
    private function attendanceQueryForClass($classId)
    {
        return Attendance::whereHas('studentEnrollment', function ($q) use ($classId) {
            $q->where('xclass_id', $classId);
        });
    }

    /**
     * Base query kelas, dibatasi ke tahun ajaran aktif.
     */
    private function activeClassesQuery()
    {
        return Xclass::with('academicYear')
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->orderBy('name');
    }

    /**
     * Display attendance selection form.
     */
    public function index()
    {
        $classes = $this->activeClassesQuery()->get();

        return view('admin.attendance.index', [
            'title' => 'Absensi Siswa',
            'classes' => $classes,
        ]);
    }

    /**
     * Show attendance table.
     */
    public function show(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|integer|exists:xclasses,id',
            'schedule_id' => 'required|integer|exists:schedules,id',
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($validated['date'])->startOfDay();

        if (!$this->canAccessClass($validated['class_id'])) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $class = Xclass::select('id', 'name', 'academic_year_id')
            ->with([
                'academicYear',
                'students' => function ($q) {
                    $q->select('students.id', 'students.name', 'students.nis')
                        ->orderBy('students.name');
                }
            ])
            ->findOrFail($validated['class_id']);

        $this->ensureActiveAcademicYear($class);

        $schedule = Schedule::select('id', 'subject_id')
            ->with('subject:id,name')
            ->findOrFail($validated['schedule_id']);

        $enrollmentByStudentId = StudentEnrollment::where('xclass_id', $class->id)
            ->pluck('id', 'student_id');

        $existingAttendances = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentByStudentId->values())
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $date->toDateString())
            ->get(['student_id', 'status'])
            ->keyBy('student_id');

        $viewData = [
            'title' => 'Absensi: ' . $class->name . ' - ' . $schedule->subject->name,
            'class' => $class,
            'schedule' => $schedule,
            'date' => $date,
            'existingAttendances' => $existingAttendances,
            'statusOptions' => Attendance::STATUS_OPTIONS,
        ];

        if ($this->isTeacher()) {
            return view('teacher.absensi.absensi', $viewData);
        }

        return view('admin.attendance.table', $viewData);
    }

    /**
     * Store attendance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:xclasses,id',
            'schedule_id' => 'required|exists:schedules,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:HADIR,IZIN,SAKIT,ALPHA',
        ]);

        if (!$this->canAccessClass($request->class_id)) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $class = Xclass::with('academicYear')->findOrFail($request->class_id);
        $this->ensureActiveAcademicYear($class);

        $date = Carbon::parse($request->date)->startOfDay();

        $enrollmentByStudentId = StudentEnrollment::where('xclass_id', $request->class_id)
            ->pluck('id', 'student_id');

        foreach ($request->attendances as $attendanceData) {
            $enrollmentId = $enrollmentByStudentId->get($attendanceData['student_id']);

            if (!$enrollmentId) {
                continue;
            }

            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $attendanceData['student_id'],
                    'student_enrollment_id' => $enrollmentId,
                    'schedule_id' => $request->schedule_id,
                    'date' => $date,
                ],
                [
                    'status' => $attendanceData['status'],
                    'user_id' => Auth::id(),
                ]
            );

            if ($attendance->status === 'ALPHA') {
                SendAlphaWhatsAppNotification::dispatch($attendance);
            }
        }

        return redirect()
            ->route('attendance.show', [
                'class_id' => $request->class_id,
                'schedule_id' => $request->schedule_id,
                'date' => $date->format('Y-m-d'),
            ])
            ->with('success', 'Absensi berhasil disimpan.');
    }

    /**
     * Get students of a class (for AJAX student filter dropdown).
     */
    public function getStudents(Xclass $class)
    {
        $students = $class->students()
            ->select('students.id', 'students.name', 'students.nis')
            ->orderBy('students.name')
            ->get();

        return response()->json($students);
    }

    public function getSchedules(Xclass $class)
    {
        $schedules = Schedule::where('xclass_id', $class->id)
            ->with('subject:id,name')
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get(['id', 'day', 'start_time', 'end_time', 'subject_id', 'xclass_id']);

        return response()->json($schedules);
    }

    /**
     * Display attendance report selection form.
     */
    public function report()
    {
        $classes = $this->activeClassesQuery()->get();

        return view('admin.attendance.report', [
            'title' => 'Laporan Absensi',
            'classes' => $classes,
        ]);
    }

    /**
     * Show attendance report for selected class, student, and date range.
     * Tidak dibatasi ke tahun ajaran aktif — laporan boleh dilihat lintas
     * tahun ajaran (histori), berbeda dari input/edit absensi.
     */
    public function reportShow(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:xclasses,id',
            'student_id' => 'nullable|exists:students,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $class = Xclass::with(['academicYear', 'students'])->findOrFail($request->class_id);
        $student = $request->student_id ? Student::find($request->student_id) : null;
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfMonth();

        $query = $this->attendanceQueryForClass($class->id)
            ->with(['schedule.subject', 'studentEnrollment.student'])
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($student) {
            $query->where('student_id', $student->id);
        }

        $attendances = $query->paginate(50)->withQueryString();

        $summaryQuery = $this->attendanceQueryForClass($class->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->when($student, fn($q) => $q->where('student_id', $student->id));

        $statusCounts = (clone $summaryQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = [
            'total' => $statusCounts->sum(),
            'hadir' => $statusCounts->get('HADIR', 0),
            'izin' => $statusCounts->get('IZIN', 0),
            'sakit' => $statusCounts->get('SAKIT', 0),
            'alpha' => $statusCounts->get('ALPHA', 0),
        ];

        return view('admin.attendance.report-show', [
            'title' => 'Laporan Absensi: ' . $class->name,
            'class' => $class,
            'student' => $student,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'attendances' => $attendances,
            'summary' => $summary,
        ]);
    }

    /**
     * Display attendance recap/summary for a class.
     */
    public function recap()
    {
        $classes = $this->activeClassesQuery()->get();

        return view('admin.attendance.recap', [
            'title' => 'Rekap Absensi',
            'classes' => $classes,
        ]);
    }

    /**
     * Show attendance recap for selected class and date range.
     * Sama seperti reportShow, sengaja tidak dibatasi tahun ajaran aktif.
     */
    public function recapShow(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:xclasses,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $class = Xclass::with(['students', 'academicYear'])->findOrFail($request->class_id);
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfMonth();

        $attendances = $this->attendanceQueryForClass($class->id)
            ->with(['schedule.subject', 'studentEnrollment.student'])
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $studentRecaps = $class->students->map(function ($student) use ($attendances) {
            $studentAttendances = $attendances->where('student_id', $student->id);
            $total = $studentAttendances->count();
            $hadir = $studentAttendances->where('status', 'HADIR')->count();

            return [
                'student' => $student,
                'total' => $total,
                'hadir' => $hadir,
                'izin' => $studentAttendances->where('status', 'IZIN')->count(),
                'sakit' => $studentAttendances->where('status', 'SAKIT')->count(),
                'alpha' => $studentAttendances->where('status', 'ALPHA')->count(),
                'percentage' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
            ];
        });

        $totalAll = $attendances->count();
        $hadirAll = $attendances->where('status', 'HADIR')->count();
        $summary = [
            'total' => $totalAll,
            'hadir' => $hadirAll,
            'izin' => $attendances->where('status', 'IZIN')->count(),
            'sakit' => $attendances->where('status', 'SAKIT')->count(),
            'alpha' => $attendances->where('status', 'ALPHA')->count(),
            'percentage' => $totalAll > 0 ? round(($hadirAll / $totalAll) * 100, 1) : 0,
        ];

        return view('admin.attendance.recap-show', [
            'title' => 'Rekap Absensi: ' . $class->name,
            'class' => $class,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'studentRecaps' => $studentRecaps,
            'summary' => $summary,
        ]);
    }

    /**
     * Display attendance list selection form (per class).
     */
    public function list()
    {
        $user = auth()->user();

        if ($user->role === 'GURU' || $user->role === 'GURU_BK') {
            $classIds = Schedule::where('user_id', $user->id)->pluck('xclass_id')->unique();
            $classes = $this->activeClassesQuery()->whereIn('id', $classIds)->get();
        } else {
            $classes = $this->activeClassesQuery()->get();
        }

        return view('admin.attendance.list', [
            'title' => 'Daftar Absensi per Kelas',
            'classes' => $classes,
        ]);
    }

    /**
     * Show attendance list for selected class.
     */
    public function listShow(Xclass $class)
    {
        $user = auth()->user();

        if (($user->role === 'GURU' || $user->role === 'GURU_BK')) {
            $hasAccess = Schedule::where('user_id', $user->id)
                ->where('xclass_id', $class->id)
                ->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke kelas ini.');
            }
        }

        $class->loadMissing('academicYear');
        $this->ensureActiveAcademicYear($class);

        $schedules = Schedule::with('subject')
            ->where('xclass_id', $class->id)
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get();

        $attendances = $this->attendanceQueryForClass($class->id)
            ->with(['schedule.subject', 'studentEnrollment.student'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.attendance.list-show', [
            'title' => 'Daftar Absensi: ' . $class->name,
            'class' => $class,
            'schedules' => $schedules,
            'attendances' => $attendances,
        ]);
    }

    /**
     * Show edit form for specific attendance session.
     */
    public function listEdit(Xclass $class, Schedule $schedule, $date)
    {
        $user = auth()->user();

        if (($user->role === 'GURU' || $user->role === 'GURU_BK')) {
            $hasAccess = Schedule::where('user_id', $user->id)
                ->where('xclass_id', $class->id)
                ->where('id', $schedule->id)
                ->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
            }
        }

        $class->loadMissing('academicYear');
        $this->ensureActiveAcademicYear($class);

        $date = Carbon::parse($date);

        $existingAttendances = $this->attendanceQueryForClass($class->id)
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        return view('admin.attendance.list-edit', [
            'title' => 'Edit Absensi: ' . $class->name . ' - ' . $schedule->subject->name,
            'class' => $class,
            'schedule' => $schedule,
            'date' => $date,
            'existingAttendances' => $existingAttendances,
        ]);
    }

    /**
     * Update attendance records for specific session.
     */
    public function listUpdate(Request $request, Xclass $class, Schedule $schedule, $date)
    {
        $user = auth()->user();

        if (($user->role === 'GURU' || $user->role === 'GURU_BK')) {
            $hasAccess = Schedule::where('user_id', $user->id)
                ->where('xclass_id', $class->id)
                ->where('id', $schedule->id)
                ->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
            }
        }

        $class->loadMissing('academicYear');
        $this->ensureActiveAcademicYear($class);

        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:HADIR,IZIN,SAKIT,ALPHA',
        ]);

        $scheduleId = $schedule->id;
        $date = Carbon::parse($date)->startOfDay();

        $enrollmentByStudentId = StudentEnrollment::where('xclass_id', $class->id)
            ->pluck('id', 'student_id');

        foreach ($request->attendances as $attendanceData) {
            $enrollmentId = $enrollmentByStudentId->get($attendanceData['student_id']);

            if (!$enrollmentId) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'student_id' => $attendanceData['student_id'],
                    'student_enrollment_id' => $enrollmentId,
                    'schedule_id' => $scheduleId,
                    'date' => $date,
                ],
                [
                    'status' => $attendanceData['status'],
                    'user_id' => Auth::id(),
                ]
            );
        }

        return redirect()
            ->route('attendance.list.show', $class)
            ->with('success', 'Absensi berhasil diperbarui.');
    }
}