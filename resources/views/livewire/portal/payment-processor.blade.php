<div class="payment-processor-wrapper" x-data="{
    showPayment: false
}" 
x-init="
    $watch('$wire.authorization_url', v => { if(v) showPayment = true });
    $watch('$wire.error_message', v => { if(v) showPayment = true });
    $watch('$wire.loading', v => { if(v) showPayment = true });
    
    // Initial state check
    if($wire.authorization_url || $wire.error_message || $wire.loading) {
        showPayment = true;
    }
"
x-show="showPayment"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
    x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full">

    <!-- Backdrop Overlay -->
    <div class="payment-processor-overlay" @click="showPayment = false" x-show="showPayment"></div>

    <!-- Payment Panel -->
    <div class="payment-processor">
        <!-- Close Button -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0;">Payment</h3>
            <button @click="showPayment = false" class="close-button"
                style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-secondary); padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        @if ($error_message)
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $error_message }}</span>
            </div>
        @endif

        @if ($authorization_url)
            <div class="alert alert-info">
                <div style="flex: 1;">
                    <p class="font-semibold" style="font-weight: 700; margin: 0;">Payment Amount:
                        ₦{{ number_format($amount, 2) }}</p>
                    <p style="font-size: 13px; margin: 8px 0 0 0; opacity: 0.9;">Reference: <code
                            style="background: rgba(0,0,0,0.05); padding: 2px 6px; border-radius: 4px; font-family: 'Space Mono', monospace; font-size: 12px;">{{ $reference }}</code>
                    </p>
                </div>
            </div>
            <a href="{{ $authorization_url }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-lock"></i> Proceed to Payment
                <i class="fas fa-arrow-up-right-from-square"></i>
            </a>
        @endif

        @if ($payment_status === 'completed')
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>Payment received successfully!</span>
            </div>
        @elseif ($payment_status === 'failed')
            <div class="alert alert-error">
                <i class="fas fa-times-circle"></i>
                <span>Payment could not be verified. Please try again.</span>
            </div>
        @endif

        @if ($loading)
            <div
                style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 32px 0; gap: 16px;">
                <div class="loading"></div>
                <p style="color: var(--text-secondary); font-size: 14px;">Processing payment...</p>
            </div>
        @endif
    </div>
</div>
