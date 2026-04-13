<?php

namespace App\Observers;

use App\Models\AddressIndexingRequest;
use App\Services\SmsNotificationService;
use App\Services\EmailNotificationService;
use App\Services\NotificationService as InAppNotificationService;

class AddressIndexingRequestObserver
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

    public function updated(AddressIndexingRequest $request): void
    {
        // Send approval notification
        if ($request->isDirty('status') && $request->status === 'approved') {
            $this->smsService->sendApprovalNotification(
                $request->applicant_phone,
                'Address Indexing request',
                $request->address_line
            );

            $this->emailService->sendAddressIndexingApprovalEmail($request);

            if ($request->user_id) {
                $this->inAppService->notifyApproval(
                    $request->user_id,
                    'Address Indexing Approved',
                    "Your Address Indexing request for {$request->address_line} has been approved."
                );
            }
        }

        // Send rejection notification
        if ($request->isDirty('status') && $request->status === 'rejected') {
            $this->smsService->sendRejectionNotification(
                $request->applicant_phone,
                'Address Indexing request',
                $request->address_line,
                $request->admin_note
            );

            $this->emailService->sendAddressIndexingRejectionEmail($request, $request->admin_note ?? '');

            if ($request->user_id) {
                $this->inAppService->notifyRejection(
                    $request->user_id,
                    'Address Indexing Rejected',
                    "Your Address Indexing request for {$request->address_line} was rejected." . ($request->admin_note ? " Reason: {$request->admin_note}" : "")
                );
            }
        }

        // Send verification notification
        if ($request->isDirty('status') && $request->status === 'verified') {
            $this->smsService->send(
                $request->applicant_phone,
                "Your address {$request->address_line} has been verified and indexed. Thank you!",
                'verification'
            );

            if ($request->user_id) {
                $this->inAppService->notifySuccess(
                    $request->user_id,
                    'Address Verified',
                    "Your address {$request->address_line} has been verified and fully indexed."
                );
            }
        }
    }
}
