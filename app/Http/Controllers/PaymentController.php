<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\AddressIndexingRequest;
use App\Models\Payment;
use App\Models\StreetRevalidation;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
        $this->middleware('auth');
    }

    /**
     * Initialize payment for any request type
     * Supports: address, address_indexing, street_revalidation
     */
    public function initializeTransaction(Request $request)
    {
        $validated = $request->validate([
            'type'       => 'required|in:address,address_indexing,street_revalidation,street_naming',
            'request_id' => 'required|integer',
            'amount'     => 'required|numeric|min:100',
        ]);

        try {
            $user = auth()->user();
            $type = $validated['type'];
            $requestId = $validated['request_id'];
            $amount = $validated['amount'];

            // Get the payable model
            $payable = $this->getPayableModel($type, $requestId);

            if (!$payable || $payable->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Check if payment already exists and is completed
            $existingPayment = $payable->payment ?? null;
            if ($existingPayment && $existingPayment->status === 'completed') {
                return response()->json(['error' => 'Payment already completed'], 400);
            }

            // Create reference
            $reference = $this->generatePaymentReference($type, $user->id);

            // Prepare metadata
            $metadata = [
                'type'        => $type,
                'request_id'  => $requestId,
                'user_id'     => $user->id,
                'model_class' => get_class($payable),
            ];

            // Initialize with Paystack
            $paystackResponse = $this->paystackService->initializeTransaction(
                $amount,
                $user->email,
                $reference,
                $metadata
            );

            if (!isset($paystackResponse['authorization_url'])) {
                return response()->json(['error' => 'Failed to initialize payment'], 400);
            }

            // Create/update payment record
            $payment = Payment::updateOrCreate(
                ['payable_id' => $payable->id, 'payable_type' => get_class($payable)],
                [
                    'user_id'              => $user->id,
                    'amount'               => $amount,
                    'currency'             => 'NGN',
                    'payment_method'       => 'paystack',
                    'status'               => 'pending',
                    'reference'            => $reference,
                    'transaction_id'       => $paystackResponse['reference'] ?? null,
                    'metadata'             => $metadata,
                ]
            );

            return response()->json([
                'status'             => true,
                'payment_id'         => $payment->id,
                'reference'          => $reference,
                'authorization_url'  => $paystackResponse['authorization_url'],
                'access_code'        => $paystackResponse['access_code'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Initialize payment for address registration (backward compatibility)
     */
    public function initializeAddressPayment(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'amount'     => 'required|numeric|min:0.01',
        ]);

        return $this->initializeTransaction($request->merge([
            'type'       => 'address',
            'request_id' => $request->input('address_id'),
        ]));
    }

    /**
     * Verify payment completion
     */
    public function verifyTransaction(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string',
        ]);

        try {
            $reference = $validated['reference'];
            $payment = Payment::where('reference', $reference)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Verify with Paystack
            $transactionData = $this->paystackService->verifyTransaction($reference);

            DB::transaction(function () use ($payment, $transactionData) {
                if ($transactionData['status'] === 'success') {
                    $payment->update([
                        'status'   => 'completed',
                        'paid_at'  => now(),
                        'metadata' => array_merge($payment->metadata ?? [], ['paystack_response' => $transactionData]),
                    ]);

                    // Update the payable model status
                    if ($payment->payable) {
                        $payment->payable->update(['status' => 'completed']);
                    }

                    // Handle backward compatibility for Address model
                    if ($payment->address) {
                        $payment->address->update([
                            'status'           => 'approved',
                            'payment_method'   => 'paystack',
                            'reference_code'   => $reference,
                        ]);
                    }
                } else {
                    $payment->update([
                        'status'   => 'failed',
                        'metadata' => array_merge($payment->metadata ?? [], ['paystack_response' => $transactionData]),
                    ]);
                }
            });

            return response()->json([
                'status'  => true,
                'message' => $payment->status === 'completed' ? 'Payment successful' : 'Payment failed',
                'payment' => $payment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Webhook handler for Paystack
     */
    public function webhook(Request $request)
    {
        $hash = hash_hmac('sha512', $request->getContent(), config('services.paystack.secret_key'));

        if ($hash !== $request->header('X-Paystack-Signature')) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'charge.success') {
            $reference = $data['reference'];
            $payment = Payment::where('reference', $reference)->first();

            if ($payment) {
                DB::transaction(function () use ($payment, $data) {
                    $payment->update([
                        'status'   => 'completed',
                        'paid_at'  => now(),
                        'metadata' => array_merge($payment->metadata ?? [], ['webhook_response' => $data]),
                    ]);

                    // Update payable if exists
                    if ($payment->payable) {
                        $payment->payable->update(['status' => 'completed']);
                    }

                    // Backward compatibility: Update address if exists
                    if ($payment->address) {
                        $payment->address->update([
                            'status'           => 'approved',
                            'payment_method'   => 'paystack',
                            'reference_code'   => $reference,
                        ]);
                    }
                });
            }
        }

        return response()->json(['message' => 'Webhook received']);
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($reference)
    {
        $payment = Payment::where('reference', $reference)
            ->where('user_id', auth()->id())
            ->with('payable')
            ->firstOrFail();

        return response()->json([
            'reference'  => $payment->reference,
            'status'     => $payment->status,
            'amount'     => $payment->amount,
            'currency'   => $payment->currency,
            'paid_at'    => $payment->paid_at,
            'payable'    => $payment->payable,
        ]);
    }

    /**
     * Get the payable model instance
     */
    protected function getPayableModel($type, $id)
    {
        return match ($type) {
            'address'              => Address::find($id),
            'address_indexing'     => AddressIndexingRequest::find($id),
            'street_revalidation'  => StreetRevalidation::find($id),
            default                => null
        };
    }

    /**
     * Generate unique payment reference
     */
    protected function generatePaymentReference($type, $userId)
    {
        $prefix = match ($type) {
            'address'              => 'ADR',
            'address_indexing'     => 'IDX',
            'street_revalidation'  => 'RVL',
            default                => 'PAY'
        };

        return "{$prefix}-{$userId}-" . time() . '-' . uniqid();
    }
}
