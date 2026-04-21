<?php

namespace App\Observers;

use App\Models\StreetNumberingPlate;
use App\Services\InAppNotificationService;
use App\Services\SmsNotificationService;

class StreetNumberingPlateRequestObserver
{
    protected SmsNotificationService $smsService;
    protected InAppNotificationService $inAppService;

    public function __construct(SmsNotificationService $smsService, InAppNotificationService $inAppService)
    {
        $this->smsService = $smsService;
        $this->inAppService = $inAppService;
    }

    public function updated(StreetNumberingPlate $request): void
    {
        if (!$request->isDirty('status')) {
            return;
        }

        $actionUrl = route('portal.request-numbering-plates', ['ref' => $request->reference_number]);

        // Send approval notification
        if ($request->status === 'approved') {
            $this->smsService->sendApprovalNotification(
                $request->user->phone,
                'Street Numbering Plate request',
                $request->reference_number
            );

            if ($request->user_id) {
                $this->inAppService->notifyApproval(
                    $request->user_id,
                    'Plate Request Approved',
                    "Your plate request ({$request->reference_number}) for {$request->street_name} has been approved.",
                    $actionUrl,
                    ['plate_request_id' => $request->id, 'status' => 'approved', 'reference' => $request->reference_number]
                );
            }
        }

        // Send rejection notification
        if ($request->status === 'rejected') {
            $this->smsService->sendRejectionNotification(
                $request->user->phone,
                'Street Numbering Plate request',
                $request->reference_number,
                $request->rejection_reason
            );

            if ($request->user_id) {
                $reason = $request->rejection_reason ?: null;
                $this->inAppService->notifyRejection(
                    $request->user_id,
                    'Plate Request Rejected',
                    "Your plate request ({$request->reference_number}) for {$request->street_name} was rejected." . ($reason ? " Reason: {$reason}" : ''),
                    $actionUrl,
                    ['plate_request_id' => $request->id, 'status' => 'rejected', 'reference' => $request->reference_number, 'reason' => $reason]
                );
            }
        }

        // Awaiting payment
        if ($request->status === 'awaiting_payment') {
            if ($request->user_id) {
                $this->inAppService->notifyPayment(
                    $request->user_id,
                    'Payment Required',
                    "Your plate request ({$request->reference_number}) is awaiting payment to proceed.",
                    route('portal.payments.index'),
                    ['plate_request_id' => $request->id, 'status' => 'awaiting_payment', 'reference' => $request->reference_number]
                );
            }
        }

        // Send ready notification
        if ($request->status === 'ready') {
            $this->smsService->sendDeliveryNotification(
                $request->user->phone,
                'Street Numbering Plates',
                $request->reference_number
            );

            if ($request->user_id) {
                $this->inAppService->notifyDelivery(
                    $request->user_id,
                    'Plates Ready',
                    "Your plates for {$request->street_name} are ready for delivery/collection (Ref: {$request->reference_number}).",
                    $actionUrl,
                    ['plate_request_id' => $request->id, 'status' => 'ready', 'reference' => $request->reference_number]
                );
            }
        }
    }
}
