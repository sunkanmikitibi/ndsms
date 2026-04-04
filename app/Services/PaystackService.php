<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class PaystackService
{
    protected string $baseUrl = 'https://api.paystack.co';

    protected string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
    }

    /**
     * Initialize a Paystack transaction
     */
    public function initializeTransaction(
        float $amount,
        string $email,
        string $reference = '',
        array $metadata = []
    ): array {
        $payload = [
            'amount' => intval($amount * 100), // Convert to kobo
            'email' => $email,
            'reference' => $reference ?: uniqid('txn_'),
            'metadata' => $metadata,
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/transaction/initialize", $payload);

        if (! $response->successful()) {
            throw new Exception('Failed to initialize Paystack transaction: '.$response->body());
        }

        return $response->json()['data'];
    }

    /**
     * Verify a Paystack transaction
     */
    public function verifyTransaction(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
        ])->get("{$this->baseUrl}/transaction/verify/{$reference}");

        if (! $response->successful()) {
            throw new Exception('Failed to verify Paystack transaction: '.$response->body());
        }

        return $response->json()['data'];
    }

    /**
     * Get transaction details
     */
    public function getTransaction(int $id): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
        ])->get("{$this->baseUrl}/transaction/{$id}");

        if (! $response->successful()) {
            throw new Exception('Failed to fetch transaction: '.$response->body());
        }

        return $response->json()['data'];
    }

    /**
     * List all transactions
     */
    public function listTransactions(int $perPage = 50, int $page = 1): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
        ])->get("{$this->baseUrl}/transaction", [
            'perPage' => $perPage,
            'page' => $page,
        ]);

        if (! $response->successful()) {
            throw new Exception('Failed to list transactions: '.$response->body());
        }

        return $response->json()['data'];
    }

    /**
     * Create a subscription
     */
    public function createSubscription(
        string $customer,
        string $plan,
        string $authorization = ''
    ): array {
        $payload = [
            'customer' => $customer,
            'plan' => $plan,
            'authorization' => $authorization,
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/subscription", $payload);

        if (! $response->successful()) {
            throw new Exception('Failed to create subscription: '.$response->body());
        }

        return $response->json()['data'];
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(string $reference): bool
    {
        try {
            $data = $this->verifyTransaction($reference);

            return $data['status'] === 'success';
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get customer by code
     */
    public function getCustomer(string $code): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
        ])->get("{$this->baseUrl}/customer/{$code}");

        if (! $response->successful()) {
            throw new Exception('Failed to fetch customer: '.$response->body());
        }

        return $response->json()['data'];
    }
}
