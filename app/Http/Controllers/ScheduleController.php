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
     * Terapkan filter school_id berdasarkan xclass atau user.
     */
    private function applySchoolFilter($query)
    {
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->whereHas('xclass', function ($q) use ($user) {
                $q->where('school_id', $user->school_id);
            });
            // Opsional: tambahkan filter pada user juga untuk keamanan ekstra
            // $query->whereHas('user', function ($q) use ($user) {
            //     $q->where('school_id', $user->school_id);
            // });
        }
        return $query;
    }

    /**
     * Cek apakah jadwal milik sekolah user (kecuali SUPERADMIN).
     */
    private function canAccessSchool(Schedule $schedule): bool
    {
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            return true;
        }
        return $schedule->xclass && $schedule->xclass->school_id === $user->school_id;
    }

    /**
     * Ambil daftar kelas yang dapat diakses user.
     */
    private function getAvailableClasses()
    {
        $query = Xclass::orderBy('name');
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }
        return $query->get();
    }

    /**
     * Ambil daftar mata pelajaran yang dapat diakses user.
     */
    private function getAvailableSubjects()
    {
        $query = Subject::orderBy('name');
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }
        return $query->get();
    }

    /**
     * Ambil daftar guru yang dapat diakses user.
     */
    private function getAvailableUsers()
    {
        $query = User::where('role', 'GURU')->orderBy('name');
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }
        return $query->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['subject', 'xclass.academicYear', 'user']);
        $user = Auth::user();

        // Filter berdasarkan sekolah (kecuali SUPERADMIN)
        $this->applySchoolFilter($query);

        // Guru: hanya jadwal miliknya dan tahun ajaran aktif
        if ($user->role === 'GURU') {
            $query->where('user_id', $user->id);
            $activeAcademicYear = AcademicYear::where('is_active', true)
                ->when($user->role !== 'SUPERADMIN', function ($q) use ($user) {
                    $q->where('school_id', $user->school_id);
                })
                ->first();
            if ($activeAcademicYear) {
                $query->whereHas('xclass', fn($q) => $q->where('academic_year_id', $activeAcademicYear->id));
            }
        }

        // Pencarian
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

        // Filter guru (hanya untuk admin, dan harus dari sekolah yang sama)
        if ($request->filled('user_id') && ($user->role === 'ADMIN' || $user->role === 'SUPERADMIN')) {
            // Pastikan user yang difilter berada di sekolah yang sama (untuk non-SUPERADMIN)
            if ($user->role !== 'SUPERADMIN') {
                $userExists = User::where('id', $request->user_id)
                    ->where('school_id', $user->school_id)
                    ->exists();
                if ($userExists) {
                    $query->where('user_id', $request->user_id);
                }
            } else {
                $query->where('user_id', $request->user_id);
            }
        }

        // Urutkan dan paginasi
        $schedules = $query
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        // Data untuk dropdown filter (hanya data yang dapat diakses)
        $days = MenuHelper::days();
        $classes = $this->getAvailableClasses();
        $subjects = $this->getAvailableSubjects();
        $users = $this->getAvailableUsers();

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
        $subjects = $this->getAvailableSubjects();
        $classes = $this->getAvailableClasses();
        $users = $this->getAvailableUsers();

        return view('admin.schedule.create', [
            'subjects' => $subjects,
            'classes'  => $classes,
            'users'    => $users,
            'days'     => MenuHelper::days(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        // Validasi tambahan: pastikan semua entitas berada di sekolah yang sama
        $this->ensureSameSchool($data['xclass_id'], $data['subject_id'], $data['user_id']);

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
        // Cek akses
        if (!$this->canAccessSchool($schedule)) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $subjects = $this->getAvailableSubjects();
        $classes = $this->getAvailableClasses();
        $users = $this->getAvailableUsers();

        return view('admin.schedule.edit', [
            'schedule' => $schedule,
            'subjects' => $subjects,
            'classes'  => $classes,
            'users'    => $users,
            'days'     => MenuHelper::days(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Cek akses
        if (!$this->canAccessSchool($schedule)) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $data = $this->validateRequest($request);

        // Validasi tambahan
        $this->ensureSameSchool($data['xclass_id'], $data['subject_id'], $data['user_id']);

        $schedule->update($data);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        // Cek akses
        if (!$this->canAccessSchool($schedule)) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * Validasi request.
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'day'        => ['required', 'in:MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'xclass_id'  => ['required', 'exists:xclasses,id'],
            'user_id'    => ['required', 'exists:users,id'],
        ]);
    }

    /**
     * Pastikan kelas, mata pelajaran, dan guru berada di sekolah yang sama.
     */
    private function ensureSameSchool($xclassId, $subjectId, $userId)
    {
        $user = Auth::user();

        $xclass = Xclass::findOrFail($xclassId);
        $subject = Subject::findOrFail($subjectId);
        $teacher = User::findOrFail($userId);

        // Jika user bukan SUPERADMIN, semua harus sesuai dengan school_id user
        if ($user->role !== 'SUPERADMIN') {
            if ($xclass->school_id !== $user->school_id) {
                abort(422, 'Kelas tidak berada di sekolah Anda.');
            }
            if ($subject->school_id !== $user->school_id) {
                abort(422, 'Mata pelajaran tidak berada di sekolah Anda.');
            }
            if ($teacher->school_id !== $user->school_id) {
                abort(422, 'Guru tidak berada di sekolah Anda.');
            }
        } else {
            // SUPERADMIN: semua harus memiliki school_id yang sama
            if ($xclass->school_id !== $subject->school_id || $xclass->school_id !== $teacher->school_id) {
                abort(422, 'Kelas, mata pelajaran, dan guru harus berada di sekolah yang sama.');
            }
        }
    }
}