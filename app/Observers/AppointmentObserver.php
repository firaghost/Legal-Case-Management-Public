<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Notifications\HearingReminder;
use Illuminate\Support\Carbon;

class AppointmentObserver
{
    public function saved(Appointment $appointment): void
    {
        // Notify the assigned lawyer if the hearing is within 3 days
        if ($appointment->hearing_date && $appointment->caseFile && $appointment->caseFile->lawyer) {
            $days = Carbon::now()->diffInDays($appointment->hearing_date, false);
            if ($days >= 0 && $days <= 3) {
                $appointment->caseFile->lawyer->notify(new HearingReminder($appointment));
            }
        }
    }
}






