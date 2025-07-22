<?php

namespace App\Notifications;

use App\Models\LitigationCase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class EarlyClosureApprovalNeeded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LitigationCase $litigation)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Early Case Closure Approval Needed')
            ->line('A case reached full recovery and requires your approval for early closure.')
            ->line('Case File #: ' . $this->litigation->caseFile->file_number)
            ->action('View Case', url('/cases/' . $this->litigation->case_file_id))
            ->line('Thank you for using LCMS.');
    }

    public function toArray($notifiable): array
    {
        return [
            'case_file_id' => $this->litigation->case_file_id,
            'file_number' => $this->litigation->caseFile->file_number,
            'recovered_amount' => $this->litigation->recovered_amount,
            'outstanding_amount' => $this->litigation->outstanding_amount,
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}






