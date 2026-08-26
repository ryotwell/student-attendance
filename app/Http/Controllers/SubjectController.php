<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
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
     * Cek apakah mata pelajaran milik sekolah user (kecuali SUPERADMIN).
     */
    private function canAccessSchool(Subject $subject): bool
    {
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            return true;
        }
        return $subject->school_id === $user->school_id;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subject::query();
        $query = $this->applySchoolFilter($query);

        // Pencarian berdasarkan nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter grade
        if ($request->filled('grade') && in_array($request->grade, ['X', 'XI', 'XII'])) {
            $query->where('grade', $request->grade);
        }

        $subjects = $query->latest()->paginate(10)->withQueryString();

        return view('admin.subject.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $schools = null;

        // Jika SUPERADMIN, berikan pilihan sekolah
        if ($user->role === 'SUPERADMIN') {
            $schools = School::orderBy('name')->get();
        }

        return view('admin.subject.create', compact('schools'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi dasar
        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'grade' => ['required', 'in:X,XI,XII'],
        ];

        // Jika SUPERADMIN, tambahkan validasi school_id
        if ($user->role === 'SUPERADMIN') {
            $rules['school_id'] = ['required', 'exists:schools,id'];
        }

        $data = $request->validate($rules);

        // Tentukan school_id
        if ($user->role === 'SUPERADMIN') {
            $data['school_id'] = $request->school_id;
        } else {
            $data['school_id'] = $user->school_id;
        }

        // Validasi unique per sekolah
        $exists = Subject::where('name', $data['name'])
            ->where('school_id', $data['school_id'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'name' => 'Mata pelajaran dengan nama ini sudah ada di sekolah ini.'
            ]);
        }

        Subject::create($data);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        return redirect()->route('subjects.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        if (!$this->canAccessSchool($subject)) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
        }

        $user = Auth::user();
        $schools = null;

        if ($user->role === 'SUPERADMIN') {
            $schools = School::orderBy('name')->get();
        }

        return view('admin.subject.edit', [
            'subject'  => $subject,
            'schools'  => $schools,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        if (!$this->canAccessSchool($subject)) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
        }

        $user = Auth::user();

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'grade' => ['required', 'in:X,XI,XII'],
        ];

        if ($user->role === 'SUPERADMIN') {
            $rules['school_id'] = ['nullable', 'exists:schools,id'];
        }

        $data = $request->validate($rules);

        // Tentukan school_id
        if ($user->role === 'SUPERADMIN' && $request->filled('school_id')) {
            $data['school_id'] = $request->school_id;
        } else {
            $data['school_id'] = $subject->school_id; // tetap gunakan yang lama
        }

        // Validasi unique per sekolah (kecuali dirinya sendiri)
        $exists = Subject::where('name', $data['name'])
            ->where('school_id', $data['school_id'])
            ->where('id', '!=', $subject->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'name' => 'Mata pelajaran dengan nama ini sudah ada di sekolah ini.'
            ]);
        }

        $subject->update($data);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        if (!$this->canAccessSchool($subject)) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
        }

        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}