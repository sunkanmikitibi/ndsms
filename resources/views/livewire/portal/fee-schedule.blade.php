<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-receipt" style="color:var(--accent);margin-right:10px;"></i>Fee Schedule</h2>
            <p>Official fee schedule for NDSMS services</p>
        </div>
    </div>

    @if ($fees->isEmpty())
        <div
            style="margin-top:20px;padding:30px 20px;background:var(--bg-secondary);border-radius:var(--radius-sm);text-align:center;">
            <i class="fas fa-info-circle"
                style="font-size:28px;color:var(--accent);margin-bottom:10px;display:block;"></i>
            <h4>No Active Fees</h4>
            <p style="color:var(--text-secondary);margin-top:8px;">Fee schedules will be available soon.</p>
        </div>
    @else
        <div class="fee-grid">
            @foreach ($groupedFees as $serviceType => $serviceFeess)
                <!-- Service Type Card -->
                <div class="fee-card">
                    <div class="fee-card-header">
                        <i
                            class="fas fa-{{ app(\App\Services\FeeService::class)->getIconForServiceType($serviceType) }}"></i>
                        <h4>{{ $services[$serviceType] ?? ucfirst(str_replace('_', ' ', $serviceType)) }}</h4>
                    </div>

                    @foreach ($serviceFeess as $fee)
                        <div class="fee-item">
                            <div style="flex:1;">
                                <div style="font-weight:500;">{{ $fee->service_name }}</div>
                                @if ($fee->description)
                                    <div style="font-size:11px;color:var(--text-secondary);margin-top:2px;">
                                        {{ $fee->description }}</div>
                                @endif
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                @if (!$fee->isActive())
                                    <span
                                        style="font-size:11px;color:var(--warning);font-weight:600;text-transform:uppercase;">Inactive</span>
                                @endif
                                <span class="fee-amount"
                                    style="color:var(--accent);font-weight:700;">{{ $fee->getFormattedAmount() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        @if ($fees->contains(fn($f) => !$f->isActive()))
            <div
                style="margin-top:20px;padding:16px 20px;background:var(--warning-light);border:1px solid var(--warning);border-radius:var(--radius-sm);font-size:13px;color:var(--warning);font-weight:600;">
                <i class="fas fa-info-circle" style="margin-right:8px;"></i>
                Some fee schedules are currently inactive. Contact support for details.
            </div>
        @endif

        <div
            style="margin-top:20px;padding:16px 20px;background:var(--accent-gold-light);border:1px solid var(--accent-gold);border-radius:var(--radius-sm);font-size:13px;color:var(--accent-gold);font-weight:600;">
            <i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i>
            All fees shown are current and subject to change. Prices are in Nigerian Naira (₦). Payment is made via the
            NDSMS payment portal.
        </div>
    @endif
</div>
