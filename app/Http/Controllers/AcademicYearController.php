<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.academic-year.index', [
            'academicYears' => AcademicYear::withCount('xclasses')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.academic-year.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        AcademicYear::create($data);

        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        return redirect()->route('academic-years.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-year.edit', [
            'academicYear' => $academicYear,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $this->validateRequest($request);

        $academicYear->update($data);

        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'in:GANJIL,GENAP'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
