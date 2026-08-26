<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Development2Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Admin
        User::create([
            'name'     => 'Akun Admin',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role'     => 'ADMIN',
        ]);

        // 2. Tahun Ajaran Aktif
        $academicYear = AcademicYear::create([
            'name'      => 'Tahun Ajaran 2026/2027',
            'semester'  => 'GANJIL',
            'is_active' => true,
        ]);

        // 3. Mata Pelajaran untuk kelas XII
        $subjectNames = [
            'Bahasa Inggris', 'PJOK', 'Ekonomi', 'Bahasa Inggris TKL',
            'Sosiologi', 'Bahasa Indonesia', 'Matematika', 'Seni Budaya',
            'PPKN', 'Geografi', 'Sejarah', 'PAI', 'Prakarya & Kewirausahaan',
        ];
        $subjects = [];
        foreach ($subjectNames as $name) {
            $subjects[$name] = Subject::firstOrCreate(
                ['name' => $name, 'grade' => 'XII']
            );
        }

        // 4. Semua Guru (termasuk yang akan menjadi wali kelas)
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
                    'name'     => $data['name'],
                    'password' => Hash::make('12345678'),
                    'role'     => 'GURU',
                ]
            );
        }

        // 5. Tentukan wali kelas dari guru yang sudah ada (misal: Bahasa Inggris, PJOK, Ekonomi)
        $waliKelas = [
            'XII A' => $teachers['Bahasa Inggris']->id,
            'XII B' => $teachers['PJOK']->id,
            'XII C' => $teachers['Ekonomi']->id,
        ];

        // 6. Buat Kelas dengan wali kelas tersebut
        $createdClasses = [];
        foreach ($waliKelas as $className => $userId) {
            $createdClasses[] = Xclass::create([
                'name'               => $className,
                'academic_year_id'   => $academicYear->id,
                'user_id'            => $userId,
            ]);
        }

        // 7. Buat 100 Siswa (pastikan factory StudentFactory ada)
        Student::factory(100)->create();

        // 8. Enroll semua siswa ke salah satu kelas secara acak
        $students = Student::all();
        foreach ($students as $student) {
            $randomClass = $createdClasses[array_rand($createdClasses)];
            StudentEnrollment::create([
                'student_id'        => $student->id,
                'academic_year_id'  => $academicYear->id,
                'xclass_id'         => $randomClass->id,
            ]);
        }

        // 9. Jadwal untuk kelas XII A (tidak diubah)
        $xclassXIIA = Xclass::where('name', 'XII A')->first();
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
                Schedule::create([
                    'day'        => $s['day'],
                    'start_time' => $s['start'],
                    'end_time'   => $s['end'],
                    'subject_id' => $subjects[$s['subject']]->id,
                    'user_id'    => $teachers[$s['subject']]->id,
                    'xclass_id'  => $xclassXIIA->id,
                ]);
            }
        }

        // 10. Guru BK
        User::create([
            'name'     => 'Guru BK',
            'email'    => 'bk@gmail.com',
            'password' => Hash::make('12345678'),
            'role'     => 'GURU_BK',
        ]);
    }
}