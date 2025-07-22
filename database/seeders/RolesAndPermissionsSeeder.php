<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Guards to use for roles and permissions
        $guards = ['web'];

        // Base permissions definition (will be created for each guard)
        $basePermissions = [
            // Document Review permissions
            [
                'name' => 'document_review.view',
                'group' => 'document_review',
                'description' => 'View document reviews',
                'guard_name' => 'web'
            ],
            [
                'name' => 'document_review.create',
                'group' => 'document_review',
                'description' => 'Create document reviews',
                'guard_name' => 'web'
            ],
            [
                'name' => 'document_review.update',
                'group' => 'document_review',
                'description' => 'Update document reviews',
                'guard_name' => 'web'
            ],
            [
                'name' => 'document_review.approve',
                'group' => 'document_review',
                'description' => 'Approve/reject document reviews',
                'guard_name' => 'web'
            ],
            [
                'name' => 'document_review.upload_version',
                'group' => 'document_review',
                'description' => 'Upload new versions of documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'document_review.download',
                'group' => 'document_review',
                'description' => 'Download document review files',
                'guard_name' => 'web'
            ],
            [
                'name' => 'document_review.preview',
                'group' => 'document_review',
                'description' => 'Preview document review files',
                'guard_name' => 'web'
            ],
            
            // Case File permissions
            [
                'name' => 'case_files.view_any',
                'group' => 'case_files',
                'description' => 'View all case files',
                'guard_name' => 'web'
            ],
            [
                'name' => 'case_files.view',
                'group' => 'case_files',
                'description' => 'View case file details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'case_files.create',
                'group' => 'case_files',
                'description' => 'Create new case files',
                'guard_name' => 'web'
            ],
            [
                'name' => 'case_files.update',
                'group' => 'case_files',
                'description' => 'Update case files',
                'guard_name' => 'web'
            ],

            // Litigation permissions
            [
                'name' => 'litigation.view_any',
                'group' => 'litigation',
                'description' => 'View all litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'litigation.view',
                'group' => 'litigation',
                'description' => 'View litigation details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'litigation.create',
                'group' => 'litigation',
                'description' => 'Create new litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'litigation.update',
                'group' => 'litigation',
                'description' => 'Update litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'litigation.manage_documents',
                'group' => 'litigation',
                'description' => 'Manage litigation documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'litigation.manage_hearings',
                'group' => 'litigation',
                'description' => 'Manage litigation hearings',
                'guard_name' => 'web'
            ],
            
            // Labor Litigation permissions
            [
                'name' => 'labor_litigation.view_any',
                'group' => 'labor_litigation',
                'description' => 'View all labor litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'labor_litigation.view',
                'group' => 'labor_litigation',
                'description' => 'View labor litigation details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'labor_litigation.create',
                'group' => 'labor_litigation',
                'description' => 'Create new labor litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'labor_litigation.update',
                'group' => 'labor_litigation',
                'description' => 'Update labor litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'labor_litigation.manage_documents',
                'group' => 'labor_litigation',
                'description' => 'Manage labor litigation documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'labor_litigation.manage_hearings',
                'group' => 'labor_litigation',
                'description' => 'Manage labor litigation hearings',
                'guard_name' => 'web'
            ],
            
            // Other Civil Litigation permissions
            [
                'name' => 'other_civil_litigation.view_any',
                'group' => 'other_civil_litigation',
                'description' => 'View all other civil litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'other_civil_litigation.view',
                'group' => 'other_civil_litigation',
                'description' => 'View other civil litigation details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'other_civil_litigation.create',
                'group' => 'other_civil_litigation',
                'description' => 'Create other civil litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'other_civil_litigation.update',
                'group' => 'other_civil_litigation',
                'description' => 'Update other civil litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'other_civil_litigation.manage_documents',
                'group' => 'other_civil_litigation',
                'description' => 'Manage other civil litigation documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'other_civil_litigation.manage_hearings',
                'group' => 'other_civil_litigation',
                'description' => 'Manage other civil litigation hearings',
                'guard_name' => 'web'
            ],
            
            // Criminal Litigation permissions
            [
                'name' => 'criminal_litigation.view_any',
                'group' => 'criminal_litigation',
                'description' => 'View all criminal litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'criminal_litigation.view',
                'group' => 'criminal_litigation',
                'description' => 'View criminal litigation details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'criminal_litigation.create',
                'group' => 'criminal_litigation',
                'description' => 'Create criminal litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'criminal_litigation.update',
                'group' => 'criminal_litigation',
                'description' => 'Update criminal litigations',
                'guard_name' => 'web'
            ],
            [
                'name' => 'criminal_litigation.manage_hearings',
                'group' => 'criminal_litigation',
                'description' => 'Manage criminal litigation hearings',
                'guard_name' => 'web'
            ],
            [
                'name' => 'criminal_litigation.manage_charges',
                'group' => 'criminal_litigation',
                'description' => 'Manage charges for criminal litigation',
                'guard_name' => 'web'
            ],
            [
                'name' => 'criminal_litigation.manage_bail',
                'group' => 'criminal_litigation',
                'description' => 'Manage bail for criminal litigation',
                'guard_name' => 'web'
            ],
            
            // Document Review permissions
            [
                'name' => 'document_review.create',
                'group' => 'document_review',
                'description' => 'Create document review requests',
                'guard_name' => 'web'
            ],
            [
                'name' => 'document_review.view',
                'group' => 'document_review',
                'description' => 'View document review requests',
                'guard_name' => 'web'
            ],
            [
                'name' => 'document_review.approve',
                'group' => 'document_review',
                'description' => 'Approve/reject document reviews',
                'guard_name' => 'web'
            ],
            [
                'name' => 'document_review.upload_version',
                'group' => 'document_review',
                'description' => 'Upload new document versions',
                'guard_name' => 'web'
            ],
            
            // User permissions
            [
                'name' => 'users.view_any',
                'group' => 'users',
                'description' => 'View all users',
                'guard_name' => 'web'
            ],
            [
                'name' => 'users.view',
                'group' => 'users',
                'description' => 'View user details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'users.create',
                'group' => 'users',
                'description' => 'Create new users',
                'guard_name' => 'web'
            ],
            [
                'name' => 'users.update',
                'group' => 'users',
                'description' => 'Update users',
                'guard_name' => 'web'
            ],
            [
                'name' => 'users.delete',
                'group' => 'users',
                'description' => 'Delete users',
                'guard_name' => 'web'
            ],
            [
                'name' => 'users.restore',
                'group' => 'users',
                'description' => 'Restore deleted users',
                'guard_name' => 'web'
            ],
            [
                'name' => 'users.force_delete',
                'group' => 'users',
                'description' => 'Permanently delete users',
                'guard_name' => 'web'
            ],
            
            // Role permissions
            [
                'name' => 'roles.view_any',
                'group' => 'roles',
                'description' => 'View all roles',
                'guard_name' => 'web'
            ],
            [
                'name' => 'roles.view',
                'group' => 'roles',
                'description' => 'View role details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'roles.create',
                'group' => 'roles',
                'description' => 'Create new roles',
                'guard_name' => 'web'
            ],
            [
                'name' => 'roles.update',
                'group' => 'roles',
                'description' => 'Update roles',
                'guard_name' => 'web'
            ],
            [
                'name' => 'roles.delete',
                'group' => 'roles',
                'description' => 'Delete roles',
                'guard_name' => 'web'
            ],
            [
                'name' => 'roles.assign',
                'group' => 'roles',
                'description' => 'Assign roles to users',
                'guard_name' => 'web'
            ],
            
            // Case File permissions
            [
                'name' => 'case_files.view_any',
                'group' => 'case_files',
                'description' => 'View all case files',
                'guard_name' => 'web'
            ],
            [
                'name' => 'case_files.view',
                'group' => 'case_files',
                'description' => 'View case file details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'case_files.create',
                'group' => 'case_files',
                'description' => 'Create new case files',
                'guard_name' => 'web'
            ],
            [
                'name' => 'case_files.update',
                'group' => 'case_files',
                'description' => 'Update case files',
                'guard_name' => 'web'
            ],
            [
                'name' => 'case_files.delete',
                'group' => 'case_files',
                'description' => 'Delete case files',
                'guard_name' => 'web'
            ],
            [
                'name' => 'case_files.restore',
                'group' => 'case_files',
                'description' => 'Restore deleted case files',
                'guard_name' => 'web'
            ],
            [
                'name' => 'case_files.force_delete',
                'group' => 'case_files',
                'description' => 'Permanently delete case files',
                'guard_name' => 'web'
            ],
            [
                'name' => 'case_files.view_logs',
                'group' => 'case_files',
                'description' => 'View case file audit logs',
                'guard_name' => 'web'
            ],
            
            // Secured Loan Recovery permissions
            [
                'name' => 'secured_loan_recovery.view_any',
                'group' => 'secured_loan_recovery',
                'description' => 'View all secured loan recoveries',
                'guard_name' => 'web'
            ],
            [
                'name' => 'secured_loan_recovery.view',
                'group' => 'secured_loan_recovery',
                'description' => 'View secured loan recovery details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'secured_loan_recovery.create',
                'group' => 'secured_loan_recovery',
                'description' => 'Create new secured loan recoveries',
                'guard_name' => 'web'
            ],
            [
                'name' => 'secured_loan_recovery.update',
                'group' => 'secured_loan_recovery',
                'description' => 'Update secured loan recoveries',
                'guard_name' => 'web'
            ],
            [
                'name' => 'secured_loan_recovery.delete',
                'group' => 'secured_loan_recovery',
                'description' => 'Delete secured loan recoveries',
                'guard_name' => 'web'
            ],
            [
                'name' => 'secured_loan_recovery.manage_documents',
                'group' => 'secured_loan_recovery',
                'description' => 'Manage secured loan recovery documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'secured_loan_recovery.manage_repayments',
                'group' => 'secured_loan_recovery',
                'description' => 'Manage loan repayments',
                'guard_name' => 'web'
            ],
            
            // Legal Advisory permissions
            [
                'name' => 'legal_advisory.view_any',
                'group' => 'legal_advisory',
                'description' => 'View all legal advisories',
                'guard_name' => 'web'
            ],
            [
                'name' => 'legal_advisory.view',
                'group' => 'legal_advisory',
                'description' => 'View legal advisory details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'legal_advisory.create',
                'group' => 'legal_advisory',
                'description' => 'Create new legal advisories',
                'guard_name' => 'web'
            ],
            [
                'name' => 'legal_advisory.update',
                'group' => 'legal_advisory',
                'description' => 'Update legal advisories',
                'guard_name' => 'web'
            ],
            [
                'name' => 'legal_advisory.delete',
                'group' => 'legal_advisory',
                'description' => 'Delete legal advisories',
                'guard_name' => 'web'
            ],
            [
                'name' => 'legal_advisory.manage_documents',
                'group' => 'legal_advisory',
                'description' => 'Manage legal advisory documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'legal_advisory.manage_stakeholders',
                'group' => 'legal_advisory',
                'description' => 'Manage legal advisory stakeholders',
                'guard_name' => 'web'
            ],
            [
                'name' => 'legal_advisory.submit_review',
                'group' => 'legal_advisory',
                'description' => 'Submit legal advisory for review',
                'guard_name' => 'web'
            ],
            [
                'name' => 'legal_advisory.approve',
                'group' => 'legal_advisory',
                'description' => 'Approve legal advisories',
                'guard_name' => 'web'
            ],
            
            // Document permissions
            [
                'name' => 'documents.view_any',
                'group' => 'documents',
                'description' => 'View all documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'documents.view',
                'group' => 'documents',
                'description' => 'View document details',
                'guard_name' => 'web'
            ],
            [
                'name' => 'documents.upload',
                'group' => 'documents',
                'description' => 'Upload new documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'documents.update',
                'group' => 'documents',
                'description' => 'Update documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'documents.delete',
                'group' => 'documents',
                'description' => 'Delete documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'documents.download',
                'group' => 'documents',
                'description' => 'Download documents',
                'guard_name' => 'web'
            ],
            [
                'name' => 'documents.share',
                'group' => 'documents',
                'description' => 'Share documents',
                'guard_name' => 'web'
            ],
            
            // Report permissions
            [
                'name' => 'reports.view_any',
                'group' => 'reports',
                'description' => 'View all reports',
                'guard_name' => 'web'
            ],
            [
                'name' => 'reports.generate',
                'group' => 'reports',
                'description' => 'Generate reports',
                'guard_name' => 'web'
            ],
            [
                'name' => 'reports.export',
                'group' => 'reports',
                'description' => 'Export reports',
                'guard_name' => 'web'
            ],
            
            // Search permissions
            [
                'name' => 'search.basic',
                'group' => 'search',
                'description' => 'Perform basic search',
                'guard_name' => 'web'
            ],
            [
                'name' => 'search.advanced',
                'group' => 'search',
                'description' => 'Perform advanced search',
                'guard_name' => 'web'
            ],
        ];

        // Create permissions for each guard
        foreach ($guards as $guard) {
            foreach ($basePermissions as $perm) {
                Permission::firstOrCreate([
                    'name' => $perm['name'],
                    'guard_name' => $guard,
                ], [
                    'group' => $perm['group'] ?? null,
                    'description' => $perm['description'] ?? null,
                ]);
            }
        }

        // Create roles for each guard
        foreach ($guards as $guard) {
            // Admin
            Role::firstOrCreate([
                'name' => 'admin',
                'guard_name' => $guard
            ], [
                'description' => 'System administrator with full access.',
                'is_default' => false
            ]);
            // Supervisor
            Role::firstOrCreate([
                'name' => 'supervisor',
                'guard_name' => $guard
            ], [
                'description' => 'Supervisor role with oversight capabilities.',
                'is_default' => false
            ]);
            // Lawyer
            Role::firstOrCreate([
                'name' => 'lawyer',
                'guard_name' => $guard
            ], [
                'description' => 'Lawyer role with access to assigned cases and documents.',
                'is_default' => true
            ]);
        }

        // Sync permissions to admin role per guard to avoid guard mismatch errors
        foreach ($guards as $guard) {
            $adminRole = Role::where('name', 'admin')->where('guard_name', $guard)->first();
            if ($adminRole) {
                // sync by permission names scoped to the correct guard
                $adminRole->syncPermissions(Permission::where('guard_name', $guard)->pluck('name')->toArray());
            }
        }
        
        // Create Supervisor role
        $supervisorRole = Role::firstOrCreate([
            'name' => 'supervisor',
            'guard_name' => 'web'
        ], [
            'description' => 'Supervisor role with access to manage cases and users.',
            'is_default' => false
        ]);
        
        // Define supervisor permissions
        $supervisorPermissions = [
            // Case Files
            'case_files.view_any', 'case_files.view', 'case_files.create', 'case_files.update',
            // Litigation
            'litigation.view_any', 'litigation.view', 'litigation.create', 'litigation.update',
            'litigation.manage_documents', 'litigation.manage_hearings',
            // Labor Litigation
            'labor_litigation.view_any', 'labor_litigation.view', 'labor_litigation.create', 
            'labor_litigation.update', 'labor_litigation.manage_documents', 'labor_litigation.manage_hearings',
            // Other Civil Litigation
            'other_civil_litigation.view_any', 'other_civil_litigation.view', 'other_civil_litigation.create', 
            'other_civil_litigation.update', 'other_civil_litigation.manage_documents', 'other_civil_litigation.manage_hearings',
            // Criminal Litigation
            'criminal_litigation.view_any', 'criminal_litigation.view', 'criminal_litigation.create', 
            'criminal_litigation.update', 'criminal_litigation.manage_charges', 'criminal_litigation.manage_hearings',
            'criminal_litigation.manage_bail',
            // Secured Loan Recovery
            'secured_loan_recovery.view_any', 'secured_loan_recovery.view', 'secured_loan_recovery.create', 
            'secured_loan_recovery.update', 'secured_loan_recovery.manage_documents', 'secured_loan_recovery.manage_repayments',
            // Legal Advisory
            'legal_advisory.view_any', 'legal_advisory.view', 'legal_advisory.create', 'legal_advisory.update',
            'legal_advisory.manage_documents', 'legal_advisory.manage_stakeholders', 'legal_advisory.submit_review',
            // Document Review
            'document_review.view', 'document_review.approve',
            // Documents
            'documents.view_any', 'documents.view', 'documents.upload', 'documents.update', 'documents.download',
            // Reports
            'reports.view_any', 'reports.generate', 'reports.export',
            // Search
            'search.basic', 'search.advanced'
        ];
        
        // Sync supervisor permissions for each guard
        foreach ($guards as $guard) {
            $role = Role::where('name', 'supervisor')->where('guard_name', $guard)->first();
            if ($role) {
                $role->syncPermissions($supervisorPermissions);
            }
        }
        
        // Create Lawyer role
        $lawyerRole = Role::firstOrCreate([
            'name' => 'lawyer',
            'guard_name' => 'web'
        ], [
            'description' => 'Lawyer role with access to assigned cases and documents.',
            'is_default' => true
        ]);
        
        // Define lawyer permissions
        $lawyerPermissions = [
            // Case Files
            'case_files.view_any', 'case_files.view', 'case_files.update',
            // Litigation
            'litigation.view_any', 'litigation.view', 'litigation.update',
            'litigation.manage_documents', 'litigation.manage_hearings',
            // Documents
            'documents.view', 'documents.upload', 'documents.update', 'documents.download',
            // Document Review
            'document_review.view', 'document_review.create', 'document_review.upload_version',
            // Search
            'search.basic'
        ];
        
        // Sync lawyer permissions for each guard
        foreach ($guards as $guard) {
            $role = Role::where('name', 'lawyer')->where('guard_name', $guard)->first();
            if ($role) {
                $role->syncPermissions($lawyerPermissions);
            }
        }
        
        // Create default admin user if not exists
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@lcms.test'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
        
        // Assign admin role to admin user
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }
        
        // Create sample supervisor user if not exists
        $supervisorUser = User::firstOrCreate(
            ['email' => 'supervisor@lcms.test'],
            [
                'name' => 'Supervisor User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
        
        // Assign supervisor role if not already assigned
        if (!$supervisorUser->hasRole('supervisor')) {
            $supervisorUser->assignRole('supervisor');
        }
        
        // Create sample lawyer user if not exists
        $lawyerUser = User::firstOrCreate(
            ['email' => 'lawyer@lcms.test'],
            [
                'name' => 'Lawyer User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );
        
        // Assign lawyer role if not already assigned
        if (!$lawyerUser->hasRole('lawyer')) {
            $lawyerUser->assignRole('lawyer');
        }
    }
}






