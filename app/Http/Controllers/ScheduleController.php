<?php

namespace App\Http\Controllers;

use App\Helpers\MenuHelper;
use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Dibatasi ke jadwal pada kelas yang berada di tahun ajaran yang
     * sedang aktif. Schedule tidak punya academic_year_id sendiri —
     * terikat ke xclass, dan xclass yang terikat ke academic_year_id.
     * Tanpa filter ini, jadwal dari kelas tahun ajaran lama tetap
     * muncul di daftar.
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['subject', 'xclass.academicYear', 'user']);

        $user = auth()->user();

        // Guru: hanya jadwal miliknya dan tahun ajaran aktif
        if ($user->role === 'GURU') {
            $query->where('user_id', $user->id);
            $activeAcademicYear = AcademicYear::where('is_active', true)->first();
            if ($activeAcademicYear) {
                $query->whereHas('xclass', fn($q) => $q->where('academic_year_id', $activeAcademicYear->id));
            }
        }

        // Pencarian (mata pelajaran / kelas)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('subject', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('xclass', fn($xq) => $xq->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter hari
        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        // Filter kelas
        if ($request->filled('xclass_id')) {
            $query->where('xclass_id', $request->xclass_id);
        }

        // Filter mata pelajaran
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter guru (hanya untuk admin)
        if ($request->filled('user_id') && $user->role === 'ADMIN') {
            $query->where('user_id', $request->user_id);
        }

        // Urutkan dan paginasi
        $schedules = $query
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        // Data untuk dropdown filter
        $days = MenuHelper::days();
        $classes = Xclass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $users = User::where('role', 'GURU')->orderBy('name')->get();

        if ($user->isTeacher()) {
            return view('teacher.schedule.index', compact('schedules', 'days', 'classes', 'subjects'));
        }

        return view('admin.schedule.index', compact('schedules', 'days', 'classes', 'subjects', 'users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.schedule.create', [
            'subjects' => Subject::orderBy('name')->get(),
            'classes' => Xclass::orderBy('name')->get(),
            'users' => User::where('role', 'GURU')->orderBy('name')->get(),
            'days' => MenuHelper::days(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        Schedule::create($data);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        return redirect()->route('schedules.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        return view('admin.schedule.edit', [
            'schedule' => $schedule,
            'subjects' => Subject::orderBy('name')->get(),
            'classes' => Xclass::orderBy('name')->get(),
            'users' => User::where('role', 'GURU')->orderBy('name')->get(),
            'days' => MenuHelper::days(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $data = $this->validateRequest($request);

        $schedule->update($data);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'day' => ['required', 'in:MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'xclass_id' => ['required', 'exists:xclasses,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);
    }
}