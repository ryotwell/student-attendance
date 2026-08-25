<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Xclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
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
        return view('admin.promotion.create', [
            'sourceXclasses' => Xclass::with('academicYear')
                ->whereHas('academicYear', fn ($q) => $q->where('is_active', false))
                ->orderBy('name')
                ->get(),
            'targetXclasses' => Xclass::with('academicYear')
                ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Daftar siswa aktif di satu kelas asal (dipakai AJAX saat kelas asal dipilih).
     */
    public function studentsInClass(Xclass $xclass)
    {
        $students = $xclass->studentEnrollments()
            ->with('student')
            ->whereHas('student', fn ($q) => $q->where('status', 'AKTIF'))
            ->get()
            ->pluck('student')
            ->values();

        return response()->json($students);
    }

    /**
     * Proses kenaikan kelas untuk siswa-siswa terpilih ke satu kelas tujuan,
     * ATAU meluluskan siswa-siswa terpilih.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'action' => ['required', 'in:NAIK,LULUS'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'exists:students,id'],
            'target_xclass_id' => ['required_if:action,NAIK', 'nullable', 'exists:xclasses,id'],
        ]);

        DB::transaction(function () use ($data) {
            if ($data['action'] === 'LULUS') {
                Student::whereIn('id', $data['student_ids'])
                    ->update(['status' => 'LULUS']);

                return;
            }

            // Ambil academic_year_id dari kelas tujuan itu sendiri,
            // bukan dari input terpisah, supaya xclass_id dan
            // academic_year_id yang tersimpan selalu konsisten.
            $targetXclass = Xclass::whereHas('academicYear', fn ($q) => $q->where('is_active', true))
                ->findOrFail($data['target_xclass_id']);

            foreach ($data['student_ids'] as $studentId) {
                StudentEnrollment::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'academic_year_id' => $targetXclass->academic_year_id,
                    ],
                    [
                        'xclass_id' => $targetXclass->id,
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