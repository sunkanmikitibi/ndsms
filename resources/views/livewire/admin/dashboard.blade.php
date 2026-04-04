<div>
<div class="page-header">
    <div>
        <h2><i class="fas fa-chart-pie" style="color:var(--accent);margin-right:10px;"></i>Dashboard</h2>
        <p>Welcome back, {{ auth()->user()->name }} — NDSMS Admin Overview</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <span style="font-size:12px;color:var(--text-secondary);">{{ now()->format('D, d M Y') }}</span>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-map-marker-alt"></i></div>
        <div class="stat-info">
            <h3>{{ number_format($stats['total_addresses']) }}</h3>
            <span>Total Addresses</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-road"></i></div>
        <div class="stat-info">
            <h3>{{ number_format($stats['total_streets']) }}</h3>
            <span>Registered Streets</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-clipboard-list"></i></div>
        <div class="stat-info">
            <h3>{{ number_format($stats['pending_approvals']) }}</h3>
            <span>Pending Approvals</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3>{{ number_format($stats['total_users']) }}</h3>
            <span>System Users</span>
        </div>
    </div>
</div>

<!-- Recent Applications -->
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="font-size:16px;font-weight:700;">Recent Applications</h3>
        <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline btn-sm">View All</a>
    </div>
    @if($recent_applications->isEmpty())
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h4>No applications yet</h4>
            <p>Street applications will appear here once submitted.</p>
        </div>
    @else
    <div class="table-wrapper" style="margin-top:0;border:none;">
        <table>
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Street Name</th>
                    <th>Type</th>
                    <th>Ward</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_applications as $app)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $app->user?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--text-secondary);">{{ $app->user?->email }}</div>
                    </td>
                    <td style="font-weight:600;">{{ $app->street_name }}</td>
                    <td><span class="ward-badge">{{ ucfirst($app->type ?? 'street') }}</span></td>
                    <td>{{ $app->ward }}</td>
                    <td><span class="status-badge {{ $app->status }}">{{ str_replace('_', ' ', $app->status) }}</span></td>
                    <td style="color:var(--text-secondary);font-size:12px;">{{ $app->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline btn-sm btn-edit">Review</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
</div>
