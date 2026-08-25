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

class DevelopmentSeeder extends Seeder
{
    public static function run(): void
    {

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        $admin = User::firstOrCreate(
            [
                'email'=>'admin@gmail.com'
            ],
            [
                'name'=>'Akun Admin',
                'password'=>bcrypt('12345678'),
                'role'=>'ADMIN'
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | ACADEMIC YEAR
        |--------------------------------------------------------------------------
        */

        $academicYear = AcademicYear::create([
            'name'=>'Tahun Ajaran 2026/2027',
            'semester'=>'GANJIL',
            'is_active'=>true,
        ]);


        /*
        |--------------------------------------------------------------------------
        | GURU
        |--------------------------------------------------------------------------
        */

        $teachers = collect([
            [
                'name'=>'Budi Santoso',
                'email'=>'budi@gmail.com'
            ],
            [
                'name'=>'Dewi Anggraini',
                'email'=>'dewi@gmail.com'
            ],
            [
                'name'=>'Ahmad Wijaya',
                'email'=>'ahmad@gmail.com'
            ],
            [
                'name'=>'Lestari Putri',
                'email'=>'lestari@gmail.com'
            ],

        ])->map(
            fn($teacher)=>User::firstOrCreate(
                [
                    'email'=>$teacher['email']
                ],
                [
                    'name'=>$teacher['name'],
                    'password'=>bcrypt('12345678'),
                    'role'=>'GURU'
                ]
            )
        );


        /*
        |--------------------------------------------------------------------------
        | WALI KELAS
        |--------------------------------------------------------------------------
        */

        $waliKelas = $teachers->values();


        /*
        |--------------------------------------------------------------------------
        | KELAS
        |--------------------------------------------------------------------------
        */

        $classes=[];

        foreach([
            'XII A',
            'XII B',
            'XII C'
        ] as $index=>$name){

            $classes[] = Xclass::create([
                'name'=>$name,
                'academic_year_id'=>$academicYear->id,
                'user_id'=>$waliKelas[$index % count($waliKelas)]->id
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SISWA
        |--------------------------------------------------------------------------
        */
        $students = Student::factory(100)->create();

        /*
        |--------------------------------------------------------------------------
        | ENROLLMENT SISWA
        |--------------------------------------------------------------------------
        */
        foreach($students as $index=>$student){
            StudentEnrollment::create([
                'student_id'=>$student->id,
                'academic_year_id'=>$academicYear->id,
                'xclass_id'=>$classes[$index % 3]->id
            ]);
        }



        /*
        |--------------------------------------------------------------------------
        | SUBJECT
        |--------------------------------------------------------------------------
        */
        $subjects = collect([

            'Bahasa Inggris',
            'PJOK',
            'Ekonomi',
            'Bahasa Indonesia',
            'Matematika',
            'Seni Budaya',
            'PPKN',
            'Geografi',
            'Sejarah',
            'PAI',
            'Prakarya'

        ])
        ->mapWithKeys(function($name){
            return [
                $name=>Subject::create([
                    'name'=>$name,
                    'grade'=>'XII'
                ])
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | SCHEDULE XII A
        |--------------------------------------------------------------------------
        */
        $scheduleData=[
            [
                'day'=>'MONDAY',
                'start'=>'07:00',
                'end'=>'08:00',
                'subject'=>'Matematika'
            ],

            [
                'day'=>'MONDAY',
                'start'=>'08:00',
                'end'=>'09:00',
                'subject'=>'Bahasa Inggris'
            ],

            [
                'day'=>'TUESDAY',
                'start'=>'07:00',
                'end'=>'08:00',
                'subject'=>'Ekonomi'
            ],

            [
                'day'=>'WEDNESDAY',
                'start'=>'07:00',
                'end'=>'08:00',
                'subject'=>'PPKN'
            ],

            [
                'day'=>'THURSDAY',
                'start'=>'07:00',
                'end'=>'08:00',
                'subject'=>'Sejarah'
            ],

            [
                'day'=>'FRIDAY',
                'start'=>'07:00',
                'end'=>'08:00',
                'subject'=>'PAI'
            ],

        ];


        $xclass = $classes[0];


        foreach($scheduleData as $item){
            Schedule::create([
                'day'=>$item['day'],
                'start_time'=>$item['start'],
                'end_time'=>$item['end'],
                'subject_id'=>$subjects[$item['subject']]->id,
                'user_id'=>$teachers->random()->id,
                'xclass_id'=>$xclass->id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GURU BK
        |--------------------------------------------------------------------------
        */
        User::firstOrCreate(

            [
                'email'=>'bk@gmail.com'
            ],

            [
                'name'=>'Guru BK',

                'password'=>bcrypt('12345678'),

                'role'=>'GURU_BK'
            ]

        );

    }
}