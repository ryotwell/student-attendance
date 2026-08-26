<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subject::query();

        // Pencarian berdasarkan nama mata pelajaran
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter grade (X, XI, XII)
        if ($request->filled('grade') && in_array($request->grade, ['X', 'XI', 'XII'])) {
            $query->where('grade', $request->grade);
        }

        // Pagination dengan tetap mempertahankan query string
        $subjects = $query->latest()->paginate(10)->withQueryString();

        return view('admin.subject.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subject.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $data = $request->validate([
        'name'  => ['required', 'string', 'max:255', 'unique:subjects,name'],
        'grade' => ['required', 'in:X,XI,XII'], // tambahkan
    ]);

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
        return view('admin.subject.edit', [
            'subject' => $subject,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255', "unique:subjects,name,{$subject->id}"],
            'grade' => ['required', 'in:X,XI,XII'], // tambahkan
        ]);

        $subject->update($data);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
