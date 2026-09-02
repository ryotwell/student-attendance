<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
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
     * Cek apakah kelas milik sekolah user (kecuali SUPERADMIN).
     */
    private function canAccessSchool(Xclass $class): bool
    {
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            return true;
        }
        return $class->school_id === $user->school_id;
    }

    /**
     * Ambil daftar tahun ajaran yang dapat diakses user.
     */
    private function getAvailableAcademicYears()
    {
        $query = AcademicYear::latest();
        $this->applySchoolFilter($query);
        return $query->get();
    }

    /**
     * Ambil daftar guru yang dapat diakses user (hanya role GURU).
     */
    private function getAvailableTeachers()
    {
        $query = User::where('role', 'GURU')->orderBy('name');
        $this->applySchoolFilter($query);
        return $query->get();
    }

    /**
     * Display a listing of the resource with search, filter, and pagination.
     */
    public function index(Request $request)
    {
        $query = Xclass::with([
            'academicYear',
            'user'
        ])->withCount('students');

        // Filter sekolah
        $query = $this->applySchoolFilter($query);

        // Pencarian berdasarkan nama kelas
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter tahun ajaran
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Filter wali kelas (user_id)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $classes = $query->latest()->paginate(10)->withQueryString();

        // Data untuk dropdown filter (hanya yang dapat diakses user)
        $academicYears = $this->getAvailableAcademicYears();
        $users = $this->getAvailableTeachers();

        return view('admin.class.index', compact('classes', 'academicYears', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academicYears = $this->getAvailableAcademicYears();
        $users = $this->getAvailableTeachers();

        return view('admin.class.create', [
            'academicYears' => $academicYears,
            'users' => $users,
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
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        // Tentukan school_id
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            // SUPERADMIN: bisa pilih sekolah? Atau default? Kita bisa ambil dari academic_year
            // Ambil school_id dari tahun ajaran yang dipilih (asumsi tahun ajaran sudah punya school_id)
            $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
            $data['school_id'] = $academicYear->school_id;
        } else {
            $data['school_id'] = $user->school_id;
        }

        // Validasi tambahan: pastikan academic_year dan user (jika ada) berada di sekolah yang sama
        $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
        if ($academicYear->school_id != $data['school_id']) {
            return back()->withInput()->withErrors([
                'academic_year_id' => 'Tahun ajaran tidak sesuai dengan sekolah.'
            ]);
        }

        if (!empty($data['user_id'])) {
            $teacher = User::findOrFail($data['user_id']);
            if ($teacher->school_id != $data['school_id']) {
                return back()->withInput()->withErrors([
                    'user_id' => 'Guru tidak berada di sekolah yang sama.'
                ]);
            }
        }

        // Cek guru hanya boleh wali 1 kelas dalam academic year yang sama (di sekolah yang sama)
        if (!empty($data['user_id'])) {
            $exists = Xclass::where('academic_year_id', $data['academic_year_id'])
                ->where('school_id', $data['school_id'])
                ->where('user_id', $data['user_id'])
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_id' => 'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini di sekolah ini.'
                    ]);
            }
        }

        Xclass::create($data);

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
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
        // Cek akses
        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $academicYears = $this->getAvailableAcademicYears();
        $users = $this->getAvailableTeachers();

        return view('admin.class.edit', [
            'class' => $class,
            'academicYears' => $academicYears,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Xclass $class)
    {
        // Cek akses
        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        // Untuk SUPERADMIN, izinkan mengubah sekolah? Biasanya tidak, tapi kita bisa.
        // Kita pertahankan school_id yang sudah ada.
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN' && $request->filled('school_id')) {
            // Jika SUPERADMIN mengirim school_id, gunakan
            $request->validate([
                'school_id' => 'exists:schools,id',
            ]);
            $data['school_id'] = $request->school_id;
        } else {
            // Non-SUPERADMIN atau tanpa school_id, tetap gunakan yang lama
            $data['school_id'] = $class->school_id;
        }

        // Validasi tambahan: pastikan academic_year dan user (jika ada) berada di sekolah yang sama
        $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
        if ($academicYear->school_id != $data['school_id']) {
            return back()->withInput()->withErrors([
                'academic_year_id' => 'Tahun ajaran tidak sesuai dengan sekolah.'
            ]);
        }

        if (!empty($data['user_id'])) {
            $teacher = User::findOrFail($data['user_id']);
            if ($teacher->school_id != $data['school_id']) {
                return back()->withInput()->withErrors([
                    'user_id' => 'Guru tidak berada di sekolah yang sama.'
                ]);
            }
        }

        // Cek guru duplikat, kecuali kelas yang sedang diedit
        if (!empty($data['user_id'])) {
            $exists = Xclass::where('academic_year_id', $data['academic_year_id'])
                ->where('school_id', $data['school_id'])
                ->where('user_id', $data['user_id'])
                ->where('id', '!=', $class->id)
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_id' => 'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini di sekolah ini.'
                    ]);
            }
        }

        $class->update($data);

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Xclass $class)
    {
        // Cek akses
        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * See class schedules
     */
    public function schedules(Xclass $class)
    {
        // Cek akses
        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $class->load('schedules.subject');

        return view('admin.class.schedule', [
            'title' => 'Jadwal Kelas ' . $class->name,
            'class' => $class,
        ]);
    }

    /**
     * Get students by class (JSON)
     */
    public function students(Xclass $class)
    {
        // Cek akses
        if (!$this->canAccessSchool($class)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $students = $class->students()
            ->select('students.id', 'students.nis', 'students.name')
            ->orderBy('students.name')
            ->get();

        return response()->json($students);
    }
}