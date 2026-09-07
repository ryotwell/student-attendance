<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Xclass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class StudentImport implements
    ToCollection,
    WithHeadingRow,
    SkipsEmptyRows
{
    protected int $academicYearId;

    protected int $schoolId;

    protected array $errors = [];

    protected int $successCount = 0;

    protected int $excelRow = 2;

    public function __construct(
        int $academicYearId,
        int $schoolId
    ) {
        $this->academicYearId = $academicYearId;
        $this->schoolId = $schoolId;
    }

    /**
     * Proses seluruh baris Excel.
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {

            $rowNumber = $this->excelRow++;

            $data = $row->toArray();

            /*
             * Ambil data.
             */
            $name = $this->cleanValue(
                $data['nama_siswa'] ?? null
            );

            $nis = $this->cleanValue(
                $data['nis'] ?? null
            );

            $nisn = $this->cleanValue(
                $data['nisn'] ?? null
            );

            $gender = $this->normalizeGender(
                $data['jenis_kelamin'] ?? null
            );

            $status = $this->normalizeStatus(
                $data['status'] ?? null
            );

            $parentName = $this->cleanValue(
                $data['nama_orang_tua'] ?? null
            );

            $parentPhone = $this->cleanValue(
                $data['no_hp_orang_tua'] ?? null
            );

            $kodeKelas = $this->cleanValue(
                $data['kode_kelas'] ?? null
            );

            /*
             * Nama siswa.
             */
            if (!$name) {
                $this->addError(
                    $rowNumber,
                    'Nama siswa wajib diisi.'
                );

                continue;
            }

            if (strlen($name) > 255) {
                $this->addError(
                    $rowNumber,
                    'Nama siswa maksimal 255 karakter.'
                );

                continue;
            }

            /*
             * NIS.
             */
            if (!$nis) {
                $this->addError(
                    $rowNumber,
                    'NIS wajib diisi.'
                );

                continue;
            }

            if (strlen($nis) > 20) {
                $this->addError(
                    $rowNumber,
                    'NIS maksimal 20 karakter.'
                );

                continue;
            }

            /*
             * Cek NIS.
             */
            $existingNis = Student::where(
                    'nis',
                    $nis
                )
                ->where(
                    'school_id',
                    $this->schoolId
                )
                ->exists();

            if ($existingNis) {
                $this->addError(
                    $rowNumber,
                    "NIS '{$nis}' sudah terdaftar."
                );

                continue;
            }

            /*
             * NISN.
             */
            if (!$nisn) {
                $this->addError(
                    $rowNumber,
                    'NISN wajib diisi.'
                );

                continue;
            }

            if (strlen($nisn) > 20) {
                $this->addError(
                    $rowNumber,
                    'NISN maksimal 20 karakter.'
                );

                continue;
            }

            /*
             * Cek NISN.
             */
            $existingNisn = Student::where(
                    'nisn',
                    $nisn
                )
                ->where(
                    'school_id',
                    $this->schoolId
                )
                ->exists();

            if ($existingNisn) {
                $this->addError(
                    $rowNumber,
                    "NISN '{$nisn}' sudah terdaftar."
                );

                continue;
            }

            /*
             * Jenis kelamin.
             */
            if (!$gender) {
                $this->addError(
                    $rowNumber,
                    'Jenis kelamin tidak valid. Gunakan L/P, MALE/FEMALE, atau LAKI-LAKI/PEREMPUAN.'
                );

                continue;
            }

            /*
             * Status.
             */
            if (!$status) {
                $this->addError(
                    $rowNumber,
                    'Status tidak valid. Gunakan AKTIF, LULUS, PINDAH, atau KELUAR.'
                );

                continue;
            }

            /*
             * Nama orang tua.
             */
            if (
                $parentName &&
                strlen($parentName) > 255
            ) {
                $this->addError(
                    $rowNumber,
                    'Nama orang tua maksimal 255 karakter.'
                );

                continue;
            }

            /*
             * Nomor HP.
             */
            if (
                $parentPhone &&
                strlen($parentPhone) > 20
            ) {
                $this->addError(
                    $rowNumber,
                    'Nomor HP orang tua maksimal 20 karakter.'
                );

                continue;
            }

            /*
             * Kode kelas.
             */
            if (!$kodeKelas) {
                $this->addError(
                    $rowNumber,
                    'Kode kelas wajib diisi.'
                );

                continue;
            }

            /*
             * Cari kelas.
             */
            $xclass = Xclass::where(
                    'school_id',
                    $this->schoolId
                )
                ->where(
                    'academic_year_id',
                    $this->academicYearId
                )
                ->where(
                    'kode_kelas',
                    $kodeKelas
                )
                ->first();

            if (!$xclass) {
                $this->addError(
                    $rowNumber,
                    "Kode kelas '{$kodeKelas}' tidak ditemukan pada tahun ajaran yang dipilih."
                );

                continue;
            }

            /*
             * Simpan siswa dan enrollment.
             */
            try {

                DB::transaction(function () use (
                    $name,
                    $nis,
                    $nisn,
                    $gender,
                    $status,
                    $parentName,
                    $parentPhone,
                    $xclass
                ) {

                    $student = Student::create([
                        'name' => $name,
                        'nis' => $nis,
                        'nisn' => $nisn,
                        'gender' => $gender,
                        'status' => $status,
                        'parent_name' => $parentName,
                        'parent_phone' => $parentPhone,
                        'school_id' => $this->schoolId,
                    ]);

                    StudentEnrollment::create([
                        'student_id' => $student->id,
                        'xclass_id' => $xclass->id,
                        'academic_year_id' => $this->academicYearId,
                        'school_id' => $this->schoolId,
                    ]);
                });

                $this->successCount++;

            } catch (Throwable $e) {

                $this->addError(
                    $rowNumber,
                    'Gagal menyimpan data: ' .
                    $this->getReadableException($e)
                );

                continue;
            }
        }
    }

    /**
     * Bersihkan data Excel.
     */
    private function cleanValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * Normalisasi gender.
     */
    private function normalizeGender($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = strtoupper(
            trim((string) $value)
        );

        return match ($value) {

            'L',
            'LAKI',
            'LAKI-LAKI',
            'LAKI LAKI',
            'MALE',
            'M' => 'MALE',

            'P',
            'PEREMPUAN',
            'FEMALE',
            'F' => 'FEMALE',

            default => null,
        };
    }

    /**
     * Normalisasi status.
     */
    private function normalizeStatus($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = strtoupper(
            trim((string) $value)
        );

        return match ($value) {

            'AKTIF',
            'ACTIVE' => 'AKTIF',

            'LULUS',
            'GRADUATED' => 'LULUS',

            'PINDAH',
            'MUTASI' => 'PINDAH',

            'KELUAR' => 'KELUAR',

            default => null,
        };
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
     * Pesan error database.
     */
    private function getReadableException(
        Throwable $e
    ): string {
        $message = $e->getMessage();

        if (
            str_contains(
                strtolower($message),
                'duplicate'
            )
        ) {
            return 'Data duplikat. NIS atau NISN mungkin sudah terdaftar.';
        }

        if (
            str_contains(
                strtolower($message),
                'too long'
            )
        ) {
            return 'Ada data yang melebihi batas panjang field.';
        }

        if (
            str_contains(
                strtolower($message),
                'foreign key'
            )
        ) {
            return 'Data kelas atau tahun ajaran tidak valid.';
        }

        return $message;
    }

    /**
     * Ambil error.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Jumlah berhasil.
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
}