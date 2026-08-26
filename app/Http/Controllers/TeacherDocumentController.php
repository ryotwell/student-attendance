<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TeacherDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDocumentController extends Controller
{
    /**
     * Mendapatkan filter school_id berdasarkan role user.
     */
    private function applySchoolFilter($query)
    {
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }
        return $query;
    }

    public function index(Request $request)
    {
        $query = TeacherDocument::with(['user']);

        // Filter berdasarkan sekolah (kecuali SUPERADMIN)
        $this->applySchoolFilter($query);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $documents = $query->latest()->paginate(15);

        return view('admin.teacher-documents.index', compact('documents'));
    }

    public function show(TeacherDocument $teacherDocument)
    {
        // Pastikan dokumen milik sekolah user (kecuali SUPERADMIN)
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN' && $teacherDocument->school_id !== $user->school_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $teacherDocument->load('user');

        return view('admin.teacher-documents.show', compact('teacherDocument'));
    }

    public function verify(Request $request, TeacherDocument $teacherDocument)
    {
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN' && $teacherDocument->school_id !== $user->school_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $teacherDocument->update([
            'status'      => 'VERIFIED',
            'note'        => null,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Dokumen berhasil diverifikasi.');
    }

    public function reject(Request $request, TeacherDocument $teacherDocument)
    {
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN' && $teacherDocument->school_id !== $user->school_id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $data = $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $teacherDocument->update([
            'status'      => 'REJECTED',
            'note'        => $data['note'],
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Dokumen ditolak.');
    }
}