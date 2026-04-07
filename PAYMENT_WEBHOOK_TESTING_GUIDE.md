# Payment Webhook Testing Guide

## Overview

This guide provides comprehensive instructions for testing the Paystack payment webhook integration in the NDSMS application. The webhook ensures that payment statuses are accurately updated and requests are processed when payments are completed.

---

## Pre-Testing Checklist

Before testing the payment webhook, ensure:

- [ ] Paystack account is set up with valid credentials
- [ ] `PAYSTACK_PUBLIC_KEY` and `PAYSTACK_SECRET_KEY` configured in .env
- [ ] Application is running (local dev server or staging)
- [ ] Database is properly migrated
- [ ] Payment routes are accessible
- [ ] Webhook route is publicly accessible

---

## 1. Local Testing with Mock Provider

### Using Laravel Tinker

```bash
php artisan tinker
```

Then simulate a webhook:

```php
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;

$controller = app(PaymentController::class);

// Create mock webhook payload
$payload = [
    'event' => 'charge.success',
    'data' => [
        'reference' => 'PAY-123-' . time() . '-abc123',
        'amount' => 50000,
        'currency' => 'NGN',
        'status' => 'success',
        'customer' => [
            'email' => 'user@example.com',
        ],
    ],
];

// Create and send request
$request = Request::create(
    '/webhook/paystack',
    'POST',
    $payload,
    [],
    [],
    ['HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', json_encode($payload), config('services.paystack.secret_key'))]
);

$response = $controller->webhook($request);
$response->content();
```

### Test Different Event Types

```php
// Test charge success
$payload['event'] = 'charge.success';
// ... webhook call ...

// Test charge failed
$payload['event'] = 'charge.failed';
$payload['data']['status'] = 'failed';
// ... webhook call ...
```

---

## 2. Testing with Postman

### Setup Postman

1. **Create New Request**
    - Method: POST
    - URL: `http://localhost:8000/webhook/paystack`

2. **Add Headers**
    - Content-Type: application/json
    - X-Paystack-Signature: [See signature calculation below]

3. **Create Webhook Payload**

```json
{
    "event": "charge.success",
    "data": {
        "id": 123456789,
        "reference": "PAY-USER123-1712500000-abc123",
        "amount": 50000,
        "currency": "NGN",
        "status": "success",
        "paid_at": "2026-04-07T10:30:00Z",
        "customer": {
            "id": 1,
            "email": "user@example.com",
            "phone": "+2348012345678"
        },
        "authorization": {
            "authorization_code": "AUTH_ABC123",
            "bin": "400000",
            "last4": "0002",
            "exp_month": "12",
            "exp_year": "2028",
            "channel": "card",
            "card_type": "visa",
            "bank": "Test Bank",
            "country_code": "NG",
            "brand": "Visa"
        }
    }
}
```

### Calculate Signature

In Postman **Pre-request Script** tab:

```javascript
const payload = JSON.stringify(pm.request.body.raw);
const secretKey = pm.environment.get("paystack_secret_key");
const hash = CryptoJS.enc.Hex.stringify(
    CryptoJS.HmacSHA512(payload, secretKey),
);

pm.request.headers.add({
    key: "X-Paystack-Signature",
    value: hash,
});
```

---

## 3. Testing with cURL

### Generate Webhook Request

```bash
#!/bin/bash

PAYSTACK_SECRET_KEY="sk_test_your_secret_key"
WEBHOOK_URL="http://localhost:8000/webhook/paystack"

# Create payload
PAYLOAD='{"event":"charge.success","data":{"reference":"PAY-USER123-1712500000-abc123","amount":50000,"currency":"NGN","status":"success"}}'

# Calculate signature
SIGNATURE=$(echo -n "$PAYLOAD" | openssl dgst -sha512 -hmac "$PAYSTACK_SECRET_KEY" -hex | cut -d' ' -f2)

# Send webhook
curl -X POST "$WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Paystack-Signature: $SIGNATURE" \
  -d "$PAYLOAD"
```

---

## 4. Testing with ngrok (Public URL)

### Setup ngrok

```bash
# Download from https://ngrok.com

# Start ngrok tunnel
./ngrok http 8000

# You'll get a URL like: https://abc123.ngrok.io
```

### Update Paystack Webhook Settings

1. Go to Paystack Dashboard
2. Navigate to: **Settings → API Keys & Webhooks**
3. Add webhook URL: `https://abc123.ngrok.io/webhook/paystack`
4. Select events: `charge.success`, `charge.failed`

### Test from Paystack Dashboard

Paystack provides a webhook test button in the dashboard that will send a test webhook to your configured URL.

---

## 5. Integration Test Suite

### Create Test File

Create `tests/Feature/PaymentWebhookTest.php`:

