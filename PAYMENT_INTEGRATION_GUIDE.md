# Payment Event Integration Example

This guide shows you how to integrate the payment flow with the Livewire components.

## Overview

The Livewire components dispatch events when the user submits their form:

- `initiate-indexing-payment` (from RegisterAddressIndexing)
- `initiate-revalidation-payment` (from StreetRevalidationForm)

You need to listen to these events and trigger the payment process.

---

## Option 1: Portal Dashboard Component Integration

Create or update `app/Livewire/Portal/Dashboard.php`:

```php
<?php

namespace App\Livewire\Portal;

use App\Models\Fee;
use App\Models\AddressIndexingRequest;
use App\Models\StreetRevalidation;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.portal')]
class Dashboard extends Component
{
    public $showPaymentModal = false;
    public $paymentData = [];

    /**
     * Listen to payment initiation events
     */
    #[\Livewire\Attributes\On('initiate-indexing-payment')]
    public function initiateAddressIndexingPayment($addressIndexingRequestId)
    {
        $request = AddressIndexingRequest::findOrFail($addressIndexingRequestId);

        // Get current fee
        $fee = Fee::where('service_type', 'address_indexing')
            ->where('status', 'active')
            ->latest('created_at')
            ->first();

        $amount = $fee?->amount ?? 1500;

        $this->initiatePayment(
            'address_indexing',
            $addressIndexingRequestId,
            $amount,
            $request->address_line
        );
    }

    /**
     * Listen to revalidation payment events
     */
    #[\Livewire\Attributes\On('initiate-revalidation-payment')]
    public function initiateRevalidationPayment($streetRevalidationId)
    {
        $revalidation = StreetRevalidation::findOrFail($streetRevalidationId);

        // Get current fee
        $fee = Fee::where('service_type', 'street_revalidation')
            ->where('status', 'active')
            ->latest('created_at')
            ->first();

        $amount = $fee?->amount ?? 1000;

        $this->initiatePayment(
            'street_revalidation',
            $streetRevalidationId,
            $amount,
            "Revalidation: {$revalidation->street_name}"
        );
    }

    /**
     * Generic payment initialization
     */
    protected function initiatePayment($type, $requestId, $amount, $description)
    {
        try {
            // Call the payment initialization endpoint
            $response = Http::post(route('payment.initialize'), [
                'type'       => $type,
                'request_id' => $requestId,
                'amount'     => $amount,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Store payment data for reference
                $this->paymentData = [
                    'type'          => $type,
                    'description'   => $description,
                    'amount'        => $amount,
                    'reference'     => $data['reference'],
                ];

                // Redirect to Paystack checkout
                return $this->redirect($data['authorization_url'], navigate: false);
            } else {
                $this->dispatch('toast',
                    type: 'error',
                    message: 'Failed to initialize payment: ' . $response->json('error')
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('toast',
                type: 'error',
                message: 'Payment initialization error: ' . $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.portal.dashboard');
    }
}
```

---

## Option 2: Dedicated Payment Modal Component

Create `app/Livewire/Portal/PaymentModal.php`:

```php
<?php

namespace App\Livewire\Portal;

use App\Models\Fee;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentModal extends Component
{
    public $showModal = false;
    public $paymentData = [];
    public $loading = false;

    #[On('initiate-indexing-payment')]
    #[On('initiate-revalidation-payment')]
    public function handlePaymentInitiation($name, $values)
    {
        $type = $name === 'initiate-indexing-payment'
            ? 'address_indexing'
            : 'street_revalidation';

        $requestId = array_values($values)[0]; // Get the ID

        // Get fee
        $fee = Fee::where('service_type', $type)
            ->where('status', 'active')
            ->latest('created_at')
            ->first();

        $amount = $fee?->amount ?? ($type === 'address_indexing' ? 1500 : 1000);

        $this->paymentData = [
            'type'       => $type,
            'request_id' => $requestId,
            'amount'     => $amount,
        ];

        $this->showModal = true;
    }

    public function processPayment()
    {
        $this->loading = true;

        try {
            $response = Http::post(route('payment.initialize'), $this->paymentData);

            if ($response->successful()) {
                $this->redirect($response['authorization_url'], navigate: false);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
            $this->loading = false;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.portal.payment-modal');
    }
}
```

