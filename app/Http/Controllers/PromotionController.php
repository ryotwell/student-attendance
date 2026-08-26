<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PromotionController extends Controller
{
    /**
     * Terapkan filter school_id jika user bukan SUPERADMIN untuk Xclass.
     */
    private function applySchoolFilterToXclass($query)
    {
        $user = Auth::user();
        if ($user->role !== 'SUPERADMIN') {
            $query->where('school_id', $user->school_id);
        }
        return $query;
    }

    /**
     * Cek apakah kelas milik sekolah user (kecuali SUPERADMIN).
     */
    private function canAccessXclass(Xclass $xclass): bool
    {
        $user = Auth::user();
        if ($user->role === 'SUPERADMIN') {
            return true;
        }
        return $xclass->school_id === $user->school_id;
    }

    /**
     * Pastikan semua student_id yang dipilih berada di sekolah yang sama.
     * Jika $schoolId diberikan, semua siswa harus memiliki school_id tersebut.
     * Jika tidak, semua siswa harus memiliki school_id yang sama (untuk SUPERADMIN).
     */
    private function validateStudentsBelongToSchool(array $studentIds, ?int $schoolId = null): void
    {
        $students = Student::whereIn('id', $studentIds)->get(['id', 'school_id']);
        if ($students->count() !== count($studentIds)) {
            throw ValidationException::withMessages([
                'student_ids' => 'Salah satu siswa tidak ditemukan.'
            ]);
        }

        if ($schoolId !== null) {
            // Validasi ketat: semua siswa harus memiliki school_id yang sama dengan $schoolId
            $invalid = $students->firstWhere('school_id', '!=', $schoolId);
            if ($invalid) {
                throw ValidationException::withMessages([
                    'student_ids' => 'Salah satu siswa tidak berada di sekolah yang sama.'
                ]);
            }
        } else {
            // SUPERADMIN: semua siswa harus berada di sekolah yang sama (konsistensi)
            $schoolIds = $students->pluck('school_id')->unique();
            if ($schoolIds->count() > 1) {
                throw ValidationException::withMessages([
                    'student_ids' => 'Semua siswa harus berasal dari sekolah yang sama.'
                ]);
            }
        }
    }

    /**
     * Halaman utama kenaikan kelas.
     *
     * Kelas asal: dari tahun ajaran yang SUDAH TIDAK aktif (kelas lama,
     * tempat siswa berada sebelum naik/lulus).
     * Kelas tujuan: dari tahun ajaran yang SEDANG aktif (kelas baru,
     * tempat siswa dipindahkan/dinaikkan).
     */
    public function create()
    {
        $sourceXclasses = Xclass::with('academicYear')
            ->whereHas('academicYear', fn($q) => $q->where('is_active', false))
            ->orderBy('name');
        $sourceXclasses = $this->applySchoolFilterToXclass($sourceXclasses)->get();

        $targetXclasses = Xclass::with('academicYear')
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderBy('name');
        $targetXclasses = $this->applySchoolFilterToXclass($targetXclasses)->get();

        return view('admin.promotion.create', [
            'sourceXclasses' => $sourceXclasses,
            'targetXclasses' => $targetXclasses,
        ]);
    }

    /**
     * Daftar siswa aktif di satu kelas asal (dipakai AJAX saat kelas asal dipilih).
     */
    public function studentsInClass(Xclass $xclass)
    {
        if (!$this->canAccessXclass($xclass)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $students = $xclass->studentEnrollments()
            ->with('student')
            ->whereHas('student', fn($q) => $q->where('status', 'AKTIF'))
            ->get()
            ->pluck('student')
            ->filter(fn($student) => $student->school_id === $xclass->school_id)
            ->values();

        return response()->json($students);
    }

    /**
     * Proses kenaikan kelas untuk siswa-siswa terpilih ke satu kelas tujuan,
     * ATAU meluluskan siswa-siswa terpilih.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'action'          => ['required', 'in:NAIK,LULUS'],
            'student_ids'     => ['required', 'array', 'min:1'],
            'student_ids.*'   => ['required', 'exists:students,id'],
            'target_xclass_id'=> ['required_if:action,NAIK', 'nullable', 'exists:xclasses,id'],
        ]);

        // Validasi dasar: semua siswa harus dari sekolah yang sama
        $this->validateStudentsBelongToSchool($data['student_ids']);

        $targetXclass = null;
        $targetXclassId = null;
        $targetAcademicYearId = null;

        if ($data['action'] === 'NAIK') {
            // Ambil kelas tujuan beserta tahun ajaran aktif
            $targetXclass = Xclass::with('academicYear')
                ->where('id', $data['target_xclass_id'])
                ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
                ->first();

            if (!$targetXclass) {
                return back()->withInput()->withErrors([
                    'target_xclass_id' => 'Kelas tujuan tidak valid atau tidak memiliki tahun ajaran aktif.'
                ]);
            }

            // Jika user bukan SUPERADMIN, pastikan kelas tujuan berada di sekolah yang sama
            if ($user->role !== 'SUPERADMIN') {
                if ($targetXclass->school_id !== $user->school_id) {
                    return back()->withInput()->withErrors([
                        'target_xclass_id' => 'Kelas tujuan tidak berada di sekolah Anda.'
                    ]);
                }
            } else {
                // SUPERADMIN: pastikan semua siswa berasal dari sekolah yang sama dengan kelas tujuan
                $studentSchoolIds = Student::whereIn('id', $data['student_ids'])->pluck('school_id')->unique();
                if ($studentSchoolIds->count() !== 1 || $studentSchoolIds->first() !== $targetXclass->school_id) {
                    return back()->withInput()->withErrors([
                        'student_ids' => 'Semua siswa harus berasal dari sekolah yang sama dengan kelas tujuan.'
                    ]);
                }
            }

            $targetXclassId = $targetXclass->id;
            $targetAcademicYearId = $targetXclass->academic_year_id;
        }

        DB::transaction(function () use ($data, $targetXclassId, $targetAcademicYearId) {
            if ($data['action'] === 'LULUS') {
                Student::whereIn('id', $data['student_ids'])->update(['status' => 'LULUS']);
                return;
            }

            // Aksi NAIK: pindahkan enrollment ke kelas tujuan di tahun ajaran aktif
            foreach ($data['student_ids'] as $studentId) {
                StudentEnrollment::updateOrCreate(
                    [
                        'student_id'       => $studentId,
                        'academic_year_id' => $targetAcademicYearId,
                    ],
                    [
                        'xclass_id' => $targetXclassId,
                        // Jika model StudentEnrollment memiliki kolom school_id dan tidak diisi otomatis,
                        // kita bisa set manual di sini:
                        // 'school_id' => Xclass::find($targetXclassId)->school_id,
                    ]
                );
            }
        });

        $count = count($data['student_ids']);
        $message = $data['action'] === 'LULUS'
            ? "{$count} siswa berhasil ditandai lulus."
            : "{$count} siswa berhasil naik/dipindahkan ke kelas tujuan.";

        return redirect()->route('admin.promotion.create')->with('success', $message);
    }
}