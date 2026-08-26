<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicYearController extends Controller
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
     * Cek apakah tahun ajaran milik sekolah user (kecuali SUPERADMIN).
     */
    private function canAccessSchool(AcademicYear $academicYear): bool
    {
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            return true;
        }
        return $academicYear->school_id === $user->school_id;
    }

    /**
     * Display a listing of the resource with search, filter, and pagination.
     */
    public function index(Request $request)
    {
        $query = AcademicYear::withCount('xclasses');
        $query = $this->applySchoolFilter($query);

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

        // Tentukan school_id
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            // SUPERADMIN bisa memilih sekolah via request (jika ada)
            if ($request->filled('school_id')) {
                $request->validate([
                    'school_id' => 'required|exists:schools,id',
                ]);
                $data['school_id'] = $request->school_id;
            } else {
                // Fallback: gunakan sekolah pertama (atau bisa diarahkan ke halaman pilih sekolah)
                $firstSchool = School::first();
                if (!$firstSchool) {
                    return back()->withErrors(['school_id' => 'Belum ada sekolah.']);
                }
                $data['school_id'] = $firstSchool->id;
            }
        } else {
            // Non-SUPERADMIN otomatis menggunakan sekolahnya
            $data['school_id'] = $user->school_id;
        }

        // Jika is_active true, nonaktifkan semua tahun ajaran lain di sekolah yang sama
        if (isset($data['is_active']) && $data['is_active']) {
            AcademicYear::where('school_id', $data['school_id'])
                ->where('is_active', true)
                ->update(['is_active' => false]);
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
        // Cek akses sekolah
        if (!$this->canAccessSchool($academicYear)) {
            abort(403, 'Anda tidak memiliki akses ke tahun ajaran ini.');
        }

        return view('admin.academic-year.edit', [
            'academicYear' => $academicYear,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        // Cek akses sekolah
        if (!$this->canAccessSchool($academicYear)) {
            abort(403, 'Anda tidak memiliki akses ke tahun ajaran ini.');
        }

        $data = $this->validateRequest($request);

        // Jika is_active true dan berbeda dengan status sebelumnya,
        // nonaktifkan semua tahun ajaran lain di sekolah yang sama
        if (isset($data['is_active']) && $data['is_active'] && !$academicYear->is_active) {
            AcademicYear::where('school_id', $academicYear->school_id)
                ->where('is_active', true)
                ->where('id', '!=', $academicYear->id)
                ->update(['is_active' => false]);
        }

        $academicYear->update($data);

        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Cek akses sekolah
        if (!$this->canAccessSchool($academicYear)) {
            abort(403, 'Anda tidak memiliki akses ke tahun ajaran ini.');
        }

        $academicYear->delete();

        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'semester' => ['required', 'in:GANJIL,GENAP'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}