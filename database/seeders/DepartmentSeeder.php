<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'IT Department'
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'HR Department'
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Finance Department'
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'Operations Department'
            ]
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
} 