<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Development2Seeder extends Seeder
{
    public static function run(): void
    {
        // 1. Buat sekolah "MA Muallimin NWDI Pancor"
        $school = School::firstOrCreate(
            [
                'name'      => 'MA Muallimin NWDI Pancor',
                'address'   => 'Jl. Pendidikan No. 1, Pancor, Lombok Timur',
                'phone'     => '081234567890',
                'email'     => 'info@mamuallimin.sch.id',
                'is_active' => true,
            ]
        );
        $school2 = School::firstOrCreate(
            [
                'name'      => 'SMKN 1 Selong',
                'address'   => 'Jl. Pendidikan No. 1, Pancor, Lombok Timur',
                'phone'     => '081234567890',
                'email'     => 'info@smkn1selong.sch.id',
                'is_active' => true,
            ]
        );

        // 2. Buat Admin untuk sekolah tersebut
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'      => 'Akun Admin',
                'password'  => Hash::make('12345678'),
                'role'      => 'ADMIN',
                'school_id' => $school->id,
            ]
        );
        User::firstOrCreate(
            ['email' => 'smkn1selong@gmail.com'],
            [
                'name'      => 'SMKN 1 Selong',
                'password'  => Hash::make('12345678'),
                'role'      => 'ADMIN',
                'school_id' => $school2->id,
            ]
        );

        // 3. Tahun Ajaran Aktif
        $academicYear = AcademicYear::firstOrCreate(
            [
                'name'      => 'Tahun Ajaran 2026/2027',
                'semester'  => 'GANJIL',
                'school_id' => $school->id,
            ],
            ['is_active' => true]
        );

        // 4. HAPUS BAGIAN MATA PELAJARAN (tidak ada tabel subjects lagi)

        // 5. Semua Guru (termasuk yang akan menjadi wali kelas)
        $teacherData = [
            'Bahasa Inggris'            => ['name' => 'Budi Santoso', 'email' => 'budi.santoso@sekolah.id'],
            'PJOK'                      => ['name' => 'Dewi Anggraini', 'email' => 'dewi.anggraini@sekolah.id'],
            'Ekonomi'                   => ['name' => 'Ahmad Wijaya', 'email' => 'ahmad.wijaya@sekolah.id'],
            'Bahasa Inggris TKL'        => ['name' => 'Siti Rahayu', 'email' => 'siti.rahayu@sekolah.id'],
            'Sosiologi'                 => ['name' => 'Rina Sari', 'email' => 'rina.sari@sekolah.id'],
            'Bahasa Indonesia'          => ['name' => 'Hendra Gunawan', 'email' => 'hendra.gunawan@sekolah.id'],
            'Matematika'                => ['name' => 'Lestari Putri', 'email' => 'lestari.putri@sekolah.id'],
            'Seni Budaya'               => ['name' => 'Yoga Pratama', 'email' => 'yoga.pratama@sekolah.id'],
            'PPKN'                      => ['name' => 'Maya Indah', 'email' => 'maya.indah@sekolah.id'],
            'Geografi'                  => ['name' => 'Fajar Nugroho', 'email' => 'fajar.nugroho@sekolah.id'],
            'Sejarah'                   => ['name' => 'Nina Kartika', 'email' => 'nina.kartika@sekolah.id'],
            'PAI'                       => ['name' => 'Ustadz Rahman', 'email' => 'rahman@sekolah.id'],
            'Prakarya & Kewirausahaan'  => ['name' => 'Doni Setiawan', 'email' => 'doni.setiawan@sekolah.id'],
        ];

        $teachers = [];
        foreach ($teacherData as $subject => $data) {
            $teachers[$subject] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'],
                    'password'  => Hash::make('12345678'),
                    'role'      => 'GURU',
                    'school_id' => $school->id,
                ]
            );
        }

        // 6. Tentukan wali kelas dari guru yang sudah ada (misal: Bahasa Inggris, PJOK, Ekonomi)
        $waliKelas = [
            'XII A' => $teachers['Bahasa Inggris']->id,
            'XII B' => $teachers['PJOK']->id,
            'XII C' => $teachers['Ekonomi']->id,
        ];

        // 7. Buat Kelas dengan wali kelas tersebut
        $createdClasses = [];
        foreach ($waliKelas as $className => $userId) {
            $createdClasses[] = Xclass::firstOrCreate(
                [
                    'name'              => $className,
                    'academic_year_id'  => $academicYear->id,
                    'school_id'         => $school->id,
                ],
                [
                    'user_id'           => $userId,
                ]
            );
        }

        // 8. Buat 100 Siswa (pastikan factory StudentFactory mendukung school_id)
        Student::factory(100)->create(['school_id' => $school->id]);

        // 9. Enroll semua siswa ke salah satu kelas secara acak
        $students = Student::where('school_id', $school->id)->get();
        foreach ($students as $student) {
            $randomClass = $createdClasses[array_rand($createdClasses)];
            StudentEnrollment::firstOrCreate(
                [
                    'student_id'       => $student->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'xclass_id'        => $randomClass->id,
                    'school_id'        => $school->id,
                ]
            );
        }

        // 10. Jadwal untuk kelas XII A (menggunakan subject_name, tanpa subject_id)
        $xclassXIIA = Xclass::where('name', 'XII A')
            ->where('academic_year_id', $academicYear->id)
            ->where('school_id', $school->id)
            ->first();
        if ($xclassXIIA) {
            $schedules = [
                ['day' => 'MONDAY',    'start' => '07:00', 'end' => '08:00', 'subject' => 'Bahasa Inggris'],
                ['day' => 'MONDAY',    'start' => '08:00', 'end' => '09:00', 'subject' => 'PJOK'],
                ['day' => 'MONDAY',    'start' => '09:00', 'end' => '10:00', 'subject' => 'Ekonomi'],
                ['day' => 'TUESDAY',   'start' => '07:00', 'end' => '08:00', 'subject' => 'Bahasa Inggris TKL'],
                ['day' => 'TUESDAY',   'start' => '08:00', 'end' => '09:00', 'subject' => 'Sosiologi'],
                ['day' => 'TUESDAY',   'start' => '09:00', 'end' => '10:00', 'subject' => 'Bahasa Indonesia'],
                ['day' => 'WEDNESDAY', 'start' => '07:00', 'end' => '08:00', 'subject' => 'Matematika'],
                ['day' => 'WEDNESDAY', 'start' => '08:00', 'end' => '09:00', 'subject' => 'Seni Budaya'],
                ['day' => 'WEDNESDAY', 'start' => '09:00', 'end' => '10:00', 'subject' => 'Sosiologi'],
                ['day' => 'WEDNESDAY', 'start' => '10:00', 'end' => '11:00', 'subject' => 'PPKN'],
                ['day' => 'THURSDAY',  'start' => '07:00', 'end' => '08:00', 'subject' => 'Geografi'],
                ['day' => 'THURSDAY',  'start' => '08:00', 'end' => '09:00', 'subject' => 'Sejarah'],
                ['day' => 'THURSDAY',  'start' => '09:00', 'end' => '10:00', 'subject' => 'PAI'],
                ['day' => 'THURSDAY',  'start' => '10:00', 'end' => '11:00', 'subject' => 'Ekonomi'],
                ['day' => 'FRIDAY',    'start' => '07:00', 'end' => '08:00', 'subject' => 'Matematika'],
                ['day' => 'FRIDAY',    'start' => '08:00', 'end' => '09:00', 'subject' => 'Geografi'],
                ['day' => 'SATURDAY',  'start' => '07:00', 'end' => '08:00', 'subject' => 'Prakarya & Kewirausahaan'],
                ['day' => 'SATURDAY',  'start' => '08:00', 'end' => '09:00', 'subject' => 'Bahasa Indonesia'],
                ['day' => 'SATURDAY',  'start' => '09:00', 'end' => '10:00', 'subject' => 'Bahasa Inggris TKL'],
            ];

            foreach ($schedules as $s) {
                // Cari guru berdasarkan nama subject
                $teacher = $teachers[$s['subject']] ?? null;
                if (!$teacher) {
                    continue; // lewati jika guru tidak ditemukan (tapi seharusnya ada)
                }

                Schedule::firstOrCreate(
                    [
                        'day'          => $s['day'],
                        'start_time'   => $s['start'],
                        'end_time'     => $s['end'],
                        'subject_name' => $s['subject'], // <-- field baru
                        'xclass_id'    => $xclassXIIA->id,
                        'school_id'    => $school->id,
                    ],
                    [
                        'user_id'      => $teacher->id,
                    ]
                );
            }
        }

        // 11. Guru BK
        User::firstOrCreate(
            ['email' => 'bk@gmail.com'],
            [
                'name'      => 'Guru BK',
                'password'  => Hash::make('12345678'),
                'role'      => 'GURU_BK',
                'school_id' => $school->id,
            ]
        );

        // superadmin
        User::firstOrCreate(
            ['email' => 'super@gmail.com'],
            [
                'name'      => 'Akun Super Admin',
                'password'  => Hash::make('12345678'),
                'role'      => 'SUPERADMIN',
                'school_id' => $school->id,
            ]
        );
    }
}