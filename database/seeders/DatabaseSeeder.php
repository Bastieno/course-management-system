<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@cms.edu',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'department' => 'Administration',
        ]);

        // Create Sample Lecturer
        User::create([
            'name' => 'Dr. John Smith',
            'email' => 'john.smith@cms.edu',
            'password' => bcrypt('lecturer123'),
            'role' => 'lecturer',
            'department' => 'Computer Science',
            'phone' => '+1234567890',
        ]);

        // Create Sample Student
        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@student.cms.edu',
            'password' => bcrypt('student123'),
            'role' => 'student',
            'student_id' => 'CS2024001',
            'department' => 'Computer Science',
            'level' => '200',
            'phone' => '+0987654321',
        ]);
    }
}
