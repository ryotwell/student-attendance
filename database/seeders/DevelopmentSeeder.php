<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Models\Xclass;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevelopmentSeeder
{
    public static function run()
    {
        User::create([
            'name' => 'Akun Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'ADMIN'
        ]);

        $academicYear = AcademicYear::create(['name' => 'Tahun Ajaran 2026/2027', 'is_active' => true]);

        $classes = [
            [
                'name' => 'XII A',
                'user_id' => 2, // wali kelas
            ],
            [
                'name' => 'XII B',
                'user_id' => 3, // wali kelas
            ],
            [
                'name' => 'XII C',
                'user_id' => 4, // wali kelas
            ],
        ];
        foreach ($classes as $x) {
            Xclass::create([
                'name' => $x['name'],
                'academic_year_id' => $academicYear->id,
                'user_id' => $x['user_id']
            ]);
        }

        Student::factory(100)->create();

        // Seed subjects untuk kelas XII
        $subjectsXII = collect([
            'Bahasa Inggris', 'PJOK', 'Ekonomi', 'Bahasa Inggris TKL',
            'Sosiologi', 'Bahasa Indonesia', 'Matematika', 'Seni Budaya',
            'PPKN', 'Geografi', 'Sejarah', 'PAI', 'Prakarya & Kewirausahaan',
        ])->mapWithKeys(fn ($name) => [$name => Subject::firstOrCreate(['name' => $name, 'grade' => 'XII'])]);

        // Seed guru (role GURU) untuk tiap mata pelajaran
        $teachers = collect([
            'Bahasa Inggris' => ['name' => 'Budi Santoso', 'email' => 'budi.santoso@sekolah.id'],
            'PJOK' => ['name' => 'Dewi Anggraini', 'email' => 'dewi.anggraini@sekolah.id'],
            'Ekonomi' => ['name' => 'Ahmad Wijaya', 'email' => 'ahmad.wijaya@sekolah.id'],
            'Bahasa Inggris TKL' => ['name' => 'Siti Rahayu', 'email' => 'siti.rahayu@sekolah.id'],
            'Sosiologi' => ['name' => 'Rina Sari', 'email' => 'rina.sari@sekolah.id'],
            'Bahasa Indonesia' => ['name' => 'Hendra Gunawan', 'email' => 'hendra.gunawan@sekolah.id'],
            'Matematika' => ['name' => 'Lestari Putri', 'email' => 'lestari.putri@sekolah.id'],
            'Seni Budaya' => ['name' => 'Yoga Pratama', 'email' => 'yoga.pratama@sekolah.id'],
            'PPKN' => ['name' => 'Maya Indah', 'email' => 'maya.indah@sekolah.id'],
            'Geografi' => ['name' => 'Fajar Nugroho', 'email' => 'fajar.nugroho@sekolah.id'],
            'Sejarah' => ['name' => 'Nina Kartika', 'email' => 'nina.kartika@sekolah.id'],
            'PAI' => ['name' => 'Ustadz Rahman', 'email' => 'rahman@sekolah.id'],
            'Prakarya & Kewirausahaan' => ['name' => 'Doni Setiawan', 'email' => 'doni.setiawan@sekolah.id'],
        ])->mapWithKeys(fn ($t, $subject) => [$subject => User::firstOrCreate(
            ['email' => $t['email']],
            ['name' => $t['name'], 'password' => bcrypt('12345678'), 'role' => 'GURU']
        )]);

        // Seed jadwal kelas XII A
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
                    'subject_id' => $subjectsXII[$s['subject']]->id,
                    'user_id'    => $teachers[$s['subject']]->id,
                    'xclass_id'  => $xclassXIIA->id,
                ]);
            }
        }


        User::create([
            'name' => 'Guru BK',
            'email' => 'bk@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'GURU_BK'
        ]);
    }
}