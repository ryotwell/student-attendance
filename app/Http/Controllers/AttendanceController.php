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

        // ADMIN bebas akses
        if ($user->role === 'ADMIN') {
            return true;
        }

        // Guru hanya kelas yang diajar
        return Schedule::where('user_id', $user->id)
            ->where('xclass_id', $classId)
            ->exists();
    }


    /**
     * Base query attendance untuk 1 kelas.
     *
     * attendances tidak punya kolom xclass_id, jadi filter kelas
     * dilakukan lewat relasi studentEnrollment.xclass_id.
     */
    private function attendanceQueryForClass($classId)
    {
        return Attendance::whereHas('studentEnrollment', function ($q) use ($classId) {
            $q->where('xclass_id', $classId);
        });
    }


    /**
     * Display attendance selection form.
     */
    public function index()
    {
        $classes = Xclass::with('academicYear')
            ->orderBy('name')
            ->get();


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


        $date = Carbon::parse($validated['date'])
            ->startOfDay();



        if (!$this->canAccessClass($validated['class_id'])) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $class = Xclass::select(
            'id',
            'name'
        )
            ->with([
                // Siswa didapat lewat pivot student_enrollments, bukan kolom xclass_id
                // di tabel students (kolom itu tidak ada di schema).
                'students' => function ($q) {
                    $q->select(
                        'students.id',
                        'students.name',
                        'students.nis'
                    )
                        ->orderBy('students.name');
                }
            ])
            ->findOrFail($validated['class_id']);



        $schedule = Schedule::select(
            'id',
            'subject_id'
        )
            ->with('subject:id,name')
            ->findOrFail($validated['schedule_id']);



        // Peta student_id => student_enrollment_id untuk kelas ini,
        // dibutuhkan karena Attendance terhubung ke siswa lewat
        // student_enrollment_id, bukan xclass_id langsung.
        $enrollmentByStudentId = StudentEnrollment::where('xclass_id', $class->id)
            ->pluck('id', 'student_id');



        $existingAttendances = Attendance::query()
            ->whereIn('student_enrollment_id', $enrollmentByStudentId->values())
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $date->toDateString())
            ->get([
                'student_id',
                'status'
            ])
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

            return view(
                'teacher.absensi.absensi',
                $viewData
            );
        }

        return view(
            'admin.attendance.table',
            $viewData
        );
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



        $date = Carbon::parse($request->date);



        // Peta student_id => student_enrollment_id untuk kelas ini
        $enrollmentByStudentId = StudentEnrollment::where('xclass_id', $request->class_id)
            ->pluck('id', 'student_id');



        foreach ($request->attendances as $attendanceData) {

            $enrollmentId = $enrollmentByStudentId->get($attendanceData['student_id']);

            // Lewati jika siswa tidak terdaftar (enrolled) di kelas ini,
            // karena student_enrollment_id wajib diisi (foreign key not-nullable).
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

                SendAlphaWhatsAppNotification::dispatch(
                    $attendance
                );
            }
        }

        return redirect()
            ->route('attendance.show', [
                'class_id' => $request->class_id,
                'schedule_id' => $request->schedule_id,
                'date' => $date->format('Y-m-d'),
            ])
            ->with(
                'success',
                'Absensi berhasil disimpan.'
            );
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
        $classes = Xclass::with('academicYear')->orderBy('name')->get();

        return view('admin.attendance.report', [
            'title' => 'Laporan Absensi',
            'classes' => $classes,
        ]);
    }

    /**
     * Show attendance report for selected class, student, and date range.
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

        // Summary statistics (satu query, dikelompokkan per status,
        // menggantikan 4x query count terpisah)
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
        $classes = Xclass::with('academicYear')->orderBy('name')->get();

        return view('admin.attendance.recap', [
            'title' => 'Rekap Absensi',
            'classes' => $classes,
        ]);
    }

    /**
     * Show attendance recap for selected class and date range.
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

        // Get all attendances for the class in date range
        $attendances = $this->attendanceQueryForClass($class->id)
            ->with(['schedule.subject', 'studentEnrollment.student'])
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by student for recap
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

        // Overall summary
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
            // Guru hanya bisa lihat kelas yang diajarin
            $classIds = Schedule::where('user_id', $user->id)->pluck('xclass_id')->unique();
            $classes = Xclass::with('academicYear')->whereIn('id', $classIds)->orderBy('name')->get();
        } else {
            $classes = Xclass::with('academicYear')->orderBy('name')->get();
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

        // Cek akses guru
        if (($user->role === 'GURU' || $user->role === 'GURU_BK')) {
            $hasAccess = Schedule::where('user_id', $user->id)
                ->where('xclass_id', $class->id)
                ->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke kelas ini.');
            }
        }

        $schedules = Schedule::with('subject')
            ->where('xclass_id', $class->id)
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get();

        // Get recent attendance records for this class
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

        // Cek akses guru
        if (($user->role === 'GURU' || $user->role === 'GURU_BK')) {
            $hasAccess = Schedule::where('user_id', $user->id)
                ->where('xclass_id', $class->id)
                ->where('id', $schedule->id)
                ->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
            }
        }

        $date = Carbon::parse($date);

        // Get existing attendances for this class, schedule, and date
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

        // Cek akses guru
        if (($user->role === 'GURU' || $user->role === 'GURU_BK')) {
            $hasAccess = Schedule::where('user_id', $user->id)
                ->where('xclass_id', $class->id)
                ->where('id', $schedule->id)
                ->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
            }
        }

        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:HADIR,IZIN,SAKIT,ALPHA',
        ]);

        $scheduleId = $schedule->id;
        $date = Carbon::parse($date);

        // Peta student_id => student_enrollment_id untuk kelas ini
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