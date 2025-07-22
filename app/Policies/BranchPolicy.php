<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BranchPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'Supervisor', 'Lawyer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Branch $branch): bool
    {
        // Admin can view all branches
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Supervisors and Lawyers can view their own branch
        if ($user->hasRole(['Supervisor', 'Lawyer'])) {
            return $user->branch_id === $branch->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Branch $branch): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Branch $branch): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can manage users in this branch.
     */
    public function manageUsers(User $user, Branch $branch): bool
    {
        // Admin can manage users in all branches
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Supervisors can manage users in their own branch
        if ($user->hasRole('Supervisor')) {
            return $user->branch_id === $branch->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can view cases in this branch.
     */
    public function viewCases(User $user, Branch $branch): bool
    {
        // Admin can view cases in all branches
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Supervisors and Lawyers can view cases in their own branch
        if ($user->hasRole(['Supervisor', 'Lawyer'])) {
            return $user->branch_id === $branch->id;
        }
        
        return false;
    }
}






