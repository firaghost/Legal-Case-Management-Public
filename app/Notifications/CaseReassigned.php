<?php

namespace App\Notifications;

use App\Models\CaseFile;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CaseReassigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $case;
    protected $oldLawyer;
    protected $newLawyer;
    protected $supervisor;

    /**
     * Create a new notification instance.
     */
    public function __construct(CaseFile $case, ?User $oldLawyer, User $newLawyer, User $supervisor)
    {
        $this->case = $case;
        $this->oldLawyer = $oldLawyer;
        $this->newLawyer = $newLawyer;
        $this->supervisor = $supervisor;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $isNewLawyer = $notifiable->id === $this->newLawyer->id;
        
        if ($isNewLawyer) {
            return (new MailMessage)
                ->subject('New Case Assignment - ' . $this->case->file_number)
                ->greeting('Hello ' . $notifiable->name . ',')
                ->line('You have been assigned a new case by ' . $this->supervisor->name . '.')
                ->line('**Case Details:**')
                ->line('Case Number: ' . $this->case->file_number)
                ->line('Title: ' . ($this->case->title ?: 'N/A'))
                ->line('Type: ' . ($this->case->caseType->name ?? 'N/A'))
                ->line('Status: ' . $this->case->status)
                ->when($this->oldLawyer, function ($message) {
                    return $message->line('Previously assigned to: ' . $this->oldLawyer->name);
                })
                ->action('View Case', url('/lawyer/cases/' . $this->case->id))
                ->line('Please review the case details and begin working on it accordingly.')
                ->line('Thank you for your attention to this matter.');
        } else {
            return (new MailMessage)
                ->subject('Case Reassigned - ' . $this->case->file_number)
                ->greeting('Hello ' . $notifiable->name . ',')
                ->line('Your case ' . $this->case->file_number . ' has been reassigned to ' . $this->newLawyer->name . ' by ' . $this->supervisor->name . '.')
                ->line('**Case Details:**')
                ->line('Case Number: ' . $this->case->file_number)
                ->line('Title: ' . ($this->case->title ?: 'N/A'))
                ->line('Type: ' . ($this->case->caseType->name ?? 'N/A'))
                ->line('New assignee: ' . $this->newLawyer->name)
                ->line('You no longer have access to this case as it has been transferred to the new lawyer.')
                ->line('Please ensure all relevant case materials and updates are transferred appropriately.')
                ->line('Thank you for your previous work on this case.');
        }
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $isNewLawyer = $notifiable->id === $this->newLawyer->id;
        
        return [
            'type' => 'case_reassigned',
            'case_id' => $this->case->id,
            'case_file_number' => $this->case->file_number,
            'case_title' => $this->case->title,
            'old_lawyer_id' => $this->oldLawyer?->id,
            'old_lawyer_name' => $this->oldLawyer?->name,
            'new_lawyer_id' => $this->newLawyer->id,
            'new_lawyer_name' => $this->newLawyer->name,
            'supervisor_id' => $this->supervisor->id,
            'supervisor_name' => $this->supervisor->name,
            'reassigned_at' => now()->toISOString(),
            'message' => $isNewLawyer 
                ? 'You have been assigned case ' . $this->case->file_number . ' by ' . $this->supervisor->name
                : 'Your case ' . $this->case->file_number . ' has been reassigned to ' . $this->newLawyer->name . ' by ' . $this->supervisor->name,
            'action_url' => $isNewLawyer 
                ? url('/lawyer/cases/' . $this->case->id)
                : null,
        ];
    }
}






