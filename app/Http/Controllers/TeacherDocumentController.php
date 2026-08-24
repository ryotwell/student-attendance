<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TeacherDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDocumentController extends Controller
{
    public function index(Request $request)
    {
        $documents = TeacherDocument::query()
            ->with(['user'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15);

        return view('admin.teacher-documents.index', compact('documents'));
    }


    public function show(TeacherDocument $teacherDocument)
    {
        $teacherDocument->load('user');

        return view('admin.teacher-documents.show', compact('teacherDocument'));
    }


    public function verify(Request $request, TeacherDocument $teacherDocument)
    {
        $teacherDocument->update([
            'status' => 'VERIFIED',
            'note' => null,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return back()
            ->with('success', 'Dokumen berhasil diverifikasi.');
    }


    public function reject(Request $request, TeacherDocument $teacherDocument)
    {
        $data = $request->validate([
            'note' => [
                'required',
                'string',
                'max:1000'
            ],
        ]);


        $teacherDocument->update([
            'status' => 'REJECTED',
            'note' => $data['note'],
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);


        return back()
            ->with('success', 'Dokumen ditolak.');
    }
}