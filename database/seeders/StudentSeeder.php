<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Student::create([
        //     'Stud_name' => 'John Doe',
        //     'Stud_email' => 'john@student.edu',
        //     'password' => Hash::make('password'),
        // ]);
        // Student::factory()->count(1)->create();
    }
}
