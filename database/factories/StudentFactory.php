<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'Stud_name' => $this->faker->name(),
            'Stud_email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'), // Default password
        ];
    }
}
