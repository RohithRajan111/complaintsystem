<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\Dept;
use App\Models\Student; // Import the Dept model
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition(): array
    {
        // This factory will now require a list of student and department IDs to be passed in.
        // We will do this from the Seeder for maximum performance.
        return [
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(3),
            // 'Student_id' and 'Dept_id' will be provided by the Seeder.
            'status' => $this->faker->randomElement(['pending', 'solved', 'checking', 'rejected', 'withdrawn']),
            // We will leave attachment_path as null for dummy data to save space.
        ];
    }
}
