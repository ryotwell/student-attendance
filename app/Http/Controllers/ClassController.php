<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    /**
     * Terapkan filter school_id jika user bukan SUPERADMIN.
     */
    private function applySchoolFilter($query)
    {
        $user = Auth::user();

        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }

        return $query;
    }

    /**
     * Cek apakah kelas dapat diakses user.
     *
     * SUPERADMIN:
     * - Bisa mengakses semua sekolah.
     *
     * User selain SUPERADMIN:
     * - Hanya bisa mengakses kelas dari sekolahnya sendiri.
     */
    private function canAccessSchool(Xclass $class): bool
    {
        $user = Auth::user();

        if ($user->role === 'SUPERADMIN') {
            return true;
        }

        return (int) $class->school_id === (int) $user->school_id;
    }

    /**
     * Ambil daftar tahun ajaran yang dapat diakses user.
     */
    private function getAvailableAcademicYears()
    {
        $query = AcademicYear::query()
            ->latest();

        $this->applySchoolFilter($query);

        return $query->get();
    }

    /**
     * Ambil daftar guru yang dapat diakses user.
     *
     * Hanya user dengan role GURU.
     */
    private function getAvailableTeachers()
    {
        $query = User::query()
            ->where('role', 'GURU')
            ->orderBy('name');

        $this->applySchoolFilter($query);

        return $query->get();
    }

    /**
     * Display a listing of classes.
     */
    public function index(Request $request)
    {
        $query = Xclass::query()
            ->with([
                'academicYear',
                'user',
            ])
            ->withCount('students');

        // Filter berdasarkan sekolah
        $this->applySchoolFilter($query);

        // Pencarian berdasarkan nama kelas atau kode kelas
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'kode_kelas',
                    'like',
                    '%' . $search . '%'
                );
            });
        }

        // Filter tahun ajaran
        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        // Filter wali kelas
        if ($request->filled('user_id')) {
            $query->where(
                'user_id',
                $request->user_id
            );
        }

        $classes = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Data dropdown filter
        $academicYears = $this->getAvailableAcademicYears();
        $users = $this->getAvailableTeachers();

        return view(
            'admin.class.index',
            compact(
                'classes',
                'academicYears',
                'users'
            )
        );
    }

    /**
     * Show the form for creating a new class.
     */
    public function create()
    {
        $academicYears = $this->getAvailableAcademicYears();
        $users = $this->getAvailableTeachers();

        return view('admin.class.create', [
            'academicYears' => $academicYears,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created class.
     */
    public function store(Request $request)
    {
        /*
         * Validasi input.
         */
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'kode_kelas' => [
                'required',
                'string',
                'max:255',
            ],

            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'user_id' => [
                'nullable',
                'exists:users,id',
            ],
        ], [
            'name.required' =>
                'Nama kelas wajib diisi.',

            'kode_kelas.required' =>
                'Kode kelas wajib diisi.',

            'academic_year_id.required' =>
                'Tahun ajaran wajib dipilih.',

            'academic_year_id.exists' =>
                'Tahun ajaran tidak valid.',

            'user_id.exists' =>
                'Wali kelas tidak valid.',
        ]);

        $user = Auth::user();

        /*
         * Tentukan school_id.
         *
         * SUPERADMIN:
         * school_id diambil dari academic year.
         *
         * User biasa:
         * school_id diambil dari akun user.
         */
        if ($user->role === 'SUPERADMIN') {

            $academicYear = AcademicYear::findOrFail(
                $data['academic_year_id']
            );

            $data['school_id'] = $academicYear->school_id;

        } else {

            $data['school_id'] = $user->school_id;

        }

        /*
         * Pastikan tahun ajaran berasal dari sekolah
         * yang sama dengan kelas.
         */
        $academicYear = AcademicYear::findOrFail(
            $data['academic_year_id']
        );

        if (
            (int) $academicYear->school_id !==
            (int) $data['school_id']
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'academic_year_id' =>
                        'Tahun ajaran tidak sesuai dengan sekolah.',
                ]);
        }

        /*
         * Pastikan kode kelas tidak duplikat
         * pada sekolah dan tahun ajaran yang sama.
         */
        $exists = Xclass::query()
            ->where(
                'kode_kelas',
                $data['kode_kelas']
            )
            ->where(
                'school_id',
                $data['school_id']
            )
            ->where(
                'academic_year_id',
                $data['academic_year_id']
            )
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'kode_kelas' =>
                        'Kode kelas tersebut sudah digunakan pada tahun ajaran ini.',
                ]);
        }

        /*
         * Jika wali kelas dipilih,
         * pastikan guru berasal dari sekolah yang sama.
         */
        if (!empty($data['user_id'])) {

            $teacher = User::findOrFail(
                $data['user_id']
            );

            if ($teacher->role !== 'GURU') {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_id' =>
                            'Wali kelas harus merupakan user dengan role GURU.',
                    ]);
            }

            if (
                (int) $teacher->school_id !==
                (int) $data['school_id']
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_id' =>
                            'Guru tidak berada di sekolah yang sama.',
                    ]);
            }
        }

        /*
         * Satu guru hanya boleh menjadi wali
         * untuk satu kelas pada satu tahun ajaran.
         */
        if (!empty($data['user_id'])) {

            $exists = Xclass::query()
                ->where(
                    'academic_year_id',
                    $data['academic_year_id']
                )
                ->where(
                    'school_id',
                    $data['school_id']
                )
                ->where(
                    'user_id',
                    $data['user_id']
                )
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_id' =>
                            'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini di sekolah ini.',
                    ]);
            }
        }

        /*
         * Buat kelas.
         */
        Xclass::create($data);

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Kelas berhasil ditambahkan.'
            );
    }

    /**
     * Display the specified class.
     */
    public function show(Xclass $class)
    {
        return redirect()
            ->route('classes.index');
    }

    /**
     * Show the form for editing the specified class.
     */
    public function edit(Xclass $class)
    {
        // Cek akses sekolah
        if (!$this->canAccessSchool($class)) {
            abort(
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

        $academicYears = $this->getAvailableAcademicYears();
        $users = $this->getAvailableTeachers();

        return view('admin.class.edit', [
            'class' => $class,
            'academicYears' => $academicYears,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified class.
     */
    public function update(
        Request $request,
        Xclass $class
    ) {
        // Cek akses sekolah
        if (!$this->canAccessSchool($class)) {
            abort(
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

        /*
         * Validasi input.
         */
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'kode_kelas' => [
                'required',
                'string',
                'max:255',
            ],

            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'user_id' => [
                'nullable',
                'exists:users,id',
            ],
        ], [
            'name.required' =>
                'Nama kelas wajib diisi.',

            'kode_kelas.required' =>
                'Kode kelas wajib diisi.',

            'academic_year_id.required' =>
                'Tahun ajaran wajib dipilih.',

            'academic_year_id.exists' =>
                'Tahun ajaran tidak valid.',

            'user_id.exists' =>
                'Wali kelas tidak valid.',
        ]);

        $user = Auth::user();

        /*
         * Secara default, kelas tetap berada
         * di sekolah sebelumnya.
         */
        $data['school_id'] = $class->school_id;

        /*
         * SUPERADMIN boleh mengubah school_id
         * apabila field tersebut dikirim dari form.
         */
        if (
            $user->role === 'SUPERADMIN' &&
            $request->filled('school_id')
        ) {

            $request->validate([
                'school_id' => [
                    'required',
                    'exists:schools,id',
                ],
            ]);

            $data['school_id'] =
                $request->school_id;
        }

        /*
         * Pastikan tahun ajaran sesuai sekolah.
         */
        $academicYear = AcademicYear::findOrFail(
            $data['academic_year_id']
        );

        if (
            (int) $academicYear->school_id !==
            (int) $data['school_id']
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'academic_year_id' =>
                        'Tahun ajaran tidak sesuai dengan sekolah.',
                ]);
        }

        /*
         * Pastikan kode kelas tidak digunakan
         * oleh kelas lain pada sekolah dan
         * tahun ajaran yang sama.
         *
         * Kelas yang sedang diedit dikecualikan.
         */
        $exists = Xclass::query()
            ->where(
                'kode_kelas',
                $data['kode_kelas']
            )
            ->where(
                'school_id',
                $data['school_id']
            )
            ->where(
                'academic_year_id',
                $data['academic_year_id']
            )
            ->where(
                'id',
                '!=',
                $class->id
            )
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'kode_kelas' =>
                        'Kode kelas tersebut sudah digunakan pada tahun ajaran ini.',
                ]);
        }

        /*
         * Jika wali kelas dipilih,
         * validasi role dan sekolah.
         */
        if (!empty($data['user_id'])) {

            $teacher = User::findOrFail(
                $data['user_id']
            );

            if ($teacher->role !== 'GURU') {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_id' =>
                            'Wali kelas harus merupakan user dengan role GURU.',
                    ]);
            }

            if (
                (int) $teacher->school_id !==
                (int) $data['school_id']
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_id' =>
                            'Guru tidak berada di sekolah yang sama.',
                    ]);
            }
        }

        /*
         * Cek apakah guru sudah menjadi wali
         * kelas lain pada tahun ajaran yang sama.
         *
         * Kelas yang sedang diedit dikecualikan.
         */
        if (!empty($data['user_id'])) {

            $exists = Xclass::query()
                ->where(
                    'academic_year_id',
                    $data['academic_year_id']
                )
                ->where(
                    'school_id',
                    $data['school_id']
                )
                ->where(
                    'user_id',
                    $data['user_id']
                )
                ->where(
                    'id',
                    '!=',
                    $class->id
                )
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_id' =>
                            'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini di sekolah ini.',
                    ]);
            }
        }

        /*
         * Update kelas.
         */
        $class->update($data);

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Kelas berhasil diperbarui.'
            );
    }

    /**
     * Remove the specified class.
     */
    public function destroy(Xclass $class)
    {
        // Cek akses sekolah
        if (!$this->canAccessSchool($class)) {
            abort(
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

        $class->delete();

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Kelas berhasil dihapus.'
            );
    }

    /**
     * Menampilkan jadwal pelajaran kelas.
     */
    public function schedules(Xclass $class)
    {
        // Cek akses sekolah
        if (!$this->canAccessSchool($class)) {
            abort(
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

        /*
         * Load jadwal kelas.
         */
        $class->load([
            'schedules.user',
        ]);

        return view(
            'admin.class.schedule',
            [
                'title' =>
                    'Jadwal Kelas ' . $class->name,

                'class' => $class,
            ]
        );
    }

    /**
     * Get students by class (JSON).
     */
    public function students(Xclass $class)
    {
        // Cek akses sekolah
        if (!$this->canAccessSchool($class)) {
            abort(
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

        /*
         * Relasi students() menggunakan
         * student_enrollments sebagai pivot.
         */
        $students = $class
            ->students()
            ->select([
                'students.id',
                'students.nis',
                'students.name',
            ])
            ->orderBy('students.name')
            ->get();

        return response()->json(
            $students
        );
    }
}