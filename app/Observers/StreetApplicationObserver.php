<?php

namespace App\Observers;

use App\Models\StreetApplication;
use App\Services\SmsNotificationService;
use App\Services\EmailNotificationService;
use App\Services\InAppNotificationService;

class StreetApplicationObserver
{
    protected SmsNotificationService $smsService;
    protected EmailNotificationService $emailService;
    protected InAppNotificationService $inAppService;

    public function __construct(
        SmsNotificationService $smsService,
        EmailNotificationService $emailService,
        InAppNotificationService $inAppService
    ) {
        $this->smsService = $smsService;
        $this->emailService = $emailService;
        $this->inAppService = $inAppService;
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

            $this->emailService->sendStreetApplicationApprovalEmail($application);

            if ($application->user_id) {
                $this->inAppService->notifyApproval(
                    $application->user_id,
                    'Street Registration Approved',
                    "Your Street Registration application for {$application->street_name} has been approved."
                );
            }
        }

        // Send rejection notification
        if ($application->isDirty('status') && $application->status === 'rejected') {
            $this->smsService->sendRejectionNotification(
                $application->user->phone,
                'Street registration',
                $application->street_name
            );

            $this->emailService->sendStreetApplicationRejectionEmail($application, $application->admin_note ?? '');

            if ($application->user_id) {
                $this->inAppService->notifyRejection(
                    $application->user_id,
                    'Street Registration Rejected',
                    "Your Street Registration application for {$application->street_name} was rejected." . ($application->admin_note ? " Reason: {$application->admin_note}" : "")
                );
            }
        }
    }
}
