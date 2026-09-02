<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use App\Models\CounselingCase;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CounselingCaseController extends Controller
{
    /**
     * Terapkan filter school_id jika user bukan SUPERADMIN.
     */
    private function applySchoolFilter($query)
    {
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }
        return $query;
    }

    /**
     * Cek apakah kasus milik sekolah user (kecuali SUPERADMIN).
     */
    private function canAccessSchool(CounselingCase $case): bool
    {
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            return true;
        }
        return $case->school_id === $user->school_id;
    }

    /**
     * Pastikan siswa yang dipilih berada di sekolah yang sama dengan user (kecuali SUPERADMIN).
     */
    private function ensureStudentBelongsToSchool(Student $student): void
    {
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN' && $student->school_id !== $user->school_id) {
            abort(403, 'Siswa tidak berada di sekolah Anda.');
        }
    }

    /**
     * Semua kasus, dari semua guru BK (tim BK berbagi visibilitas penuh di sekolah yang sama).
     */
    public function index(Request $request)
    {
        $query = CounselingCase::with(['student.currentEnrollment.xclass', 'user'])
            ->latest('date');

        // Filter berdasarkan sekolah
        $query = $this->applySchoolFilter($query);

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
            'cases'           => $cases,
            'categoryOptions' => CounselingCase::CATEGORY_OPTIONS,
        ]);
    }

    /**
     * Riwayat kasus untuk satu siswa spesifik.
     */
    public function byStudent(Student $student)
    {
        // Pastikan akses ke siswa
        $this->ensureStudentBelongsToSchool($student);

        $cases = $student->counselingCases()
            ->with('user')
            ->latest('date')
            ->get();

        return view('teacher.bk.cases.by-student', [
            'student'         => $student,
            'cases'           => $cases,
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

            // Pastikan siswa dapat diakses
            if ($selectedStudent) {
                $this->ensureStudentBelongsToSchool($selectedStudent);
            }
        }

        return view('teacher.bk.cases.create', [
            'categoryOptions' => CounselingCase::CATEGORY_OPTIONS,
            'selectedStudent' => $selectedStudent,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        // Validasi tambahan: pastikan siswa di sekolah yang sama
        $student = Student::findOrFail($data['student_id']);
        $this->ensureStudentBelongsToSchool($student);

        $data['school_id'] = $student->school_id; // ambil dari siswa
        $data['user_id'] = Auth::id();
        $data['student_enrollment_id'] = $this->resolveEnrollmentId($data['student_id']);

        CounselingCase::create($data);

        return redirect()->route('bk.cases.index')->with('success', 'Catatan kasus berhasil disimpan.');
    }

    public function edit(CounselingCase $counselingCase)
    {
        // Cek akses
        if (!$this->canAccessSchool($counselingCase)) {
            abort(403, 'Anda tidak memiliki akses ke kasus ini.');
        }

        $counselingCase->load('student.currentEnrollment.xclass');

        return view('teacher.bk.cases.edit', [
            'counselingCase'  => $counselingCase,
            'categoryOptions' => CounselingCase::CATEGORY_OPTIONS,
        ]);
    }

    public function update(Request $request, CounselingCase $counselingCase)
    {
        // Cek akses
        if (!$this->canAccessSchool($counselingCase)) {
            abort(403, 'Anda tidak memiliki akses ke kasus ini.');
        }

        $data = $this->validateRequest($request);

        // Validasi tambahan: pastikan siswa di sekolah yang sama
        $student = Student::findOrFail($data['student_id']);
        $this->ensureStudentBelongsToSchool($student);

        // Pastikan school_id konsisten (tidak berubah)
        $data['school_id'] = $counselingCase->school_id;
        $data['student_enrollment_id'] = $this->resolveEnrollmentId($data['student_id']);

        $counselingCase->update($data);

        return redirect()->route('bk.cases.index')->with('success', 'Catatan kasus berhasil diperbarui.');
    }

    public function destroy(CounselingCase $counselingCase)
    {
        // Cek akses
        if (!$this->canAccessSchool($counselingCase)) {
            abort(403, 'Anda tidak memiliki akses ke kasus ini.');
        }

        $counselingCase->delete();

        return redirect()->route('bk.cases.index')->with('success', 'Catatan kasus berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'student_id'   => ['required', 'exists:students,id'],
            'category'     => ['required', 'in:AKADEMIK,PERILAKU,KEHADIRAN,SOSIAL,LAINNYA'],
            'date'         => ['required', 'date'],
            'description'  => ['required', 'string'],
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
        $user = Auth::user();

        $enrollment = StudentEnrollment::where('student_id', $studentId)
            ->whereHas('academicYear', function ($q) use ($user) {
                $q->where('is_active', true);
                // Filter juga berdasarkan sekolah untuk keamanan ekstra
                if ($user->role !== 'SUPERADMIN') {
                    $q->where('school_id', $user->school_id);
                }
            })
            ->first();

        abort_if(
            !$enrollment,
            403,
            'Siswa ini belum memiliki enrollment pada tahun ajaran aktif.'
        );

        return $enrollment->id;
    }

    /**
     * Endpoint pencarian siswa untuk dropdown search (Alpine.js).
     * Menyertakan parent_phone supaya fitur "Kirim WhatsApp" di form berfungsi.
     * Hanya menampilkan siswa dari sekolah yang sama (kecuali SUPERADMIN).
     */
    public function searchStudents(Request $request)
    {
        $user = Auth::user();

        $query = Student::query()
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('nis', 'like', '%' . $request->q . '%');
            });

        // Filter sekolah
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }

        $students = $query->limit(20)->get();

        return response()->json(
            $students->map(function ($student) {
                return [
                    'id'           => $student->id,
                    'text'         => $student->name . ' - ' . $student->nis,
                    'parent_phone' => $student->parent_phone,
                ];
            })
        );
    }
}