<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Only create users in local/testing environment
        if (!app()->environment('production')) {
            $admin = User::firstOrCreate([
                'email' => 'admin@lcms.test'], [
                'name' => 'Admin User',
                'password' => Hash::make(env('DEFAULT_ADMIN_PASSWORD', Str::random(16))),
                'email_verified_at' => now(),
                'password_change_required' => true,
            ]);

            $supervisor = User::firstOrCreate([
                'email' => 'supervisor@lcms.test'], [
                'name' => 'Supervisor User',
                'password' => Hash::make(env('DEFAULT_SUPERVISOR_PASSWORD', Str::random(16))),
                'email_verified_at' => now(),
                'password_change_required' => true,
            ]);

            $lawyer = User::firstOrCreate([
                'email' => 'lawyer@lcms.test'], [
                'name' => 'Lawyer User',
                'password' => Hash::make(env('DEFAULT_LAWYER_PASSWORD', Str::random(16))),
                'email_verified_at' => now(),
                'password_change_required' => true,
            ]);
        } else {
            // In production, don't create default users
            return;
        }

        $adminId = $admin->id;
        $supervisorId = $supervisor->id;
        $lawyerId = $lawyer->id;

        // Map roles - using lowercase to match RoleSeeder
        $roles = DB::table('roles')->pluck('id', 'name');

        DB::table('role_user')->insertOrIgnore([
            ['role_id' => $roles['admin'] ?? 1, 'user_id' => $adminId],
            ['role_id' => $roles['supervisor'] ?? 2, 'user_id' => $supervisorId],
            ['role_id' => $roles['lawyer'] ?? 3, 'user_id' => $lawyerId],
        ]);
    }
}






