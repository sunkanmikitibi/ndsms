# Email Notifications Testing Guide

## Overview

This guide provides comprehensive instructions for testing the email notification system in the NDSMS application. The system automatically sends emails when requests are approved, rejected, or ready for delivery.

---

## Features

✅ **Automatic Email Notifications**

- Approval notifications
- Rejection notifications with reasons
- Delivery/Ready notifications
- Payment confirmations
- Custom email templates

✅ **Queue Support**

- Email sending can be queued for better performance
- Retry logic for failed emails
- Background processing

✅ **Database Notifications**

- In-app notification center
- Email + Database dual notification
- Audit trail

---

## 1. Configuration

### Setup Mail Driver

Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@ndsms.ng
MAIL_FROM_NAME="NDSMS"

# For production Gmail
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

### Available Mail Drivers

```env
# Development/Testing
MAIL_MAILER=log          # Logs emails to storage/logs

# Testing with visual preview
MAIL_MAILER=mailtrap     # Via Mailtrap service

# Production
MAIL_MAILER=smtp         # SMTP server
MAIL_MAILER=sendmail     # Sendmail binary
MAIL_MAILER=mailgun      # Mailgun service
MAIL_MAILER=ses          # AWS SES
```

---

## 2. Testing with Log Driver (Development)

### Configure for Testing

```env
MAIL_MAILER=log
```

### Check Logged Emails

```bash
tail -f storage/logs/laravel.log | grep -i "mail"
```

### Example Log Output

```
[2026-04-07 10:30:45] local.INFO: Mail message sent {"from":{"address":"noreply@ndsms.ng","name":"NDSMS"},"to":{"address":"user@example.com"},...}
```

---

## 3. Testing with Mailtrap

### Setup Mailtrap Account

1. Visit https://mailtrap.io
2. Sign up for free account
3. Create new inbox
4. Get SMTP credentials

### Update .env

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=test@ndsms.ng
```

### Test Email Sending

```php
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

Mail::raw('Test email content', function (Message $message) {
    $message->to('test@example.com')
            ->subject('Test Subject');
});
```

### View in Mailtrap

- Check Mailtrap inbox at https://mailtrap.io
- See full email content, headers, attachments
- Test email parsing

---

## 4. Testing in Laravel Tinker

### Test Approval Email

```bash
php artisan tinker
```

```php
use App\Models\StreetNumberingPlateRequest;
use App\Models\User;
use App\Notifications\StreetNumberingPlateApprovedNotification;

// Create test user
$user = User::first();

// Create test request
$request = StreetNumberingPlateRequest::factory()
    ->for($user)
    ->create(['status' => 'pending']);

// Send approval notification
$user->notify(new StreetNumberingPlateApprovedNotification($request));
```

### Test Rejection Email

```php
use App\Notifications\StreetNumberingPlateRejectedNotification;

$user = User::first();
$request = StreetNumberingPlateRequest::first();

$user->notify(
    new StreetNumberingPlateRejectedNotification(
        $request,
        'Street name does not match official records'
    )
);
```

### Test Ready Email

```php
use App\Notifications\StreetNumberingPlateReadyNotification;

$user = User::first();
$request = StreetNumberingPlateRequest::first();

$user->notify(new StreetNumberingPlateReadyNotification($request));
```

---

## 5. Unit Tests

### Create Test File

Create `tests/Unit/EmailNotificationTest.php`:

```php
namespace Tests\Unit;

use App\Models\StreetNumberingPlateRequest;
use App\Models\User;
use App\Notifications\StreetNumberingPlateApprovedNotification;
use App\Services\EmailNotificationService;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    protected User $user;
    protected EmailNotificationService $emailService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->emailService = app(EmailNotificationService::class);
    }

    public function test_approval_notification_is_sent()
    {
        Notification::fake();

        $request = StreetNumberingPlateRequest::factory()
            ->for($this->user)
            ->create();

        $this->emailService->sendStreetNumberingPlateApprovalEmail($request);

        Notification::assertSentTo(
            $this->user,
            StreetNumberingPlateApprovedNotification::class
        );
    }

    public function test_rejection_notification_includes_reason()
    {
        Notification::fake();

        $request = StreetNumberingPlateRequest::factory()
            ->for($this->user)
            ->create();

        $reason = 'Street name does not match records';

        $this->emailService->sendStreetNumberingPlateRejectionEmail($request, $reason);

        Notification::assertSentTo(
            $this->user,
            StreetNumberingPlateRejectedNotification::class,
            function ($notification) use ($reason) {
                return $notification->reason === $reason;
            }
        );
    }

    public function test_ready_notification_is_sent()
    {
        Notification::fake();

        $request = StreetNumberingPlateRequest::factory()
            ->for($this->user)
            ->create();

        $this->emailService->sendStreetNumberingPlateReadyEmail($request);

        Notification::assertSentTo(
            $this->user,
            StreetNumberingPlateReadyNotification::class
        );
    }

    public function test_email_not_sent_if_user_has_no_email()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => null]);
        $request = StreetNumberingPlateRequest::factory()
            ->for($user)
            ->create();

        $result = $this->emailService->sendStreetNumberingPlateApprovalEmail($request);

        $this->assertFalse($result);
        Notification::assertNotSentTo($user, StreetNumberingPlateApprovedNotification::class);
    }
}
```

### Run Tests

```bash
# Run email tests
php artisan test tests/Unit/EmailNotificationTest.php

