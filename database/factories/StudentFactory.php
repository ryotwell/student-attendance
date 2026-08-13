<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Xclass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $xclass = Xclass::inRandomOrder()->first() ?? Xclass::create([
            'name' => fake()->randomElement(['7 A', '7 B', '7 C']),
            'academic_year_id' => AcademicYear::create([
                'name' => fake()->word() . ' ' . fake()->year(),
                'semester' => 'GANJIL',
                'is_active' => false,
            ])->id,
        ]);

        return [
            'name' => fake()->name(),
            'nis' => (string) fake()->unique()->numberBetween(100000, 999999),
            'nisn' => (string) fake()->unique()->numberBetween(1000000000, 9999999999),
            'gender' => fake()->randomElement(['MALE', 'FEMALE']),
            'xclass_id' => $xclass->id,
        ];
    }
}
