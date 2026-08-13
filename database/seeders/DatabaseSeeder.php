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

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::create([
            'name' => 'Zulzario Zaeri',
            'email' => 'ryotwell@gmail.com',
            'password' => bcrypt('12345678')
        ]);

        $classes = ['7 A', '7 B', '7 C',];

        $academicYear = AcademicYear::create(['name' => 'Tahun Ajaran 2026/2027', 'is_active' => true]);

        foreach($classes as $x) {
            Xclass::create(['name' => $x, 'academic_year_id' => $academicYear->id]);
        }

        Student::factory(100)->create();

        // Seed subjects for grade 7
        $subjects7 = collect([
            'Bahasa Inggris', 'PJOK', 'Ekonomi', 'Bahasa Inggris TKL',
            'Sosiologi', 'Bahasa Indonesia', 'Matematika', 'Seni Budaya',
            'PPKN', 'Geografi', 'Sejarah', 'PAI', 'Prakarya & Kewirausahaan',
        ])->mapWithKeys(fn ($name) => [$name => Subject::firstOrCreate(['name' => $name, 'grade' => '7'])]);

        // Seed jadwal kelas 7A
        $xclass7A = Xclass::where('name', '7 A')->first();
        if ($xclass7A) {
            $schedules = [
                ['day' => 'MONDAY',    'start' => '07:30', 'end' => '08:00', 'subject' => 'Bahasa Inggris'],
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
                    'subject_id' => $subjects7[$s['subject']]->id,
                    'xclass_id'  => $xclass7A->id,
                ]);
            }
        }
    }
}
