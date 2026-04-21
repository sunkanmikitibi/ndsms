<x-layouts.portal title="Retry Payment">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-redo" style="color:var(--accent);margin-right:10px;"></i>Retry Payment</h2>
            <p>Reference: <code>{{ $payment->reference }}</code></p>
        </div>
        <div>
            <a href="{{ route('portal.payments.show', $payment) }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Payment
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom:24px;">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Payment Summary -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
            <h3>Payment Summary</h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Amount</label>
                    <span
                        style="font-weight:600;font-size:24px;color:var(--accent);">₦{{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="info-item">
                    <label>Type</label>
                    <span>{{ ucwords(str_replace(['_', '-'], ' ', $payment->metadata['type'] ?? 'unknown')) }}</span>
                </div>
                <div class="info-item">
                    <label>Status</label>
                    <span class="status-badge {{ $payment->status }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
                <div class="info-item">
                    <label>Original Reference</label>
                    <span style="font-family:'Space Mono',monospace;">{{ $payment->reference }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Retry Form -->
    <div class="card">
        <div class="card-header">
            <h3>Confirm Payment Retry</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Note:</strong> Retrying this payment will create a new transaction with Paystack.
                You will be redirected to complete the payment securely.
            </div>

            <form id="retryForm" method="POST" action="{{ route('portal.payments.retry.process', $payment) }}">
                @csrf

                <div style="margin-bottom:24px;">
                    <h4>What happens next?</h4>
                    <ul style="color:var(--text-secondary);line-height:1.6;">
                        <li>You will be redirected to Paystack's secure payment page</li>
                        <li>Complete your payment using your preferred payment method</li>
                        <li>You will be redirected back to the portal after payment</li>
                        <li>Your payment status will be updated automatically</li>
                    </ul>
                </div>

                <div style="display:flex;gap:12px;">
                    <button type="submit" class="btn btn-primary" id="retryBtn">
                        <i class="fas fa-credit-card"></i>
                        Proceed to Payment
                    </button>
                    <a href="{{ route('portal.payments.show', $payment) }}" class="btn btn-outline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('retryForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('retryBtn');
            const originalText = btn.innerHTML;

            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            // Submit form
            this.submit();
        });
    </script>
    @endpush
</x-layouts.portal>
