<div>
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-credit-card" style="color:var(--accent);margin-right:10px;"></i>Payment Details</h2>
            <p>Reference: <code>{{ $payment->reference }}</code></p>
        </div>
        <div>
            <a href="{{ route('portal.payments.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Payments
            </a>
        </div>
    </div>

    <!-- Payment Status Card -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
            <h3>Payment Status</h3>
        </div>
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
                <span class="status-badge {{ $payment->status }} large">
                    {{ ucfirst($payment->status) }}
                </span>
                @if ($payment->paid_at)
                    <span style="color:var(--text-secondary);">
                        <i class="fas fa-calendar-check"></i>
                        Paid on {{ $payment->paid_at->format('d M Y H:i') }}
                    </span>
                @endif
            </div>

            @if (in_array($payment->status, ['failed', 'pending']))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Payment Issue:</strong> This payment {{ $payment->status }}.
                    @if ($payment->status === 'failed')
                        You can retry this payment below.
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Payment Details -->
    <div class="grid grid-2">
        <div class="card">
            <div class="card-header">
                <h3>Payment Information</h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Reference</label>
                        <span style="font-family:'Space Mono',monospace;">{{ $payment->reference }}</span>
                    </div>
                    <div class="info-item">
                        <label>Amount</label>
                        <span style="font-weight:600;font-size:18px;">₦{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="info-item">
                        <label>Currency</label>
                        <span>{{ strtoupper($payment->currency) }}</span>
                    </div>
                    <div class="info-item">
                        <label>Payment Method</label>
                        <span>{{ $payment->payment_method ?? 'Paystack' }}</span>
                    </div>
                    <div class="info-item">
                        <label>Created</label>
                        <span>{{ $payment->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <label>Last Updated</label>
                        <span>{{ $payment->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Request Details</h3>
            </div>
            <div class="card-body">
                @if ($payment->payable)
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Type</label>
                            <span>{{ ucwords(str_replace(['_', '-'], ' ', $payment->metadata['type'] ?? 'unknown')) }}</span>
                        </div>
                        <div class="info-item">
                            <label>Request ID</label>
                            <span>{{ $payment->payable->id }}</span>
                        </div>
                        <div class="info-item">
                            <label>Status</label>
                            <span class="status-badge {{ $payment->payable->status ?? 'unknown' }}">
                                {{ ucfirst($payment->payable->status ?? 'unknown') }}
                            </span>
                        </div>
                        @if (isset($payment->payable->created_at))
                            <div class="info-item">
                                <label>Request Date</label>
                                <span>{{ $payment->payable->created_at->format('d M Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <p>Request details not available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Retry Section -->
    @if (in_array($payment->status, ['failed', 'pending']))
        <div class="card" style="margin-top:24px;">
            <div class="card-header">
                <h3>Retry Payment</h3>
            </div>
            <div class="card-body">
                <p>If your payment failed or is still pending, you can retry it here.</p>
                <a href="{{ route('portal.payments.retry', $payment) }}" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Retry Payment
                </a>
            </div>
        </div>
    @endif

    <!-- Metadata (if available) -->
    @if ($payment->metadata && count($payment->metadata) > 0)
        <div class="card" style="margin-top:24px;">
            <div class="card-header">
                <h3>Additional Information</h3>
            </div>
            <div class="card-body">
                <pre style="background:var(--bg-secondary);padding:16px;border-radius:8px;overflow-x:auto;font-size:12px;">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif
</div>
