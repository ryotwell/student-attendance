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
            'class_id' => 'required|integer',
            'schedule_id' => 'required|integer',
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
                'students' => function ($q) {

                    $q->select(
                        'students.id',
                        'students.xclass_id',
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



        $existingAttendances = Attendance::query()
            ->where('xclass_id', $class->id)
            ->where('schedule_id', $schedule->id)
            ->where('date', $date->toDateString())
            ->get([
                'student_id',
                'status'
            ])
            ->keyBy('student_id');



        $viewData = [
            'title' => 'Absensi: '.$class->name.' - '.$schedule->subject->name,
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



        foreach ($request->attendances as $attendanceData) {


            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $attendanceData['student_id'],
                    'xclass_id' => $request->class_id,
                    'schedule_id' => $request->schedule_id,
                    'date' => $date,
                    'user_id' => Auth::id(),
                ],
                [
                    'status' => $attendanceData['status'],
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
                'class_id'=>$request->class_id,
                'schedule_id'=>$request->schedule_id,
                'date'=>$date->format('Y-m-d'),
            ])
            ->with(
                'success',
                'Absensi berhasil disimpan.'
            );
    }
}