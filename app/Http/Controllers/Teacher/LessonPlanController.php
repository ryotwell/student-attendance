<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\LessonPlan;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonPlanController extends Controller
{
    /**
     * Tahun ajaran aktif untuk user (per sekolah, kecuali SUPERADMIN).
     */
    private function activeAcademicYear()
    {
        $user = Auth::user();
        return AcademicYear::where('is_active', true)
            ->when($user->role !== 'SUPERADMIN', fn ($q) => $q->where('school_id', $user->school_id))
            ->first();
    }

    /**
     * Daftar jadwal mengajar user di tahun ajaran aktif (untuk dropdown).
     */
    private function userSchedules()
    {
        $user = Auth::user();
        $academicYear = $this->activeAcademicYear();

        return Schedule::with('xclass')
            ->where('user_id', $user->id)
            ->when($user->role !== 'SUPERADMIN', fn ($q) => $q->where('school_id', $user->school_id))
            ->when($academicYear, fn ($q) => $q->whereHas(
                'xclass',
                fn ($q2) => $q2->where('academic_year_id', $academicYear->id)
            ))
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Pastikan lesson plan milik user login (dan sekolah sama kecuali SUPERADMIN).
     */
    private function canAccess(LessonPlan $lessonPlan): bool
    {
        $user = Auth::user();
        if ($lessonPlan->user_id !== $user->id) {
            return false;
        }
        return $user->role === 'SUPERADMIN' || $lessonPlan->school_id === $user->school_id;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $academicYear = $this->activeAcademicYear();

        $query = LessonPlan::with(['schedule', 'xclass'])
            ->where('user_id', $user->id);

        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }

        // Filter tahun ajaran (default tahun ajaran aktif)
        $selectedAcademicYearId = $request->input(
            'academic_year_id',
            $academicYear?->id
        );

        if ($selectedAcademicYearId) {
            $query->where('academic_year_id', $selectedAcademicYearId);
        }

        $lessonPlans = $query->latest('date')->paginate(10)->withQueryString();

        $academicYears = AcademicYear::query()
            ->when($user->role !== 'SUPERADMIN', fn ($q) => $q->where('school_id', $user->school_id))
            ->latest()
            ->get();

        return view('teacher.lesson-plans.index', compact(
            'lessonPlans',
            'academicYears',
            'selectedAcademicYearId'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schedules = $this->userSchedules();

        return view('teacher.lesson-plans.create', compact('schedules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'schedule_id' => ['nullable', 'exists:schedules,id'],
        ]);

        $schedule = $data['schedule_id'] ? Schedule::with('xclass')->findOrFail($data['schedule_id']) : null;

        // Pastikan jadwal milik user dan satu sekolah
        if ($schedule && $schedule->user_id !== $user->id) {
            return back()->withInput()->withErrors([
                'schedule_id' => 'Jadwal tersebut bukan milik Anda.',
            ]);
        }

        if ($schedule && $user->role !== 'SUPERADMIN' && $schedule->school_id !== $user->school_id) {
            return back()->withInput()->withErrors([
                'schedule_id' => 'Jadwal tidak sesuai dengan sekolah Anda.',
            ]);
        }

        $data['user_id'] = $user->id;
        $data['school_id'] = $user->school_id;
        $data['xclass_id'] = $schedule?->xclass_id;
        $data['academic_year_id'] = $schedule?->xclass?->academic_year_id;

        LessonPlan::create($data);

        return redirect()
            ->route('lesson-plans.index')
            ->with('success', 'Rencana pembelajaran berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LessonPlan $lessonPlan)
    {
        if (!$this->canAccess($lessonPlan)) {
            abort(403, 'Anda tidak memiliki akses ke rencana pembelajaran ini.');
        }

        $schedules = $this->userSchedules();

        return view('teacher.lesson-plans.edit', compact('lessonPlan', 'schedules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LessonPlan $lessonPlan)
    {
        if (!$this->canAccess($lessonPlan)) {
            abort(403, 'Anda tidak memiliki akses ke rencana pembelajaran ini.');
        }

        $user = Auth::user();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'schedule_id' => ['nullable', 'exists:schedules,id'],
        ]);

        $schedule = $data['schedule_id'] ? Schedule::with('xclass')->findOrFail($data['schedule_id']) : null;

        if ($schedule && $schedule->user_id !== $user->id) {
            return back()->withInput()->withErrors([
                'schedule_id' => 'Jadwal tersebut bukan milik Anda.',
            ]);
        }

        if ($schedule && $user->role !== 'SUPERADMIN' && $schedule->school_id !== $user->school_id) {
            return back()->withInput()->withErrors([
                'schedule_id' => 'Jadwal tidak sesuai dengan sekolah Anda.',
            ]);
        }

        $data['xclass_id'] = $schedule?->xclass_id;
        $data['academic_year_id'] = $schedule?->xclass?->academic_year_id;

        $lessonPlan->update($data);

        return redirect()
            ->route('lesson-plans.index')
            ->with('success', 'Rencana pembelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LessonPlan $lessonPlan)
    {
        if (!$this->canAccess($lessonPlan)) {
            abort(403, 'Anda tidak memiliki akses ke rencana pembelajaran ini.');
        }

        $lessonPlan->delete();

        return redirect()
            ->route('lesson-plans.index')
            ->with('success', 'Rencana pembelajaran berhasil dihapus.');
    }
}