# Run with coverage
php artisan test tests/Unit/EmailNotificationTest.php --coverage
```

---

## 6. Integration Tests

### Test Email Flow

Create `tests/Feature/EmailIntegrationTest.php`:

```php
namespace Tests\Feature;

use App\Models\StreetNumberingPlateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailIntegrationTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->user = User::factory()->create();
    }

    public function test_full_email_workflow()
    {
        // Create request
        $request = StreetNumberingPlateRequest::factory()
            ->for($this->user)
            ->create(['status' => 'pending']);

        // Simulate approval
        $request->update(['status' => 'approved']);

        // Verify email was queued/sent
        Mail::assertQueued(\Illuminate\Mail\Mailable::class);
    }

    public function test_rejection_email_contains_reason()
    {
        $request = StreetNumberingPlateRequest::factory()
            ->for($this->user)
            ->create();

        $reason = 'Invalid street coordinates';
        $request->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        // Check if email was sent with reason
        Mail::assertQueued(\Illuminate\Mail\Mailable::class);
    }
}
```

---

## 7. Testing Observers

### Test Email Observer

Create `tests/Unit/EmailObserverTest.php`:

```php
namespace Tests\Unit;

use App\Models\StreetNumberingPlateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailObserverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_approval_email_sent_when_status_changes()
    {
        $user = User::factory()->create();
        $request = StreetNumberingPlateRequest::factory()
            ->for($user)
            ->create(['status' => 'pending']);

        // Observer should trigger when status changes
        $request->update(['status' => 'approved']);

        Notification::assertSentTo($user, function ($notification) {
            return get_class($notification) === 'App\Notifications\StreetNumberingPlateApprovedNotification';
        });
    }

    public function test_rejection_email_sent_with_reason()
    {
        $user = User::factory()->create();
        $request = StreetNumberingPlateRequest::factory()
            ->for($user)
            ->create(['status' => 'pending']);

        $reason = 'Duplicate request';
        $request->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        Notification::assertSentTo($user, function ($notification) {
            return $notification->reason === $reason;
        });
    }
}
```

---

## 8. Manual Testing Steps

### Step 1: Update Request Status

In database or Tinker:

```php
$request = StreetNumberingPlateRequest::find(1);
$request->update(['status' => 'approved']);
```

### Step 2: Check Email

For log driver:

```bash
tail storage/logs/laravel.log
```

For Mailtrap: Check inbox at https://mailtrap.io

### Step 3: Verify Content

Email should contain:

- ✅ Approval message
- ✅ Request reference number
- ✅ Street name
- ✅ Quantity
- ✅ Action link
- ✅ Professional footer

---

## 9. Production Deployment

### Setup Production Mail

```env
# Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-specific-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@ndsms.ng
MAIL_FROM_NAME=NDSMS

# Or Mailgun
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.ndsms.ng
MAILGUN_SECRET=your-mailgun-api-key
```

### Queue Configuration

For better performance, queue emails:

```env
QUEUE_CONNECTION=database
```

### Run Migration

```bash
php artisan migrate
```

### Start Queue Worker

```bash
php artisan queue:work --daemon
```

---

## 10. Common Issues & Troubleshooting

### Email Not Sending

```bash
# 1. Check mail configuration
php artisan config:show mail

# 2. Test mail connection
php artisan tinker
Mail::raw('Test', function ($m) { $m->to('test@example.com'); });

# 3. Check logs
tail storage/logs/laravel.log | grep -i error
```

### SMTP Authentication Failed

```bash
# Verify credentials
echo $MAIL_PASSWORD

# For Gmail, use App-specific password
# For other providers, verify encryption settings
```

### Email Going to Spam

```
1. Set up proper SPF, DKIM, DMARC records
2. Use authenticated sender (From: address should match)
3. Test with spy-glass tools
4. Monitor delivery rates
```

---

## 11. Monitoring

### Check Sent Emails

```sql
-- Laravel notifications table
SELECT * FROM notifications
WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY created_at DESC;

-- Check failed jobs if using queues
SELECT * FROM failed_jobs
WHERE queue = 'mail'
ORDER BY failed_at DESC;
```

### Log Monitoring

```bash
# Watch email logs in real-time
tail -f storage/logs/laravel.log | grep -i "notification"

# Count emails sent today
grep -c "notification sent" storage/logs/laravel.log
```

---

## 12. Best Practices

### ✅ DO

- Always include unique reference numbers
- Add clear action links
- Use user's name in greeting
- Include footer with contact info
- Test templates thoroughly
- Monitor delivery rates

### ❌ DON'T

- Send sensitive data in plain text
- Use generic greetings
- Skip unsubscribe options
- Send too many emails
- Skip testing in staging

---

## Version

Created: April 7, 2026
Last Updated: April 7, 2026
