<?php

namespace App\Services;

use App\Models\SmsNotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNotificationService
{
    private string $apiProvider;
    private string $apiKey;
    private string $senderId;

    public function __construct()
    {
        $this->apiProvider = config('services.sms.provider', 'twilio');
        $this->apiKey = config('services.sms.api_key');
        $this->senderId = config('services.sms.sender_id', 'NDSMS');
    }

    /**
     * Send SMS to a recipient
     *
     * @param string $phoneNumber Phone number in international format (e.g., +234xxxxxxxxxx)
     * @param string $message SMS message content
     * @param string $type Notification type (e.g., 'approval', 'rejection', 'payment')
     * @param array $metadata Additional metadata to store
     * @return bool Success status
     */
    public function send(
        string $phoneNumber,
        string $message,
        string $type = 'general',
        array $metadata = []
    ): bool {
        try {
            // Validate phone number
            if (!$this->isValidPhoneNumber($phoneNumber)) {
                Log::warning("Invalid phone number: {$phoneNumber}");
                return false;
            }

            $response = match ($this->apiProvider) {
                'twilio' => $this->sendViaTwilio($phoneNumber, $message),
                'infobip' => $this->sendViaInfobip($phoneNumber, $message),
                'mock' => $this->sendViaMock($phoneNumber, $message),
                default => throw new \Exception("Unknown SMS provider: {$this->apiProvider}"),
            };

            if ($response['success']) {
                $this->logNotification(
                    $phoneNumber,
                    $message,
                    $type,
                    'sent',
                    $response['message_id'] ?? null,
                    $metadata
                );
                return true;
            }

            $this->logNotification(
                $phoneNumber,
                $message,
                $type,
                'failed',
                null,
                array_merge($metadata, ['error' => $response['error'] ?? 'Unknown error'])
            );
            return false;
        } catch (\Exception $e) {
            Log::error("SMS sending error: " . $e->getMessage());
            $this->logNotification(
                $phoneNumber,
                $message,
                $type,
                'error',
                null,
                array_merge($metadata, ['exception' => $e->getMessage()])
            );
            return false;
        }
    }

    /**
     * Send bulk SMS messages
     *
     * @param array $recipients Array of phone numbers
     * @param string $message SMS message content
     * @param string $type Notification type
     * @return array Results array with success/failure counts
     */
    public function sendBulk(
        array $recipients,
        string $message,
        string $type = 'general'
    ): array {
        $results = [
            'total' => count($recipients),
            'sent' => 0,
            'failed' => 0,
            'details' => [],
        ];

        foreach ($recipients as $phoneNumber) {
            $success = $this->send($phoneNumber, $message, $type);
            if ($success) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }
            $results['details'][$phoneNumber] = $success;
        }

        return $results;
    }

    /**
     * Send SMS via Twilio
     */
    private function sendViaTwilio(string $phoneNumber, string $message): array
    {
        $accountSid = config('services.sms.twilio_account_sid');
        $authToken = config('services.sms.twilio_auth_token');
        $fromNumber = config('services.sms.twilio_from_number');

        if (!$accountSid || !$authToken || !$fromNumber) {
            return [
                'success' => false,
                'error' => 'Twilio credentials not configured',
            ];
        }

        try {
            $response = Http::withBasicAuth($accountSid, $authToken)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                    'From' => $fromNumber,
                    'To' => $phoneNumber,
                    'Body' => $message,
                ])
                ->throw();

            return [
                'success' => true,
                'message_id' => $response->json('sid'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS via Infobip
     */
    private function sendViaInfobip(string $phoneNumber, string $message): array
    {
        $apiKey = $this->apiKey;
        $from = $this->senderId;

        if (!$apiKey) {
            return [
                'success' => false,
                'error' => 'Infobip API key not configured',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "App {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.infobip.com/sms/2/text/single', [
                'from' => $from,
                'to' => str_replace('+', '', $phoneNumber),
                'text' => $message,
            ])->throw();

            $data = $response->json();
            if (isset($data['messages'][0]['messageId'])) {
                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['messageId'],
                ];
            }

            return [
                'success' => false,
                'error' => $data['messages'][0]['status']['description'] ?? 'Unknown error',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mock SMS sending for development/testing
     */
    private function sendViaMock(string $phoneNumber, string $message): array
    {
        Log::info("Mock SMS to {$phoneNumber}: {$message}");
        return [
            'success' => true,
            'message_id' => 'mock_' . uniqid(),
        ];
    }

    /**
     * Validate Nigerian phone number
     */
    private function isValidPhoneNumber(string $phoneNumber): bool
    {
        // Accepts formats like +234XXXXXXXXXX, +2349XXXXXXXXX, 09XXXXXXXXX, etc.
        return preg_match('/^(\+234|0)[0-9]{10}$/', preg_replace('/\s+/', '', $phoneNumber)) === 1;
    }

    /**
     * Log SMS notification
     */
    private function logNotification(
        string $phoneNumber,
        string $message,
        string $type,
        string $status,
        ?string $messageId = null,
        array $metadata = []
    ): void {
        try {
            SmsNotificationLog::create([
                'phone_number' => $phoneNumber,
                'message' => $message,
                'type' => $type,
                'status' => $status,
                'message_id' => $messageId,
                'metadata' => $metadata,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log SMS notification: " . $e->getMessage());
        }
    }

    /**
     * Get notification retry policy
     */
    public function shouldRetry(SmsNotificationLog $log): bool
    {
        // Retry failed messages once after 5 minutes
        if ($log->status === 'failed' && $log->retry_count < 1) {
            return $log->created_at->diffInMinutes(now()) >= 5;
        }

        return false;
    }

    /**
     * Retry failed notification
     */
    public function retry(SmsNotificationLog $log): bool
    {
        $metadata = $log->metadata ?? [];
        $success = $this->send(
            $log->phone_number,
            $log->message,
            $log->type,
            $metadata
        );

        $log->update([
            'retry_count' => $log->retry_count + 1,
            'last_retry_at' => now(),
        ]);

        return $success;
    }

    /**
     * Send approval notification
     */
    public function sendApprovalNotification(string $phoneNumber, string $itemType, string $reference): bool
    {
        $message = "Your {$itemType} request ({$reference}) has been approved. Thank you!";
        return $this->send($phoneNumber, $message, 'approval');
    }

    /**
     * Send rejection notification
     */
    public function sendRejectionNotification(string $phoneNumber, string $itemType, string $reference, string $reason = ''): bool
    {
        $message = "Your {$itemType} request ({$reference}) has been rejected.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }
        return $this->send($phoneNumber, $message, 'rejection');
    }

    /**
     * Send payment confirmation notification
     */
    public function sendPaymentNotification(string $phoneNumber, string $amount, string $reference): bool
    {
        $message = "Payment of ₦{$amount} confirmed (Ref: {$reference}). Thank you!";
        return $this->send($phoneNumber, $message, 'payment');
    }

    /**
     * Send OTP notification
     */
    public function sendOtpNotification(string $phoneNumber, string $otp, int $expiryMinutes = 10): bool
    {
        $message = "Your verification code is: {$otp}. Valid for {$expiryMinutes} minutes.";
        return $this->send($phoneNumber, $message, 'otp');
    }

    /**
     * Send delivery notification
     */
    public function sendDeliveryNotification(string $phoneNumber, string $itemType, string $reference): bool
    {
        $message = "Your {$itemType} ({$reference}) is ready for delivery. Please collect at your earliest convenience.";
        return $this->send($phoneNumber, $message, 'delivery');
    }
}
