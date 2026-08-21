<?php

namespace App\Http\Controllers\GuruBk;

use App\Http\Controllers\Controller;
use App\Models\CounselingCase;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounselingCaseController extends Controller
{
    /**
     * Semua kasus, dari semua guru BK (tim BK berbagi visibilitas penuh).
     */
    public function index(Request $request)
    {
        $query = CounselingCase::with(['student.xclass', 'user'])
            ->latest('date');

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $cases = $query->paginate(15)->withQueryString();

        return view('teacher.bk.cases.index', [
            'cases' => $cases,
            'categoryOptions' => CounselingCase::CATEGORY_OPTIONS,
        ]);
    }

    /**
     * Riwayat kasus untuk satu siswa spesifik.
     */
    public function byStudent(Student $student)
    {
        $cases = $student->counselingCases()
            ->with('user')
            ->latest('date')
            ->get();

        return view('teacher.bk.cases.by-student', [
            'student' => $student,
            'cases' => $cases,
            'categoryOptions' => CounselingCase::CATEGORY_OPTIONS,
        ]);
    }

    public function create(Request $request)
    {
        return view('teacher.bk.cases.create', [
            'students' => Student::with('xclass')->orderBy('name')->get(),
            'categoryOptions' => CounselingCase::CATEGORY_OPTIONS,
            'selectedStudentId' => $request->query('student_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $data['user_id'] = Auth::id();

        CounselingCase::create($data);

        return redirect()->route('bk.cases.index')->with('success', 'Catatan kasus berhasil disimpan.');
    }

    public function edit(CounselingCase $counselingCase)
    {
        return view('teacher.bk.cases.edit', [
            'counselingCase' => $counselingCase,
            'students' => Student::with('xclass')->orderBy('name')->get(),
            'categoryOptions' => CounselingCase::CATEGORY_OPTIONS,
        ]);
    }

    public function update(Request $request, CounselingCase $counselingCase)
    {
        $data = $this->validateRequest($request);

        $counselingCase->update($data);

        return redirect()->route('bk.cases.index')->with('success', 'Catatan kasus berhasil diperbarui.');
    }

    public function destroy(CounselingCase $counselingCase)
    {
        $counselingCase->delete();

        return redirect()->route('bk.cases.index')->with('success', 'Catatan kasus berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'category' => ['required', 'in:AKADEMIK,PERILAKU,KEHADIRAN,SOSIAL,LAINNYA'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'action_taken' => ['nullable', 'string'],
        ]);
    }

    public function searchStudents(Request $request)
    {
        $search = $request->query('q', '');

        $students = Student::with('xclass')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(fn ($student) => [
                'id' => $student->id,
                'text' => "{$student->name} — {$student->xclass->name} (NIS: {$student->nis})",
            ]);

        return response()->json($students);
    }
}