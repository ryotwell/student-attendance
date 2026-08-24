<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherDocumentController extends Controller
{
    public function index(Request $request)
    {
        $document = $request->user()->teacherDocument;

        return view('teacher.documents.index', compact('document'));
    }

    public function create()
    {
        return view('teacher.documents.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'file_url' => ['required', 'url'],
        ]);

        $request->user()->teacherDocument()->create([
            'file_url' => $data['file_url'],
            'status' => 'PENDING',
        ]);

        return redirect()
            ->route('teacher.documents.index')
            ->with('success', 'Dokumen berhasil dikirim.');
    }

    public function edit(Request $request)
    {
        $document = $request->user()->teacherDocument;

        return view('teacher.documents.edit', compact('document'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'file_url' => ['required', 'url'],
        ]);

        $document = $request->user()->teacherDocument;

        $document->update([
            'file_url' => $data['file_url'],
            'status' => 'PENDING',
            'note' => null,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        return redirect()
            ->route('teacher.documents.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }
}