<?php

namespace App\Mail;

use App\Models\StreetNumberingPlate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StreetNumberingPlateRequest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public StreetNumberingPlate $numberingPlateRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Street Numbering Plate Request Submitted - Reference: ' . $this->numberingPlateRequest->reference_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.street-numbering-plate-request',
            with: [
                'numberingPlateRequest' => $this->numberingPlateRequest,
                'totalCost' => $this->numberingPlateRequest->getTotalCost(),
                'statusLabel' => $this->numberingPlateRequest->getStatusLabel(),
                'plateTypeLabel' => $this->numberingPlateRequest->getPlateTypeLabel(),
                'materialLabel' => $this->numberingPlateRequest->getMaterialLabel(),
            ],
        );
    }
}
