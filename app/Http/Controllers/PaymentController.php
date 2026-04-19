<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\AddressIndexingRequest;
use App\Models\Payment;
use App\Models\StreetNumberingPlate;
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

            $payable = $this->getPayableModel($type, $requestId);

            if (!$payable || $payable->user_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $existingPayment = $payable->payment ?? null;
            if ($existingPayment && $existingPayment->status === 'completed') {
                return response()->json(['error' => 'Payment already completed'], 400);
            }

            $reference = $this->generatePaymentReference($type, $user->id);

            $metadata = [
                'type'        => $type,
                'request_id'  => $requestId,
                'user_id'     => $user->id,
                'model_class' => get_class($payable),
            ];

            $paystackResponse = $this->paystackService->initializeTransaction(
                $amount,
                $user->email,
                $reference,
                $metadata,
                route('payment.callback', [], true)
            );

            if (!isset($paystackResponse['authorization_url'])) {
                return response()->json(['error' => 'Failed to initialize payment'], 400);
            }

            $payment = Payment::updateOrCreate(
                ['payable_id' => $payable->id, 'payable_type' => get_class($payable)],
                [
                    'user_id'        => $user->id,
                    'amount'         => $amount,
                    'currency'       => 'NGN',
                    'payment_method' => 'paystack',
                    'status'         => 'pending',
                    'reference'      => $reference,
                    'transaction_id' => $paystackResponse['reference'] ?? null,
                    'metadata'       => $metadata,
                ]
            );

            return response()->json([
                'status'            => true,
                'payment_id'        => $payment->id,
                'reference'         => $reference,
                'authorization_url' => $paystackResponse['authorization_url'],
                'access_code'       => $paystackResponse['access_code'] ?? null,
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

            $transactionData = $this->paystackService->verifyTransaction($reference);
            $this->applyPaystackTransactionResult($payment, $transactionData);

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
     * Handle Paystack redirect callback after user payment
     */
    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('portal.payments.index')
                ->with('error', 'Missing payment reference.');
        }

        $payment = Payment::where('reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('portal.payments.index')
                ->with('error', 'Payment not found.');
        }

        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $transactionData = $this->paystackService->verifyTransaction($reference);
            $this->applyPaystackTransactionResult($payment, $transactionData);

            $message = $payment->status === 'completed'
                ? 'Payment completed successfully.'
                : 'Payment verification completed. The transaction did not succeed.';

            return redirect()->route('portal.payments.show', $payment)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('portal.payments.show', $payment)
                ->with('error', 'Payment verification failed: ' . $e->getMessage());
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
                DB::transaction(function () use ($payment, $data, $reference) {
                    $payment->update([
                        'status'   => 'completed',
                        'paid_at'  => now(),
                        'metadata' => array_merge($payment->metadata ?? [], ['webhook_response' => $data]),
                    ]);

                    if ($payment->payable) {
                        $payment->payable->update(['status' => 'completed']);
                    }

                    if ($payment->address) {
                        $payment->address->update([
                            'status'         => 'approved',
                            'payment_method' => 'paystack',
                            'reference_code' => $reference,
                        ]);
                    }
                });
            }
        }

        return response()->json(['message' => 'Webhook received']);
    }

    /**
     * Apply the Paystack transaction result to the payment
     */
    protected function applyPaystackTransactionResult(Payment $payment, array $transactionData): void
    {
        DB::transaction(function () use ($payment, $transactionData) {
            if ($transactionData['status'] === 'success') {
                $payment->update([
                    'status'   => 'completed',
                    'paid_at'  => now(),
                    'metadata' => array_merge($payment->metadata ?? [], ['paystack_response' => $transactionData]),
                ]);

                if ($payment->payable) {
                    $payment->payable->update(['status' => 'completed']);
                }

                if ($payment->address) {
                    $payment->address->update([
                        'status'         => 'approved',
                        'payment_method' => 'paystack',
                        'reference_code' => $payment->reference,
                    ]);
                }
            } else {
                $payment->update([
                    'status'   => 'failed',
                    'metadata' => array_merge($payment->metadata ?? [], ['paystack_response' => $transactionData]),
                ]);
            }
        });
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
            'reference' => $payment->reference,
            'status'    => $payment->status,
            'amount'    => $payment->amount,
            'currency'  => $payment->currency,
            'paid_at'   => $payment->paid_at,
            'payable'   => $payment->payable,
        ]);
    }

    /**
     * User payment management dashboard
     */
    public function userPayments()
    {
        $payments = Payment::where('user_id', auth()->id())
            ->with('payable')
            ->latest()
            ->paginate(20);

        $stats = [
            'total'     => $payments->total(),
            'pending'   => Payment::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'completed' => Payment::where('user_id', auth()->id())->where('status', 'completed')->count(),
            'failed'    => Payment::where('user_id', auth()->id())->where('status', 'failed')->count(),
        ];

        return view('portal.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show individual payment details
     */
    public function showPayment(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $payment->load('payable');

        return view('portal.payments.show', compact('payment'));
    }

    /**
     * Show payment retry page
     */
    public function showRetry(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($payment->status, ['failed', 'pending'])) {
            return redirect()->route('portal.payments.show', $payment)
                ->with('error', 'This payment cannot be retried.');
        }

        $payment->load('payable');

        return view('portal.payments.retry', compact('payment'));
    }

    /**
     * Process payment retry
     */
    public function processRetry(Payment $payment, Request $request)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($payment->status, ['failed', 'pending'])) {
            return response()->json(['error' => 'This payment cannot be retried.'], 400);
        }

        try {
            $newReference = $this->generatePaymentReference(
                $this->getPaymentType($payment),
                $payment->user_id
            );

            $metadata = [
                'type'        => $this->getPaymentType($payment),
                'request_id'  => $this->getRequestId($payment),
                'user_id'     => $payment->user_id,
                'retry_of'    => $payment->reference,
                'description' => 'Payment Retry',
            ];

            $paystackResponse = $this->paystackService->initializeTransaction(
                $payment->amount,
                $payment->user->email,
                $newReference,
                $metadata,
                route('payment.callback', [], true)
            );

            if (!isset($paystackResponse['authorization_url'])) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Failed to initialize payment retry'], 400);
                }

                return redirect()->route('portal.payments.show', $payment)
                    ->with('error', 'Failed to initialize payment retry.');
            }

            $retryPayment = Payment::create([
                'user_id'        => $payment->user_id,
                'payable_id'     => $payment->payable_id,
                'payable_type'   => $payment->payable_type,
                'amount'         => $payment->amount,
                'currency'       => $payment->currency,
                'payment_method' => $payment->payment_method ?? 'paystack',
                'status'         => 'pending',
                'reference'      => $newReference,
                'transaction_id' => $paystackResponse['reference'] ?? null,
                'metadata'       => $metadata,
            ]);

            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'retry_attempt'      => ($payment->metadata['retry_attempt'] ?? 0) + 1,
                    'retry_reference'    => $newReference,
                    'retry_initiated_at' => now(),
                ]),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success'           => true,
                    'reference'         => $newReference,
                    'authorization_url' => $paystackResponse['authorization_url'],
                    'message'           => 'Payment retry initialized successfully.',
                    'payment_id'        => $retryPayment->id,
                ]);
            }

            return redirect($paystackResponse['authorization_url']);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Failed to retry payment: ' . $e->getMessage(),
                ], 400);
            }

            return redirect()->route('portal.payments.show', $payment)
                ->with('error', 'Failed to retry payment: ' . $e->getMessage());
        }
    }

    /**
     * Upload a proof of payment (optional offline bank transfer receipts).
     */
    public function uploadProof(Payment $payment, Request $request)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        $file = $request->file('proof');

        $path = $file->store('payment_proofs', 'public');

        $proofEntry = [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'uploaded_at' => now()->toDateTimeString(),
            'uploaded_by' => auth()->id(),
        ];

        $metadata = $payment->metadata ?? [];
        $metadata['proofs'] = array_merge($metadata['proofs'] ?? [], [$proofEntry]);

        $payment->update(['metadata' => $metadata]);

        return redirect()->route('portal.payments.show', $payment)
            ->with('success', 'Proof uploaded successfully. We will verify and update the payment status.');
    }

    /**
     * Get the payable model instance
     */
    protected function getPayableModel($type, $id)
    {
        return match ($type) {
            'address'                => Address::find($id),
            'address_indexing'       => AddressIndexingRequest::find($id),
            'street_revalidation'    => StreetRevalidation::find($id),
            'street_numbering_plate' => StreetNumberingPlate::find($id),
            default                  => null,
        };
    }

    /**
     * Generate unique payment reference
     */
    protected function generatePaymentReference($type, $userId)
    {
        $prefix = match ($type) {
            'address'                => 'ADR',
            'address_indexing'       => 'IDX',
            'street_revalidation'    => 'RVL',
            'street_numbering_plate' => 'SNP',
            default                  => 'PAY',
        };

        return "{$prefix}-{$userId}-" . time() . '-' . uniqid();
    }

    /**
     * Get payment type from payment record
     */
    protected function getPaymentType(Payment $payment)
    {
        if ($payment->payable_type) {
            return match ($payment->payable_type) {
                'App\\Models\\AddressIndexingRequest' => 'address_indexing',
                'App\\Models\\StreetRevalidation'    => 'street_revalidation',
                'App\\Models\\StreetNumberingPlate'  => 'street_numbering_plate',
                'App\\Models\\Address'               => 'address',
                default                                 => 'unknown',
            };
        }

        return $payment->metadata['type'] ?? 'unknown';
    }

    /**
     * Get request ID from payment record
     */
    protected function getRequestId(Payment $payment)
    {
        if ($payment->payable) {
            return $payment->payable->id;
        }

        return $payment->metadata['request_id'] ?? null;
    }
}
