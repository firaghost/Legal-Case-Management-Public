<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class HearingReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Upcoming Hearing Reminder')
            ->line('You have an upcoming hearing scheduled.')
            ->line('Case File #: ' . $this->appointment->caseFile->file_number)
            ->line('Hearing Date: ' . $this->appointment->hearing_date->format('Y-m-d'))
            ->action('View Case', url('/cases/' . $this->appointment->case_file_id))
            ->line('Please be prepared.');
    }

    public function toArray($notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'case_file_id' => $this->appointment->case_file_id,
            'file_number' => $this->appointment->caseFile->file_number,
            'hearing_date' => $this->appointment->hearing_date,
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}






