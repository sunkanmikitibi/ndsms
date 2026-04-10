<?php

namespace App\Notifications;

use App\Models\StreetNumberingPlateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StreetNumberingPlateApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected StreetNumberingPlateRequest $request;

    public function __construct(StreetNumberingPlateRequest $request)
    {
        $this->request = $request;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
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

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'street_numbering_plate_approved',
            'title' => 'Street Numbering Plate Request Approved',
            'message' => "Your request {$this->request->reference_number} for {$this->request->street_name} has been approved.",
            'request_id' => $this->request->id,
            'reference' => $this->request->reference_number,
            'url' => route('portal.request-numbering-plates', ['ref' => $this->request->reference_number]),
        ];
    }
}
