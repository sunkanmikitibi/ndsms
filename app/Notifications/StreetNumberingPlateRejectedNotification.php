<?php

namespace App\Notifications;

use App\Models\StreetNumberingPlate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StreetNumberingPlateRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected StreetNumberingPlate $request;
    protected string $reason;

    public function __construct(StreetNumberingPlate $request, string $reason = '')
    {
        $this->request = $request;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        // In-app notifications use the custom Notification model/table.
        // Keep this notification mail-only to avoid writing to Laravel's database notification channel.
        return ['mail'];
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
            ->line("Town: {$this->request->town}")
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

    // Database channel intentionally not used (see via()).
}
