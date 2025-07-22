<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'name' => 'Head Office',
                'code' => 'HO',
                'address' => '123 Main Street, City Center',
                'phone' => '+1234567890',
                'email' => 'ho@example.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'West Branch',
                'code' => 'WEST',
                'address' => '456 West Avenue, West District',
                'phone' => '+1234567891',
                'email' => 'west@example.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'East Branch',
                'code' => 'EAST',
                'address' => '789 East Road, East District',
                'phone' => '+1234567892',
                'email' => 'east@example.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Idempotent insert: if code already exists update values, else insert
        \App\Models\Branch::upsert(
            $rows,
            ['code'], // Unique by code
            ['name', 'address', 'phone', 'email', 'is_active', 'updated_at']
        );
    }
}





