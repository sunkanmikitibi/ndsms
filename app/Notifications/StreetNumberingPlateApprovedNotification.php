<?php

namespace App\Notifications;

use App\Models\StreetNumberingPlate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StreetNumberingPlateApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected StreetNumberingPlate $request;

    public function __construct(StreetNumberingPlate $request)
    {
        $this->request = $request;
    }

    public function via(object $notifiable): array
    {
        // In-app notifications use the custom Notification model/table.
        // Keep this notification mail-only to avoid writing to Laravel's database notification channel.
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Street Numbering Plate Request Approved')
            ->greeting("Hello {$notifiable->name},")
            ->line("Great news! Your street numbering plate request has been approved.")
            ->line("**Request Details:**")
            ->line("Reference: {$this->request->reference_number}")
            ->line("Street: {$this->request->street_name}")
            ->line("Town: {$this->request->town}")
            ->line("Quantity: {$this->request->quantity_requested} plates")
            ->line("Estimated Amount: ₦" . number_format($this->request->getTotalCost(), 2))
            ->line("Status: Approved ✓")
            ->action('View Request Details', route('portal.request-numbering-plates', ['ref' => $this->request->reference_number]))
            ->line("Your request will now proceed to production. You will receive updates on the status.")
            ->salutation('Thank you for using NDSMS')
            ->markdown('mail.markdown');
    }

    // Database channel intentionally not used (see via()).
}
