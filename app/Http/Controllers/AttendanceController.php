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
     * Cek apakah user dapat mengakses sekolah dari suatu kelas.
     */
    private function canAccessSchool($class): bool
    {
        $user = auth()->user();
        if ($user->role === 'SUPERADMIN') {
            return true;
        }
        return $class->school_id === $user->school_id;
    }

    /**
     * Apply filter school_id ke query jika user bukan SUPERADMIN.
     */
    private function applySchoolFilter($query)
    {
        $user = auth()->user();
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }
        return $query;
    }

    /**
     * Check class access berdasarkan role:
     * - ADMIN: akses ke semua kelas di sekolahnya (sudah dicek oleh canAccessSchool)
     * - GURU/GURU_BK: akses hanya jika punya jadwal di kelas tersebut
     */
    private function canAccessClass($classId): bool
    {
        $user = auth()->user();

        if ($user->role === 'ADMIN') {
            return true;
        }

        // Untuk guru, cek apakah ada jadwal di kelas tersebut
        return Schedule::where('user_id', $user->id)
            ->where('xclass_id', $classId)
            ->exists();
    }

    /**
     * Pastikan kelas adalah bagian dari tahun ajaran aktif.
     */
    private function ensureActiveAcademicYear(Xclass $class): void
    {
        $isActive = $class->academicYear && $class->academicYear->is_active;

        abort_if(
            !$isActive,
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
     * Base query kelas, dibatasi ke tahun ajaran aktif dan sekolah user.
     */
    private function activeClassesQuery()
    {
        $query = Xclass::with('academicYear')
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderBy('name');

        return $this->applySchoolFilter($query);
    }

    /**
     * Display attendance selection form.
     */
    public function index()
    {
        $classes = $this->activeClassesQuery()->get();

        return view('admin.attendance.index', [
            'title'   => 'Absensi Siswa',
            'classes' => $classes,
        ]);
    }

    /**
     * Show attendance table.
     */
    public function show(Request $request)
    {
        $validated = $request->validate([
            'class_id'    => 'required|integer|exists:xclasses,id',
            'schedule_id' => 'required|integer|exists:schedules,id',
            'date'        => 'required|date',
        ]);

        $class = Xclass::select('id', 'name', 'academic_year_id', 'school_id')
            ->with([
                'academicYear',
                'students' => function ($q) {
                    $q->select('students.id', 'students.name', 'students.nis')
                        ->orderBy('students.name');
                }
            ])
            ->findOrFail($validated['class_id']);

        // Cek akses sekolah
        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke sekolah ini.');
        }

        // Cek akses kelas (ADMIN otomatis true, guru cek jadwal)
        if (!$this->canAccessClass($class->id)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $this->ensureActiveAcademicYear($class);

        $schedule = Schedule::select('id', 'subject_name')
            ->findOrFail($validated['schedule_id']);

        $date = Carbon::parse($validated['date'])->startOfDay();

        $enrollmentByStudentId = StudentEnrollment::where('xclass_id', $class->id)
            ->pluck('id', 'student_id');

        $existingAttendances = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentByStudentId->values())
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $date->toDateString())
            ->get(['student_id', 'status'])
            ->keyBy('student_id');

        $viewData = [
            'title'               => 'Absensi: ' . $class->name . ' - ' . $schedule->subject_name,
            'class'               => $class,
            'schedule'            => $schedule,
            'date'                => $date,
            'existingAttendances' => $existingAttendances,
            'statusOptions'       => Attendance::STATUS_OPTIONS,
        ];

        return view('teacher.absensi.absensi', $viewData);
    }

    /**
     * Store attendance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id'    => 'required|exists:xclasses,id',
            'schedule_id' => 'required|exists:schedules,id',
            'date'        => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status'     => 'required|in:HADIR,IZIN,SAKIT,ALPHA',
        ]);

        $class = Xclass::with('academicYear')->findOrFail($request->class_id);

        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke sekolah ini.');
        }

        if (!$this->canAccessClass($class->id)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

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
                    'student_id'            => $attendanceData['student_id'],
                    'student_enrollment_id' => $enrollmentId,
                    'schedule_id'           => $request->schedule_id,
                    'date'                  => $date,
                ],
                [
                    'status'    => $attendanceData['status'],
                    'user_id'   => Auth::id(),
                    'school_id' => $class->school_id,
                ]
            );

            if ($attendance->status === 'ALPHA') {
                if(Auth::user()->school->package === 'premium') {
                    SendAlphaWhatsAppNotification::dispatch($attendance);
                }
            }
        }

        return redirect()
            ->route('attendance.show', [
                'class_id'    => $request->class_id,
                'schedule_id' => $request->schedule_id,
                'date'        => $date->format('Y-m-d'),
            ])
            ->with('success', 'Absensi berhasil disimpan.');
    }

    /**
     * Get students of a class (for AJAX student filter dropdown).
     */
    public function getStudents(Xclass $class)
    {
        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke sekolah ini.');
        }

        $students = $class->students()
            ->select('students.id', 'students.name', 'students.nis')
            ->orderBy('students.name')
            ->get();

        return response()->json($students);
    }

    /**
     * Get schedules of a class (for AJAX schedule dropdown).
     * Mengirim subject_name, bukan subject_id.
     */
    public function getSchedules(Xclass $class)
    {
        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke sekolah ini.');
        }

        $schedules = Schedule::where('xclass_id', $class->id)
            ->select('id', 'day', 'start_time', 'end_time', 'subject_name', 'xclass_id')
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get();

        return response()->json($schedules);
    }

    /**
     * Display attendance report selection form.
     */
    public function report()
    {
        $classes = $this->activeClassesQuery()->get();

        return view('admin.attendance.report', [
            'title'   => 'Laporan Absensi',
            'classes' => $classes,
        ]);
    }

    /**
     * Show attendance report for selected class, student, and date range.
     */
    public function reportShow(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:xclasses,id',
            'student_id' => 'nullable|exists:students,id',
            'date_from'  => 'nullable|date',
            'date_to'    => 'nullable|date|after_or_equal:date_from',
        ]);

        $class = Xclass::with(['academicYear', 'students'])->findOrFail($request->class_id);

        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke sekolah ini.');
        }

        $student = $request->student_id ? Student::find($request->student_id) : null;
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfMonth();

        $query = $this->attendanceQueryForClass($class->id)
            ->with(['schedule', 'studentEnrollment.student']) // <-- tanpa subject
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
            'izin'  => $statusCounts->get('IZIN', 0),
            'sakit' => $statusCounts->get('SAKIT', 0),
            'alpha' => $statusCounts->get('ALPHA', 0),
        ];

        return view('admin.attendance.report-show', [
            'title'       => 'Laporan Absensi: ' . $class->name,
            'class'       => $class,
            'student'     => $student,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'attendances' => $attendances,
            'summary'     => $summary,
        ]);
    }

    /**
     * Display attendance recap/summary for a class.
     */
    public function recap()
    {
        $classes = $this->activeClassesQuery()->get();

        return view('admin.attendance.recap', [
            'title'   => 'Rekap Absensi',
            'classes' => $classes,
        ]);
    }

    /**
     * Show attendance recap for selected class and date range.
     */
    public function recapShow(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:xclasses,id',
            'date_from'  => 'nullable|date',
            'date_to'    => 'nullable|date|after_or_equal:date_from',
        ]);

        $class = Xclass::with(['students', 'academicYear'])->findOrFail($request->class_id);

        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke sekolah ini.');
        }

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfMonth();

        $attendances = $this->attendanceQueryForClass($class->id)
            ->with(['schedule', 'studentEnrollment.student']) // <-- tanpa subject
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $studentRecaps = $class->students->map(function ($student) use ($attendances) {
            $studentAttendances = $attendances->where('student_id', $student->id);
            $total = $studentAttendances->count();
            $hadir = $studentAttendances->where('status', 'HADIR')->count();

            return [
                'student'    => $student,
                'total'      => $total,
                'hadir'      => $hadir,
                'izin'       => $studentAttendances->where('status', 'IZIN')->count(),
                'sakit'      => $studentAttendances->where('status', 'SAKIT')->count(),
                'alpha'      => $studentAttendances->where('status', 'ALPHA')->count(),
                'percentage' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
            ];
        });

        $totalAll = $attendances->count();
        $hadirAll = $attendances->where('status', 'HADIR')->count();
        $summary = [
            'total'      => $totalAll,
            'hadir'      => $hadirAll,
            'izin'       => $attendances->where('status', 'IZIN')->count(),
            'sakit'      => $attendances->where('status', 'SAKIT')->count(),
            'alpha'      => $attendances->where('status', 'ALPHA')->count(),
            'percentage' => $totalAll > 0 ? round(($hadirAll / $totalAll) * 100, 1) : 0,
        ];

        return view('admin.attendance.recap-show', [
            'title'         => 'Rekap Absensi: ' . $class->name,
            'class'         => $class,
            'dateFrom'      => $dateFrom,
            'dateTo'        => $dateTo,
            'studentRecaps' => $studentRecaps,
            'summary'       => $summary,
        ]);
    }
}