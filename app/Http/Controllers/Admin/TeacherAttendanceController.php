<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherAttendance;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAttendanceController extends Controller
{
    /**
     * Menampilkan daftar absensi guru.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $this->ensureAdmin($user);

        $isSuperAdmin = $this->isSuperAdmin($user);

        /*
        |--------------------------------------------------------------------------
        | Query Absensi
        |--------------------------------------------------------------------------
        */

        $query = TeacherAttendance::query()
            ->with([
                'user:id,name,email,role,school_id',
                'school:id,name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        $this->applyFilters(
            $query,
            $request,
            $user,
            $isSuperAdmin
        );

        /*
        |--------------------------------------------------------------------------
        | Data Absensi
        |--------------------------------------------------------------------------
        */

        $attendances = $query
            ->orderByDesc('date')
            ->orderByDesc('check_in')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Daftar Guru
        |--------------------------------------------------------------------------
        */

        $teachersQuery = User::query()
            ->select([
                'id',
                'name',
                'email',
                'role',
                'school_id',
            ])
            ->whereIn('role', [
                'GURU',
                'GURU_BK',
            ])
            ->orderBy('name');

        /*
        |--------------------------------------------------------------------------
        | Filter Guru Berdasarkan Sekolah
        |--------------------------------------------------------------------------
        */

        if (!$isSuperAdmin && $user->school_id) {
            $teachersQuery->where(
                'school_id',
                $user->school_id
            );
        }

        $teachers = $teachersQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        |
        | Sebelumnya statistik menjalankan beberapa query COUNT().
        | Sekarang seluruh statistik dihitung dalam SATU query agregasi.
        |
        */

        $statisticsQuery = TeacherAttendance::query();

        /*
        |--------------------------------------------------------------------------
        | Filter Sekolah Statistik
        |--------------------------------------------------------------------------
        */

        if (!$isSuperAdmin && $user->school_id) {
            $statisticsQuery->where(
                'school_id',
                $user->school_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Periode Statistik
        |--------------------------------------------------------------------------
        */

        $this->applyStatisticsPeriod(
            $statisticsQuery,
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | Filter Guru Statistik
        |--------------------------------------------------------------------------
        */

        if ($request->filled('teacher_id')) {
            $statisticsQuery->where(
                'user_id',
                $request->teacher_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Statistik Dalam Satu Query
        |--------------------------------------------------------------------------
        */

        $stats = $statisticsQuery
            ->selectRaw(
                '
                COUNT(*) AS total,

                SUM(
                    CASE
                        WHEN status = ?
                        THEN 1
                        ELSE 0
                    END
                ) AS hadir,

                SUM(
                    CASE
                        WHEN status = ?
                        THEN 1
                        ELSE 0
                    END
                ) AS izin,

                SUM(
                    CASE
                        WHEN status = ?
                        THEN 1
                        ELSE 0
                    END
                ) AS sakit,

                SUM(
                    CASE
                        WHEN status = ?
                        THEN 1
                        ELSE 0
                    END
                ) AS alpha,

                SUM(
                    CASE
                        WHEN check_out IS NOT NULL
                        THEN 1
                        ELSE 0
                    END
                ) AS sudah_pulang,

                SUM(
                    CASE
                        WHEN status = ?
                        AND check_out IS NULL
                        THEN 1
                        ELSE 0
                    END
                ) AS belum_pulang
                ',
                [
                    'HADIR',
                    'IZIN',
                    'SAKIT',
                    'ALPHA',
                    'HADIR',
                ]
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Format Statistik
        |--------------------------------------------------------------------------
        */

        $statistics = [
            'total' => (int) ($stats->total ?? 0),
            'hadir' => (int) ($stats->hadir ?? 0),
            'izin' => (int) ($stats->izin ?? 0),
            'sakit' => (int) ($stats->sakit ?? 0),
            'alpha' => (int) ($stats->alpha ?? 0),
            'sudah_pulang' => (int) ($stats->sudah_pulang ?? 0),
            'belum_pulang' => (int) ($stats->belum_pulang ?? 0),
        ];

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.teacher-attendance.index',
            compact(
                'attendances',
                'teachers',
                'statistics'
            )
        );
    }


    /**
     * Menampilkan detail absensi guru.
     */
    public function show(TeacherAttendance $teacherAttendance)
    {
        $user = Auth::user();

        $this->ensureAdmin($user);

        /*
        |--------------------------------------------------------------------------
        | Cek Akses Sekolah
        |--------------------------------------------------------------------------
        */

        $isSuperAdmin = $this->isSuperAdmin($user);

        if (
            !$isSuperAdmin &&
            $user->school_id &&
            $teacherAttendance->school_id != $user->school_id
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke data ini.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Load Relasi Yang Dibutuhkan
        |--------------------------------------------------------------------------
        */

        $teacherAttendance->load([
            'user:id,name,email,role,school_id',
            'school:id,name',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Pastikan User Adalah Guru
        |--------------------------------------------------------------------------
        */

        if (
            !$teacherAttendance->user ||
            !in_array(
                strtoupper(
                    $teacherAttendance->user->role ?? ''
                ),
                [
                    'GURU',
                    'GURU_BK',
                ],
                true
            )
        ) {
            abort(404);
        }

        return view(
            'admin.teacher-attendance.show',
            compact('teacherAttendance')
        );
    }


    /**
     * Export absensi guru ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        $this->ensureAdmin($user);

        $isSuperAdmin = $this->isSuperAdmin($user);

        /*
        |--------------------------------------------------------------------------
        | Query Absensi
        |--------------------------------------------------------------------------
        */

        $query = TeacherAttendance::query()
            ->with([
                'user:id,name,email,role,school_id',
                'school:id,name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Terapkan Filter
        |--------------------------------------------------------------------------
        */

        $this->applyFilters(
            $query,
            $request,
            $user,
            $isSuperAdmin
        );

        /*
        |--------------------------------------------------------------------------
        | Data PDF
        |--------------------------------------------------------------------------
        */

        $attendances = $query
            ->orderBy('date')
            ->orderBy('check_in')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Informasi Filter
        |--------------------------------------------------------------------------
        */

        $filterDate = $request->date;
        $filterMonth = $request->month;
        $filterStatus = $request->status;
        $filterSearch = $request->search;

        /*
        |--------------------------------------------------------------------------
        | Guru Yang Dipilih
        |--------------------------------------------------------------------------
        */

        $teacher = null;

        if ($request->filled('teacher_id')) {

            $teacherQuery = User::query()
                ->select([
                    'id',
                    'name',
                    'email',
                    'role',
                    'school_id',
                ])
                ->whereIn('role', [
                    'GURU',
                    'GURU_BK',
                ])
                ->where(
                    'id',
                    $request->teacher_id
                );

            /*
            |--------------------------------------------------------------------------
            | Pastikan Admin Tidak Bisa Mengakses Guru Sekolah Lain
            |--------------------------------------------------------------------------
            */

            if (!$isSuperAdmin && $user->school_id) {
                $teacherQuery->where(
                    'school_id',
                    $user->school_id
                );
            }

            $teacher = $teacherQuery->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Generate PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'admin.teacher-attendance.pdf',
            compact(
                'attendances',
                'filterDate',
                'filterMonth',
                'filterStatus',
                'filterSearch',
                'teacher'
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Paper
        |--------------------------------------------------------------------------
        */

        $pdf->setPaper(
            'A4',
            'landscape'
        );

        /*
        |--------------------------------------------------------------------------
        | Filename
        |--------------------------------------------------------------------------
        */

        $filename = 'laporan-absensi-guru';

        if ($filterDate) {

            $filename .= '-' . $filterDate;

        } elseif ($filterMonth) {

            $filename .= '-' . $filterMonth;

        } else {

            $filename .= '-' . now()->format('Y-m-d');
        }

        $filename .= '.pdf';

        return $pdf->download($filename);
    }


    /**
     * Menerapkan seluruh filter absensi.
     */
    private function applyFilters(
        Builder $query,
        Request $request,
        $user,
        bool $isSuperAdmin
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Filter Sekolah
        |--------------------------------------------------------------------------
        */

        if (!$isSuperAdmin && $user->school_id) {
            $query->where(
                'school_id',
                $user->school_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Tanggal
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date')) {

            /*
            | Karena field date bertipe DATE,
            | gunakan where() agar index dapat digunakan.
            */

            $query->where(
                'date',
                $request->date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Bulan
        |--------------------------------------------------------------------------
        */

        elseif (
            $request->filled('month') &&
            preg_match(
                '/^\d{4}-\d{2}$/',
                $request->month
            )
        ) {

            try {

                $startDate = Carbon::createFromFormat(
                    'Y-m',
                    $request->month
                )->startOfMonth();

                $endDate = $startDate->copy()->endOfMonth();

                $query->whereBetween(
                    'date',
                    [
                        $startDate->toDateString(),
                        $endDate->toDateString(),
                    ]
                );

            } catch (\Throwable $e) {

                /*
                | Jika format bulan tidak valid,
                | filter bulan diabaikan.
                */

            }
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $status = strtoupper(
                trim($request->status)
            );

            if (
                in_array(
                    $status,
                    [
                        'HADIR',
                        'IZIN',
                        'SAKIT',
                        'ALPHA',
                    ],
                    true
                )
            ) {

                $query->where(
                    'status',
                    $status
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Guru
        |--------------------------------------------------------------------------
        */

        if ($request->filled('teacher_id')) {

            $query->where(
                'user_id',
                $request->teacher_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pencarian Guru
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            if ($search !== '') {

                $query->whereHas(
                    'user',
                    function (Builder $userQuery) use ($search) {

                        $userQuery->where(
                            function (Builder $q) use ($search) {

                                $q->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                );

                            }
                        );

                    }
                );
            }
        }
    }


    /**
     * Menerapkan periode untuk statistik.
     */
    private function applyStatisticsPeriod(
        Builder $query,
        Request $request
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Tanggal
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date')) {

            $query->where(
                'date',
                $request->date
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Bulan
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('month') &&
            preg_match(
                '/^\d{4}-\d{2}$/',
                $request->month
            )
        ) {

            try {

                $startDate = Carbon::createFromFormat(
                    'Y-m',
                    $request->month
                )->startOfMonth();

                $endDate = $startDate->copy()->endOfMonth();

                $query->whereBetween(
                    'date',
                    [
                        $startDate->toDateString(),
                        $endDate->toDateString(),
                    ]
                );

                return;

            } catch (\Throwable $e) {
                // Lanjut menggunakan tanggal hari ini.
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Default: Hari Ini
        |--------------------------------------------------------------------------
        */

        $query->where(
            'date',
            today()->toDateString()
        );
    }


    /**
     * Mengecek apakah user merupakan Super Admin.
     */
    private function isSuperAdmin($user): bool
    {
        return in_array(
            strtoupper($user->role ?? ''),
            [
                'SUPERADMIN',
                'SUPER_ADMIN',
            ],
            true
        );
    }


    /**
     * Memastikan user adalah Admin.
     */
    private function ensureAdmin($user): void
    {
        $allowedRoles = [
            'ADMIN',
            'SUPERADMIN',
            'SUPER_ADMIN',
        ];

        if (
            !$user ||
            !in_array(
                strtoupper($user->role ?? ''),
                $allowedRoles,
                true
            )
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke halaman ini.'
            );
        }
    }
}