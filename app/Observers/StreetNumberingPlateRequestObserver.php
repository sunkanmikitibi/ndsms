<?php

namespace App\Observers;

use App\Models\StreetNumberingPlate;
use App\Services\SmsNotificationService;

class StreetNumberingPlateRequestObserver
{
    protected SmsNotificationService $smsService;

    public function __construct(SmsNotificationService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function updated(StreetNumberingPlate $request): void
    {
        // Send approval notification
        if ($request->isDirty('status') && $request->status === 'approved') {
            $this->smsService->sendApprovalNotification(
                $request->user->phone,
                'Street Numbering Plate request',
                $request->reference_number
            );
        }

        // Send rejection notification
        if ($request->isDirty('status') && $request->status === 'rejected') {
            $this->smsService->sendRejectionNotification(
                $request->user->phone,
                'Street Numbering Plate request',
                $request->reference_number,
                $request->rejection_reason
            );
        }

        // Send ready notification
        if ($request->isDirty('status') && $request->status === 'ready') {
            $this->smsService->sendDeliveryNotification(
                $request->user->phone,
                'Street Numbering Plates',
                $request->reference_number
            );
        }
    }
}
