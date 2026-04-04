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
                $metadata
            );

            // Create payment record
            Payment::create([
                'reference' => $this->reference,
                'address_id' => $address->id,
                'user_id' => auth()->id(),
                'amount' => $this->amount,
                'status' => 'pending',
                'gateway' => 'paystack',
                'gateway_response' => $paymentData,
            ]);

            $this->authorization_url = $paymentData['authorization_url'];
            $this->dispatch('payment-initialized', reference: $this->reference);

        } catch (\Exception $e) {
            $this->error_message = 'Failed to initialize payment: '.$e->getMessage();
        } finally {
            $this->loading = false;
        }
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
                        'gateway_response' => $transactionData,
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
                        'gateway_response' => $transactionData,
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
