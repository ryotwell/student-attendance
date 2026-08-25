<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.student.index', [
            // Model Student tidak punya relasi xclass() langsung; kelas siswa
            // didapat lewat currentEnrollment.xclass (tabel student_enrollments).
            'students' => Student::with('currentEnrollment.xclass')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.student.create', [
            'classes' => Xclass::with('academicYear')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $xclassId = $data['xclass_id'];
        unset($data['xclass_id']);

        DB::transaction(function () use ($data, $xclassId) {
            $student = Student::create($data);

            $academicYear = $this->resolveAcademicYearForClass($xclassId);

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
            'classes' => Xclass::with('academicYear')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $data = $this->validateRequest($request, $student);
        $xclassId = $data['xclass_id'];
        unset($data['xclass_id']);

        DB::transaction(function () use ($data, $xclassId, $student) {
            $student->update($data);

            $enrollment = $student->currentEnrollment()->first();

            if ($enrollment) {
                // Pindah kelas: kalau kelas berubah, tahun ajaran mengikuti
                // kelas tujuan (jaga konsistensi unique [student_id, academic_year_id]).
                if ($enrollment->xclass_id !== $xclassId) {
                    $academicYear = $this->resolveAcademicYearForClass($xclassId);

                    // Kalau siswa sudah pernah enroll di tahun ajaran tujuan
                    // (misal dikembalikan ke tahun ajaran lama), unique constraint
                    // (student_id, academic_year_id) akan bentrok dengan enrollment
                    // lama itu. Pakai enrollment yang sudah ada, jangan buat baru.
                    $existingEnrollment = StudentEnrollment::where('student_id', $student->id)
                        ->where('academic_year_id', $academicYear->id)
                        ->where('id', '!=', $enrollment->id)
                        ->first();

                    if ($existingEnrollment) {
                        $existingEnrollment->update(['xclass_id' => $xclassId]);
                    } else {
                        $enrollment->update([
                            'xclass_id' => $xclassId,
                            'academic_year_id' => $academicYear->id,
                        ]);
                    }
                }
            } else {
                $academicYear = $this->resolveAcademicYearForClass($xclassId);

                $this->enrollStudent($student->id, $xclassId, $academicYear->id);
            }
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

    private function validateRequest(Request $request, ?Student $student = null): array
    {
        $ignoreId = $student?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:20', "unique:students,nis,{$ignoreId}"],
            'nisn' => ['required', 'string', 'max:20', "unique:students,nisn,{$ignoreId}"],
            'gender' => ['required', 'in:MALE,FEMALE'],
            'xclass_id' => ['required', 'exists:xclasses,id'],
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
     * Tahun ajaran dari kelas terpilih (kelas selalu terikat ke satu tahun ajaran).
     */
    private function resolveAcademicYearForClass(int $xclassId): AcademicYear
    {
        return Xclass::findOrFail($xclassId)->academicYear;
    }
}