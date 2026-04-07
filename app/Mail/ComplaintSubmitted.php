<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public string $userEmail,
        public string $complaintType,
        public string $subject,
        public string $message,
        public string $submittedAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Complaint Received: ' . $this->subject,
            from: config('mail.from.address'),
            to: config('app.support_email', config('mail.from.address')),
            replyTo: $this->userEmail,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.complaint-submitted',
            with: [
                'userName' => $this->userName,
                'userEmail' => $this->userEmail,
                'complaintType' => $this->complaintType,
                'subject' => $this->subject,
                'message' => $this->message,
                'submittedAt' => $this->submittedAt,
            ],
        );
    }
}
