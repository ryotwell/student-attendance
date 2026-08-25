<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use App\Models\CounselingCase;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounselingCaseController extends Controller
{
    /**
     * Semua kasus, dari semua guru BK (tim BK berbagi visibilitas penuh).
     */
    public function index(Request $request)
    {
        $query = CounselingCase::with(['student.currentEnrollment.xclass', 'user'])
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

    /**
     * Form tambah kasus baru. Jika datang dengan ?student_id=..,
     * siswa tersebut langsung di-preload (nama + telepon ortu)
     * supaya form tidak perlu di-search ulang.
     */
    public function create(Request $request)
    {
        $selectedStudent = null;

        if ($request->filled('student_id')) {
            $selectedStudent = Student::with('currentEnrollment.xclass')
                ->find($request->query('student_id'));
        }

        return view('teacher.bk.cases.create', [
            'categoryOptions' => CounselingCase::CATEGORY_OPTIONS,
            'selectedStudent' => $selectedStudent,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $data['user_id'] = Auth::id();
        $data['student_enrollment_id'] = $this->resolveEnrollmentId($data['student_id']);

        CounselingCase::create($data);

        return redirect()->route('bk.cases.index')->with('success', 'Catatan kasus berhasil disimpan.');
    }

    public function edit(CounselingCase $counselingCase)
    {
        $counselingCase->load('student.currentEnrollment.xclass');

        return view('teacher.bk.cases.edit', [
            'counselingCase' => $counselingCase,
            'categoryOptions' => CounselingCase::CATEGORY_OPTIONS,
        ]);
    }

    public function update(Request $request, CounselingCase $counselingCase)
    {
        $data = $this->validateRequest($request);
        $data['student_enrollment_id'] = $this->resolveEnrollmentId($data['student_id']);

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

    /**
     * Ambil enrollment aktif (tahun ajaran berjalan) milik siswa.
     * Kasus BK selalu dikaitkan ke enrollment siswa pada tahun ajaran
     * yang sedang aktif, bukan sekadar enrollment terbaru.
     */
    private function resolveEnrollmentId(int $studentId): int
    {
        $enrollment = StudentEnrollment::where('student_id', $studentId)
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->first();

        abort_if(
            ! $enrollment,
            403, 'Siswa ini belum memiliki enrollment pada tahun ajaran aktif.');

        return $enrollment->id;
    }

    /**
     * Endpoint pencarian siswa untuk dropdown search (Alpine.js).
     * Menyertakan parent_phone supaya fitur "Kirim WhatsApp" di form berfungsi.
     */
    public function searchStudents(Request $request)
    {
        $students = Student::query()
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('nis', 'like', '%' . $request->q . '%');
            })
            ->limit(20)
            ->get();

        return response()->json(
            $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'text' => $student->name . ' - ' . $student->nis,
                    'parent_phone' => $student->parent_phone,
                ];
            })
        );
    }
}