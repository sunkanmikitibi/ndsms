<?php

namespace App\Notifications;

use App\Models\StreetNumberingPlateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StreetNumberingPlateRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected StreetNumberingPlateRequest $request;
    protected string $reason;

    public function __construct(StreetNumberingPlateRequest $request, string $reason = '')
    {
        $this->request = $request;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Street Numbering Plate Request Update')
            ->greeting("Hello {$notifiable->name},")
            ->line("We have reviewed your street numbering plate request and unfortunately it has been rejected at this time.")
            ->line("**Request Details:**")
            ->line("Reference: {$this->request->reference_number}")
            ->line("Street: {$this->request->street_name}")
            ->line("Ward: {$this->request->ward}")
            ->line("Status: Rejected");

        if ($this->reason) {
            $mail->line("**Reason for Rejection:**")
                ->line($this->reason);
        }

        return $mail->action('View Request', route('portal.request-numbering-plates', ['ref' => $this->request->reference_number]))
            ->line("If you believe this is an error or have additional information, please contact our support team.")
            ->salutation('Thank you for using NDSMS')
            ->markdown('mail.markdown');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'street_numbering_plate_rejected',
            'title' => 'Street Numbering Plate Request Rejected',
            'message' => "Your request {$this->request->reference_number} for {$this->request->street_name} has been rejected." . ($this->reason ? " Reason: {$this->reason}" : ''),
            'request_id' => $this->request->id,
            'reference' => $this->request->reference_number,
            'reason' => $this->reason,
            'url' => route('portal.request-numbering-plates', ['ref' => $this->request->reference_number]),
        ];
    }
}