Then create `resources/views/livewire/portal/payment-modal.blade.php`:

```blade
<div>
    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal">
        <div class="modal-content" @click.stop>
            <div style="padding:24px;">
                <h3 style="font-size:18px;font-weight:700;margin-bottom:20px;">
                    <i class="fas fa-credit-card" style="color:var(--accent);margin-right:8px;"></i>
                    Confirm Payment
                </h3>

                <div style="background:var(--bg-input);padding:16px;border-radius:var(--radius-sm);margin-bottom:20px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:14px;">
                        <div>
                            <span style="color:var(--text-secondary);">Service Type</span><br>
                            <strong>{{ ucfirst(str_replace('_', ' ', $paymentData['type'] ?? 'N/A')) }}</strong>
                        </div>
                        <div>
                            <span style="color:var(--text-secondary);">Amount</span><br>
                            <strong style="font-size:18px;color:var(--accent);">₦{{ number_format($paymentData['amount'] ?? 0) }}</strong>
                        </div>
                    </div>
                </div>

                <p style="color:var(--text-secondary);font-size:13px;margin-bottom:20px;">
                    You will be redirected to Paystack to complete your payment securely.
                </p>

                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="btn btn-outline"
                        {{ $loading ? 'disabled' : '' }}>
                        Cancel
                    </button>
                    <button
                        type="button"
                        wire:click="processPayment"
                        wire:loading.attr="disabled"
                        class="btn btn-primary">
                        <span wire:loading.remove>
                            <i class="fas fa-lock"></i> Proceed to Payment
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin"></i> Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
```

---

## Option 3: Using Blade JavaScript Events

If you prefer a simpler approach without a separate component, use Blade with Alpine.js:

```blade
<div x-data="paymentHandler()">
    <!-- Your page content here -->

    <script>
        function paymentHandler() {
            return {
                async initiate(type, requestId) {
                    try {
                        // Get fee amount from your fee schedule
                        const amount = type === 'address_indexing' ? 1500 : 1000;

                        const response = await fetch('{{ route("payment.initialize") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                type: type,
                                request_id: requestId,
                                amount: amount
                            })
                        });

                        const data = await response.json();

                        if (data.status) {
                            // Redirect to Paystack
                            window.location.href = data.authorization_url;
                        } else {
                            alert('Payment initialization failed: ' + data.error);
                        }
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                }
            }
        }

        // Listen to Livewire events
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('commit.response', ({ response }) => {
                // This will be called after Livewire actions
            });
        });
    </script>
</div>
```

---

## Integration Checklist

- [ ] Choose one of the three integration options above
- [ ] Update the appropriate component/view
- [ ] Verify fees are set in the Fee Schedule admin panel
- [ ] Test address indexing payment flow:
    1. Fill form and submit
    2. Payment modal appears
    3. Confirm payment
    4. Redirected to Paystack
    5. Complete payment with test card
    6. Return and verify status
- [ ] Test street revalidation payment flow (same steps)

---

## Test Card Numbers (Paystack)

**Successful Payment:**

- Card: 4084 0343 1234 5678
- Expiry: Any future date
- CVV: 123

**Insufficient Funds:**

- Card: 4084 0343 1234 5670
- Expiry: Any future date
- CVV: 123

---

## Paystack Test Keys

Make sure you're using test keys in `.env`:

```env
PAYSTACK_PUBLIC_KEY=pk_test_your_test_key
PAYSTACK_SECRET_KEY=sk_test_your_test_key
```

---

## Next Steps After Integration

1. Test the full payment flow
2. Verify payment records are created correctly
3. Implement webhook handling (already in PaymentController)
4. Set up admin approval workflow (optional)
5. Monitor payment success rates
6. Train staff on the new features

---

## Troubleshooting

**Payment initialization fails:**

- Check if fees are configured in admin panel
- Verify Paystack credentials
- Check browser console for errors

**Payment not verifying:**

- Check PaymentController webhook setup
- Verify Paystack webhooks are configured
- Check database for Payment records

**Modal not appearing:**

- Verify component is included in layout
- Check browser console for JavaScript errors
- Verify Livewire version compatibility

---

For more information, see:

- `USER_PORTAL_FEATURES_GUIDE.md`
- `IMPLEMENTATION_COMPLETE.md`
