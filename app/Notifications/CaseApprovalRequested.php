<?php

namespace App\Notifications;

use App\Models\CaseFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CaseApprovalRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CaseFile $case)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail']; // Enable both database and email notifications
    }

    public function toArray($notifiable): array
    {
        return [
            'case_id' => $this->case->id,
            'file_number' => $this->case->file_number,
            'title' => $this->case->title,
            'case_type' => $this->case->caseType->name ?? '',
            'branch' => $this->case->branch->name ?? '',
            'work_unit' => $this->case->workUnit->name ?? '',
            'claimed_amount' => $this->case->claimed_amount,
            'recovered_amount' => $this->case->recovered_amount,
            'outstanding_amount' => $this->case->outstanding_amount,
            'created_by' => $this->case->creator->name ?? '',
            'created_at' => $this->case->created_at?->toDateTimeString(),
            'approval_type' => 'proceed', // Add approval type for clarity
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Case Requires Approval to Proceed: ' . $this->case->file_number)
            ->greeting('Hello Supervisor,')
            ->line('A new case has been created and requires your approval to proceed.')
            ->line('Case Number: ' . $this->case->file_number)
            ->line('Title: ' . $this->case->title)
            ->line('Type: ' . ($this->case->caseType->name ?? ''))
            ->line('Branch: ' . ($this->case->branch->name ?? ''))
            ->line('Work Unit: ' . ($this->case->workUnit->name ?? ''))
            ->line('Claimed Amount: ' . number_format($this->case->claimed_amount, 2))
            ->line('Recovered Amount: ' . number_format($this->case->recovered_amount, 2))
            ->line('Outstanding Amount: ' . number_format($this->case->outstanding_amount, 2))
            ->line('Created By: ' . ($this->case->creator->name ?? ''))
            ->action('Review Case', url('/cases/approvals?search=' . $this->case->file_number))
            ->line('Thank you for using the Legal Case Management System.');
    }
} 





