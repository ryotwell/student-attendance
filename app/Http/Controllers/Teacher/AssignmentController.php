<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\LessonPlan;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Menampilkan daftar tugas milik guru yang sedang login.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $assignments = Assignment::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->with([
                'xclass',
                'academicYear',
                'lessonPlan',
            ])

            // Search
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })

            // Filter kelas
            ->when($request->filled('xclass_id'), function ($query) use ($request) {
                $query->where('xclass_id', $request->xclass_id);
            })

            // Filter status
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })

            ->latest()
            ->paginate(10)
            ->withQueryString();

        /*
         * Hanya kelas milik sekolah guru.
         * Ditampilkan untuk kebutuhan filter dan form.
         */
        $classes = Xclass::query()
            ->where('school_id', $user->school_id)
            ->with('academicYear')
            ->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        return view('teacher.assignment.index', compact(
            'assignments',
            'classes'
        ));
    }


    /**
     * Form tambah tugas.
     */
    public function create()
    {
        $user = auth()->user();

        /*
         * Kelas yang tersedia hanya kelas:
         * - milik sekolah guru
         * - tahun ajaran aktif
         */
        $classes = Xclass::query()
            ->where('school_id', $user->school_id)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })
            ->with('academicYear')
            ->orderBy('name')
            ->get();

        /*
         * Rencana pembelajaran milik guru.
         */
        $lessonPlans = LessonPlan::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })
            ->with([
                'xclass',
                'academicYear',
            ])
            ->latest('date')
            ->get();

        return view('teacher.assignment.create', compact(
            'classes',
            'lessonPlans'
        ));
    }


    /**
     * Menyimpan tugas baru.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'xclass_id' => [
                'required',
                'integer',
                'exists:xclasses,id',
            ],

            'lesson_plan_id' => [
                'nullable',
                'integer',
                'exists:lesson_plans,id',
            ],

            'due_date' => [
                'nullable',
                'date',
            ],

            'status' => [
                'required',
                'in:DRAFT,PUBLISHED,CLOSED',
            ],

            'attachment' => [
                'nullable',
                'file',
                'max:10240',
            ],
        ]);

        /*
         * Pastikan kelas memang milik sekolah guru
         * dan tahun ajarannya aktif.
         */
        $class = Xclass::query()
            ->where('id', $validated['xclass_id'])
            ->where('school_id', $user->school_id)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })
            ->firstOrFail();

        /*
         * Academic year diambil dari kelas.
         * Guru tidak bisa memanipulasi academic_year_id dari request.
         */
        $academicYearId = $class->academic_year_id;

        /*
         * Jika lesson plan diberikan, pastikan lesson plan:
         * - milik guru
         * - milik sekolah guru
         * - berasal dari kelas yang dipilih
         * - berasal dari tahun ajaran yang sama
         */
        if (!empty($validated['lesson_plan_id'])) {
            $lessonPlan = LessonPlan::query()
                ->where('id', $validated['lesson_plan_id'])
                ->where('user_id', $user->id)
                ->where('school_id', $user->school_id)
                ->where('xclass_id', $class->id)
                ->where('academic_year_id', $academicYearId)
                ->first();

            if (!$lessonPlan) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'lesson_plan_id' =>
                            'Rencana pembelajaran tidak sesuai dengan kelas yang dipilih.',
                    ]);
            }
        }

        /*
         * Upload lampiran.
         */
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('assignments', 'public');
        }

        /*
         * Data yang dikontrol server.
         */
        $validated['user_id'] = $user->id;
        $validated['school_id'] = $user->school_id;
        $validated['academic_year_id'] = $academicYearId;

        Assignment::create($validated);

        return redirect()
            ->route('assignments.index')
            ->with('success', 'Tugas berhasil ditambahkan.');
    }


    /**
     * Form edit tugas.
     */
    public function edit(Assignment $assignment)
    {
        $user = auth()->user();

        /*
         * Guru hanya boleh mengedit tugas miliknya
         * dan dari sekolahnya sendiri.
         */
        abort_unless(
            $assignment->user_id === $user->id &&
            $assignment->school_id === $user->school_id,
            403
        );

        $assignment->load([
            'xclass',
            'academicYear',
            'lessonPlan',
        ]);

        $classes = Xclass::query()
            ->where('school_id', $user->school_id)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })
            ->with('academicYear')
            ->orderBy('name')
            ->get();

        $lessonPlans = LessonPlan::query()
            ->where('user_id', $user->id)
            ->where('school_id', $user->school_id)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })
            ->with([
                'xclass',
                'academicYear',
            ])
            ->latest('date')
            ->get();

        return view('teacher.assignment.edit', compact(
            'assignment',
            'classes',
            'lessonPlans'
        ));
    }


    /**
     * Memperbarui tugas.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $user = auth()->user();

        /*
         * Pastikan tugas milik guru yang sedang login.
         */
        abort_unless(
            $assignment->user_id === $user->id &&
            $assignment->school_id === $user->school_id,
            403
        );

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'xclass_id' => [
                'required',
                'integer',
                'exists:xclasses,id',
            ],

            'lesson_plan_id' => [
                'nullable',
                'integer',
                'exists:lesson_plans,id',
            ],

            'due_date' => [
                'nullable',
                'date',
            ],

            'status' => [
                'required',
                'in:DRAFT,PUBLISHED,CLOSED',
            ],

            'attachment' => [
                'nullable',
                'file',
                'max:10240',
            ],
        ]);

        /*
         * Pastikan kelas yang dipilih milik sekolah guru
         * dan berasal dari tahun ajaran aktif.
         */
        $class = Xclass::query()
            ->where('id', $validated['xclass_id'])
            ->where('school_id', $user->school_id)
            ->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })
            ->firstOrFail();

        $academicYearId = $class->academic_year_id;

        /*
         * Validasi lesson plan jika dipilih.
         */
        if (!empty($validated['lesson_plan_id'])) {
            $lessonPlan = LessonPlan::query()
                ->where('id', $validated['lesson_plan_id'])
                ->where('user_id', $user->id)
                ->where('school_id', $user->school_id)
                ->where('xclass_id', $class->id)
                ->where('academic_year_id', $academicYearId)
                ->first();

            if (!$lessonPlan) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'lesson_plan_id' =>
                            'Rencana pembelajaran tidak sesuai dengan kelas yang dipilih.',
                    ]);
            }
        }

        /*
         * Upload attachment baru.
         */
        if ($request->hasFile('attachment')) {

            // Hapus file lama jika ada.
            if ($assignment->attachment) {
                Storage::disk('public')
                    ->delete($assignment->attachment);
            }

            $validated['attachment'] = $request
                ->file('attachment')
                ->store('assignments', 'public');
        } else {
            /*
             * Jangan menghapus attachment lama
             * jika user tidak memilih file baru.
             */
            unset($validated['attachment']);
        }

        /*
         * Field yang ditentukan server.
         */
        $validated['academic_year_id'] = $academicYearId;

        $assignment->update($validated);

        return redirect()
            ->route('assignments.index')
            ->with('success', 'Tugas berhasil diperbarui.');
    }


    /**
     * Menghapus tugas.
     */
    public function destroy(Assignment $assignment)
    {
        $user = auth()->user();

        /*
         * Guru hanya boleh menghapus tugas miliknya.
         */
        abort_unless(
            $assignment->user_id === $user->id &&
            $assignment->school_id === $user->school_id,
            403
        );

        /*
         * Hapus file attachment.
         */
        if ($assignment->attachment) {
            Storage::disk('public')
                ->delete($assignment->attachment);
        }

        $assignment->delete();

        return redirect()
            ->route('assignments.index')
            ->with('success', 'Tugas berhasil dihapus.');
    }
}
