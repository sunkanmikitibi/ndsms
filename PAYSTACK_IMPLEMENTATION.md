# Paystack Payment Gateway Implementation

## Overview
This implementation integrates Paystack payment gateway for address registration and street application payments in the NDSMS system.

## Architecture

### Components

#### 1. **PaystackService** (`app/Services/PaystackService.php`)
Core service for Paystack API interactions:
- `initializeTransaction()` - Initialize payment with Paystack
- `verifyTransaction()` - Verify payment completion
- `getTransaction()` - Get transaction details
- `listTransactions()` - List all transactions
- `checkPaymentStatus()` - Quick payment status check
- `getCustomer()` - Retrieve customer information

#### 2. **Payment Model** (`app/Models/Payment.php`)
Tracks all payment records:
```
Fields:
- reference (unique)
- address_id (FK)
- street_application_id (FK)
- user_id (FK)
- amount (decimal)
- currency (NGN)
- payment_method (paystack, flutterwave, bank_transfer)
- status (pending, completed, failed)
- transaction_id
- paid_at
- metadata (JSON)
```

#### 3. **PaymentController** (`app/Http/Controllers/PaymentController.php`)
Handles payment operations:
- `initializeAddressPayment()` - Start address registration payment
- `verifyPayment()` - Verify payment completion
- `webhook()` - Handle Paystack webhooks
- `getPaymentStatus()` - Get current payment status

#### 4. **PaymentProcessor Component** (`app/Livewire/Portal/PaymentProcessor.php`)
Livewire component for frontend payment handling:
- Initialize payment
- Verify after return from Paystack
- Display payment status
- Manage payment UI state

## Configuration

### Environment Variables
Add to `.env`:
```env
PAYSTACK_PUBLIC_KEY=pk_test_xxxxxx
PAYSTACK_SECRET_KEY=sk_test_xxxxxx
PAYSTACK_MERCHANT_ID=optional_merchant_id
```

### Services Config
Already configured in `config/services.php`:
```php
'paystack' => [
    'public_key'  => env('PAYSTACK_PUBLIC_KEY'),
    'secret_key'  => env('PAYSTACK_SECRET_KEY'),
    'merchant_id' => env('PAYSTACK_MERCHANT_ID'),
],
```

## Database Setup

### Migrations
**File**: `database/migrations/2026_04_04_000000_create_payments_table.php`

Run migrations:
```bash
php artisan migrate
```

This creates the `payments` table with:
- Tracking of all transactions
- Relationships to addresses and users
- Metadata storage for transaction details
- Status tracking (pending, completed, failed)

## Usage

### 1. Initialize Payment

**Frontend (Livewire):**
```php
// Dispatch event to initialize payment
$this->dispatch('initiate-payment', addressId: $address->id, amount: 5000);
```

**API Endpoint:**
```
POST /payment/initialize
Content-Type: application/json

{
    "address_id": 1,
    "amount": 5000.00
}
```

Response:
```json
{
    "success": true,
    "payment_id": 123,
    "reference": "ADR-1-1712186400",
    "authorization_url": "https://checkout.paystack.com/xxxxx",
    "access_code": "xxxxx"
}
```

### 2. Redirect to Paystack

User is redirected to Paystack checkout URL. After payment, Paystack redirects back to your callback URL.

### 3. Verify Payment

**Frontend (After returning from Paystack):**
```php
// Dispatch event to verify payment
$this->dispatch('verify-payment', reference: $reference);
```

**API Endpoint:**
```
POST /payment/verify
Content-Type: application/json

{
    "reference": "ADR-1-1712186400"
}
```

Response:
```json
{
    "success": true,
    "status": "completed",
    "message": "Payment successful"
}
```

### 4. Check Payment Status

**API Endpoint:**
```
GET /payment/status?reference=ADR-1-1712186400
```

Response:
```json
{
    "reference": "ADR-1-1712186400",
    "status": "completed",
    "amount": 5000,
    "currency": "NGN",
    "paid_at": "2026-04-04 12:00:00",
    "address_id": 1
}
```

## Webhook Handling

