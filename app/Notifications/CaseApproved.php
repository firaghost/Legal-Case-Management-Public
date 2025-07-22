<?php

namespace App\Notifications;

use App\Models\CaseFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CaseApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected CaseFile $case;
    protected $supervisor;

    public function __construct(CaseFile $case, $supervisor)
    {
        $this->case = $case;
        $this->supervisor = $supervisor;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
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
            'approved_at' => $this->case->approved_at instanceof \Carbon\Carbon
                ? $this->case->approved_at->toDateTimeString()
                : $this->case->approved_at,
            'message' => 'Case approved by ' . ($this->supervisor->name ?? 'supervisor'),
            'approved_by' => $this->supervisor->name ?? '',
        'approval_type' => 'initial',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Case Approved: ' . $this->case->file_number)
            ->line('Approved By: ' . ($this->supervisor->name ?? ''))
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line('Your case has been approved by a supervisor and is now open for action.')
            ->line('Case Number: ' . $this->case->file_number)
            ->line('Title: ' . $this->case->title)
            ->line('Type: ' . ($this->case->caseType->name ?? ''))
            ->line('Branch: ' . ($this->case->branch->name ?? ''))
            ->line('Work Unit: ' . ($this->case->workUnit->name ?? ''))
            ->line('Claimed Amount: ' . number_format($this->case->claimed_amount, 2))
            ->line('Recovered Amount: ' . number_format($this->case->recovered_amount, 2))
            ->line('Outstanding Amount: ' . number_format($this->case->outstanding_amount, 2))
            ->line('Approved At: ' . ($this->case->approved_at ? \Carbon\Carbon::parse($this->case->approved_at)->toDateTimeString() : ''))
            ->action('View Case', url('/lawyer/cases/' . $this->case->id))
            ->line('Thank you for using the Legal Case Management System.');
    }
} 





