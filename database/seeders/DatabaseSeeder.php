<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // First seed roles and permissions
            RolesAndPermissionsSeeder::class,
            // Then seed users (depends on roles)
            UserSeeder::class,
            // Then seed other data that might depend on users
            BranchSeeder::class,
            // Finally seed case types and related data
            CaseTypeSeeder::class,
        ]);
    }
}






