# User Portal Features Implementation Guide

## Overview

This guide covers the implementation of three new features for staff/user role in the NDSMS system:

1. **Street Naming Request** - Submit requests for street naming and make payments
2. **Address Indexing** - Register addresses for Google Maps indexing with payment
3. **Street Revalidation** - Request revalidation of existing streets with payment

---

## 1. Database Setup

### Run Migrations

```bash
php artisan migrate
```

This creates the following tables:

- `address_indexing_requests` - Stores address indexing applications
- `street_revalidations` - Stores street revalidation requests
- Updates `payments` table with polymorphic support (payable_id, payable_type)

---

## 2. Route Setup

Add these routes to `routes/web.php` in the authenticated portal routes section:

```php
Route::middleware(['auth', 'verified'])->prefix('portal')->group(function () {
    // ... existing routes ...

    // New User Portal Features
    Route::get('register-address-indexing', RegisterAddressIndexing::class)
        ->name('portal.register-address-indexing')
        ->middleware('can:access-portal');

    Route::get('street-revalidation', StreetRevalidationForm::class)
        ->name('portal.street-revalidation')
        ->middleware('can:access-portal');

    // Payment routes (already exist, will handle new types)
    Route::post('payment/initialize', [PaymentController::class, 'initializeTransaction'])
        ->name('portal.payment.initialize');

    Route::post('payment/verify', [PaymentController::class, 'verifyTransaction'])
        ->name('portal.payment.verify');

    Route::get('payment/status/{reference}', [PaymentController::class, 'getPaymentStatus'])
        ->name('portal.payment.status');
});
```

---

## 3. Navigation Menu Updates

Update your portal navigation/menu to include these new features. Add to `resources/views/components/layouts/portal.blade.php` or similar:

```blade
<!-- In sidebar/menu -->
<a href="{{ route('portal.register-address-indexing') }}"
   class="menu-item {{ request()->routeIs('portal.register-address-indexing') ? 'active' : '' }}">
    <i class="fas fa-map-marker-alt"></i>
    <span>Address Indexing</span>
</a>

<a href="{{ route('portal.street-revalidation') }}"
   class="menu-item {{ request()->routeIs('portal.street-revalidation') ? 'active' : '' }}">
    <i class="fas fa-sync-alt"></i>
    <span>Street Revalidation</span>
</a>
```

---

## 4. Payment Integration

### Update PaymentController

The existing PaymentController needs to be extended to handle the new request types. Update your `app/Http/Controllers/PaymentController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\AddressIndexingRequest;
use App\Models\StreetRevalidation;
use App\Models\Payment;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaystackService $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    /**
     * Initialize payment for any request type
     *
     * Expected payload:
     * {
     *   "type": "street_naming|address_indexing|street_revalidation",
     *   "request_id": 123,
     *   "amount": 2500,
     *   "email": "user@example.com"
     * }
     */
    public function initializeTransaction(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:street_naming,address_indexing,street_revalidation',
            'request_id' => 'required|integer',
            'amount'     => 'required|numeric|min:100',
            'email'      => 'required|email',
        ]);

        $user = auth()->user();
        $type = $request->input('type');
        $requestId = $request->input('request_id');

        // Verify the request belongs to the user
        $payable = $this->getPayableModel($type, $requestId);

        if (!$payable || $payable->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if payment already exists
        $existingPayment = $payable->payment;
        if ($existingPayment && $existingPayment->status === 'completed') {
            return response()->json(['error' => 'Payment already completed'], 400);
        }

        // Create payment record
        $payment = Payment::create([
            'user_id'              => $user->id,
            'payable_id'           => $payable->id,
            'payable_type'         => get_class($payable),
            'amount'               => $request->input('amount'),
            'currency'             => 'NGN',
            'payment_method'       => 'paystack',
            'status'               => 'pending',
            'reference'            => 'PA_' . time() . '_' . uniqid(),
        ]);

        // Initialize Paystack transaction
        $response = $this->paystack->initializeTransaction(
            $payment->reference,
            $request->input('amount') * 100, // Convert to kobo
            $request->input('email'),
            [
                'payment_id'  => $payment->id,
                'type'        => $type,
                'request_id'  => $requestId,
            ]
        );

        if ($response['status']) {
            $payment->update(['transaction_id' => $response['data']['reference']]);
            return response()->json([
                'status'          => true,
                'authorization_url' => $response['data']['authorization_url'],
                'reference'       => $payment->reference,
            ]);
        }

        $payment->delete();
        return response()->json(['error' => 'Failed to initialize payment'], 400);
    }

    /**
     * Verify payment completion
     */
    public function verifyTransaction(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $payment = Payment::where('reference', $request->input('reference'))->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Verify with Paystack
        $response = $this->paystack->verifyTransaction($payment->transaction_id);

        if ($response['status'] && $response['data']['status'] === 'success') {
            $payment->markAsCompleted();

            // Update the payable model status
            if ($payment->payable) {
                $payment->payable->update(['status' => 'approved']);
            }

            return response()->json([
                'status'   => true,
                'message'  => 'Payment verified successfully',
                'payment'  => $payment,
            ]);
        }

        $payment->markAsFailed();
        return response()->json(['error' => 'Payment verification failed'], 400);
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($reference)
    {
        $payment = Payment::where('reference', $reference)
            ->with('payable')
            ->firstOrFail();

        return response()->json([
            'status'    => $payment->status,
            'amount'    => $payment->amount,
            'paid_at'   => $payment->paid_at,
            'payable'   => $payment->payable,
        ]);
    }

    /**
     * Get the payable model instance
     */
    protected function getPayableModel($type, $id)
    {
        return match ($type) {
            'address_indexing'  => AddressIndexingRequest::find($id),
            'street_revalidation' => StreetRevalidation::find($id),
            'street_naming'     => StreetApplication::find($id),
            default             => null
        };
    }
}
```

