<?php

namespace App\Observers;

use App\Models\StreetNumberingPlate;
use App\Services\EmailNotificationService;

class StreetNumberingPlateRequestEmailObserver
{
    protected EmailNotificationService $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function updated(StreetNumberingPlate $request): void
    {
        // Send approval email
        if ($request->isDirty('status') && $request->status === 'approved') {
            $this->emailService->sendStreetNumberingPlateApprovalEmail($request);
        }

        // Send rejection email
        if ($request->isDirty('status') && $request->status === 'rejected') {
            $this->emailService->sendStreetNumberingPlateRejectionEmail(
                $request,
                $request->rejection_reason
            );
        }

        // Send ready email
        if ($request->isDirty('status') && $request->status === 'ready') {
            $this->emailService->sendStreetNumberingPlateReadyEmail($request);
        }
    }
}
