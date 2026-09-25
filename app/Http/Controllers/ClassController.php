<?php

namespace App\Http\Controllers;

use App\Imports\ClassImport;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

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
     * Ambil tahun ajaran yang dapat diakses user.
     */
    private function getAvailableAcademicYears()
    {
        $query = AcademicYear::query()
            ->latest();

        $this->applySchoolFilter($query);

        return $query->get();
    }

    /**
     * Ambil daftar guru.
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
     * Daftar kelas.
     */
    public function index(Request $request)
    {
        $query = Xclass::query()
            ->with([
                'academicYear',
                'user',
            ])
            ->withCount('students');

        $this->applySchoolFilter($query);

        /*
         * Search nama kelas atau kode kelas.
         */
        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                );

                $q->orWhere(
                    'kode_kelas',
                    'like',
                    '%' . $search . '%'
                );

            });
        }

        /*
         * Filter tahun ajaran.
         */
        if ($request->filled('academic_year_id')) {

            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        /*
         * Filter wali kelas.
         */
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

        $academicYears =
            $this->getAvailableAcademicYears();

        $users =
            $this->getAvailableTeachers();

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
     * Form tambah kelas.
     */
    public function create()
    {
        $academicYears =
            $this->getAvailableAcademicYears();

        $users =
            $this->getAvailableTeachers();

        return view('admin.class.create', [
            'academicYears' => $academicYears,
            'users' => $users,
        ]);
    }

    /**
     * Simpan kelas.
     */
    public function store(Request $request)
    {
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
         * Tentukan sekolah.
         */
        if ($user->role === 'SUPERADMIN') {

            $academicYear =
                AcademicYear::findOrFail(
                    $data['academic_year_id']
                );

            $data['school_id'] =
                $academicYear->school_id;

        } else {

            $data['school_id'] =
                $user->school_id;
        }

        /*
         * Pastikan tahun ajaran sesuai sekolah.
         */
        $academicYear =
            AcademicYear::findOrFail(
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
         * Cek kode kelas.
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
         * Validasi wali kelas.
         */
        if (!empty($data['user_id'])) {

            $teacher =
                User::findOrFail(
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
         * untuk satu kelas dalam satu tahun ajaran.
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
                            'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini.',
                    ]);
            }
        }

        Xclass::create($data);

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Kelas berhasil ditambahkan.'
            );
    }

    /**
     * Form import kelas.
     */
    public function importForm()
    {
        $academicYears =
            $this->getAvailableAcademicYears();

        return view(
            'admin.class.import',
            [
                'academicYears' => $academicYears,
            ]
        );
    }

    /**
     * Import kelas dari Excel.
     */
    public function import(Request $request)
    {
        /*
        * Validasi file.
        */
        $request->validate([
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:5120',
            ],
        ], [
            'academic_year_id.required' =>
                'Tahun ajaran wajib dipilih.',

            'academic_year_id.exists' =>
                'Tahun ajaran tidak valid.',

            'file.required' =>
                'File Excel wajib dipilih.',

            'file.file' =>
                'File yang diupload tidak valid.',

            'file.mimes' =>
                'File harus berformat XLSX, XLS, atau CSV.',

            'file.max' =>
                'Ukuran file maksimal 5 MB.',
        ]);

        $user = Auth::user();

        /*
        * Ambil tahun ajaran.
        */
        $academicYear = AcademicYear::findOrFail(
            $request->academic_year_id
        );

        /*
        * Tentukan school_id.
        */
        if ($user->role === 'SUPERADMIN') {

            $schoolId = $academicYear->school_id;

        } else {

            $schoolId = $user->school_id;

            /*
            * Pastikan tahun ajaran milik sekolah user.
            */
            if (
                (int) $academicYear->school_id !==
                (int) $schoolId
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'academic_year_id' =>
                            'Tahun ajaran tidak sesuai dengan sekolah Anda.',
                    ]);
            }
        }

        try {

            /*
            * Buat object import.
            */
            $import = new ClassImport(
                $academicYear->id,
                $schoolId
            );

            /*
            * Jalankan import.
            */
            Excel::import(
                $import,
                $request->file('file')
            );

            /*
            * Ambil hasil.
            */
            $successCount =
                $import->getSuccessCount();

            $errors =
                $import->getErrors();

            /*
            * Jika tidak ada yang berhasil
            * dan semuanya gagal.
            */
            if (
                $successCount === 0 &&
                count($errors) > 0
            ) {

                return back()
                    ->withInput()
                    ->with(
                        'import_errors',
                        $errors
                    )
                    ->with(
                        'import_success_count',
                        0
                    )
                    ->with(
                        'import_error_count',
                        count($errors)
                    );
            }

            /*
            * Berhasil sebagian atau seluruhnya.
            */
            return redirect()
                ->route('classes.index')
                ->with(
                    'success',
                    "Import selesai. {$successCount} kelas berhasil diimport."
                )
                ->with(
                    'import_errors',
                    $errors
                )
                ->with(
                    'import_success_count',
                    $successCount
                )
                ->with(
                    'import_error_count',
                    count($errors)
                );

        } catch (\Throwable $e) {

            /*
            * Simpan detail error ke log.
            */
            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'file' =>
                        'Terjadi kesalahan saat membaca atau memproses file Excel. Pastikan format file sesuai template.',
                ]);
        }
    }

    /**
     * Detail kelas.
     */
    public function show(Xclass $class)
    {
        return redirect()
            ->route('classes.index');
    }

    /**
     * Form edit kelas.
     */
    public function edit(Xclass $class)
    {
        if (!$this->canAccessSchool($class)) {
            abort(
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

        $academicYears =
            $this->getAvailableAcademicYears();

        $users =
            $this->getAvailableTeachers();

        return view(
            'admin.class.edit',
            [
                'class' => $class,
                'academicYears' => $academicYears,
                'users' => $users,
            ]
        );
    }

    /**
     * Update kelas.
     */
    public function update(
        Request $request,
        Xclass $class
    ) {
        if (!$this->canAccessSchool($class)) {
            abort(
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

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
         * Sekolah tetap mengikuti kelas.
         */
        $data['school_id'] =
            $class->school_id;

        /*
         * SUPERADMIN boleh mengubah sekolah.
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
         * Validasi tahun ajaran.
         */
        $academicYear =
            AcademicYear::findOrFail(
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
         * Cek kode kelas.
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
         * Validasi wali kelas.
         */
        if (!empty($data['user_id'])) {

            $teacher =
                User::findOrFail(
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
         * Cek wali kelas duplikat.
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
                            'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini.',
                    ]);
            }
        }

        $class->update($data);

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Kelas berhasil diperbarui.'
            );
    }

    /**
     * Hapus kelas.
     */
    public function destroy(Xclass $class)
    {
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
     * Jadwal kelas.
     */
    public function schedules(Xclass $class)
    {
        if (!$this->canAccessSchool($class)) {
            abort(
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

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
     * Siswa berdasarkan kelas.
     */
    public function students(Xclass $class)
    {
        if (!$this->canAccessSchool($class)) {
            abort(
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

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