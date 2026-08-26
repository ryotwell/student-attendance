<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
                // Filter tahun ajaran berdasarkan sekolah akan otomatis karena academicYear sudah di-filter di activeAcademicYearOrFail()
            })
            ->orderBy('name');

        // Filter berdasarkan sekolah (kecuali SUPERADMIN)
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }

        return $query->get();
    }

    /**
     * Tahun ajaran aktif milik sekolah user (atau semua jika SUPERADMIN).
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
        if ($request->filled('gender') && in_array($request->gender, ['MALE', 'FEMALE'])) {
            $query->where('gender', $request->gender);
        }

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['AKTIF', 'LULUS', 'PINDAH', 'KELUAR'])) {
            $query->where('status', $request->status);
        }

        // Filter kelas (berdasarkan enrollment pada tahun ajaran aktif)
        if ($request->filled('xclass_id')) {
            $query->whereHas('currentEnrollment', function ($q) use ($request) {
                $q->where('xclass_id', $request->xclass_id);
            });
        }

        $students = $query->latest()->paginate(10)->withQueryString();

        // Ambil daftar kelas pada tahun ajaran aktif (sesuai sekolah user)
        $classes = $this->activeYearClasses();

        return view('admin.student.index', compact('students', 'classes'));
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

        $data = $this->validateRequest($request, $academicYear);
        $xclassId = $data['xclass_id'];
        unset($data['xclass_id']);

        // Tentukan school_id
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            // SUPERADMIN: ambil dari xclass yang dipilih (pastikan konsisten)
            $xclass = Xclass::findOrFail($xclassId);
            $data['school_id'] = $xclass->school_id;
        } else {
            $data['school_id'] = $user->school_id;
        }

        // Validasi tambahan: pastikan xclass_id sesuai dengan school_id
        $xclass = Xclass::findOrFail($xclassId);
        if ($xclass->school_id != $data['school_id']) {
            return back()->withInput()->withErrors([
                'xclass_id' => 'Kelas tidak sesuai dengan sekolah.'
            ]);
        }

        DB::transaction(function () use ($data, $xclassId, $academicYear) {
            $student = Student::create($data);
            $this->enrollStudent($student->id, $xclassId, $academicYear->id);
        });

        return redirect()->route('students.index')->with('success', 'Siswa berhasil ditambahkan.');
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
            abort(403, 'Anda tidak memiliki akses ke siswa ini.');
        }

        $student->load('currentEnrollment.xclass');

        return view('admin.student.edit', [
            'student' => $student,
            'classes' => $this->activeYearClasses(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        if (!$this->canAccessSchool($student)) {
            abort(403, 'Anda tidak memiliki akses ke siswa ini.');
        }

        $academicYear = $this->activeAcademicYearOrFail();

        $data = $this->validateRequest($request, $academicYear, $student);
        $xclassId = $data['xclass_id'];
        unset($data['xclass_id']);

        // Untuk SUPERADMIN, izinkan mengganti school_id? Biasanya tidak, tapi kita bisa.
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN' && $request->filled('school_id')) {
            $request->validate(['school_id' => 'exists:schools,id']);
            $data['school_id'] = $request->school_id;
        } else {
            $data['school_id'] = $student->school_id; // tetap gunakan yang lama
        }

        // Validasi tambahan: pastikan xclass_id sesuai dengan school_id
        $xclass = Xclass::findOrFail($xclassId);
        if ($xclass->school_id != $data['school_id']) {
            return back()->withInput()->withErrors([
                'xclass_id' => 'Kelas tidak sesuai dengan sekolah.'
            ]);
        }

        DB::transaction(function () use ($data, $xclassId, $academicYear, $student) {
            $student->update($data);
            $this->enrollStudent($student->id, $xclassId, $academicYear->id);
        });

        return redirect()->route('students.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        if (!$this->canAccessSchool($student)) {
            abort(403, 'Anda tidak memiliki akses ke siswa ini.');
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    private function validateRequest(Request $request, AcademicYear $academicYear, ?Student $student = null): array
    {
        $ignoreId = $student?->id ?? 'NULL';

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:20', "unique:students,nis,{$ignoreId}"],
            'nisn' => ['required', 'string', 'max:20', "unique:students,nisn,{$ignoreId}"],
            'gender' => ['required', 'in:MALE,FEMALE'],
            'xclass_id' => [
                'required',
                'exists:xclasses,id,academic_year_id,' . $academicYear->id,
            ],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:20'],
        ];

        // Jika SUPERADMIN, izinkan memilih school_id (opsional)
        if (Auth::user()->role === 'SUPERADMIN') {
            $rules['school_id'] = ['nullable', 'exists:schools,id'];
        }

        return $request->validate($rules);
    }

    /**
     * Buat enrollment baru; kalau siswa ternyata sudah punya enrollment
     * di tahun ajaran yang sama (unique student_id+academic_year_id),
     * update kelasnya saja.
     */
    private function enrollStudent(int $studentId, int $xclassId, int $academicYearId): void
    {
        $existing = StudentEnrollment::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->first();

        if ($existing) {
            $existing->update(['xclass_id' => $xclassId]);
            return;
        }

        StudentEnrollment::create([
            'student_id' => $studentId,
            'xclass_id' => $xclassId,
            'academic_year_id' => $academicYearId,
            // school_id otomatis diisi oleh model melalui relasi? Bisa juga kita set manual
            // Tapi sebaiknya model StudentEnrollment memiliki school_id juga.
            // Jika tidak, kita isi melalui observer atau event, atau kita set di sini.
            // Untuk amannya, kita ambil dari xclass.
            'school_id' => Xclass::find($xclassId)->school_id,
        ]);
    }
}