### Setup in Paystack Dashboard
1. Go to Settings → API Keys & Webhooks
2. Set Webhook URL: `https://yourdomain.com/webhook/paystack`
3. Enable these events:
   - charge.success
   - charge.failed

### Processing Webhooks

The webhook endpoint automatically:
1. Validates the Paystack signature
2. Processes successful payments
3. Updates payment status
4. Updates address registration status
5. Records transaction details

## Address Registration Flow with Payments

### Current Flow (RegisterAddress Component):
1. User fills address details
2. Selects payment method (paystack/flutterwave/bank_transfer)
3. Submits address registration
4. Address created with status: `pending`

### Updated Flow (With Paystack):
1. User fills address details
2. Address submitted → status `pending_payment`
3. Payment component displays amount due
4. User clicks "Pay with Paystack"
5. Redirected to Paystack checkout
6. After payment → returns with reference
7. Verify payment endpoint confirms
8. Address status → `approved` (payment confirmed)

### Integration Code Example

In RegisterAddress component:
```php
public function submitWithPayment(): void
{
    // Create address
    $address = Address::create([...]);
    
    // Dispatch payment initialization
    $this->dispatch('initiate-payment', 
        addressId: $address->id, 
        amount: 5000
    );
    
    // Listen for payment verification
    $this->on('payment-verified', function ($status) {
        if ($status === 'success') {
            // Payment successful - address approved
            $this->dispatch('toast', type: 'success', message: 'Payment successful!');
        }
    });
}
```

## Error Handling

### Validation Errors
```json
{
    "success": false,
    "error": "The amount field is required"
}
```

### Authorization Errors
```json
{
    "error": "Unauthorized",
    "status": 403
}
```

### Payment Verification Failures
Automatically handled with status: `failed` and error message displayed to user.

## Security Considerations

1. **Secret Key**: Never expose `PAYSTACK_SECRET_KEY` in client-side code
2. **Signature Verification**: All webhooks are verified using Paystack signature
3. **HTTPS**: Ensure HTTPS in production
4. **CSRF Protection**: Laravel's CSRF token included in all POST requests
5. **Authentication**: All payment endpoints require user authentication

## Managing Payments in Admin

### In Approvals Section
Admins can:
1. View all applications with payment status
2. See which payments are pending/completed/failed
3. Filter by payment status
4. Mark applications as `awaiting_payment`
5. View payment reference codes

### Retrieve Payment Information
```php
use App\Models\Payment;

// Get payment by reference
$payment = Payment::where('reference', 'ADR-1-1712186400')->first();

// Get user's payments
$payments = auth()->user()->payments;

// Get payments for address
$payment = Payment::where('address_id', $addressId)->first();
```

## Testing

### Test Keys
For testing, use Paystack test keys:
```env
PAYSTACK_PUBLIC_KEY=pk_test_xxxxxx
PAYSTACK_SECRET_KEY=sk_test_xxxxxx
```

### Test Cards
Paystack provides test card numbers. Refer to Paystack documentation.

### Local Testing
1. Build local app with test keys
2. Visit registration form
3. Click "Pay with Paystack"
4. Use test card credentials
5. Verify payment is recorded in local database

## Troubleshooting

### Payment not initializing
- Check PAYSTACK_SECRET_KEY in .env
- Verify amount is > 0
- Check user email is valid

### Webhook not received
- Verify webhook URL is publicly accessible
- Check Paystack dashboard webhook settings
- Review application logs

### Payment stuck in pending
- Manually verify using PaymentController::verifyPayment()
- Check Paystack transaction history
- Review gateway_response in payments table

## Future Enhancements

1. **Multiple Payment Gateways**: Already structured for flutterwave, bank_transfer
2. **Refunds**: Implement partial/full refunds
3. **Payment Plans**: Recurring payments for subscriptions
4. **Receipt Generation**: Auto-generate payment receipts
5. **Analytics**: Payment analytics dashboard
6. **Retry Logic**: Automatic retry for failed payments

## Support

For issues:
1. Check application logs: `storage/logs/`
2. Review Paystack transaction history
3. Verify webhook delivery in Paystack dashboard
4. Check database records in `payments` table
