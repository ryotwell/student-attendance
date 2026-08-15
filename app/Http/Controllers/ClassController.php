<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Xclass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.class.index', [
            'classes' => Xclass::with('academicYear')->withCount('students')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.class.create', [
            'academicYears' => AcademicYear::latest()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        Xclass::create($data);

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Xclass $class)
    {
        return redirect()->route('classes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Xclass $class)
    {
        return view('admin.class.edit', [
            'class' => $class,
            'academicYears' => AcademicYear::latest()->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Xclass $class)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $class->update($data);

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Xclass $class)
    {
        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil dihapus.');
    }

    // for see class schedules
    public function schedules(\App\Models\Xclass $class)
    {
        $class->load('schedules.subject');

        return view('admin.class.schedule', [
            'title' => 'Jadwal Kelas ' . $class->name,
            'class' => $class,
        ]);
    }
public function students(Xclass $class)
    {
        $students = $class->students()->select('id', 'nis', 'name')->orderBy('name')->get();

        return response()->json($students);
    }
}