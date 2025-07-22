<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * Mark all unread notifications as read.
     */
    public function markAllRead(): RedirectResponse
    {
        $user = auth()->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return back();
    }

    /**
     * View a notification: mark it read and redirect to linked case.
     */
    public function open(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $caseId = $notification->data['case_id'] ?? null;
        if ($caseId) {
            $user = auth()->user();
            $primaryRole = strtolower($user->roles->first()?->name ?? ($user->role ?? ''));
            
            // Check if this is a case reassignment notification for an old lawyer
            if (isset($notification->data['type']) && $notification->data['type'] === 'case_reassigned') {
                $isNewLawyer = $user->id === ($notification->data['new_lawyer_id'] ?? null);
                
                // If the user is the old lawyer, just mark as read and return
                if (!$isNewLawyer) {
                    return back();
                }
            }
            
            if($primaryRole === 'supervisor'){
                // Supervisors should land on read-only view (lawyer.show already hides edit button based on ability)
                return redirect()->route('supervisor.cases.show', $caseId);
            }
            
            // Try to access the case and handle authorization gracefully
            try {
                $case = \App\Models\CaseFile::findOrFail($caseId);
                
                // Check if user can view this case
                if ($primaryRole === 'lawyer' && $case->lawyer_id !== $user->id) {
                    return back()->with('info', 'You no longer have access to this case as it may have been reassigned.');
                }
                
                return redirect()->route('lawyer.cases.show', $caseId);
            } catch (\Exception $e) {
                return back()->with('error', 'Case not found or access denied.');
            }
        }
        return back();
    }
}






