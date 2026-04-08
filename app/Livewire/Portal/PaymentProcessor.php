<?php

namespace App\Livewire\Portal;

use App\Models\Address;
use App\Models\Payment;
use App\Services\PaystackService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentProcessor extends Component
{
    public $address_id = null;
    public $amount = 0;
    public $reference = '';
    public $payment_status = null;
    public $loading = false;
    public $authorization_url = null;
    public $error_message = null;

    protected PaystackService $paystackService;

    public function mount(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    #[On('initiate-payment')]
    public function initiatePayment($addressId, $amount)
    {
        $this->address_id = $addressId;
        $this->amount = $amount;
        $this->loading = true;
        $this->error_message = null;

        try {
            $address = Address::findOrFail($addressId);

            // Verify ownership
            if ($address->applicant_phone !== auth()->user()->phone) {
                $this->error_message = 'Unauthorized access to this address.';
                $this->loading = false;
                return;
            }

            // Create reference
            $this->reference = 'ADR-'.auth()->id().'-'.time();

            // Initialize Paystack transaction
            $metadata = [
                'address_id' => $address->id,
                'user_id' => auth()->id(),
                'house_number' => $address->house_number,
            ];

            $paymentData = $this->paystackService->initializeTransaction(
                $this->amount,
                auth()->user()->email,
                $this->reference,
                $metadata,
                route('payment.callback', [], true)
            );

            // Create payment record
            Payment::create([
                'reference' => $this->reference,
                'address_id' => $address->id,
                'user_id' => auth()->id(),
                'amount' => $this->amount,
                'status' => 'pending',
                'payment_method' => 'paystack',
                'metadata' => $metadata,
            ]);

            $this->authorization_url = $paymentData['authorization_url'];
            $this->dispatch('payment-initialized', reference: $this->reference);

        } catch (\Exception $e) {
            $this->error_message = 'Failed to initialize payment: '.$e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    #[On('initiate-indexing-payment')]
    public function initiateIndexingPayment($addressIndexingRequestId)
    {
        $this->initiateGenericPayment('address_indexing', $addressIndexingRequestId, 'Address Indexing Request');
    }

    #[On('initiate-revalidation-payment')]
    public function initiateRevalidationPayment($streetRevalidationId)
    {
        $this->initiateGenericPayment('street_revalidation', $streetRevalidationId, 'Street Revalidation Request');
    }

    #[On('initiate-plate-payment')]
    public function initiatePlatePayment($plateRequestId)
    {
        $this->initiateGenericPayment('street_numbering_plate', $plateRequestId, 'Street Numbering Plate Request');
    }

    protected function initiateGenericPayment($type, $requestId, $description)
    {
        $this->loading = true;
        $this->error_message = null;

        try {
            // Get fee amount from database or use defaults
            $amount = $this->getFeeAmount($type);

            // Create reference
            $this->reference = strtoupper(substr($type, 0, 3)).'-'.auth()->id().'-'.time();

            // Initialize Paystack transaction
            $metadata = [
                'type' => $type,
                'request_id' => $requestId,
                'user_id' => auth()->id(),
                'description' => $description,
            ];

            $paymentData = $this->paystackService->initializeTransaction(
                $amount,
                auth()->user()->email,
                $this->reference,
                $metadata,
                route('payment.callback', [], true)
            );

            // Create payment record using polymorphic relationship
            $payable = $this->getPayableModel($type, $requestId);

            Payment::create([
                'payable_id' => $payable->id,
                'payable_type' => get_class($payable),
                'user_id' => auth()->id(),
                'amount' => $amount,
                'currency' => 'NGN',
                'payment_method' => 'paystack',
                'status' => 'pending',
                'reference' => $this->reference,
                'transaction_id' => $paymentData['reference'] ?? null,
                'metadata' => $metadata,
            ]);

            $this->authorization_url = $paymentData['authorization_url'];
            $this->dispatch('payment-initialized', reference: $this->reference);

        } catch (\Exception $e) {
            $this->error_message = 'Failed to initialize payment: '.$e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    protected function getFeeAmount($type)
    {
        // Try to get from FeeSchedule model, fallback to defaults
        $fee = \App\Models\FeeSchedule::where('service_type', $type)
            ->where('status', 'active')
            ->latest('effective_from')
            ->first();

        return $fee?->base_amount ?? match($type) {
            'address_indexing' => 1500,
            'street_revalidation' => 1000,
            'street_numbering_plate' => 500,
            default => 1000
        };
    }

    protected function getPayableModel($type, $id)
    {
        return match ($type) {
            'address' => \App\Models\Address::find($id),
            'address_indexing' => \App\Models\AddressIndexingRequest::find($id),
            'street_revalidation' => \App\Models\StreetRevalidation::find($id),
            'street_numbering_plate' => \App\Models\StreetNumberingPlate::find($id),
            default => null
        };
    }

    #[On('verify-payment')]
    public function verifyPayment($reference)
    {
        $this->reference = $reference;
        $this->loading = true;
        $this->error_message = null;

        try {
            $payment = Payment::where('reference', $reference)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $transactionData = $this->paystackService->verifyTransaction($reference);

            DB::transaction(function () use ($payment, $transactionData) {
                if ($transactionData['status'] === 'success') {
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                        'metadata' => array_merge($payment->metadata ?? [], ['paystack_response' => $transactionData]),
                    ]);

                    if ($payment->address) {
                        $payment->address->update([
                            'status' => 'approved',
                            'payment_method' => 'paystack',
                            'reference_code' => $reference,
                        ]);
                    }

                    $this->payment_status = 'completed';
                    $this->dispatch('payment-verified', status: 'success');
                } else {
                    $payment->update([
                        'status' => 'failed',
                        'metadata' => array_merge($payment->metadata ?? [], ['paystack_response' => $transactionData]),
                    ]);

                    $this->payment_status = 'failed';
                    $this->error_message = $transactionData['gateway_response']['message'] ?? 'Payment verification failed';
                    $this->dispatch('payment-verified', status: 'failed');
                }
            });

        } catch (\Exception $e) {
            $this->error_message = 'Verification error: '.$e->getMessage();
            $this->payment_status = 'error';
            $this->dispatch('payment-verified', status: 'error');
        } finally {
            $this->loading = false;
        }
    }

    #[On('check-payment-status')]
    public function checkPaymentStatus($reference)
    {
        try {
            $payment = Payment::where('reference', $reference)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            return [
                'status' => $payment->status,
                'amount' => $payment->amount,
                'paid_at' => $payment->paid_at,
            ];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function render()
    {
        return view('livewire.portal.payment-processor');
    }
}
