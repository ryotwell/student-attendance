<?php

namespace App\Http\Controllers;

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
     */
    public function index()
    {
        $query = Schedule::with(['subject', 'xclass']);

        if (auth()->user()->role === 'GURU') {
            $query->where('user_id', auth()->id());
        }

        $schedules = $query
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get();

        // return $schedules;

        if(Auth::user()->isTeacher()) {
            return view('teacher.schedule.index', compact('schedules'));
        }

        return view('admin.schedule.index', compact('schedules'));
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
            'days' => $this->days(),
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
            'days' => $this->days(),
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

    private function days(): array
    {
        return [
            'MONDAY' => 'Senin',
            'TUESDAY' => 'Selasa',
            'WEDNESDAY' => 'Rabu',
            'THURSDAY' => 'Kamis',
            'FRIDAY' => 'Jumat',
            'SATURDAY' => 'Sabtu',
            'SUNDAY' => 'Minggu',
        ];
    }
}
