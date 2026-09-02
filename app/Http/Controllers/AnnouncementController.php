<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
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
     * Cek apakah pengumuman milik sekolah user (kecuali SUPERADMIN).
     */
    private function canAccessSchool(Announcement $announcement): bool
    {
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            return true;
        }
        return $announcement->school_id === $user->school_id;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Announcement::query();
        $query = $this->applySchoolFilter($query);

        // Pencarian berdasarkan judul atau konten
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['DRAFT', 'PUBLISHED'])) {
            $query->where('status', $request->status);
        }

        // Pagination
        $announcements = $query->latest()->paginate(10)->withQueryString();

        $user = Auth::user();

        if ($user->isTeacher() || $user->isTeacherBK()) {
            return view('teacher.announcement.index', compact('announcements'));
        }

        return view('admin.announcement.index', compact('announcements'));
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

        return view('admin.announcement.create', compact('schools'));
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
            // SUPERADMIN bisa memilih sekolah via request
            $request->validate([
                'school_id' => 'required|exists:schools,id',
            ]);
            $data['school_id'] = $request->school_id;
        } else {
            $data['school_id'] = $user->school_id;
        }

        Announcement::create($data);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        // Cek akses
        if (!$this->canAccessSchool($announcement)) {
            abort(403, 'Anda tidak memiliki akses ke pengumuman ini.');
        }

        return view('teacher.announcement.show', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        // Cek akses
        if (!$this->canAccessSchool($announcement)) {
            abort(403, 'Anda tidak memiliki akses ke pengumuman ini.');
        }

        $user = Auth::user();
        $schools = null;

        // Jika SUPERADMIN, berikan pilihan sekolah
        if ($user->role === 'SUPERADMIN') {
            $schools = School::orderBy('name')->get();
        }

        return view('admin.announcement.edit', [
            'announcement' => $announcement,
            'schools'      => $schools,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        // Cek akses
        if (!$this->canAccessSchool($announcement)) {
            abort(403, 'Anda tidak memiliki akses ke pengumuman ini.');
        }

        $data = $this->validateRequest($request);

        // Jika SUPERADMIN, izinkan mengganti sekolah
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN' && $request->filled('school_id')) {
            $request->validate([
                'school_id' => 'required|exists:schools,id',
            ]);
            $data['school_id'] = $request->school_id;
        }

        $announcement->update($data);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        // Cek akses
        if (!$this->canAccessSchool($announcement)) {
            abort(403, 'Anda tidak memiliki akses ke pengumuman ini.');
        }

        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'status'  => ['required', 'in:DRAFT,PUBLISHED'],
        ]);
    }
}