<?php

namespace App\Policies;

use App\Models\LegalAdvisoryCase;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LegalAdvisoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('legal_advisory.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LegalAdvisoryCase $legalAdvisoryCase): bool
    {
        // Users can view their own advisories
        if ($legalAdvisoryCase->assigned_lawyer_id === $user->id) {
            return true;
        }
        
        // Check if user is a stakeholder in this advisory
        if ($legalAdvisoryCase->stakeholders()->where('user_id', $user->id)->exists()) {
            return true;
        }
        
        // Supervisors and admins can view all advisories
        if ($user->isSupervisor() || $user->isAdmin()) {
            return true;
        }
        
        return $user->can('legal_advisory.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('legal_advisory.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LegalAdvisoryCase $legalAdvisoryCase): bool
    {
        // Assigned lawyer can update the advisory
        if ($legalAdvisoryCase->assigned_lawyer_id === $user->id) {
            return $user->can('legal_advisory.update');
        }
        
        // Supervisors and admins can update any advisory
        if ($user->isSupervisor() || $user->isAdmin()) {
            return $user->can('legal_advisory.update');
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LegalAdvisoryCase $legalAdvisoryCase): bool
    {
        // Only admins can delete advisories
        return $user->isAdmin() && $user->can('legal_advisory.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LegalAdvisoryCase $legalAdvisoryCase): bool
    {
        // Only admins can restore advisories
        return $user->isAdmin() && $user->can('legal_advisory.restore');
    }
    
    /**
     * Determine whether the user can upload documents to the advisory.
     */
    public function uploadDocument(User $user, LegalAdvisoryCase $legalAdvisoryCase): bool
    {
        // Assigned lawyer and admins can upload documents
        if ($legalAdvisoryCase->assigned_lawyer_id === $user->id || $user->isAdmin()) {
            return $user->can('legal_advisory.upload_document');
        }
        
        return false;
    }
    
    /**
     * Determine whether the user can submit a review for the advisory.
     */
    public function submitReview(User $user, LegalAdvisoryCase $legalAdvisoryCase): bool
    {
        // Only assigned lawyer can submit reviews
        if ($legalAdvisoryCase->assigned_lawyer_id === $user->id) {
            return $user->can('legal_advisory.review');
        }
        
        return false;
    }
    
    /**
     * Determine whether the user can approve the advisory.
     */
    public function approve(User $user, LegalAdvisoryCase $legalAdvisoryCase): bool
    {
        // Only supervisors and admins can approve
        if ($user->isSupervisor() || $user->isAdmin()) {
            return $user->can('legal_advisory.approve');
        }
        
        return false;
    }
    
    /**
     * Determine whether the user can manage stakeholders for the advisory.
     */
    public function manageStakeholders(User $user, LegalAdvisoryCase $legalAdvisoryCase): bool
    {
        // Assigned lawyer and admins can manage stakeholders
        if ($legalAdvisoryCase->assigned_lawyer_id === $user->id || $user->isAdmin()) {
            return $user->can('legal_advisory.manage_stakeholders');
        }
        
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LegalAdvisoryCase $legalAdvisoryCase): bool
    {
        return false;
    }
}






