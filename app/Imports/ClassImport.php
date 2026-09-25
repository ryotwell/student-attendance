<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Xclass;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClassImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    SkipsEmptyRows
{
    protected int $academicYearId;

    protected int $schoolId;

    /**
     * Menyimpan error import.
     */
    protected array $errors = [];

    /**
     * Jumlah berhasil.
     */
    protected int $successCount = 0;

    /**
     * Constructor.
     */
    public function __construct(
        int $academicYearId,
        int $schoolId
    ) {
        $this->academicYearId = $academicYearId;
        $this->schoolId = $schoolId;
    }

    /**
     * Proses data Excel.
     */
    public function collection(Collection $rows): void
    {
        /*
         * Nomor baris Excel dimulai dari 2
         * karena baris pertama adalah header.
         */
        $excelRow = 2;

        foreach ($rows as $row) {

            /*
             * Ambil data.
             */
            $namaKelas = trim(
                (string) ($row['nama_kelas'] ?? '')
            );

            $kodeKelas = trim(
                (string) ($row['kode_kelas'] ?? '')
            );

            $waliKelas = trim(
                (string) ($row['wali_kelas'] ?? '')
            );

            /*
             * ==============================
             * VALIDASI MANUAL
             * ==============================
             */

            /*
             * Nama kelas wajib.
             */
            if ($namaKelas === '') {

                $this->addError(
                    $excelRow,
                    'Nama_Kelas wajib diisi.'
                );

                $excelRow++;

                continue;
            }

            /*
             * Kode kelas wajib.
             */
            if ($kodeKelas === '') {

                $this->addError(
                    $excelRow,
                    'Kode_Kelas wajib diisi.'
                );

                $excelRow++;

                continue;
            }

            /*
             * ==============================
             * VALIDASI WALI KELAS
             * ==============================
             */

            $teacher = null;

            if ($waliKelas !== '') {

                /*
                 * Validasi format email.
                 */
                if (!filter_var(
                    $waliKelas,
                    FILTER_VALIDATE_EMAIL
                )) {

                    $this->addError(
                        $excelRow,
                        "Wali_Kelas '{$waliKelas}' bukan email yang valid."
                    );

                    $excelRow++;

                    continue;
                }

                /*
                 * Cari user berdasarkan email.
                 *
                 * Wajib role GURU.
                 */
                $teacher = User::query()
                    ->where(
                        'email',
                        $waliKelas
                    )
                    ->where(
                        'role',
                        'GURU'
                    )
                    ->where(
                        'school_id',
                        $this->schoolId
                    )
                    ->first();

                /*
                 * Email tidak ditemukan.
                 */
                if (!$teacher) {

                    $this->addError(
                        $excelRow,
                        "Wali_Kelas dengan email '{$waliKelas}' tidak ditemukan atau bukan guru pada sekolah ini."
                    );

                    $excelRow++;

                    continue;
                }

                /*
                 * Cek apakah guru sudah menjadi
                 * wali kelas.
                 */
                $waliExists = Xclass::query()
                    ->where(
                        'academic_year_id',
                        $this->academicYearId
                    )
                    ->where(
                        'school_id',
                        $this->schoolId
                    )
                    ->where(
                        'user_id',
                        $teacher->id
                    )
                    ->exists();

                if ($waliExists) {

                    $this->addError(
                        $excelRow,
                        "Guru dengan email '{$waliKelas}' sudah menjadi wali kelas pada tahun ajaran ini."
                    );

                    $excelRow++;

                    continue;
                }
            }

            /*
             * ==============================
             * VALIDASI KODE KELAS
             * ==============================
             */

            /*
             * Cek database.
             */
            $kodeExists = Xclass::query()
                ->where(
                    'kode_kelas',
                    $kodeKelas
                )
                ->where(
                    'school_id',
                    $this->schoolId
                )
                ->where(
                    'academic_year_id',
                    $this->academicYearId
                )
                ->exists();

            if ($kodeExists) {

                $this->addError(
                    $excelRow,
                    "Kode_Kelas '{$kodeKelas}' sudah digunakan pada tahun ajaran ini."
                );

                $excelRow++;

                continue;
            }

            /*
             * ==============================
             * SIMPAN KELAS
             * ==============================
             */

            try {

                Xclass::create([
                    'name' =>
                        $namaKelas,

                    'kode_kelas' =>
                        $kodeKelas,

                    'academic_year_id' =>
                        $this->academicYearId,

                    'user_id' =>
                        $teacher?->id,

                    'school_id' =>
                        $this->schoolId,
                ]);

                $this->successCount++;

            } catch (\Throwable $e) {

                report($e);

                $this->addError(
                    $excelRow,
                    "Gagal menyimpan kelas '{$namaKelas}'."
                );
            }

            $excelRow++;
        }
    }

    /**
     * Tambahkan error.
     */
    private function addError(
        int $row,
        string $message
    ): void {
        $this->errors[] = [
            'row' => $row,
            'message' => $message,
        ];
    }

    /**
     * Ambil daftar error.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Jumlah data berhasil.
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Validasi Laravel Excel.
     *
     * Tetap digunakan untuk validasi dasar.
     */
    public function rules(): array
    {
        return [
            'nama_kelas' => [
                'nullable',
                'string',
                'max:255',
            ],

            'kode_kelas' => [
                'nullable',
                'string',
                'max:255',
            ],

            'wali_kelas' => [
                'nullable',
                'email',
            ],
        ];
    }

    /**
     * Pesan validasi.
     */
    public function customValidationMessages(): array
    {
        return [
            'nama_kelas.string' =>
                'Nama_Kelas harus berupa teks.',

            'nama_kelas.max' =>
                'Nama_Kelas maksimal 255 karakter.',

            'kode_kelas.string' =>
                'Kode_Kelas harus berupa teks.',

            'kode_kelas.max' =>
                'Kode_Kelas maksimal 255 karakter.',

            'wali_kelas.email' =>
                'Wali_Kelas harus berupa email yang valid.',
        ];
    }
}