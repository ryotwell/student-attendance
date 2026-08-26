<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource with search, filter, and pagination.
     */
    public function index(Request $request)
    {
        $query = AcademicYear::withCount('xclasses');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('semester') && in_array($request->semester, ['GANJIL', 'GENAP'])) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('is_active') && in_array($request->is_active, ['1', '0'])) {
            $query->where('is_active', $request->is_active);
        }

        $academicYears = $query->latest()->paginate(10)->withQueryString();

        return view('admin.academic-year.index', compact('academicYears'));
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

        // Jika data is_active = true, nonaktifkan semua tahun ajaran lain
        if (isset($data['is_active']) && $data['is_active']) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

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

        // Jika data is_active = true dan berbeda dengan status sebelumnya, 
        // nonaktifkan semua tahun ajaran lain
        if (isset($data['is_active']) && $data['is_active'] && !$academicYear->is_active) {
            AcademicYear::where('is_active', true)->where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

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