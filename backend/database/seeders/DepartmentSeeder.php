<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Information and Communication Technology',
                'code' => 'ICT',
                'description' => 'Systems, networks, software support and digital services.',
            ],
            [
                'name' => 'Human Resources and Administration',
                'code' => 'HRA',
                'description' => 'Staff welfare, recruitment, training and office administration.',
            ],
            [
                'name' => 'Finance and Accounts',
                'code' => 'FIN',
                'description' => 'Budgeting, accounting, payments and financial reporting.',
            ],
            [
                'name' => 'Legal Services',
                'code' => 'LEG',
                'description' => 'Legal advice, contracts and regulatory compliance.',
            ],
            [
                'name' => 'Internal Audit',
                'code' => 'AUD',
                'description' => 'Risk assessment, internal controls and audit reviews.',
            ],
            [
                'name' => 'Public Relations and Marketing',
                'code' => 'PRM',
                'description' => 'Communication, public awareness and customer engagement.',
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                $department + ['is_active' => true]
            );
        }
    }
}