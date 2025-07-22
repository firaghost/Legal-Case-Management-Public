<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $permissions = [
            // User Management
            ['name' => 'users.view', 'description' => 'View users', 'group' => 'users'],
            ['name' => 'users.create', 'description' => 'Create users', 'group' => 'users'],
            ['name' => 'users.edit', 'description' => 'Edit users', 'group' => 'users'],
            ['name' => 'users.delete', 'description' => 'Delete users', 'group' => 'users'],
            
            // Role Management
            ['name' => 'roles.view', 'description' => 'View roles', 'group' => 'roles'],
            ['name' => 'roles.create', 'description' => 'Create roles', 'group' => 'roles'],
            ['name' => 'roles.edit', 'description' => 'Edit roles', 'group' => 'roles'],
            ['name' => 'roles.delete', 'description' => 'Delete roles', 'group' => 'roles'],
            
            // Case Files
            ['name' => 'case_files.view', 'description' => 'View all case files', 'group' => 'case_files'],
            ['name' => 'case_files.create', 'description' => 'Create case files', 'group' => 'case_files'],
            ['name' => 'case_files.edit', 'description' => 'Edit case files', 'group' => 'case_files'],
            ['name' => 'case_files.delete', 'description' => 'Delete case files', 'group' => 'case_files'],
            ['name' => 'case_files.assign', 'description' => 'Assign cases to lawyers', 'group' => 'case_files'],
            ['name' => 'case_files.close', 'description' => 'Close/reopen cases', 'group' => 'case_files'],
            
            // Litigation Cases
            ['name' => 'litigation_cases.view', 'description' => 'View litigation cases', 'group' => 'litigation'],
            ['name' => 'litigation_cases.create', 'description' => 'Create litigation cases', 'group' => 'litigation'],
            ['name' => 'litigation_cases.edit', 'description' => 'Edit litigation cases', 'group' => 'litigation'],
            ['name' => 'litigation_cases.delete', 'description' => 'Delete litigation cases', 'group' => 'litigation'],
            
            // Loan Recovery
            ['name' => 'loan_recovery.view', 'description' => 'View loan recovery cases', 'group' => 'loan_recovery'],
            ['name' => 'loan_recovery.create', 'description' => 'Create loan recovery cases', 'group' => 'loan_recovery'],
            ['name' => 'loan_recovery.edit', 'description' => 'Edit loan recovery cases', 'group' => 'loan_recovery'],
            ['name' => 'loan_recovery.delete', 'description' => 'Delete loan recovery cases', 'group' => 'loan_recovery'],
            
            // Criminal Cases
            ['name' => 'criminal_cases.view', 'description' => 'View criminal cases', 'group' => 'criminal_cases'],
            ['name' => 'criminal_cases.create', 'description' => 'Create criminal cases', 'group' => 'criminal_cases'],
            ['name' => 'criminal_cases.edit', 'description' => 'Edit criminal cases', 'group' => 'criminal_cases'],
            ['name' => 'criminal_cases.delete', 'description' => 'Delete criminal cases', 'group' => 'criminal_cases'],
            
            // Legal Advisory
            ['name' => 'legal_advisory.view', 'description' => 'View legal advisory cases', 'group' => 'legal_advisory'],
            ['name' => 'legal_advisory.create', 'description' => 'Create legal advisory cases', 'group' => 'legal_advisory'],
            ['name' => 'legal_advisory.edit', 'description' => 'Edit legal advisory cases', 'group' => 'legal_advisory'],
            ['name' => 'legal_advisory.delete', 'description' => 'Delete legal advisory cases', 'group' => 'legal_advisory'],
            
            // Documents
            ['name' => 'documents.upload', 'description' => 'Upload documents', 'group' => 'documents'],
            ['name' => 'documents.download', 'description' => 'Download documents', 'group' => 'documents'],
            ['name' => 'documents.delete', 'description' => 'Delete documents', 'group' => 'documents'],
            
            // Reports
            ['name' => 'reports.view', 'description' => 'View reports', 'group' => 'reports'],
            ['name' => 'reports.generate', 'description' => 'Generate reports', 'group' => 'reports'],
            ['name' => 'reports.export', 'description' => 'Export reports', 'group' => 'reports'],
            
            // Settings
            ['name' => 'settings.view', 'description' => 'View system settings', 'group' => 'settings'],
            ['name' => 'settings.edit', 'description' => 'Edit system settings', 'group' => 'settings'],
            
            // Audit Logs
            ['name' => 'audit_logs.view', 'description' => 'View audit logs', 'group' => 'audit_logs'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::updateOrCreate(
                ['name' => $permission['name']],
                array_merge($permission, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
        
        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }
    
    /**
     * Assign permissions to roles.
     */
    protected function assignPermissionsToRoles(): void
    {
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $supervisorRole = \App\Models\Role::where('name', 'supervisor')->first();
        $lawyerRole = \App\Models\Role::where('name', 'lawyer')->first();
        
        if ($adminRole) {
            // Admin gets all permissions
            $permissions = \App\Models\Permission::pluck('id')->toArray();
            $adminRole->permissions()->sync($permissions);
        }
        
        if ($supervisorRole) {
            // Supervisor permissions
            $supervisorPermissions = [
                'case_files.view', 'case_files.create', 'case_files.edit', 'case_files.assign', 'case_files.close',
                'litigation_cases.view', 'litigation_cases.create', 'litigation_cases.edit',
                'loan_recovery.view', 'loan_recovery.create', 'loan_recovery.edit',
                'criminal_cases.view', 'criminal_cases.create', 'criminal_cases.edit',
                'legal_advisory.view', 'legal_advisory.create', 'legal_advisory.edit',
                'documents.upload', 'documents.download', 'documents.delete',
                'reports.view', 'reports.generate', 'reports.export',
            ];
            
            $permissionIds = \App\Models\Permission::whereIn('name', $supervisorPermissions)
                ->pluck('id')
                ->toArray();
                
            $supervisorRole->permissions()->sync($permissionIds);
        }
        
        if ($lawyerRole) {
            // Lawyer permissions
            $lawyerPermissions = [
                'case_files.view', 'case_files.create',
                'litigation_cases.view', 'litigation_cases.edit',
                'loan_recovery.view', 'loan_recovery.edit',
                'criminal_cases.view', 'criminal_cases.edit',
                'legal_advisory.view', 'legal_advisory.edit',
                'documents.upload', 'documents.download',
            ];
            
            $permissionIds = \App\Models\Permission::whereIn('name', $lawyerPermissions)
                ->pluck('id')
                ->toArray();
                
            $lawyerRole->permissions()->sync($permissionIds);
        }
    }
}






