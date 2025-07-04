<?php

namespace Database\Seeders;

use App\Models\Dept;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dept::create([
            'Dept_name' => 'Technical Department',
            'Dept_email' => 'TD@gmail.com',
            'Hod_name' => 'Rahees',
            'password' => Hash::make('password'),
        ]);
        Dept::create([
            'Dept_name' => 'HR Department',
            'Dept_email' => 'HR@gmail.com',
            'Hod_name' => 'Nejla',
            'password' => Hash::make('password'),
        ]);
        Dept::create([
            'Dept_name' => 'Managerial Department',
            'Dept_email' => 'MR@gmail.com',
            'Hod_name' => 'Ahmed',
            'password' => Hash::make('password'),
        ]);
        Dept::create([
            'Dept_name' => 'Maintanance Department',
            'Dept_email' => 'MD@gmail.com',
            'Hod_name' => 'Hari',
            'password' => Hash::make('password'),
        ]);
    }
}
