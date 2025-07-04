<?php

namespace Database\Seeders;

use App\Models\Dept;
use App\Models\Student;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LargeDatasetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder is optimized for inserting very large datasets efficiently.
     */
    public function run(): void
    {
        $this->command->info('Starting large dataset seeder...');

        // --- 1. Initial Setup for Performance ---
        DB::connection()->disableQueryLog(); // Saves memory
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Speeds up inserts

        $faker = Faker::create();

        // --- 2. Seed Students (A smaller, manageable number) ---
        $this->command->info('Seeding 1,000 students...');
        $studentsToInsert = [];
        $password = Hash::make('password');
        for ($i = 0; $i < 1000; $i++) {
            $studentsToInsert[] = [
                'Stud_name' => $faker->name(),
                'Stud_email' => $faker->unique()->safeEmail(),
                'password' => $password,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('students')->insert($studentsToInsert);
        $this->command->info('Students seeded.');

        DB::beginTransaction();

        try {
            $studentIds = Student::pluck('id')->all();
            $departmentIds = Dept::pluck('id')->all();

            if (empty($studentIds) || empty($departmentIds)) {
                $missing = empty($studentIds) ? 'students' : 'departments';
                throw new \Exception("FATAL ERROR: No {$missing} found. Please seed them before running this seeder.");
            }

            $this->command->info('Pre-generating 500 unique text samples for performance...');
            $sampleTitles = [];
            $sampleDescriptions = [];
            for ($i = 0; $i < 500; $i++) {
                $sampleTitles[] = $faker->sentence(6);
                $sampleDescriptions[] = $faker->paragraph(3);
            }

            $totalComplaints = 1000000;
            $chunkSize = 8000;
            $statuses = ['pending', 'solved', 'checking', 'rejected', 'withdrawn'];
            $now = now();

            $this->command->info("Seeding {$totalComplaints} complaints in chunks of {$chunkSize}...");
            $progressBar = $this->command->getOutput()->createProgressBar($totalComplaints);
            
            $complaintsToInsert = [];
            for ($i = 1; $i <= $totalComplaints; $i++) {
                $complaintsToInsert[] = [
                    'title' => $sampleTitles[array_rand($sampleTitles)],
                    'description' => $sampleDescriptions[array_rand($sampleDescriptions)],
                    'Student_id' => $studentIds[array_rand($studentIds)],
                    'Dept_id' => $departmentIds[array_rand($departmentIds)],
                    'status' => $statuses[array_rand($statuses)],
                    'attachment_path' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // When the chunk is full, insert it and reset the array
                if (count($complaintsToInsert) === $chunkSize) {
                    DB::table('complaints')->insert($complaintsToInsert);
                    $complaintsToInsert = []; // Reset the chunk
                    $progressBar->advance($chunkSize);
                }
            }

            // Insert any remaining records that didn't make a full chunk
            if (!empty($complaintsToInsert)) {
                DB::table('complaints')->insert($complaintsToInsert);
                $progressBar->advance(count($complaintsToInsert));
            }

            $progressBar->finish();
            $this->command->info("\nAll complaints inserted successfully.");

            // --- 7. Commit the Transaction ---
            DB::commit();
            $this->command->info('Database transaction committed.');

        } catch (\Exception $e) {
            // --- 8. Handle Errors ---
            DB::rollBack(); // Undo all changes if something went wrong
            $this->command->error("\nAn error occurred: " . $e->getMessage());
            $this->command->error('Transaction has been rolled back. No data was inserted for complaints.');
        }

        // --- 9. Final Cleanup ---
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Seeding process complete.');
    }
}