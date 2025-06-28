<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Computer Science',
                'code' => 'CSC',
                'description' => 'Department of Computer Science offering undergraduate and graduate programs in computing, software engineering, and information technology.',
                'head_of_department' => 'Dr. John Smith',
                'building' => 'Science Complex A',
                'phone' => '+234-123-456-7890',
                'email' => 'csc@university.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MTH',
                'description' => 'Department of Mathematics providing comprehensive education in pure and applied mathematics.',
                'head_of_department' => 'Prof. Sarah Johnson',
                'building' => 'Mathematics Building',
                'phone' => '+234-123-456-7891',
                'email' => 'math@university.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Physics',
                'code' => 'PHY',
                'description' => 'Department of Physics exploring the fundamental principles of matter and energy.',
                'head_of_department' => 'Dr. Michael Brown',
                'building' => 'Physics Laboratory',
                'phone' => '+234-123-456-7892',
                'email' => 'physics@university.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Chemistry',
                'code' => 'CHM',
                'description' => 'Department of Chemistry studying the composition, structure, and properties of matter.',
                'head_of_department' => 'Prof. Emily Davis',
                'building' => 'Chemistry Laboratory',
                'phone' => '+234-123-456-7893',
                'email' => 'chemistry@university.edu',
                'is_active' => true,
            ],
            [
                'name' => 'Biology',
                'code' => 'BIO',
                'description' => 'Department of Biology focusing on the study of living organisms and life processes.',
                'head_of_department' => 'Dr. Robert Wilson',
                'building' => 'Life Sciences Building',
                'phone' => '+234-123-456-7894',
                'email' => 'biology@university.edu',
                'is_active' => true,
            ],
            [
                'name' => 'English',
                'code' => 'ENG',
                'description' => 'Department of English Language and Literature promoting language skills and literary appreciation.',
                'head_of_department' => 'Prof. Lisa Anderson',
                'building' => 'Humanities Building',
                'phone' => '+234-123-456-7895',
                'email' => 'english@university.edu',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
