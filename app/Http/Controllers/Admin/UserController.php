<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(): View
    {
        $query = User::withCount('assignedCases')
            ->orderBy('name');

        if (request('role')) {
            $query->whereHas('roles', function($q) {
                $q->where('name', request('role'));
            });
        }

        // Filter by active/inactive status if requested
        if (request()->has('status')) {
            $status = request('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'supervisor', 'lawyer','default'])],
        ]);

        // Use a transaction to ensure data consistency
        return DB::transaction(function () use ($validated, $user) {
            // Update user details
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'] // Update the role column
            ]);

            // Remove all current roles and assign the new one
            $user->roles()->detach();
            
            // Find the role by name and attach it directly to avoid any Spatie package issues
            $role = \Spatie\Permission\Models\Role::where('name', $validated['role'])->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            // Clear the permission cache to reflect the role changes immediately
            $user->clearPermissionCache();

            return redirect()
                ->route('admin.users')
                ->with('status', 'User updated successfully');
        });
    }

    /**
     * Disable a user account.
     */
    public function disable(User $user): RedirectResponse
    {
        $user->disable();
        return back()->with('status', 'User has been disabled.');
    }

    /**
     * Enable a user account.
     */
    public function enable(User $user): RedirectResponse
    {
        $user->enable();
        return back()->with('status', 'User has been enabled.');
    }

    /**
     * Permanently delete a user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return back()->with('status', 'User has been permanently deleted.');
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'supervisor', 'lawyer', 'default'])],
        ]);

        // Start a database transaction
        return DB::transaction(function () use ($validated) {
            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'password_change_required' => true, // Force password change on first login
                'role' => $validated['role'], // Set the role column
                'is_active' => true, // New users are active by default
            ]);

            // Find the role by name and attach it directly
            $role = \Spatie\Permission\Models\Role::where('name', $validated['role'])->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            return redirect()
                ->route('admin.users')
                ->with('status', 'User created successfully');
        });
    }
}