---

## 5. Livewire Payment Integration

Update the Livewire components to dispatch payment events. The components already include:

```php
// In RegisterAddressIndexing - after form submission
$this->dispatch('initiate-indexing-payment',
    addressIndexingRequestId: $this->lastRequest->id,
);

// In StreetRevalidationForm - after form submission
$this->dispatch('initiate-revalidation-payment',
    streetRevalidationId: $this->lastRevalidation->id,
);
```

Listen to these events in your portal component and trigger the payment initialization.

---

## 6. Admin Panel - Approve/Reject Features

Add menu items and components for admin to manage these requests:

### Routes

```php
Route::middleware(['auth', 'can:manage-system'])->prefix('admin')->group(function () {
    // Address Indexing Management
    Route::get('address-indexing-requests', AddressIndexingRequestIndex::class)
        ->name('admin.address-indexing-requests.index');
    Route::get('address-indexing-requests/{id}', AddressIndexingRequestShow::class)
        ->name('admin.address-indexing-requests.show');
    Route::post('address-indexing-requests/{id}/approve', [AddressIndexingRequestController::class, 'approve'])
        ->name('admin.address-indexing-requests.approve');
    Route::post('address-indexing-requests/{id}/reject', [AddressIndexingRequestController::class, 'reject'])
        ->name('admin.address-indexing-requests.reject');

    // Street Revalidation Management
    Route::get('street-revalidations', StreetRevalidationIndex::class)
        ->name('admin.street-revalidations.index');
    Route::get('street-revalidations/{id}', StreetRevalidationShow::class)
        ->name('admin.street-revalidations.show');
    Route::post('street-revalidations/{id}/approve', [StreetRevalidationController::class, 'approve'])
        ->name('admin.street-revalidations.approve');
    Route::post('street-revalidations/{id}/reject', [StreetRevalidationController::class, 'reject'])
        ->name('admin.street-revalidations.reject');
});
```

---

## 7. Fee Configuration

### Setting Fees in Admin Fee Schedule

These should be configurable in your admin Fee Schedule module:

**Service Types to Add:**

- Service Type: `address_indexing` - Amount: ₦1,500 (configurable)
- Service Type: `street_revalidation` - Amount: ₦1,000 (configurable)
- Service Type: `street_naming` - Amount: ₦2,500 (configurable)

When users submit a request, fetch the current fee:

```php
// In PaymentController or Livewire component
$fee = Fee::where('service_type', 'address_indexing')
    ->where('status', 'active')
    ->latest('effective_from')
    ->first();

$amount = $fee?->amount ?? 1500; // fallback amount
```

---

## 8. Data Relationships

### User Model Relationships

Add these to `app/Models/User.php`:

```php
public function addressIndexingRequests()
{
    return $this->hasMany(AddressIndexingRequest::class);
}

public function streetRevalidations()
{
    return $this->hasMany(StreetRevalidation::class);
}
```

### Payment Model (Already Updated)

```php
public function payable()
{
    return $this->morphTo();
}
```

---

## 9. Key Features Summary

### Address Indexing Request

- **Data Collected:**
    - Applicant name & phone
    - Full address with coordinates (lat/long)
    - House number
    - Owner details
    - Property images (up to 5)
    - Description
- **Workflow:**
    1. User fills 4-step form
    2. Creates AddressIndexingRequest record
    3. Initiates payment via Paystack
    4. Payment completion triggers status update to "completed"
    5. Admin reviews and approves

### Street Revalidation

- **Options:**
    - Select from existing streets (with search)
    - Create new revalidation application
- **Data Collected:**
    - Street name & ward
    - Reason for revalidation
    - Current status (active/inactive/disputed/under_review)
    - Supporting documents (up to 5 files)
- **Workflow:**
    1. User chooses existing street or records new one
    2. Provides reason and details
    3. Uploads supporting documents
    4. Creates StreetRevalidation record
    5. Initiates payment
    6. Admin reviews and process

### Street Naming (Existing - Enhanced)

- Already implemented via StreetApplication model
- Can be integrated with same payment flow

---

## 10. Testing Checklist

- [ ] Run migrations successfully
- [ ] Routes work and are accessible
- [ ] RegisterAddressIndexing form displays all 4 steps
- [ ] StreetRevalidationForm displays both tabs and steps
- [ ] Form validation works properly
- [ ] Files upload correctly
- [ ] Payment initialization triggers
- [ ] Paystack payment page loads
- [ ] Payment verification works
- [ ] Status updates after successful payment
- [ ] Admin can view requests
- [ ] Admin can approve/reject requests

---

## 11. Additional Notes

### Image & Document Storage

Images are stored in `storage/app/public/property-images/`
Documents are stored in `storage/app/public/revalidation-documents/`

Make sure storage is symlinked:

```bash
php artisan storage:link
```

### Validation Rules

All validation rules are defined in the Livewire components and can be customized as needed.

### Error Handling

Components dispatch toast messages for user feedback. Ensure your layout has a toast component.

### Permissions

Make sure your role-based system allows staff users to access these features:

```php
// In your permission seeder
$user->givePermissionTo('access-portal');
$user->givePermissionTo('submit-address-indexing');
$user->givePermissionTo('submit-street-revalidation');
```

---

## 12. Next Steps After Implementation

1. **Run migrations:** `php artisan migrate`
2. **Create admin components** for managing requests
3. **Configure fee schedule** in admin panel
4. **Test payment flow** with Paystack test keys
5. **Set up webhooks** in Paystack dashboard
6. **Train admin staff** on approving requests
7. **Monitor payment conversions** and track success rates
