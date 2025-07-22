<?php

namespace App\Observers;

use App\Models\LitigationCase;
use App\Notifications\EarlyClosureApprovalNeeded;
use Illuminate\Support\Facades\Notification;

class LitigationCaseObserver
{
    /**
     * Handle the LitigationCase "updated" event.
     */
    public function updated(LitigationCase $litigation): void
    {
        // Early closure logic: if recovered >= outstanding and not already early_closed
        if (!$litigation->early_closed && $litigation->recovered_amount >= $litigation->outstanding_amount && $litigation->outstanding_amount > 0) {
            $litigation->early_closed = true;
            $litigation->saveQuietly();

            // Notify supervisors for approval
            $supervisors = \App\Models\User::role('supervisor')->get();
            Notification::send($supervisors, new EarlyClosureApprovalNeeded($litigation));
        }
    }
}






