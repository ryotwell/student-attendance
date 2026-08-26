<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::with('currentEnrollment.xclass');

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

        // Pagination (10 per halaman) dengan mempertahankan query string
        $students = $query->latest()->paginate(10)->withQueryString();

        // Ambil daftar kelas pada tahun ajaran aktif untuk dropdown filter
        $classes = Xclass::whereHas('academicYear', fn($q) => $q->where('is_active', true))
                         ->orderBy('name')
                         ->get();

        return view('admin.student.index', compact('students', 'classes'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.student.create', [
            // Hanya kelas pada tahun ajaran aktif yang boleh dipilih,
            // karena enrollment baru selalu mengikuti tahun ajaran aktif.
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
        // xclass ikut di-load karena form butuh xclass_id untuk pre-select dropdown.
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
        $academicYear = $this->activeAcademicYearOrFail();

        $data = $this->validateRequest($request, $academicYear, $student);
        $xclassId = $data['xclass_id'];
        unset($data['xclass_id']);

        DB::transaction(function () use ($data, $xclassId, $academicYear, $student) {
            $student->update($data);

            // Enrollment untuk tahun ajaran aktif (kalau sudah ada, tinggal
            // update kelasnya; kalau belum ada, buat baru).
            $this->enrollStudent($student->id, $xclassId, $academicYear->id);
        });

        return redirect()->route('students.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    private function validateRequest(Request $request, AcademicYear $academicYear, ?Student $student = null): array
    {
        $ignoreId = $student?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:20', "unique:students,nis,{$ignoreId}"],
            'nisn' => ['required', 'string', 'max:20', "unique:students,nisn,{$ignoreId}"],
            'gender' => ['required', 'in:MALE,FEMALE'],
            // Kelas harus milik tahun ajaran aktif, bukan sekadar exists di xclasses.
            'xclass_id' => [
                'required',
                'exists:xclasses,id,academic_year_id,' . $academicYear->id,
            ],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:20'],
        ]);
    }

    /**
     * Buat enrollment baru; kalau siswa ternyata sudah punya enrollment
     * di tahun ajaran yang sama (unique student_id+academic_year_id),
     * update kelasnya saja alih-alih insert baru yang akan gagal.
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
        ]);
    }

    /**
     * Daftar kelas pada tahun ajaran yang sedang aktif saja.
     */
    private function activeYearClasses()
    {
        return Xclass::with('academicYear')
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();
    }

    /**
     * Tahun ajaran aktif. Gagal (redirect back dengan error) kalau
     * belum ada tahun ajaran yang diset aktif, supaya data siswa
     * tidak ke-enroll ke tahun ajaran yang salah.
     */
    private function activeAcademicYearOrFail(): AcademicYear
    {
        $academicYear = AcademicYear::where('is_active', true)->first();

        if (! $academicYear) {
            throw ValidationException::withMessages([
                'xclass_id' => 'Belum ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.',
            ]);
        }

        return $academicYear;
    }
}