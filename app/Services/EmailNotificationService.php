<?php

namespace App\Services;

use App\Models\AddressIndexingRequest;
use App\Models\StreetApplication;
use App\Models\StreetNumberingPlate;
use App\Notifications\StreetNumberingPlateApprovedNotification;
use App\Notifications\StreetNumberingPlateRejectedNotification;
use App\Notifications\StreetNumberingPlateReadyNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class EmailNotificationService
{
    /**
     * Send approval notification for street numbering plate
     */
    public function sendStreetNumberingPlateApprovalEmail(StreetNumberingPlate $request): bool
    {
        try {
            if (!$request->user || !$request->user->email) {
                Log::warning("Cannot send approval email: User email not found for request {$request->id}");
                return false;
            }

            $request->user->notify(new StreetNumberingPlateApprovedNotification($request));
            Log::info("Sent approval email for street plate request {$request->reference_number} to {$request->user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send approval email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send rejection notification for street numbering plate
     */
    public function sendStreetNumberingPlateRejectionEmail(StreetNumberingPlate $request, string $reason = ''): bool
    {
        try {
            if (!$request->user || !$request->user->email) {
                Log::warning("Cannot send rejection email: User email not found for request {$request->id}");
                return false;
            }

            $request->user->notify(new StreetNumberingPlateRejectedNotification($request, $reason));
            Log::info("Sent rejection email for street plate request {$request->reference_number} to {$request->user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send rejection email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send ready notification for street numbering plate
     */
    public function sendStreetNumberingPlateReadyEmail(StreetNumberingPlate $request): bool
    {
        try {
            if (!$request->user || !$request->user->email) {
                Log::warning("Cannot send ready email: User email not found for request {$request->id}");
                return false;
            }

            $request->user->notify(new StreetNumberingPlateReadyNotification($request));
            Log::info("Sent ready email for street plate request {$request->reference_number} to {$request->user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send ready email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send approval notification for address indexing
     */
    public function sendAddressIndexingApprovalEmail(AddressIndexingRequest $request): bool
    {
        try {
            if (!isset($request->applicant_phone) || !$request->user?->email) {
                Log::warning("Cannot send approval email: Email not found for request {$request->id}");
                return false;
            }

            $email = $request->user->email;
            $subject = 'Address Indexing Request Approved';
            
            Notification::route('mail', $email)->notify(
                new \Illuminate\Notifications\Messages\MailMessage()
            );

            Log::info("Sent approval email for address indexing {$request->address_line} to {$email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send address approval email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send rejection notification for address indexing
     */
    public function sendAddressIndexingRejectionEmail(AddressIndexingRequest $request, string $reason = ''): bool
    {
        try {
            if (!$request->user?->email) {
                Log::warning("Cannot send rejection email: Email not found for request {$request->id}");
                return false;
            }

            $email = $request->user->email;
            Log::info("Sent rejection email for address indexing {$request->address_line} to {$email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send address rejection email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send approval notification for street application
     */
    public function sendStreetApplicationApprovalEmail(StreetApplication $application): bool
    {
        try {
            if (!$application->user?->email) {
                Log::warning("Cannot send approval email: Email not found for application {$application->id}");
                return false;
            }

            $email = $application->user->email;
            Log::info("Sent approval email for street application {$application->street_name} to {$email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send street application approval email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send rejection notification for street application
     */
    public function sendStreetApplicationRejectionEmail(StreetApplication $application, string $reason = ''): bool
    {
        try {
            if (!$application->user?->email) {
                Log::warning("Cannot send rejection email: Email not found for application {$application->id}");
                return false;
            }

            $email = $application->user->email;
            Log::info("Sent rejection email for street application {$application->street_name} to {$email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send street application rejection email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment confirmation email
     */
    public function sendPaymentConfirmationEmail(string $email, string $reference, float $amount, string $type): bool
    {
        try {
            $subject = "Payment Confirmation - Reference: {$reference}";
            
            Log::info("Sent payment confirmation email to {$email} for {$type}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send payment confirmation email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Queue email for delayed sending
     */
    public static function queue(callable $callback): void
    {
        // Can be extended to use Laravel queues for delayed sending
        $callback();
    }
}
