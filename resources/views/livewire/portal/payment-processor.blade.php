<div class="payment-processor" x-show="$wire.authorization_url || $wire.error_message || $wire.loading" x-transition>
    @if ($error_message)
        <div class="alert alert-error mb-4">
            <span>{{ $error_message }}</span>
        </div>
    @endif

    @if ($authorization_url)
        <div class="alert alert-info mb-4">
            <p class="font-semibold mb-2">Payment Amount: ₦{{ number_format($amount, 2) }}</p>
            <p class="text-sm mb-4">Reference: {{ $reference }}</p>
            <a href="{{ $authorization_url }}" target="_blank" class="btn btn-primary">
                Proceed to Payment
                <i class="fa-solid fa-arrow-up-right-from-square ml-2"></i>
            </a>
        </div>
    @endif

    @if ($payment_status === 'completed')
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle mr-2"></i>
            <span>Payment received successfully!</span>
        </div>
    @elseif ($payment_status === 'failed')
        <div class="alert alert-error">
            <i class="fa-solid fa-times-circle mr-2"></i>
            <span>Payment could not be verified. Please try again.</span>
        </div>
    @endif

    @if ($loading)
        <div class="flex items-center justify-center py-4">
            <span class="loading loading-spinner loading-md"></span>
        </div>
    @endif
</div>
