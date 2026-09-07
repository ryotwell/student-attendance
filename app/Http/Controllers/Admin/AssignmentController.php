<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;

class AssignmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        abort_unless($user->role === 'ADMIN', 403);

        $school = $user->school;

        abort_if(
            !$school,
            404,
            'Sekolah belum terhubung dengan akun ini.'
        );

        $assignments = Assignment::with([
            'user',
            'xclass',
            'academicYear',
            'lessonPlan',
        ])
            ->where('school_id', $school->id)
            ->latest()
            ->paginate(12);

        return view(
            'admin.assignments.index',
            compact('assignments')
        );
    }

    public function show(Assignment $assignment)
    {
        $user = auth()->user();

        abort_unless($user->role === 'ADMIN', 403);

        $school = $user->school;

        abort_if(
            !$school,
            404,
            'Sekolah belum terhubung dengan akun ini.'
        );

        // Mencegah Admin melihat assignment sekolah lain
        abort_unless(
            $assignment->school_id === $school->id,
            404
        );

        $assignment->load([
            'user',
            'xclass',
            'academicYear',
            'lessonPlan',
        ]);

        return view(
            'admin.assignments.show',
            compact('assignment')
        );
    }
}