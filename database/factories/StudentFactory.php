<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [

            'name' => fake()->name(),

            'nis' => (string) fake()
                ->unique()
                ->numberBetween(100000, 999999),

            'nisn' => (string) fake()
                ->unique()
                ->numberBetween(1000000000, 9999999999),

            'gender' => fake()->randomElement([
                'MALE',
                'FEMALE'
            ]),

            'parent_name' => fake()->name(),

            'parent_phone' => fake()
                ->numerify('6285737074723'),

        ];
    }
}