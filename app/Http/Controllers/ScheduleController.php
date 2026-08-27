<?php

namespace App\Http\Controllers;

use App\Helpers\MenuHelper;
use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Terapkan filter school_id berdasarkan xclass.
     */
    private function applySchoolFilter($query)
    {
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->whereHas('xclass', function ($q) use ($user) {
                $q->where('school_id', $user->school_id);
            });
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
        $query = Schedule::with(['xclass.academicYear', 'user']);
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

        // Pencarian (cari di subject_name, xclass.name, user.name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject_name', 'like', "%{$search}%")
                  ->orWhereHas('xclass', fn($xq) => $xq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
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

        // Filter guru (hanya untuk admin/superadmin)
        if ($request->filled('user_id') && ($user->role === 'ADMIN' || $user->role === 'SUPERADMIN')) {
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

        // Data untuk dropdown filter (hanya kelas dan guru)
        $days = MenuHelper::days();
        $classes = $this->getAvailableClasses();
        $users = $this->getAvailableUsers();

        if ($user->isTeacher()) {
            return view('teacher.schedule.index', compact('schedules', 'days', 'classes'));
        }

        return view('admin.schedule.index', compact('schedules', 'days', 'classes', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = $this->getAvailableClasses();
        $users = $this->getAvailableUsers();

        return view('admin.schedule.create', [
            'classes' => $classes,
            'users'   => $users,
            'days'    => MenuHelper::days(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        // Ambil school_id dari kelas yang dipilih
        $xclass = Xclass::findOrFail($data['xclass_id']);
        $data['school_id'] = $xclass->school_id;

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
        if (!$this->canAccessSchool($schedule)) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $classes = $this->getAvailableClasses();
        $users = $this->getAvailableUsers();

        return view('admin.schedule.edit', [
            'schedule' => $schedule,
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
        if (!$this->canAccessSchool($schedule)) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $data = $this->validateRequest($request);

        // Ambil school_id dari kelas yang dipilih (bisa berubah)
        $xclass = Xclass::findOrFail($data['xclass_id']);
        $data['school_id'] = $xclass->school_id;

        $schedule->update($data);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        if (!$this->canAccessSchool($schedule)) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * Validasi request.
     * subject_name wajib diisi sebagai string.
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'day'          => ['required', 'in:MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY'],
            'start_time'   => ['required', 'date_format:H:i'],
            'end_time'     => ['required', 'date_format:H:i', 'after:start_time'],
            'subject_name' => ['required', 'string', 'max:255'],
            'xclass_id'    => ['required', 'exists:xclasses,id'],
            'user_id'      => ['required', 'exists:users,id'],
        ]);
    }

    /**
     * Pastikan kelas dan guru berada di sekolah yang sama.
     */
    private function ensureSameSchool($xclassId, $userId)
    {
        $user = Auth::user();

        $xclass = Xclass::findOrFail($xclassId);
        $teacher = User::findOrFail($userId);

        if ($user->role !== 'SUPERADMIN') {
            if ($xclass->school_id !== $user->school_id) {
                abort(422, 'Kelas tidak berada di sekolah Anda.');
            }
            if ($teacher->school_id !== $user->school_id) {
                abort(422, 'Guru tidak berada di sekolah Anda.');
            }
        } else {
            if ($xclass->school_id !== $teacher->school_id) {
                abort(422, 'Kelas dan guru harus berada di sekolah yang sama.');
            }
        }
    }
}