<?php

namespace App\Notifications;

use App\Models\StreetNumberingPlateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StreetNumberingPlateReadyNotification extends Notification implements ShouldQueue
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
            ->subject('Your Street Numbering Plates Are Ready!')
            ->greeting("Hello {$notifiable->name},")
            ->line("Exciting news! Your street numbering plates are now ready for collection or delivery.")
            ->line("**Order Details:**")
            ->line("Reference: {$this->request->reference_number}")
            ->line("Street: {$this->request->street_name}")
            ->line("Quantity: {$this->request->quantity_requested} plates")
            ->line("Status: Ready for Delivery ✓")
            ->action('View Delivery Details', route('portal.request-numbering-plates', ['ref' => $this->request->reference_number]))
            ->line("Please arrange for collection at your earliest convenience. If delivery is preferred, contact our support team to schedule.")
            ->line("Thank you for choosing NDSMS!")
            ->markdown('mail.markdown');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'street_numbering_plate_ready',
            'title' => 'Your Plates Are Ready!',
            'message' => "Your {$this->request->quantity_requested} street numbering plates for {$this->request->street_name} are ready for collection.",
            'request_id' => $this->request->id,
            'reference' => $this->request->reference_number,
            'url' => route('portal.request-numbering-plates', ['ref' => $this->request->reference_number]),
        ];
    }
}
