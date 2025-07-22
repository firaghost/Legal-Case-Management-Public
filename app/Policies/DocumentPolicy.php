<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
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
    public function view(User $user, Document $document): bool
    {
        // Admin and Supervisor can view all documents
        if ($user->hasRole(['Admin', 'Supervisor'])) {
            return true;
        }
        
        // Lawyers can view documents from their cases
        if ($user->hasRole('Lawyer')) {
            return $document->caseFile->lawyer_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Supervisor', 'Lawyer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        // Admin can update all documents
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Supervisors can update documents in their supervised cases
        if ($user->hasRole('Supervisor')) {
            return $document->caseFile->supervisor_id === $user->id;
        }
        
        // Lawyers can update documents from their cases
        if ($user->hasRole('Lawyer')) {
            return $document->caseFile->lawyer_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        // Admin can delete all documents
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Supervisors can delete documents in their supervised cases
        if ($user->hasRole('Supervisor')) {
            return $document->caseFile->supervisor_id === $user->id;
        }
        
        // Lawyers can delete documents they uploaded
        if ($user->hasRole('Lawyer')) {
            return $document->uploaded_by === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can download the document.
     */
    public function download(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }
}






