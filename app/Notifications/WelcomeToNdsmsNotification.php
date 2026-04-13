<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeToNdsmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        return (new MailMessage)
            ->subject('Welcome to NDSMS - Civic Sovereignty')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Welcome to the National Digital Street Management System (NDSMS). We are thrilled to have you onboard.')
            ->line('Your account has been successfully created. You can now use the citizen portal to register addresses, submit street validation requests, apply for numbering plates, and track the status of your applications.')
            ->action('Access Your Portal', route('portal.dashboard'))
            ->line('If you have any questions or require assistance, do not hesitate to contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'info',
            'title' => 'Welcome to NDSMS',
            'message' => 'Your account has been set up successfully. Welcome to the portal.',
            'icon' => 'fas fa-hand-wave',
            'action_url' => route('portal.dashboard'),
            'action_label' => 'View Dashboard'
        ];
    }
}
