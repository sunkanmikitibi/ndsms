<?php

namespace App\Observers;

use App\Models\Street;
use App\Services\InAppNotificationService;

class StreetObserver
{
    private InAppNotificationService $notificationService;

    public function __construct(InAppNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Street "created" event.
     */
    public function created(Street $street): void
    {
        // Notify user that their street was created successfully
        if ($street->user_id) {
            $this->notificationService->notifySuccess(
                $street->user_id,
                'Street Added Successfully',
                "Your street '{$street->name}' has been added to the system.",
                route('portal.streets.show', $street),
                ['street_id' => $street->id, 'action' => 'created']
            );
        }
    }

    /**
     * Handle the Street "updated" event.
     */
    public function updated(Street $street): void
    {
        // Check if status changed to approved
        if ($street->isDirty('status') && $street->status === 'approved') {
            if ($street->user_id) {
                $this->notificationService->notifyApproval(
                    $street->user_id,
                    'Street Approved',
                    "Your street '{$street->name}' has been approved by the admin.",
                    route('portal.streets.show', $street),
                    ['street_id' => $street->id, 'action' => 'approved']
                );
            }
        }

        // Check if status changed to rejected
        if ($street->isDirty('status') && $street->status === 'rejected') {
            if ($street->user_id) {
                $reason = $street->rejection_reason ?? 'No reason provided';
                $this->notificationService->notifyRejection(
                    $street->user_id,
                    'Street Rejected',
                    "Your street '{$street->name}' was rejected. Reason: {$reason}",
                    route('portal.streets.edit', $street),
                    ['street_id' => $street->id, 'action' => 'rejected', 'reason' => $reason]
                );
            }
        }

        // Check if status changed to on_hold
        if ($street->isDirty('status') && $street->status === 'on_hold') {
            if ($street->user_id) {
                $this->notificationService->notifyWarning(
                    $street->user_id,
                    'Street Placed on Hold',
                    "Your street '{$street->name}' has been placed on hold pending review.",
                    route('portal.streets.show', $street),
                    ['street_id' => $street->id, 'action' => 'on_hold']
                );
            }
        }
    }

    /**
     * Handle the Street "deleted" event.
     */
    public function deleted(Street $street): void
    {
        // Notify user that their street was deleted
        if ($street->user_id) {
            $this->notificationService->notifyInfo(
                $street->user_id,
                'Street Removed',
                "Your street '{$street->name}' has been removed from the system.",
                null,
                ['street_id' => $street->id, 'action' => 'deleted']
            );
        }
    }
}
