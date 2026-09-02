<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = School::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter status aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        // Filter level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $schools = $query->latest()->paginate(10)->withQueryString();

        return view('superadmin.schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.schools.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string|max:255',
            'npsn' => 'nullable|string|max:20|unique:schools,npsn',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'level' => ['required', Rule::in(['SD', 'SMP', 'SMA'])],
            'is_active' => 'sometimes|boolean',
            'package' => ['required', Rule::in(['basic', 'premium'])],
        ]);

        School::create($validated);

        return redirect()->route('superadmin.schools.index')
            ->with('success', 'Sekolah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        // Ambil semua user yang terkait dengan sekolah ini, urutkan berdasarkan role
        $users = User::where('school_id', $school->id)
            ->orderByRaw("FIELD(role, 'SUPERADMIN', 'ADMIN', 'GURU', 'GURU_BK')")
            ->get();

        // Statistik tambahan
        $totalStudents = $school->students()->count();
        $totalTeachers = $school->users()->where('role', 'GURU')->count();
        $totalBk = $school->users()->where('role', 'GURU_BK')->count();
        $totalAdmins = $school->users()->where('role', 'ADMIN')->count();

        return view('superadmin.schools.show', compact(
            'school',
            'users',
            'totalStudents',
            'totalTeachers',
            'totalBk',
            'totalAdmins'
        ));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        return view('superadmin.schools.edit', compact('school'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string|max:255',
            'npsn' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('schools')->ignore($school->id),
            ],
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'level' => ['required', Rule::in(['SD', 'SMP', 'SMA'])],
            'is_active' => 'sometimes|boolean',
            'package' => ['required', Rule::in(['basic', 'premium'])],
        ]);

        $school->update($validated);

        return redirect()->route('superadmin.schools.index')
            ->with('success', 'Sekolah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        // Cek apakah sekolah memiliki data terkait (users, students, dll)
        // Jika ada, mungkin lebih baik soft delete atau tolak hapus
        if ($school->users()->exists() || $school->students()->exists()) {
            return back()->with('error', 'Sekolah tidak dapat dihapus karena masih memiliki data terkait (pengguna atau siswa).');
        }

        $school->delete();

        return redirect()->route('superadmin.schools.index')
            ->with('success', 'Sekolah berhasil dihapus.');
    }

    /**
     * Toggle active status of the school.
     */
    public function toggleActive(School $school)
    {
        $school->update(['is_active' => !$school->is_active]);

        $status = $school->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('superadmin.schools.index')
            ->with('success', "Sekolah berhasil {$status}.");
    }
}