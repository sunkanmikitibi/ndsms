<div>
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-credit-card" style="color:var(--accent);margin-right:10px;"></i>My Payments</h2>
            <p>View and manage all your payment transactions</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid" style="margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-list"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['total'] }}</h3>
                <span>Total Payments</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gold"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['pending'] }}</h3>
                <span>Pending</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['completed'] }}</h3>
                <span>Completed</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
                <h3>{{ $stats['failed'] }}</h3>
                <span>Failed</span>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    @if ($payments->isEmpty())
        <div class="empty-state">
            <i class="fas fa-credit-card"></i>
            <h4>No payments yet</h4>
            <p>You haven't made any payments yet.</p>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td style="font-family:'Space Mono',monospace;font-weight:600;">
                                {{ $payment->reference }}
                            </td>
                            <td>
                                <span class="town-badge">
                                    {{ ucwords(str_replace(['_', '-'], ' ', $payment->metadata['type'] ?? 'unknown')) }}
                                </span>
                            </td>
                            <td style="font-weight:600;">
                                ₦{{ number_format($payment->amount, 2) }}
                            </td>
                            <td>
                                <span class="status-badge {{ $payment->status }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td style="color:var(--text-secondary);font-size:13px;">
                                {{ $payment->created_at->format('d M Y H:i') }}
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <a href="{{ route('portal.payments.show', $payment) }}"
                                        class="btn btn-outline btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if (in_array($payment->status, ['failed', 'pending']))
                                        <a href="{{ route('portal.payments.retry', $payment) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-redo"></i> Retry
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {{ $payments->links() }}
    @endif
</div>
