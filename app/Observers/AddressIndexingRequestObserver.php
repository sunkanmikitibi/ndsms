<?php

namespace App\Observers;

use App\Models\AddressIndexingRequest;
use App\Services\SmsNotificationService;

class AddressIndexingRequestObserver
{
    protected SmsNotificationService $smsService;

    public function __construct(SmsNotificationService $smsService)
    {
        $this->smsService = $smsService;
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
        }

        // Send rejection notification
        if ($request->isDirty('status') && $request->status === 'rejected') {
            $this->smsService->sendRejectionNotification(
                $request->applicant_phone,
                'Address Indexing request',
                $request->address_line,
                $request->admin_note
            );
        }

        // Send verification notification
        if ($request->isDirty('status') && $request->status === 'verified') {
            $this->smsService->send(
                $request->applicant_phone,
                "Your address {$request->address_line} has been verified and indexed. Thank you!",
                'verification'
            );
        }
    }
}
