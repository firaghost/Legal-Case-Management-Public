<?php

namespace App\Notifications;



use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\CaseFile;

class CaseClosureApproved extends Notification
{
   

    public function __construct(public CaseFile $case)
    {
        //
    }

    public function via(object $notifiable): array
    {
        // Only store in database; mail can be added later
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'case_id'    => $this->case->id,
            'file_number'=> $this->case->file_number,
            'approved_at'=> $this->case->approved_at?->toDateTimeString(),
        ];
    }

    /**
     * Optional email notification
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Case Closure Approved')
            ->line("Your case {$this->case->file_number} has been approved for closure.")
            ->action('View Case', url(route('lawyer.cases.show', $this->case->id)));
    }
}






