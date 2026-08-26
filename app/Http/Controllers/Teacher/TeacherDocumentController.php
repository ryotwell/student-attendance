<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDocumentController extends Controller
{
    /**
     * Halaman dokumen guru
     */
    public function index(Request $request)
    {
        $this->ensureTeacher($request->user());

        $document = $request->user()->teacherDocument;

        return view('teacher.documents.index', compact('document'));
    }

    /**
     * Form upload/link dokumen
     */
    public function create(Request $request)
    {
        $this->ensureTeacher($request->user());

        return view('teacher.documents.create');
    }

    /**
     * Simpan dokumen baru
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $this->ensureTeacher($user);

        // Satu guru hanya satu dokumen
        if ($user->teacherDocument) {
            return redirect()
                ->route('teacher.documents.index')
                ->with('error', 'Dokumen sudah pernah dikirim.');
        }

        $data = $request->validate([
            'file_url' => ['required', 'url']
        ]);

        $user->teacherDocument()->create([
            'file_url' => $data['file_url'],
            'status'   => 'PENDING',
            'school_id' => $user->school_id, // tambahkan school_id
        ]);

        return redirect()
            ->route('teacher.documents.index')
            ->with('success', 'Dokumen berhasil dikirim dan menunggu verifikasi admin.');
    }

    /**
     * Edit dokumen
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $this->ensureTeacher($user);

        $document = $user->teacherDocument;

        abort_if(!$document, 404);

        return view('teacher.documents.edit', compact('document'));
    }

    /**
     * Update dokumen
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $this->ensureTeacher($user);

        $document = $user->teacherDocument;

        abort_if(!$document, 404);

        $data = $request->validate([
            'file_url' => ['required', 'url']
        ]);

        $document->update([
            'file_url'     => $data['file_url'],
            'status'       => 'PENDING',
            'note'         => null,
            'verified_by'  => null,
            'verified_at'  => null,
            // school_id tetap, tidak diubah
        ]);

        return redirect()
            ->route('teacher.documents.index')
            ->with('success', 'Dokumen berhasil diperbarui dan dikirim ulang untuk verifikasi.');
    }

    /**
     * Pastikan user adalah GURU (bukan GURU_BK atau lainnya)
     */
    private function ensureTeacher($user)
    {
        if ($user->role !== 'GURU') {
            abort(403, 'Hanya guru yang dapat mengakses fitur ini.');
        }
    }
}