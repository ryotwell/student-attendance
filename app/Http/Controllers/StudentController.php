<?php

namespace App\Http\Controllers;

use App\Imports\StudentImport;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class StudentController extends Controller
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
     * Cek apakah siswa milik sekolah user (kecuali SUPERADMIN).
     */
    private function canAccessSchool(Student $student): bool
    {
        $user = Auth::user();

        if ($user->role === 'SUPERADMIN') {
            return true;
        }

        return $student->school_id === $user->school_id;
    }

    /**
     * Daftar kelas pada tahun ajaran aktif yang dapat diakses user.
     */
    private function activeYearClasses()
    {
        $query = Xclass::with('academicYear')
            ->whereHas('academicYear', function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('name');

        // Filter berdasarkan sekolah kecuali SUPERADMIN
        $user = Auth::user();

        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }

        return $query->get();
    }

    /**
     * Tahun ajaran aktif milik sekolah user
     * atau semua jika SUPERADMIN.
     */
    private function activeAcademicYearOrFail(): AcademicYear
    {
        $user = Auth::user();

        $query = AcademicYear::where('is_active', true);

        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }

        $academicYear = $query->first();

        if (!$academicYear) {
            throw ValidationException::withMessages([
                'xclass_id' => 'Belum ada tahun ajaran aktif di sekolah ini. Aktifkan tahun ajaran terlebih dahulu.',
            ]);
        }

        return $academicYear;
    }

    /**
     * Daftar tahun ajaran yang dapat diakses user.
     *
     * Digunakan untuk proses import siswa.
     */
    private function availableAcademicYears()
    {
        $query = AcademicYear::query()
            ->orderByDesc('is_active')
            ->orderByDesc('id');

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
        $query = Student::with('currentEnrollment.xclass');

        $query = $this->applySchoolFilter($query);

        // Pencarian berdasarkan nama, NIS, atau NISN
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Filter gender
        if (
            $request->filled('gender') &&
            in_array($request->gender, ['MALE', 'FEMALE'])
        ) {
            $query->where('gender', $request->gender);
        }

        // Filter status
        if (
            $request->filled('status') &&
            in_array(
                $request->status,
                ['AKTIF', 'LULUS', 'PINDAH', 'KELUAR']
            )
        ) {
            $query->where('status', $request->status);
        }

        // Filter kelas
        if ($request->filled('xclass_id')) {
            $query->whereHas('currentEnrollment', function ($q) use ($request) {
                $q->where('xclass_id', $request->xclass_id);
            });
        }

        $students = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Ambil daftar kelas pada tahun ajaran aktif
        $classes = $this->activeYearClasses();

        return view(
            'admin.student.index',
            compact('students', 'classes')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.student.create', [
            'classes' => $this->activeYearClasses(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $academicYear = $this->activeAcademicYearOrFail();

        $data = $this->validateRequest(
            $request,
            $academicYear
        );

        $xclassId = $data['xclass_id'];

        unset($data['xclass_id']);

        // Tentukan school_id
        $user = Auth::user();

        if ($user->role === 'SUPERADMIN') {

            // SUPERADMIN mengambil sekolah dari kelas
            $xclass = Xclass::findOrFail($xclassId);

            $data['school_id'] = $xclass->school_id;

        } else {

            $data['school_id'] = $user->school_id;
        }

        // Pastikan kelas sesuai dengan sekolah
        $xclass = Xclass::findOrFail($xclassId);

        if ($xclass->school_id != $data['school_id']) {

            return back()
                ->withInput()
                ->withErrors([
                    'xclass_id' => 'Kelas tidak sesuai dengan sekolah.'
                ]);
        }

        DB::transaction(function () use (
            $data,
            $xclassId,
            $academicYear
        ) {

            $student = Student::create($data);

            $this->enrollStudent(
                $student->id,
                $xclassId,
                $academicYear->id
            );
        });

        return redirect()
            ->route('students.index')
            ->with(
                'success',
                'Siswa berhasil ditambahkan.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return redirect()->route('students.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        if (!$this->canAccessSchool($student)) {
            abort(
                403,
                'Anda tidak memiliki akses ke siswa ini.'
            );
        }

        $student->load(
            'currentEnrollment.xclass'
        );

        return view('admin.student.edit', [
            'student' => $student,
            'classes' => $this->activeYearClasses(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        Student $student
    ) {
        if (!$this->canAccessSchool($student)) {
            abort(
                403,
                'Anda tidak memiliki akses ke siswa ini.'
            );
        }

        $academicYear = $this->activeAcademicYearOrFail();

        $data = $this->validateRequest(
            $request,
            $academicYear,
            $student
        );

        $xclassId = $data['xclass_id'];

        unset($data['xclass_id']);

        // Untuk SUPERADMIN, school_id dapat dipilih
        $user = Auth::user();

        if (
            $user->role === 'SUPERADMIN' &&
            $request->filled('school_id')
        ) {

            $request->validate([
                'school_id' => 'exists:schools,id'
            ]);

            $data['school_id'] = $request->school_id;

        } else {

            $data['school_id'] = $student->school_id;
        }

        // Pastikan kelas sesuai dengan sekolah
        $xclass = Xclass::findOrFail($xclassId);

        if ($xclass->school_id != $data['school_id']) {

            return back()
                ->withInput()
                ->withErrors([
                    'xclass_id' => 'Kelas tidak sesuai dengan sekolah.'
                ]);
        }

        DB::transaction(function () use (
            $data,
            $xclassId,
            $academicYear,
            $student
        ) {

            $student->update($data);

            $this->enrollStudent(
                $student->id,
                $xclassId,
                $academicYear->id
            );
        });

        return redirect()
            ->route('students.index')
            ->with(
                'success',
                'Siswa berhasil diperbarui.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        if (!$this->canAccessSchool($student)) {
            abort(
                403,
                'Anda tidak memiliki akses ke siswa ini.'
            );
        }

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with(
                'success',
                'Siswa berhasil dihapus.'
            );
    }

    /**
     * Validasi request tambah/edit siswa.
     */
    private function validateRequest(
        Request $request,
        AcademicYear $academicYear,
        ?Student $student = null
    ): array {

        $ignoreId = $student?->id ?? 'NULL';

        $rules = [

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'nis' => [
                'required',
                'string',
                'max:20',
                "unique:students,nis,{$ignoreId}"
            ],

            'nisn' => [
                'required',
                'string',
                'max:20',
                "unique:students,nisn,{$ignoreId}"
            ],

            'gender' => [
                'required',
                'in:MALE,FEMALE'
            ],

            'xclass_id' => [
                'required',
                'exists:xclasses,id,academic_year_id,' .
                    $academicYear->id,
            ],

            'parent_name' => [
                'nullable',
                'string',
                'max:255'
            ],

            'parent_phone' => [
                'nullable',
                'string',
                'max:20'
            ],
        ];

        // SUPERADMIN dapat memilih school_id
        if (Auth::user()->role === 'SUPERADMIN') {

            $rules['school_id'] = [
                'nullable',
                'exists:schools,id'
            ];
        }

        return $request->validate($rules);
    }

    /**
     * Buat enrollment baru.
     *
     * Jika siswa sudah mempunyai enrollment pada
     * tahun ajaran yang sama, kelas akan diperbarui.
     */
    private function enrollStudent(
        int $studentId,
        int $xclassId,
        int $academicYearId
    ): void {

        $existing = StudentEnrollment::where(
                'student_id',
                $studentId
            )
            ->where(
                'academic_year_id',
                $academicYearId
            )
            ->first();

        if ($existing) {

            $existing->update([
                'xclass_id' => $xclassId,
            ]);

            return;
        }

        $xclass = Xclass::findOrFail($xclassId);

        StudentEnrollment::create([
            'student_id' => $studentId,
            'xclass_id' => $xclassId,
            'academic_year_id' => $academicYearId,
            'school_id' => $xclass->school_id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORT SISWA
    |--------------------------------------------------------------------------
    */

    /**
     * Form import siswa.
     *
     * User memilih tahun ajaran kemudian mengupload Excel.
     */
    public function importForm()
    {
        $academicYears = $this->availableAcademicYears();

        return view(
            'admin.student.import',
            compact('academicYears')
        );
    }

    /**
     * Proses import siswa dari Excel.
     *
     * Format Excel:
     *
     * Nama_Siswa
     * NIS
     * NISN
     * Jenis_Kelamin
     * Status
     * Nama_Orang_Tua
     * No_HP_Orang_Tua
     * Kode_Kelas
     */
    public function import(Request $request)
    {
        $request->validate([
            'academic_year_id' => [
                'required',
                'exists:academic_years,id'
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
                'Tahun ajaran tidak ditemukan.',

            'file.required' =>
                'File Excel wajib dipilih.',

            'file.file' =>
                'File yang diupload tidak valid.',

            'file.mimes' =>
                'File harus berformat XLSX, XLS, atau CSV.',

            'file.max' =>
                'Ukuran file maksimal 5 MB.',
        ]);

        try {

            $user = Auth::user();

            /*
             * Ambil tahun ajaran.
             */
            $academicYearQuery = AcademicYear::query()
                ->where(
                    'id',
                    $request->academic_year_id
                );

            /*
             * User selain SUPERADMIN hanya boleh
             * mengakses tahun ajaran sekolahnya sendiri.
             */
            if ($user->role !== 'SUPERADMIN') {

                $academicYearQuery->where(
                    'school_id',
                    $user->school_id
                );
            }

            $academicYear = $academicYearQuery->first();

            if (!$academicYear) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'academic_year_id' =>
                            'Tahun ajaran tidak sesuai dengan sekolah Anda.'
                    ]);
            }

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
                if ($academicYear->school_id != $schoolId) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            'academic_year_id' =>
                                'Tahun ajaran tidak sesuai dengan sekolah Anda.'
                        ]);
                }
            }

            /*
             * Jalankan import.
             */
            $import = new StudentImport(
                $academicYear->id,
                $schoolId
            );

            Excel::import(
                $import,
                $request->file('file')
            );

            /*
             * Ambil hasil import.
             */
            $errors = $import->getErrors();

            $successCount = $import->getSuccessCount();
            $errorCount = count($errors);

            return redirect()
                ->route('students.index')
                ->with(
                    'success',
                    "Import siswa selesai. {$successCount} data berhasil diimport dan {$errorCount} data mengalami error."
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
                    $errorCount
                );

        } catch (ValidationException $e) {

            return back()
                ->withInput()
                ->withErrors(
                    $e->errors()
                );

        } catch (Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'file' =>
                        'Terjadi kesalahan saat mengimport data siswa: ' .
                        $e->getMessage()
                ]);
        }
    }
}