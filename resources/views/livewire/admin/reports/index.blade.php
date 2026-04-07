<div>
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-chart-bar" style="color:var(--accent);margin-right:10px;"></i>Reports & Analytics</h2>
            <p>System-wide statistics and insights</p>
        </div>
    </div>

    <!-- Toolbar with Date Filter -->
    <div class="toolbar">
        <div>
            <label
                style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-right:8px;">Filter
                Period:</label>
            <select wire:model.live="filterDateRange"
                style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);color:var(--text-primary);font-family:'Outfit',sans-serif;font-size:14px;">
                <option value="7days">Last 7 Days</option>
                <option value="30days">Last 30 Days</option>
                <option value="90days">Last 90 Days</option>
                <option value="all">All Time</option>
            </select>
        </div>
    </div>

    <!-- Main Analytics Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));margin-bottom:28px;">
        <!-- Total Users -->
        <div class="stat-card">
            <div style="flex:1;">
                <div
                    style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:8px;">
                    Total Users</div>
                <div style="font-size:32px;font-weight:700;color:var(--accent);">{{ $analytics['total_users'] }}</div>
            </div>
            <i class="fas fa-users" style="font-size:28px;color:var(--accent);opacity:0.3;"></i>
        </div>

        <!-- Total Streets -->
        <div class="stat-card">
            <div style="flex:1;">
                <div
                    style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:8px;">
                    Total Streets</div>
                <div style="font-size:32px;font-weight:700;color:var(--accent);">{{ $analytics['total_streets'] }}</div>
                <div style="font-size:12px;color:var(--text-secondary);margin-top:4px;">
                    {{ $analytics['recent_streets'] }} recent</div>
            </div>
            <i class="fas fa-road" style="font-size:28px;color:var(--accent);opacity:0.3;"></i>
        </div>

        <!-- Total Addresses -->
        <div class="stat-card">
            <div style="flex:1;">
                <div
                    style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);margin-bottom:8px;">
                    Total Addresses</div>
                <div style="font-size:32px;font-weight:700;color:var(--accent);">{{ $analytics['total_addresses'] }}
                </div>
                <div style="font-size:12px;color:var(--text-secondary);margin-top:4px;">
                    {{ $analytics['recent_addresses'] }} recent</div>
            </div>
            <i class="fas fa-home" style="font-size:28px;color:var(--accent);opacity:0.3;"></i>
        </div>

        <!-- Total Revenue -->
        <div class="stat-card" style="background:var(--accent-light);border-color:var(--accent);">
            <div style="flex:1;">
                <div
                    style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--accent);margin-bottom:8px;">
                    Total Revenue</div>
                <div style="font-size:32px;font-weight:700;color:var(--accent);">
                    ₦{{ number_format($analytics['total_revenue'], 0) }}</div>
            </div>
            <i class="fas fa-credit-card" style="font-size:28px;color:var(--accent);opacity:0.2;"></i>
        </div>
    </div>

    <!-- Application Status Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));margin-bottom:28px;">
        <!-- Pending Approvals -->
        <div class="stat-card" style="background:var(--info-light);border-color:var(--info);">
            <div style="flex:1;">
                <div
                    style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--info);margin-bottom:8px;">
                    Pending</div>
                <div style="font-size:32px;font-weight:700;color:var(--info);">{{ $analytics['pending_approvals'] }}
                </div>
                <div style="font-size:11px;color:var(--info);margin-top:4px;">Approvals</div>
            </div>
            <i class="fas fa-hourglass-end" style="font-size:28px;color:var(--info);opacity:0.2;"></i>
        </div>

        <!-- Approved Applications -->
        <div class="stat-card" style="background:var(--accent-light);border-color:var(--accent);">
            <div style="flex:1;">
                <div
                    style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--accent);margin-bottom:8px;">
                    Approved</div>
                <div style="font-size:32px;font-weight:700;color:var(--accent);">
                    {{ $analytics['approved_applications'] }}</div>
                <div style="font-size:11px;color:var(--accent);margin-top:4px;">Applications</div>
            </div>
            <i class="fas fa-check-circle" style="font-size:28px;color:var(--accent);opacity:0.2;"></i>
        </div>

        <!-- Recent Applications -->
        <div class="stat-card" style="background:var(--accent-gold-light);border-color:var(--accent-gold);">
            <div style="flex:1;">
                <div
                    style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--accent-gold);margin-bottom:8px;">
                    Recent</div>
                <div style="font-size:32px;font-weight:700;color:var(--accent-gold);">
                    {{ $analytics['recent_applications'] }}</div>
                <div style="font-size:11px;color:var(--accent-gold);margin-top:4px;">Applications</div>
            </div>
            <i class="fas fa-inbox" style="font-size:28px;color:var(--accent-gold);opacity:0.2;"></i>
        </div>
    </div>

    <!-- Payment Stats & Export -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:20px;margin-bottom:28px;">
        <!-- Payment Summary -->
        <div class="card">
            <h3 style="font-size:16px;font-weight:700;margin-bottom:16px;color:var(--text-primary);">
                <i class="fas fa-credit-card" style="margin-right:8px;color:var(--accent);"></i>Payment Summary
            </h3>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div
                    style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-secondary);font-size:14px;">Total Transactions</span>
                    <span
                        style="font-weight:700;color:var(--text-primary);">{{ $paymentData['total_transactions'] }}</span>
                </div>
                <div
                    style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-secondary);font-size:14px;">Successful</span>
                    <span style="font-weight:700;color:var(--accent);">{{ $paymentData['successful'] }}
                        (₦{{ number_format($paymentData['successful_amount'], 0) }})</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:var(--text-secondary);font-size:14px;">Failed</span>
                    <span style="font-weight:700;color:var(--danger);">{{ $paymentData['failed'] }}</span>
                </div>
            </div>
        </div>

        <!-- Export Reports -->
        <div class="card">
            <h3 style="font-size:16px;font-weight:700;margin-bottom:16px;color:var(--text-primary);">
                <i class="fas fa-download" style="margin-right:8px;color:var(--accent);"></i>Export Reports
            </h3>
            <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:8px;">
                <button wire:click="exportReport('streets')" class="btn btn-primary" style="font-size:13px;">
                    <i class="fas fa-road"></i> Streets
                </button>
                <button wire:click="exportReport('addresses')" class="btn btn-primary" style="font-size:13px;">
                    <i class="fas fa-home"></i> Addresses
                </button>
                <button wire:click="exportReport('applications')" class="btn btn-primary" style="font-size:13px;">
                    <i class="fas fa-file"></i> Applications
                </button>
                <button wire:click="exportReport('payments')" class="btn btn-primary" style="font-size:13px;">
                    <i class="fas fa-credit-card"></i> Payments
                </button>
            </div>
        </div>
    </div>

    <!-- Data Distribution Cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:20px;margin-bottom:28px;">
        <!-- Streets by Type -->
        <div class="card">
            <h3 style="font-size:16px;font-weight:700;margin-bottom:16px;color:var(--text-primary);">
                <i class="fas fa-road" style="margin-right:8px;color:var(--accent);"></i>Streets by Type
            </h3>
            @if ($streetData['by_type']->count() > 0)
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @foreach ($streetData['by_type'] as $item)
                        <div
                            style="display:flex;justify-content:space-between;align-items:center;padding-bottom:8px;border-bottom:1px solid var(--border);">
                            <span
                                style="color:var(--text-secondary);font-size:14px;text-transform:capitalize;">{{ $item->type }}</span>
                            <span
                                style="font-weight:700;background:var(--accent-light);color:var(--accent);padding:4px 12px;border-radius:var(--radius-sm);font-size:13px;">{{ $item->count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color:var(--text-secondary);text-align:center;padding:20px 0;">No street data available</p>
            @endif
        </div>

        <!-- Addresses by Status -->
        <div class="card">
            <h3 style="font-size:16px;font-weight:700;margin-bottom:16px;color:var(--text-primary);">
                <i class="fas fa-home" style="margin-right:8px;color:var(--accent);"></i>Addresses by Status
            </h3>
            @if ($addressData['by_status']->count() > 0)
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @foreach ($addressData['by_status'] as $item)
                        <div
                            style="display:flex;justify-content:space-between;align-items:center;padding-bottom:8px;border-bottom:1px solid var(--border);">
                            <span
                                style="color:var(--text-secondary);font-size:14px;text-transform:capitalize;">{{ $item->status }}</span>
                            <span
                                style="font-weight:700;background:var(--accent-light);color:var(--accent);padding:4px 12px;border-radius:var(--radius-sm);font-size:13px;">{{ $item->count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color:var(--text-secondary);text-align:center;padding:20px 0;">No address data available</p>
            @endif
        </div>
    </div>

    <!-- Application Status Breakdown -->
    <div class="card">
        <h3 style="font-size:16px;font-weight:700;margin-bottom:20px;color:var(--text-primary);">
            <i class="fas fa-chart-pie" style="margin-right:8px;color:var(--accent);"></i>Application Status Breakdown
        </h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));gap:16px;">
            <div
                style="text-align:center;padding:16px;background:var(--info-light);border-radius:var(--radius-sm);border:1px solid var(--info);">
                <div style="font-size:28px;font-weight:700;color:var(--info);margin-bottom:8px;">
                    {{ $applicationData['total'] }}</div>
                <div
                    style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--info);">
                    Total</div>
            </div>
            <div
                style="text-align:center;padding:16px;background:var(--accent-gold-light);border-radius:var(--radius-sm);border:1px solid var(--accent-gold);">
                <div style="font-size:28px;font-weight:700;color:var(--accent-gold);margin-bottom:8px;">
                    {{ $applicationData['pending'] }}</div>
                <div
                    style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--accent-gold);">
                    Pending</div>
            </div>
            <div
                style="text-align:center;padding:16px;background:var(--accent-light);border-radius:var(--radius-sm);border:1px solid var(--accent);">
                <div style="font-size:28px;font-weight:700;color:var(--accent);margin-bottom:8px;">
                    {{ $applicationData['approved'] }}</div>
                <div
                    style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--accent);">
                    Approved</div>
            </div>
        </div>
    </div>
</div>
