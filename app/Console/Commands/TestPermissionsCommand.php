<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestPermissionsCommand extends Command
{
    protected $signature = 'permissions:test';
    protected $description = 'Test Spatie permissions functionality';

    public function handle()
    {
        $this->info('Testing Spatie Permissions...');

        // Create test role if not exists
        $role = Role::firstOrCreate(['name' => 'test-role']);
        $this->info('Test role created/retrieved');

        // Create test permission if not exists
        $permission = Permission::firstOrCreate(['name' => 'test-permission']);
        $this->info('Test permission created/retrieved');

        // Assign permission to role
        $role->givePermissionTo($permission);
        $this->info('Permission assigned to role');

        // Get or create a test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password')
            ]
        );
        $this->info('Test user created/retrieved');

        // Assign role to user
        $user->assignRole($role);
        $this->info('Role assigned to user');

        // Test the permission
        if ($user->hasPermissionTo('test-permission')) {
            $this->info('✅ User has permission via role: test-permission');
        } else {
            $this->error('❌ User does NOT have permission: test-permission');
        }

        // Direct permission check
        if ($user->can('test-permission')) {
            $this->info('✅ User can() check passed for: test-permission');
        } else {
            $this->error('❌ User can() check failed for: test-permission');
        }

        // Test role check
        if ($user->hasRole('test-role')) {
            $this->info('✅ User has role: test-role');
        } else {
            $this->error('❌ User does NOT have role: test-role');
        }

        $this->info('\nPermission test completed!');
    }
}






