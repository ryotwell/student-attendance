<?php

namespace App\Http\Controllers;

use App\Jobs\SendAlphaWhatsAppNotification;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Xclass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display attendance selection form.
     */
    public function index()
    {
        $classes = Xclass::with('academicYear')->orderBy('name')->get();

        return view('admin.attendance.index', [
            'title' => 'Absensi Siswa',
            'classes' => $classes,
        ]);
    }

    /**
     * Show attendance table for selected class, schedule, and date.
     */
    public function show(Request $request)
    {
        $validated = $request->validate([
            'class_id'    => 'required|integer',
            'schedule_id' => 'required|integer',
            'date'        => 'required|date',
        ]);

        $date = Carbon::parse($validated['date'])->startOfDay();

        $class = Xclass::select('id', 'name')
            ->with(['students' => function ($q) {
                $q->select('students.id', 'students.xclass_id', 'students.name', 'students.nis')
                ->orderBy('students.name');
            }])
            ->findOrFail($validated['class_id']);

        $schedule = Schedule::select('id', 'subject_id')
            ->with('subject:id,name')
            ->findOrFail($validated['schedule_id']);

        // Get existing attendances for this class, schedule, and date
        $existingAttendances = Attendance::query()
            ->where('xclass_id', $class->id)
            ->where('schedule_id', $schedule->id)
            ->where('date', $date->toDateString())
            ->get(['student_id', 'status'])
            ->keyBy('student_id');

        $viewData = [
            'title'               => 'Absensi: ' . $class->name . ' - ' . $schedule->subject->name,
            'class'               => $class,
            'schedule'            => $schedule,
            'date'                => $date,
            'existingAttendances' => $existingAttendances,
            'statusOptions'       => Attendance::STATUS_OPTIONS,
        ];

        if (Auth::user()->role === 'GURU') {
            return view('teacher.absensi.absensi', $viewData);
        }

        return view('admin.attendance.table', $viewData);
    }

    /**
     * Store attendance records.
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

        $classId = $request->class_id;
        $scheduleId = $request->schedule_id;
        $date = Carbon::parse($request->date);

        foreach ($request->attendances as $attendanceData) {
            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $attendanceData['student_id'],
                    'xclass_id' => $classId,
                    'schedule_id' => $scheduleId,
                    'date' => $date,
                    'user_id' => Auth::user()->id,
                ],
                [
                    'status' => $attendanceData['status'],
                ]
            );

            if ($attendance->status === 'ALPHA') {
                SendAlphaWhatsAppNotification::dispatch($attendance);
            }
        }

        return redirect()
            ->route('attendance.show', [
                'class_id' => $classId,
                'schedule_id' => $scheduleId,
                'date' => $date->format('Y-m-d'),
            ])
            ->with('success', 'Absensi berhasil disimpan.');
    }

    /**
     * Get schedules for a class (AJAX).
     */
    public function getSchedules(Xclass $class)
    {
        $query = Schedule::with('subject')
            ->where('xclass_id', $class->id);

        // Filter by teacher if GURU or GURU_BK
        $user = auth()->user();
        if ($user->role === 'GURU' || $user->role === 'GURU_BK') {
            $query->where('user_id', $user->id);
        }

        $schedules = $query
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'day' => $schedule->day,
                    'start_time' => $schedule->start_time->format('H:i'),
                    'end_time' => $schedule->end_time->format('H:i'),
                    'subject' => [
                        'id' => $schedule->subject->id,
                        'name' => $schedule->subject->name,
                    ],
                ];
            });

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

        $query = Attendance::with(['schedule.subject', 'student'])
            ->where('xclass_id', $class->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($student) {
            $query->where('student_id', $student->id);
        }

        $attendances = $query->paginate(50)->withQueryString();

        // Summary statistics
        $summary = [
            'total' => $attendances->total(),
            'hadir' => Attendance::where('xclass_id', $class->id)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->when($student, fn($q) => $q->where('student_id', $student->id))
                ->where('status', 'HADIR')
                ->count(),
            'izin' => Attendance::where('xclass_id', $class->id)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->when($student, fn($q) => $q->where('student_id', $student->id))
                ->where('status', 'IZIN')
                ->count(),
            'sakit' => Attendance::where('xclass_id', $class->id)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->when($student, fn($q) => $q->where('student_id', $student->id))
                ->where('status', 'SAKIT')
                ->count(),
            'alpha' => Attendance::where('xclass_id', $class->id)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->when($student, fn($q) => $q->where('student_id', $student->id))
                ->where('status', 'ALPHA')
                ->count(),
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
        $attendances = Attendance::with(['schedule.subject', 'student'])
            ->where('xclass_id', $class->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by student for recap
        $studentRecaps = $class->students->map(function ($student) use ($attendances, $dateFrom, $dateTo) {
            $studentAttendances = $attendances->where('student_id', $student->id);
            $total = $studentAttendances->count();
            
            return [
                'student' => $student,
                'total' => $total,
                'hadir' => $studentAttendances->where('status', 'HADIR')->count(),
                'izin' => $studentAttendances->where('status', 'IZIN')->count(),
                'sakit' => $studentAttendances->where('status', 'SAKIT')->count(),
                'alpha' => $studentAttendances->where('status', 'ALPHA')->count(),
                'percentage' => $total > 0 ? round(($studentAttendances->where('status', 'HADIR')->count() / $total) * 100, 1) : 0,
            ];
        });

        // Overall summary
        $totalAll = $attendances->count();
        $summary = [
            'total' => $totalAll,
            'hadir' => $attendances->where('status', 'HADIR')->count(),
            'izin' => $attendances->where('status', 'IZIN')->count(),
            'sakit' => $attendances->where('status', 'SAKIT')->count(),
            'alpha' => $attendances->where('status', 'ALPHA')->count(),
            'percentage' => $totalAll > 0 ? round(($attendances->where('status', 'HADIR')->count() / $totalAll) * 100, 1) : 0,
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
        $attendances = Attendance::with(['schedule.subject', 'student'])
            ->where('xclass_id', $class->id)
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
        $existingAttendances = Attendance::where('xclass_id', $class->id)
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

        $classId = $class->id;
        $scheduleId = $schedule->id;
        $date = Carbon::parse($date);

        foreach ($request->attendances as $attendanceData) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $attendanceData['student_id'],
                    'xclass_id' => $classId,
                    'schedule_id' => $scheduleId,
                    'date' => $date,
                ],
                [
                    'status' => $attendanceData['status'],
                ]
            );
        }

        return redirect()
            ->route('attendance.list.show', $class)
            ->with('success', 'Absensi berhasil diperbarui.');
    }
}