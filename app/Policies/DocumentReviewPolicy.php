<?php

namespace App\Policies;

use App\Models\DocumentReview;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('document_review.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentReview $documentReview): bool
    {
        // Users can view if they are the creator, assigned to, or have view permission
        return $user->can('document_review.view') || 
               $documentReview->caseFile->lawyer_id === $user->id ||
               $documentReview->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('document_review.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentReview $documentReview): bool
    {
        // Only the creator or assigned user can update
        return $documentReview->caseFile->lawyer_id === $user->id || 
               $documentReview->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, DocumentReview $documentReview): bool
    {
        // Only supervisors can approve document reviews
        return $user->hasRole('supervisor') && $user->can('document_review.approve');
    }

    /**
     * Determine whether the user can upload a new version.
     */
    public function uploadVersion(User $user, DocumentReview $documentReview): bool
    {
        // Only the creator or assigned user can upload new versions
        return $documentReview->caseFile->lawyer_id === $user->id || 
               $documentReview->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can download the document.
     */
    public function download(User $user, DocumentReview $documentReview): bool
    {
        // Any user with view permission can download
        return $this->view($user, $documentReview);
    }

    /**
     * Determine whether the user can preview the document.
     */
    public function preview(User $user, DocumentReview $documentReview): bool
    {
        // Any user with view permission can preview
        return $this->view($user, $documentReview);
    }
}






