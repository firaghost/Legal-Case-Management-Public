<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $roles = [
            [
                'name' => 'admin',
                'description' => 'System administrator with full access to all features and settings',
                'is_default' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'supervisor',
                'description' => 'Can manage cases, assign to lawyers, and generate reports',
                'is_default' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'lawyer',
                'description' => 'Can manage assigned cases and update case status',
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}






