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

class PreviewSeeder
{
    public static function run()
    {
        User::create([
            'name' => 'Akun Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'ADMIN'
        ]);

        User::create([
            'name' => 'Akhyar Rosidi',
            'email' => 'rosidi@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'GURU'
        ]);

        $academicYear = AcademicYear::create(['name' => 'Tahun Ajaran 2026/2027', 'is_active' => true]);

        foreach([
            'IPA 12 - 1',
            'IPA 12 - 2',
            'IPA 12 - 3',

            'IPS 12 - 1',

            'Agama 12 - 1',
            'Agama 12 - 2',
            'Agama 12 - 3',
            'Agama 12 - 4',
        ] as $x) {
            Xclass::create(['name' => $x, 'academic_year_id' => $academicYear->id]);
        }

        // Seed subjects for grade 7
        $subjects7 = collect([
            'Matematika', 'Informatika'
        ])->mapWithKeys(fn ($name) => [$name => Subject::firstOrCreate(['name' => $name, 'grade' => 'XII'])]);
    }
}