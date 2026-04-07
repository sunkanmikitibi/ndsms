<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\InAppNotificationService;

class PaymentObserver
{
    private InAppNotificationService $notificationService;

    public function __construct(InAppNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        // Check if payment status changed to completed/successful
        if ($payment->isDirty('status') && in_array($payment->status, ['completed', 'successful', 'paid'])) {
            if ($payment->user_id) {
                $amount = $payment->amount ? "₦" . number_format($payment->amount, 2) : 'payment';
                $this->notificationService->notifyPayment(
                    $payment->user_id,
                    'Payment Confirmed',
                    "Your {$amount} payment has been received and confirmed.",
                    $payment->receipt_url ?? route('portal.payments.show', $payment),
                    ['payment_id' => $payment->id, 'action' => 'completed', 'amount' => $payment->amount]
                );
            }
        }

        // Check if payment status changed to failed
        if ($payment->isDirty('status') && in_array($payment->status, ['failed', 'declined'])) {
            if ($payment->user_id) {
                $this->notificationService->notifyError(
                    $payment->user_id,
                    'Payment Failed',
                    "Your payment could not be processed. Please try again or contact support.",
                    route('portal.payments.retry', $payment),
                    ['payment_id' => $payment->id, 'action' => 'failed']
                );
            }
        }

        // Check if payment status changed to pending
        if ($payment->isDirty('status') && $payment->status === 'pending') {
            if ($payment->user_id) {
                $this->notificationService->notifyInfo(
                    $payment->user_id,
                    'Payment Pending',
                    "Your payment is being processed. This may take a few moments.",
                    null,
                    ['payment_id' => $payment->id, 'action' => 'pending']
                );
            }
        }
    }

    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        // Notify user of new payment initiation
        if ($payment->user_id) {
            $amount = $payment->amount ? "₦" . number_format($payment->amount, 2) : 'your';
            $this->notificationService->notifyInfo(
                $payment->user_id,
                'Payment Initiated',
                "A payment of {$amount} has been initiated. Follow the payment link to complete it.",
                $payment->payment_url ?? route('portal.payments.show', $payment),
                ['payment_id' => $payment->id, 'action' => 'initiated']
            );
        }
    }
}
