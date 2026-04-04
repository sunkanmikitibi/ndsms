# Paystack Payment Integration - Quick Reference

## Setup Steps

### 1. Configure Environment
```bash
# In .env
PAYSTACK_PUBLIC_KEY=pk_test_your_key
PAYSTACK_SECRET_KEY=sk_test_your_key
```

### 2. Run Migrations
```bash
php artisan migrate
```

### 3. Get API Keys from Paystack Dashboard
- Sign up at https://dashboard.paystack.com
- Go to Settings → API Keys & Webhooks
- Copy your test keys for development

## Database Structure

### Payments Table
```sql
- id (PK)
- reference (unique) -- e.g., "ADR-1-1712186400"
- address_id (FK) -- linked address
- street_application_id (FK) -- linked application
- user_id (FK) -- who made payment
- amount (decimal) -- NGN
- currency (default: NGN)
- status -- pending, completed, failed
- gateway -- paystack, flutterwave, etc
- gateway_response (JSON) -- Paystack response
- paid_at (timestamp)
- timestamps
```

## API Endpoints

### Initialize Payment
```bash
POST /payment/initialize
Authorization: Bearer {token}
Content-Type: application/json

{
  "address_id": 1,
  "amount": 5000.00
}

Response:
{
  "success": true,
  "reference": "ADR-1-1712186400",
  "authorization_url": "https://checkout.paystack.com/xxxxx"
}
```

### Verify Payment
```bash
POST /payment/verify
Authorization: Bearer {token}
Content-Type: application/json

{
  "reference": "ADR-1-1712186400"
}

Response:
{
  "success": true,
  "status": "completed",
  "message": "Payment successful"
}
```

### Check Status
```bash
GET /payment/status?reference=ADR-1-1712186400
Authorization: Bearer {token}

Response:
{
  "reference": "ADR-1-1712186400",
  "status": "completed",
  "amount": 5000,
  "paid_at": "2026-04-04 12:00:00"
}
```

## Livewire Integration

### From RegisterAddress Component
```php
use App\Livewire\Portal\PaymentProcessor;

class RegisterAddress extends Component {
    public function submitWithPayment() {
        // Create address
        $address = Address::create([...]);
        
        // Trigger payment
        $this->dispatch('initiate-payment', 
            addressId: $address->id,
            amount: 5000
        );
    }
}
```

### PaymentProcessor Component
```php
// In view, include the component
@livewire('portal.payment-processor')

// Listen for events
document.addEventListener('payment-verified', (e) => {
    if (e.detail.status === 'success') {
        // Payment successful
    }
});
```

## Webhook Setup

1. Go to Paystack Dashboard → Settings
2. Add webhook URL: `https://yourdomain.com/webhook/paystack`
3. Subscribe to: `charge.success`, `charge.failed`
4. Webhook automatically handles:
   - Payment status updates
   - Address approval on success
   - Payment record persistence

## Payment Flow

```
User Registration Form
        ↓
    [Submit]
        ↓
Create Address (pending_payment status)
        ↓
[Trigger Payment]
        ↓
Initialize Paystack Transaction
        ↓
Redirect to Paystack Checkout
        ↓
User Pays (or Cancels)
        ↓
Return to App with Reference
        ↓
Verify Payment
        ↓
Update Address Status → approved
Update Payment Status → completed
```

## Testing

### Test Card Numbers (Paystack)
- **Visa**: 4111 1111 1111 1111
- **Mastercard**: 5531 8866 5592 2950
- **Expiry**: Any future date
- **CVV**: Any 3 digits

### Test Payment Flow
1. Visit `/portal/register-address`
2. Fill form and select Paystack
3. Click "Pay with Paystack"
4. Use test card numbers
5. Complete payment
6. Verify in database: `select * from payments;`

## Models & Methods

### Payment Model
```php
Payment::where('reference', $ref)->first()
Payment::where('user_id', auth()->id())->get()
Payment::where('status', 'completed')->get()

$payment->isPaid()
$payment->isPending()
$payment->isFailed()
$payment->address  // relationship
$payment->user     // relationship
```

### PaystackService
```php
$service = app(PaystackService::class);
$data = $service->initializeTransaction(5000, 'user@email.com');
$verified = $service->verifyTransaction('ADR-1-1712186400');
$status = $service->checkPaymentStatus('ADR-1-1712186400');
```

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Payment not initializing | Check PAYSTACK_SECRET_KEY in .env |
| "Invalid signature" on webhook | Verify webhook URL is public & HTTPS |
| Payment stuck pending | Manually call verify endpoint |
| Address not updated after payment | Check gateway_response in DB |
| Wrong amount deducted | Verify amount calculation (in kobo) |

## Amount Conversion

Paystack works in **kobo** (100 kobo = 1 NGN):
```php
// Frontend sends: 5000 (NGN)
// Service converts: 5000 * 100 = 500000 (kobo)
// Paystack charges: 500,000 kobo = ₦5,000
```

## File Locations

```
app/Models/Payment.php
app/Services/PaystackService.php
app/Http/Controllers/PaymentController.php
app/Livewire/Portal/PaymentProcessor.php
resources/views/livewire/portal/payment-processor.blade.php
database/migrations/2026_04_04_*.php
PAYSTACK_IMPLEMENTATION.md (full docs)
```

## Debugging

### View Payment Records
```php
// In tinker
>>> Payment::all()
>>> Payment::where('status', 'pending')->get()
>>> Payment::where('reference', 'ADR-1-1234567890')->first()
```

### Check Service Configuration
```php
>>> config('services.paystack')
=> [
  'public_key' => 'pk_test_...',
  'secret_key' => 'sk_test_...'
]
```

### Test Service Initialization
```php
>>> $service = app(\App\Services\PaystackService::class)
>>> $data = $service->initializeTransaction(500, 'test@example.com')
>>> $data['authorization_url'] // Should return checkout URL
```
