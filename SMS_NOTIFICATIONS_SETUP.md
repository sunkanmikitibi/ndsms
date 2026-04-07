# SMS Notifications System Setup Guide

## Overview

The NDSMS application includes a comprehensive SMS notification system that automatically sends SMS messages to users when their requests are approved, rejected, or ready for delivery. The system supports multiple SMS providers and includes retry logic for failed messages.

## Features

✅ **Multi-Provider Support**

- Twilio
- Infobip
- Mock (for development/testing)

✅ **Automatic Notifications**

- Approval notifications
- Rejection notifications
- Payment confirmations
- OTP delivery
- Delivery/Ready notifications

✅ **Retry Management**

- Automatic retry for failed messages
- Configurable retry intervals
- Retry count tracking

✅ **Logging & Audit Trail**

- All SMS sent logged to database
- Metadata storage for troubleshooting
- Status tracking (pending, sent, failed, error)

---

## Installation & Configuration

### 1. Run Database Migration

```bash
php artisan migrate
```

This creates the `sms_notification_logs` table to store all SMS notifications.

### 2. Update .env File

Add the following environment variables:

```env
# SMS Provider Configuration
SMS_PROVIDER=mock                    # Change to 'twilio', 'infobip', or 'mock'
SMS_SENDER_ID=NDSMS                 # Sender ID for SMS

# For Twilio
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_FROM_NUMBER=+234xxxxxxxxxx

# For Infobip
SMS_API_KEY=your_infobip_api_key
```

### 3. Configure Service Provider

The SMS notification service is automatically registered in `AppServiceProvider` with model observers that trigger automatic notifications.

---

## Usage Examples

### Manual SMS Sending

```php
use App\Services\SmsNotificationService;

$smsService = app(SmsNotificationService::class);

// Send custom message
$smsService->send(
    '+2348012345678',
    'Your custom message here',
    'custom_type'
);

// Send approval notification
$smsService->sendApprovalNotification(
    $phoneNumber,
    'Street Numbering Plate request',
    'PLATE-ABC123'
);

// Send bulk messages
$results = $smsService->sendBulk(
    ['+2348012345678', '+2348087654321'],
    'Your message here',
    'general'
);
```

### Automatic Notifications

Notifications are automatically sent when:

#### Street Numbering Plate Requests

- **Approved**: Sends approval SMS to requester
- **Rejected**: Sends rejection SMS with reason
- **Ready**: Sends delivery notification

#### Address Indexing Requests

- **Approved**: Sends approval SMS to applicant
- **Rejected**: Sends rejection SMS with reason
- **Verified**: Sends verification confirmation

#### Street Applications

- **Approved**: Sends approval SMS to applicant
- **Rejected**: Sends rejection SMS with reason

---

## API Reference

### SmsNotificationService

#### `send(string $phoneNumber, string $message, string $type, array $metadata): bool`

Send a single SMS message.

**Parameters:**

- `$phoneNumber`: Phone number in format +234XXXXXXXXXX or 09XXXXXXXXX
- `$message`: SMS message content (max 160 characters recommended)
- `$type`: Notification type (approval, rejection, payment, otp, delivery, general)
- `$metadata`: Additional metadata to log

**Returns:** `true` on success, `false` on failure

#### `sendBulk(array $recipients, string $message, string $type): array`

Send SMS to multiple recipients.

**Returns:** Array with keys: `total`, `sent`, `failed`, `details`

#### `sendApprovalNotification(string $phoneNumber, string $itemType, string $reference): bool`

Send approval notification.

#### `sendRejectionNotification(string $phoneNumber, string $itemType, string $reference, string $reason): bool`

Send rejection notification with reason.

#### `sendPaymentNotification(string $phoneNumber, string $amount, string $reference): bool`

Send payment confirmation SMS.

#### `sendOtpNotification(string $phoneNumber, string $otp, int $expiryMinutes): bool`

Send OTP code for verification.

#### `sendDeliveryNotification(string $phoneNumber, string $itemType, string $reference): bool`

Send ready/delivery notification.

---

## Phone Number Validation

The system accepts the following phone number formats:

- `+234XXXXXXXXXX` (with country code)
- `09XXXXXXXXX` (without country code, assumes Nigeria)

**Examples:**

- ✅ `+2348012345678`
- ✅ `09012345678`
- ❌ `08012345678` (must start with 09 or +234)
- ❌ `2348012345678` (missing +)

---

## Testing & Development

### Mock Provider (Development)

For development and testing, use the mock provider:

```env
SMS_PROVIDER=mock
```

Messages won't actually be sent but will be logged:

```
[info] Mock SMS to +2348012345678: Your message here
```

### Testing SMS Sending

```php
// In your test
$this->artisan('tinker');

$smsService = app(\App\Services\SmsNotificationService::class);
$smsService->send('+2348012345678', 'Test message', 'test');
```

Check `sms_notification_logs` table to verify logging.

---

## Monitoring & Troubleshooting

### View SMS Logs

```php
use App\Models\SmsNotificationLog;

// Get all failed SMS
$failed = SmsNotificationLog::where('status', 'failed')->get();

// Get SMS by type
$approvals = SmsNotificationLog::where('type', 'approval')->paginate();

// Get SMS for specific phone
$logs = SmsNotificationLog::where('phone_number', '+2348012345678')->get();
```

### Database Query Examples

```sql
-- Get failed SMS in last 24 hours
SELECT * FROM sms_notification_logs
WHERE status IN ('failed', 'error')
AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY);

-- Get retry statistics
SELECT type, status, COUNT(*) as count
FROM sms_notification_logs
GROUP BY type, status;

-- Get messages requiring retry
SELECT * FROM sms_notification_logs
WHERE status = 'failed'
AND retry_count < 1
AND created_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE);
```

### Common Issues

**Issue: Messages not being sent**

1. Check SMS_PROVIDER setting in .env
2. Verify API credentials (SMS_API_KEY, TWILIO tokens)
3. Check phone number format
4. Review logs in `storage/logs/laravel.log`

**Issue: Provider authentication failure**

1. Verify API keys in .env are correct
2. Check provider account has SMS credits
3. For Twilio: verify Twilio number format

**Issue: Failed to retry**

1. Check `retry_count` in database
2. Verify `last_retry_at` timestamp
3. Check logs for error details

---

## Batch Operations

The batch operation service supports bulk SMS sending:

```php
use App\Services\BatchOperationService;
use App\Services\SmsNotificationService;

$batchService = app(BatchOperationService::class);

// Bulk approve with SMS
$results = $batchService->bulkApproveStreetNumberingPlates(
    $ids = [1, 2, 3, 4, 5],
    $notes = 'Approvals processed'
);
```

All bulk operations automatically track which requests had SMS sent successfully.

---

## Production Checklist

- [ ] Choose production SMS provider (Twilio or Infobip)
- [ ] Set up API credentials in .env
- [ ] Test SMS sending with actual phone numbers
- [ ] Set up monitoring/alerting for failed SMS
- [ ] Configure retry schedule (if using queues)
- [ ] Document SMS costs and budget
- [ ] Set up logging for audit purposes

---

## Support & References

- [Twilio SMS API](https://www.twilio.com/docs/sms)
- [Infobip SMS API](https://www.infobip.com/docs/sms)
- [Laravel Service Configuration](https://laravel.com/docs/services)

---

## Version

Created: April 7, 2026
Last Updated: April 7, 2026