```php
namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use App\Models\AddressIndexingRequest;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class PaymentWebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    protected function signWebhook(array $payload): string
    {
        $json = json_encode($payload);
        return hash_hmac('sha512', $json, config('services.paystack.secret_key'));
    }

    public function test_valid_webhook_signature()
    {
        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'TEST-' . time(),
                'amount' => 50000,
            ],
        ];

        $signature = $this->signWebhook($payload);

        $response = $this->post('/webhook/paystack', $payload, [
            'X-Paystack-Signature' => $signature,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Webhook received']);
    }

    public function test_invalid_webhook_signature()
    {
        $payload = [
            'event' => 'charge.success',
            'data' => ['reference' => 'TEST-' . time()],
        ];

        $response = $this->post('/webhook/paystack', $payload, [
            'X-Paystack-Signature' => 'invalid_signature',
        ]);

        $response->assertStatus(401);
    }

    public function test_successful_payment_updates_status()
    {
        $request = AddressIndexingRequest::factory()
            ->for($this->user)
            ->create(['status' => 'pending']);

        $payment = Payment::create([
            'user_id' => $this->user->id,
            'payable_id' => $request->id,
            'payable_type' => AddressIndexingRequest::class,
            'amount' => 5000,
            'currency' => 'NGN',
            'status' => 'pending',
            'reference' => 'PAY-TEST-' . time(),
            'payment_method' => 'paystack',
        ]);

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => $payment->reference,
                'amount' => 5000,
                'status' => 'success',
            ],
        ];

        $signature = $this->signWebhook($payload);

        $response = $this->post('/webhook/paystack', $payload, [
            'X-Paystack-Signature' => $signature,
        ]);

        $response->assertStatus(200);

        // Verify payment status changed
        $payment->refresh();
        $this->assertEquals('completed', $payment->status);
        $this->assertNotNull($payment->paid_at);

        // Verify payable status changed
        $request->refresh();
        $this->assertEquals('completed', $request->status);
    }

    public function test_failed_payment_status()
    {
        $payment = Payment::create([
            'user_id' => $this->user->id,
            'payable_id' => null,
            'payable_type' => null,
            'amount' => 5000,
            'currency' => 'NGN',
            'status' => 'pending',
            'reference' => 'PAY-FAIL-' . time(),
            'payment_method' => 'paystack',
        ]);

        $payload = [
            'event' => 'charge.failed',
            'data' => [
                'reference' => $payment->reference,
                'status' => 'failed',
            ],
        ];

        $signature = $this->signWebhook($payload);

        $this->post('/webhook/paystack', $payload, [
            'X-Paystack-Signature' => $signature,
        ]);

        $payment->refresh();
        $this->assertNull($payment->paid_at);
    }
}
```

### Run Tests

```bash
# Run webhook tests
php artisan test tests/Feature/PaymentWebhookTest.php

# Run specific test
php artisan test tests/Feature/PaymentWebhookTest.php::test_valid_webhook_signature

# With coverage
php artisan test tests/Feature/PaymentWebhookTest.php --coverage
```

---

## 6. Webhook Delivery Verification

### Check Webhook Logs

```bash
# View recent logs
tail -f storage/logs/laravel.log | grep webhook

# Search for webhook events
grep -i "webhook" storage/logs/laravel.log | tail -20
```

### Database Verification

```php
use App\Models\Payment;

// Check payment status
Payment::where('reference', 'PAY-USER123-xxx')
    ->with('payable')
    ->first();

// Get all completed payments today
Payment::where('status', 'completed')
    ->whereDate('paid_at', today())
    ->get();
```

---

## 7. End-to-End Testing Flow

### Step 1: Create Request

```bash
POST /api/address-indexing
Authorization: Bearer {token}

{
  "address": "123 Main Street",
  "house_number": "123",
  "owner_name": "John Doe",
  "owner_phone": "+2348012345678",
  "latitude": 6.5244,
  "longitude": 3.3792
}
```

### Step 2: Initialize Payment

```bash
POST /payment/initialize
Authorization: Bearer {token}

{
  "type": "address_indexing",
  "request_id": 1,
  "amount": 5000
}
```

### Step 3: Simulate Payment

```bash
# Use Postman or curl to send webhook
POST /webhook/paystack
X-Paystack-Signature: {signature}

{
  "event": "charge.success",
  "data": {
    "reference": "IDX-1-1712500000-abc123",
    "amount": 5000,
    "status": "success"
  }
}
```

### Step 4: Verify Results

```php
$payment = Payment::where('reference', 'IDX-1-1712500000-abc123')->first();
dd($payment->status); // Should be 'completed'
```

---

## 8. Troubleshooting

### Webhook Not Received

```bash
# 1. Check webhook URL is accessible
curl -X GET http://localhost:8000/webhook/paystack

# 2. Check Paystack logs in Paystack Dashboard
# 3. Enable verbose logging in Laravel
# Update .env: APP_DEBUG=true
# 4. Check application logs
tail storage/logs/laravel.log
```

### Invalid Signature Error

```bash
# Verify secret key matches
echo $PAYSTACK_SECRET_KEY

# Re-generate signature with correct key
# Ensure no whitespace in secret key
```

### Payment Not Updating

```php
// Check if payment record exists
Payment::where('reference', 'PAY-TEST-xxx')->exists();

// Check payable relationship
$payment->payable;

// Verify database transaction completed
Payment::where('status', 'completed')->exists();
```

---

## 9. Security Best Practices

### ✅ DO

- Always verify webhook signature
- Use HTTPS for webhook endpoints
- Store Paystack secret key securely in .env
- Log all webhook events for audit trail
- Verify idempotency (handle duplicate webhooks)
- Rate limit webhook endpoint

### ❌ DON'T

- Expose secret key in code
- Skip signature verification
- Expose webhook URL publicly without auth
- Log sensitive payment data
- Trust unverified webhook sources

---

## 10. Production Deployment

### Pre-Deployment

- [ ] Test with Paystack live keys
- [ ] Configure webhook in Paystack dashboard
- [ ] Set up monitoring/alerting
- [ ] Test HTTPS certificate is valid
- [ ] Verify all environment variables set
- [ ] Set APP_DEBUG=false in production

### Deployment Steps

```bash
# 1. Update environment
cp .env.production .env

# 2. Generate new application key (if needed)
php artisan key:generate

# 3. Run migrations
php artisan migrate --force

# 4. Configure webhook in Paystack Dashboard
# Settings → API Keys & Webhooks
# Add: https://yourdomain.com/webhook/paystack

# 5. Test webhook delivery
# Use Paystack test button in dashboard

# 6. Monitor logs
tail -f storage/logs/laravel.log
```

---

## Version

Created: April 7, 2026
Last Updated: April 7, 2026
