<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.class.index', [
            'classes' => Xclass::with([
                    'academicYear',
                    'user'
                ])
                ->withCount('students')
                ->latest()
                ->get(),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.class.create', [
            'academicYears' => AcademicYear::latest()->get(),
            'users' => User::where('role', 'GURU')
                ->orderBy('name', 'ASC')
                ->get(),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'academic_year_id' => [
                'required',
                'exists:academic_years,id'
            ],
            'user_id' => [
                'nullable',
                'exists:users,id'
            ],
        ]);

        /**
         * Cek guru hanya boleh wali 1 kelas
         * dalam academic year yang sama
         */
        if (!empty($data['user_id'])) {

            $exists = Xclass::where(
                    'academic_year_id',
                    $data['academic_year_id']
                )
                ->where(
                    'user_id',
                    $data['user_id']
                )
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_id' =>
                        'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini.'
                    ]);
            }
        }

        Xclass::create($data);

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Kelas berhasil ditambahkan.'
            );
    }


    /**
     * Display the specified resource.
     */
    public function show(Xclass $class)
    {
        return redirect()
            ->route('classes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Xclass $class)
    {
        return view('admin.class.edit', [
            'class' => $class,
            'academicYears' => AcademicYear::latest()->get(),
            'users' => User::where('role', 'GURU')
                ->orderBy('name', 'ASC')
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Xclass $class)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'academic_year_id' => [
                'required',
                'exists:academic_years,id'
            ],
            'user_id' => [
                'nullable',
                'exists:users,id'
            ],
        ]);


        /**
         * Cek wali kelas duplikat
         * kecuali kelas yang sedang diedit
         */
        if (!empty($data['user_id'])) {
            $exists = Xclass::where(
                    'academic_year_id',
                    $data['academic_year_id']
                )
                ->where(
                    'user_id',
                    $data['user_id']
                )
                ->where(
                    'id',
                    '!=',
                    $class->id
                )
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_id' =>
                        'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini.'
                    ]);
            }
        }

        $class->update($data);

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Kelas berhasil diperbarui.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Xclass $class)
    {
        $class->delete();

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Kelas berhasil dihapus.'
            );
    }

    /**
     * See class schedules
     */
    public function schedules(Xclass $class)
    {
        $class->load(
            'schedules.subject'
        );

        return view('admin.class.schedule', [
            'title' => 'Jadwal Kelas ' . $class->name,
            'class' => $class,
        ]);
    }

    /**
     * Get students by class
     */
    public function students(Xclass $class)
    {
        // students() adalah belongsToMany lewat pivot student_enrollments,
        // jadi kolom harus di-qualify dengan nama tabel supaya tidak
        // ambigu / salah ambil dari tabel pivot.
        $students = $class
            ->students()
            ->select(
                'students.id',
                'students.nis',
                'students.name'
            )
            ->orderBy('students.name')
            ->get();

        return response()->json($students);
    }
}