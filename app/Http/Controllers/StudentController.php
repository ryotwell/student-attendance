<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Xclass;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.student.index', [
            'students' => Student::with('xclass')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.student.create', [
            'classes' => Xclass::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        Student::create($data);

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
        return view('admin.student.edit', [
            'student' => $student,
            'classes' => Xclass::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $data = $this->validateRequest($request, $student);

        $student->update($data);

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
        $ignoreNis = $student ? ",{$student->id}" : '';
        $ignoreNisn = $student ? ",{$student->id}" : '';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:20', "unique:students,nis{$ignoreNis}"],
            'nisn' => ['required', 'string', 'max:20', "unique:students,nisn{$ignoreNisn}"],
            'gender' => ['required', 'in:MALE,FEMALE'],
            'xclass_id' => ['required', 'exists:xclasses,id'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:20'],
        ]);
    }
}
