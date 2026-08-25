<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherDocumentController extends Controller
{
    /**
     * Halaman dokumen guru
     */
    public function index(Request $request)
    {
        $document = $request->user()->teacherDocument;

        return view('teacher.documents.index', compact('document'));
    }

    /**
     * Form upload/link dokumen
     */
    public function create()
    {
        return view('teacher.documents.create');
    }

    /**
     * Simpan dokumen baru
     */
    public function store(Request $request)
    {
        $user = $request->user();

        /**
         * Karena user_id unique,
         * satu guru hanya satu dokumen
         */
        if ($user->teacherDocument) {
            return redirect()
                ->route('teacher.documents.index')
                ->with('error', 'Dokumen sudah pernah dikirim.');
        }

        $data = $request->validate([
            'file_url' => [
                'required',
                'url'
            ]
        ]);

        $user->teacherDocument()->create([
            'file_url' => $data['file_url'],
            'status' => 'PENDING',
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
        $document = $request->user()->teacherDocument;

        abort_if(!$document, 404);

        return view('teacher.documents.edit', compact('document'));
    }

    /**
     * Update dokumen
     */
    public function update(Request $request)
    {
        $document = $request->user()->teacherDocument;

        abort_if(!$document, 404);

        $data = $request->validate([
            'file_url' => [
                'required',
                'url'
            ]
        ]);

        $document->update([
            'file_url' => $data['file_url'],

            // setiap perubahan harus diverifikasi ulang
            'status' => 'PENDING',
            'note' => null,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        return redirect()
            ->route('teacher.documents.index')
            ->with('success', 'Dokumen berhasil diperbarui dan dikirim ulang untuk verifikasi.');
    }
}