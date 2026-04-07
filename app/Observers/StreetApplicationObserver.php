<?php

namespace App\Observers;

use App\Models\StreetApplication;
use App\Services\SmsNotificationService;

class StreetApplicationObserver
{
    protected SmsNotificationService $smsService;

    public function __construct(SmsNotificationService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function updated(StreetApplication $application): void
    {
        // Send approval notification
        if ($application->isDirty('status') && $application->status === 'approved') {
            $this->smsService->sendApprovalNotification(
                $application->user->phone,
                'Street registration',
                $application->street_name
            );
        }

        // Send rejection notification
        if ($application->isDirty('status') && $application->status === 'rejected') {
            $this->smsService->sendRejectionNotification(
                $application->user->phone,
                'Street registration',
                $application->street_name
            );
        }
    }
}
