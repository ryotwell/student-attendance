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
     * Cek apakah jadwal milik sekolah user.
     */
    private function canAccessSchool(Schedule $schedule): bool
    {
        $user = Auth::user();

        if ($user->role === 'SUPERADMIN') {
            return true;
        }

        return $schedule->xclass
            && $schedule->xclass->school_id === $user->school_id;
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
        $query = User::where('role', 'GURU')
            ->orderBy('name');

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
        $query = Schedule::with([
            'xclass.academicYear',
            'user',
            'guruPiket',
        ]);

        $user = Auth::user();

        /*
         * Filter berdasarkan sekolah.
         */
        $this->applySchoolFilter($query);

        /*
         * Guru hanya melihat jadwal miliknya
         * dan tahun ajaran aktif.
         */
        if ($user->role === 'GURU') {

            $query->where('user_id', $user->id);

            $activeAcademicYear = AcademicYear::where(
                'is_active',
                true
            )
                ->where(
                    'school_id',
                    $user->school_id
                )
                ->first();

            if ($activeAcademicYear) {
                $query->whereHas(
                    'xclass',
                    fn ($q) => $q->where(
                        'academic_year_id',
                        $activeAcademicYear->id
                    )
                );
            }
        }

        /*
         * Pencarian.
         */
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'subject_name',
                    'like',
                    "%{$search}%"
                )

                ->orWhereHas(
                    'xclass',
                    fn ($xq) => $xq->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                )

                ->orWhereHas(
                    'user',
                    fn ($uq) => $uq->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                )

                ->orWhereHas(
                    'guruPiket',
                    fn ($pq) => $pq->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                );
            });
        }

        /*
         * Filter hari.
         */
        if ($request->filled('day')) {
            $query->where(
                'day',
                $request->day
            );
        }

        /*
         * Filter kelas.
         */
        if ($request->filled('xclass_id')) {
            $query->where(
                'xclass_id',
                $request->xclass_id
            );
        }

        /*
         * Filter guru pengajar.
         */
        if (
            $request->filled('user_id')
            && in_array(
                $user->role,
                ['ADMIN', 'SUPERADMIN']
            )
        ) {

            if ($user->role !== 'SUPERADMIN') {

                $userExists = User::where(
                    'id',
                    $request->user_id
                )
                    ->where(
                        'school_id',
                        $user->school_id
                    )
                    ->where(
                        'role',
                        'GURU'
                    )
                    ->exists();

                if ($userExists) {
                    $query->where(
                        'user_id',
                        $request->user_id
                    );
                }

            } else {

                $query->where(
                    'user_id',
                    $request->user_id
                );
            }
        }

        /*
         * Filter guru piket.
         */
        if (
            $request->filled('guru_piket_id')
            && in_array(
                $user->role,
                ['ADMIN', 'SUPERADMIN']
            )
        ) {

            if ($user->role !== 'SUPERADMIN') {

                $piketExists = User::where(
                    'id',
                    $request->guru_piket_id
                )
                    ->where(
                        'school_id',
                        $user->school_id
                    )
                    ->where(
                        'role',
                        'GURU'
                    )
                    ->exists();

                if ($piketExists) {
                    $query->where(
                        'guru_piket_id',
                        $request->guru_piket_id
                    );
                }

            } else {

                $query->where(
                    'guru_piket_id',
                    $request->guru_piket_id
                );
            }
        }

        /*
         * Urutkan dan paginasi.
         */
        $schedules = $query
            ->orderByRaw(
                "FIELD(
                    day,
                    'MONDAY',
                    'TUESDAY',
                    'WEDNESDAY',
                    'THURSDAY',
                    'FRIDAY',
                    'SATURDAY',
                    'SUNDAY'
                )"
            )
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        /*
         * Data dropdown.
         */
        $days = MenuHelper::days();

        $classes = $this->getAvailableClasses();

        $users = $this->getAvailableUsers();

        /*
         * View guru.
         */
        if ($user->isTeacher()) {
            return view(
                'teacher.schedule.index',
                compact(
                    'schedules',
                    'days',
                    'classes'
                )
            );
        }

        /*
         * View admin.
         */
        return view(
            'admin.schedule.index',
            compact(
                'schedules',
                'days',
                'classes',
                'users'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = $this->getAvailableClasses();

        $users = $this->getAvailableUsers();

        return view(
            'admin.schedule.create',
            [
                'classes' => $classes,
                'users'   => $users,
                'days'    => MenuHelper::days(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        /*
         * Pastikan kelas, guru pengajar,
         * dan guru piket berada pada sekolah
         * yang sesuai.
         */
        $this->ensureSameSchool(
            $data['xclass_id'],
            $data['user_id'],
            $data['guru_piket_id']
        );

        /*
         * Ambil school_id berdasarkan kelas.
         */
        $xclass = Xclass::findOrFail(
            $data['xclass_id']
        );

        $data['school_id'] = $xclass->school_id;

        Schedule::create($data);

        return redirect()
            ->route('schedules.index')
            ->with(
                'success',
                'Jadwal berhasil ditambahkan.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        return redirect()
            ->route('schedules.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        if (!$this->canAccessSchool($schedule)) {
            abort(
                403,
                'Anda tidak memiliki akses ke jadwal ini.'
            );
        }

        $classes = $this->getAvailableClasses();

        $users = $this->getAvailableUsers();

        return view(
            'admin.schedule.edit',
            [
                'schedule' => $schedule,
                'classes'  => $classes,
                'users'    => $users,
                'days'     => MenuHelper::days(),
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        Schedule $schedule
    ) {
        if (!$this->canAccessSchool($schedule)) {
            abort(
                403,
                'Anda tidak memiliki akses ke jadwal ini.'
            );
        }

        $data = $this->validateRequest($request);

        /*
         * Pastikan kelas, guru pengajar,
         * dan guru piket berada pada sekolah
         * yang sesuai.
         */
        $this->ensureSameSchool(
            $data['xclass_id'],
            $data['user_id'],
            $data['guru_piket_id']
        );

        /*
         * Ambil school_id berdasarkan kelas.
         */
        $xclass = Xclass::findOrFail(
            $data['xclass_id']
        );

        $data['school_id'] = $xclass->school_id;

        $schedule->update($data);

        return redirect()
            ->route('schedules.index')
            ->with(
                'success',
                'Jadwal berhasil diperbarui.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        if (!$this->canAccessSchool($schedule)) {
            abort(
                403,
                'Anda tidak memiliki akses ke jadwal ini.'
            );
        }

        $schedule->delete();

        return redirect()
            ->route('schedules.index')
            ->with(
                'success',
                'Jadwal berhasil dihapus.'
            );
    }

    /**
     * Validasi request.
     */
    private function validateRequest(
        Request $request
    ): array {
        return $request->validate([

            'day' => [
                'required',
                'in:MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'subject_name' => [
                'required',
                'string',
                'max:255',
            ],

            'xclass_id' => [
                'required',
                'exists:xclasses,id',
            ],

            'user_id' => [
                'required',
                'exists:users,id',
            ],

            /*
             * GURU PIKET WAJIB.
             */
            'guru_piket_id' => [
                'required',
                'exists:users,id',
            ],
        ]);
    }

    /**
     * Pastikan kelas, guru pengajar,
     * dan guru piket berada di sekolah yang sama.
     */
    private function ensureSameSchool(
        $xclassId,
        $userId,
        $guruPiketId
    ) {
        $user = Auth::user();

        $xclass = Xclass::findOrFail(
            $xclassId
        );

        $teacher = User::findOrFail(
            $userId
        );

        $guruPiket = User::findOrFail(
            $guruPiketId
        );

        /*
         * Guru pengajar harus GURU.
         */
        if ($teacher->role !== 'GURU') {
            abort(
                422,
                'Guru pengajar harus merupakan guru.'
            );
        }

        /*
         * Guru piket harus GURU.
         */
        if ($guruPiket->role !== 'GURU') {
            abort(
                422,
                'Guru piket harus merupakan guru.'
            );
        }

        /*
         * ADMIN.
         */
        if ($user->role !== 'SUPERADMIN') {

            /*
             * Kelas harus berada di sekolah admin.
             */
            if (
                $xclass->school_id
                !== $user->school_id
            ) {
                abort(
                    422,
                    'Kelas tidak berada di sekolah Anda.'
                );
            }

            /*
             * Guru pengajar harus berada
             * di sekolah admin.
             */
            if (
                $teacher->school_id
                !== $user->school_id
            ) {
                abort(
                    422,
                    'Guru tidak berada di sekolah Anda.'
                );
            }

            /*
             * Guru piket harus berada
             * di sekolah admin.
             */
            if (
                $guruPiket->school_id
                !== $user->school_id
            ) {
                abort(
                    422,
                    'Guru piket tidak berada di sekolah Anda.'
                );
            }
        }

        /*
         * Untuk SUPERADMIN, kelas dan guru
         * tetap harus berasal dari sekolah yang sama.
         */
        if ($user->role === 'SUPERADMIN') {

            if (
                $teacher->school_id
                !== $xclass->school_id
            ) {
                abort(
                    422,
                    'Kelas dan guru harus berada di sekolah yang sama.'
                );
            }

            if (
                $guruPiket->school_id
                !== $xclass->school_id
            ) {
                abort(
                    422,
                    'Guru piket harus berada di sekolah yang sama dengan kelas.'
                );
            }
        }
    }
}