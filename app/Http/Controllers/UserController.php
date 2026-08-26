<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
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
     * Cek apakah user milik sekolah yang sama (kecuali SUPERADMIN).
     */
    private function canAccessSchool(User $user): bool
    {
        $currentUser = Auth::user();
        if ($currentUser->role === 'SUPERADMIN') {
            return true;
        }
        // User tanpa school_id (misalnya SUPERADMIN) tidak bisa diakses oleh non-SUPERADMIN
        if ($user->school_id === null) {
            return false;
        }
        return $user->school_id === $currentUser->school_id;
    }

    /**
     * Tampilkan daftar user
     */
    public function index(Request $request)
    {
        $query = User::query();
        $query = $this->applySchoolFilter($query);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.user.index', compact('users'));
    }

    /**
     * Tampilkan form tambah user
     */
    public function create()
    {
        $user = Auth::user();
        $schools = null;

        // SUPERADMIN bisa memilih sekolah, yang lain tidak
        if ($user->role === 'SUPERADMIN') {
            $schools = School::orderBy('name')->get();
        }

        return view('admin.user.create', compact('schools'));
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role'     => ['required', Rule::in(['ADMIN', 'GURU', 'GURU_BK'])],
        ];

        // SUPERADMIN bisa memilih sekolah
        if ($currentUser->role === 'SUPERADMIN') {
            $rules['school_id'] = ['required', 'exists:schools,id'];
        }

        $validated = $request->validate($rules);

        // Tentukan school_id
        if ($currentUser->role === 'SUPERADMIN') {
            $schoolId = $validated['school_id'];
        } else {
            $schoolId = $currentUser->school_id;
        }

        User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
            'school_id' => $schoolId,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail user
     */
    public function show(User $user)
    {
        if (!$this->canAccessSchool($user)) {
            abort(403, 'Anda tidak memiliki akses ke user ini.');
        }

        $user->load('currentClasses');

        return view('admin.user.show', compact('user'));
    }

    /**
     * Tampilkan form edit user
     */
    public function edit(User $user)
    {
        if (!$this->canAccessSchool($user)) {
            abort(403, 'Anda tidak memiliki akses ke user ini.');
        }

        $currentUser = Auth::user();
        $schools = null;

        if ($currentUser->role === 'SUPERADMIN') {
            $schools = School::orderBy('name')->get();
        }

        return view('admin.user.edit', compact('user', 'schools'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        if (!$this->canAccessSchool($user)) {
            abort(403, 'Anda tidak memiliki akses ke user ini.');
        }

        $currentUser = Auth::user();

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role'     => ['required', Rule::in(['ADMIN', 'GURU', 'GURU_BK'])],
        ];

        // SUPERADMIN bisa mengubah sekolah, yang lain tidak
        if ($currentUser->role === 'SUPERADMIN') {
            $rules['school_id'] = ['nullable', 'exists:schools,id'];
        }

        $validated = $request->validate($rules);

        // Tentukan school_id
        if ($currentUser->role === 'SUPERADMIN' && $request->filled('school_id')) {
            $user->school_id = $validated['school_id'];
        }
        // Non-SUPERADMIN tidak bisa mengubah school_id, biarkan tetap

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->role  = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        if (!$this->canAccessSchool($user)) {
            abort(403, 'Anda tidak memiliki akses ke user ini.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}