<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        // Start building the query
        $query = Role::withCount('users')
            ->with('permissions')
            ->orderBy('name');
        
        // Apply search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Apply permission filter
        if ($request->has('permission')) {
            $permissionName = $request->input('permission');
            $query->whereHas('permissions', function($q) use ($permissionName) {
                $q->where('name', $permissionName);
            });
        }
        
        $roles = $query->paginate(10)->withQueryString();
        
        // Get all permissions for the filter dropdown
        $allPermissions = Permission::orderBy('name')->get();
        
        // Get user counts
        $userModel = config('auth.providers.users.model');
        $totalUsers = $userModel::count();
        $userCount = $userModel::role(Role::all())->count();
        
        return view('admin.roles.index', [
            'roles' => $roles,
            'allPermissions' => $allPermissions,
            'totalUsers' => $totalUsers,
            'userCount' => $userCount,
        ]);
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users' => function($query) {
            $query->orderBy('name')->limit(10);
        }]);
        
        $allPermissions = Permission::orderBy('name')->get();
        
        return view('admin.roles.show', [
            'role' => $role,
            'allPermissions' => $allPermissions,
            'usersCount' => $role->users()->count(),
        ]);
    }
    
    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Convert permission IDs to integers
        $permissionIds = isset($validated['permissions']) ? 
            array_map('intval', $validated['permissions']) : [];

        // Get permission models to ensure they exist and use web guard
        $permissions = Permission::whereIn('id', $permissionIds)
            ->where('guard_name', 'web')
            ->get();

        $role = Role::firstOrCreate(
            ['name' => $validated['name']],
            [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'guard_name' => 'web' // Explicitly set guard name
            ]
        );

        // Sync permissions using the actual permission models
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')
            ->with('status', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        $role->load('permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Convert permission IDs to integers
        $permissionIds = isset($validated['permissions']) ? 
            array_map('intval', $validated['permissions']) : [];

        // Get permission models to ensure they exist and use web guard
        $permissions = Permission::whereIn('id', $permissionIds)
            ->where('guard_name', 'web')
            ->get();

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => 'web' // Ensure guard name is set
        ]);

        // Sync permissions using the actual permission models
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')
            ->with('status', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('status', 'Role deleted successfully.');
    }
} 





