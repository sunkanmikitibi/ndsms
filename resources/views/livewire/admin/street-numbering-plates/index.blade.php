<div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
        <div>
            <h1 class="admin-title">Street Numbering Plates</h1>
            <p class="admin-subtitle">Manage and approve street numbering plate requests</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-list"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Requests</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['pending'] }}</h3>
                <p>Pending Review</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['approved'] }}</h3>
                <p>Approved</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-cog"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['inProduction'] }}</h3>
                <p>In Production</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-box"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['ready'] }}</h3>
                <p>Ready</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fas fa-check-double"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['completed'] }}</h3>
                <p>Completed</p>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3>Filters & Search</h3>
        </div>
        <div class="card-body">
            <div class="filter-grid">
                <div class="form-group">
                    <label>Search (Reference, Street, Ward)</label>
                    <input type="text" wire:model.live="search" placeholder="e.g., PLATE-ABC123 or Ikeja Road"
                        class="form-control">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select wire:model.live="filterStatus" class="form-control">
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Ward</label>
                    <input type="text" wire:model.live="filterWard" placeholder="e.g., Lagos Island"
                        class="form-control">
                </div>

                <div class="form-group">
                    <label>Plate Type</label>
                    <select wire:model.live="filterType" class="form-control">
                        <option value="">All Types</option>
                        @foreach ($plateTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Per Page</label>
                    <select wire:model.live="perPage" class="form-control">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button wire:click="resetFilters" class="btn btn-outline">
                        <i class="fas fa-undo"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3>Requests ({{ $requests->total() }})</h3>
            <span class="text-sm text-gray-500">Page {{ $requests->currentPage() }} of
                {{ $requests->lastPage() }}</span>
        </div>
        <div class="card-body">
            @if ($requests->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>No requests found</h4>
                    <p>Try adjusting your filters</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>
                                    <button wire:click="toggleSort('reference_number')" class="sort-button">
                                        Reference
                                        @if ($sortBy === 'reference_number')
                                            <i class="fas fa-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th>Street</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>
                                    <button wire:click="toggleSort('status')" class="sort-button">
                                        Status
                                        @if ($sortBy === 'status')
                                            <i class="fas fa-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th>Amount</th>
                                <th>
                                    <button wire:click="toggleSort('created_at')" class="sort-button">
                                        Date
                                        @if ($sortBy === 'created_at')
                                            <i class="fas fa-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $req)
                                <tr>
                                    <td class="font-mono text-sm font-bold">{{ $req->reference_number }}</td>
                                    <td>
                                        <div class="font-semibold">{{ $req->street_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $req->ward }}</div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-info">{{ $plateTypes[$req->plate_type] ?? 'Unknown' }}</span>
                                    </td>
                                    <td class="text-center font-semibold">{{ $req->quantity_requested }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'in_production' => 'info',
                                                'ready' => 'primary',
                                                'delivered' => 'secondary',
                                                'installed' => 'success',
                                                'completed' => 'success',
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$req->status] ?? 'secondary' }}">
                                            {{ $statuses[$req->status] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <span
                                            class="font-semibold">₦{{ number_format($req->getTotalCost(), 2) }}</span>
                                    </td>
                                    <td class="text-sm">{{ $req->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button wire:click="viewDetail({{ $req->id }})"
                                                class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if ($req->status === 'pending')
                                                <button wire:click="openApproveModal({{ $req->id }})"
                                                    class="btn btn-sm btn-success" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button wire:click="openRejectModal({{ $req->id }})"
                                                    class="btn btn-sm btn-danger" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Detail Modal -->
    @if ($showDetail && $selectedRequest)
        <div class="modal-overlay" @click="$wire.closeDetail()">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h2>Request Details</h2>
                    <button type="button" wire:click="closeDetail" class="btn-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Reference & Status -->
                    <div class="detail-section">
                        <h3>Request Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Reference Number</span>
                                <span class="detail-value font-mono">{{ $selectedRequest->reference_number }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <span class="detail-value">
                                    <span
                                        class="badge badge-{{ $statusColors[$selectedRequest->status] ?? 'secondary' }}">
                                        {{ $statuses[$selectedRequest->status] ?? 'Unknown' }}
                                    </span>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Submitted</span>
                                <span
                                    class="detail-value">{{ $selectedRequest->created_at->format('F j, Y \a\t H:i') }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Requester</span>
                                <span class="detail-value">{{ $selectedRequest->user->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Street Information -->
                    <div class="detail-section">
                        <h3>Street Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Street Name</span>
                                <span class="detail-value">{{ $selectedRequest->street_name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Ward</span>
                                <span class="detail-value">{{ $selectedRequest->ward }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Plate Specifications -->
                    <div class="detail-section">
                        <h3>Plate Specifications</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Quantity</span>
                                <span class="detail-value">{{ $selectedRequest->quantity_requested }} plates</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Plate Type</span>
                                <span class="detail-value">{{ $selectedRequest->getPlateTypeLabel() }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Material</span>
                                <span class="detail-value">{{ $selectedRequest->getMaterialLabel() }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Design Variant</span>
                                <span class="detail-value">{{ $selectedRequest->design_variant ?? 'Default' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Installation Details -->
                    <div class="detail-section">
                        <h3>Installation & Delivery</h3>
                        <div class="detail-grid">
                            <div class="detail-item" style="grid-column: 1/-1;">
                                <span class="detail-label">Delivery Address</span>
                                <span class="detail-value">{{ $selectedRequest->delivery_address }}</span>
                            </div>
                            @if ($selectedRequest->installation_address)
                                <div class="detail-item" style="grid-column: 1/-1;">
                                    <span class="detail-label">Installation Address</span>
                                    <span class="detail-value">{{ $selectedRequest->installation_address }}</span>
                                </div>
                            @endif
                            @if ($selectedRequest->installation_date_requested)
                                <div class="detail-item">
                                    <span class="detail-label">Requested Installation Date</span>
                                    <span
                                        class="detail-value">{{ $selectedRequest->installation_date_requested->format('F j, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Cost Information -->
                    <div class="detail-section">
                        <h3>Cost Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Estimated Total</span>
                                <span class="detail-value text-lg font-bold" style="color: #059669;">
                                    ₦{{ number_format($selectedRequest->getTotalCost(), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    @if ($selectedRequest->admin_notes)
                        <div class="detail-section">
                            <h3>Admin Notes</h3>
                            <div class="note-box">{{ $selectedRequest->admin_notes }}</div>
                        </div>
                    @endif

                    <!-- Rejection Reason -->
                    @if ($selectedRequest->rejection_reason)
                        <div class="detail-section" style="border-left: 3px solid #dc2626;">
                            <h3>Rejection Reason</h3>
                            <div class="note-box" style="background-color: #fee2e2;">
                                {{ $selectedRequest->rejection_reason }}</div>
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" wire:click="closeDetail" class="btn btn-outline">Close</button>
                    @if ($selectedRequest->status === 'pending')
                        <button type="button" wire:click="openApproveModal({{ $selectedRequest->id }})"
                            class="btn btn-success">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button type="button" wire:click="openRejectModal({{ $selectedRequest->id }})"
                            class="btn btn-danger">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Approve Modal -->
    @if ($showApproveModal && $selectedRequest)
        <div class="modal-overlay" @click="$wire.set('showApproveModal', false)">
            <div class="modal-content" @click.stop style="max-width: 500px;">
                <div class="modal-header">
                    <h2>Approve Request?</h2>
                    <button type="button" wire:click="$set('showApproveModal', false)" class="btn-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="mb-4">
                        You are about to approve this numbering plate request:
                    </p>
                    <div class="info-box">
                        <p><strong>Reference:</strong> {{ $selectedRequest->reference_number }}</p>
                        <p><strong>Street:</strong> {{ $selectedRequest->street_name }}</p>
                        <p><strong>Quantity:</strong> {{ $selectedRequest->quantity_requested }} plates</p>
                        <p><strong>Amount:</strong> ₦{{ number_format($selectedRequest->getTotalCost(), 2) }}</p>
                    </div>

                    <div class="form-group">
                        <label>Approval Notes (Optional)</label>
                        <textarea wire:model="approvalNotes" rows="3" placeholder="Add any notes about this approval..."
                            class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" wire:click="$set('showApproveModal', false)"
                        class="btn btn-outline">Cancel</button>
                    <button type="button" wire:click="approve" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve Request
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if ($showRejectModal && $selectedRequest)
        <div class="modal-overlay" @click="$wire.set('showRejectModal', false)">
            <div class="modal-content" @click.stop style="max-width: 500px;">
                <div class="modal-header">
                    <h2>Reject Request?</h2>
                    <button type="button" wire:click="$set('showRejectModal', false)" class="btn-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="mb-4">
                        You are about to reject this numbering plate request. A reason is required.
                    </p>
                    <div class="info-box">
                        <p><strong>Reference:</strong> {{ $selectedRequest->reference_number }}</p>
                        <p><strong>Street:</strong> {{ $selectedRequest->street_name }}</p>
                    </div>

                    <div class="form-group">
                        <label>Rejection Reason *</label>
                        <textarea wire:model="rejectionReason" rows="4"
                            placeholder="Please explain why this request is being rejected..." class="form-control" required></textarea>
                        @if (!$rejectionReason)
                            <span style="color: #dc2626; font-size: 12px;">Rejection reason is required</span>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" wire:click="$set('showRejectModal', false)"
                        class="btn btn-outline">Cancel</button>
                    <button type="button" wire:click="reject" class="btn btn-danger"
                        {{ !$rejectionReason ? 'disabled' : '' }}>
                        <i class="fas fa-times"></i> Reject Request
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .admin-container {
        padding: 24px;
    }

    .admin-header {
        margin-bottom: 24px;
    }

    .admin-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .admin-subtitle {
        color: #6b7280;
        font-size: 14px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    .stat-icon.blue {
        background-color: #3b82f6;
    }

    .stat-icon.yellow {
        background-color: #f59e0b;
    }

    .stat-icon.green {
        background-color: #10b981;
    }

    .stat-icon.purple {
        background-color: #8b5cf6;
    }

    .stat-icon.orange {
        background-color: #f97316;
    }

    .stat-icon.teal {
        background-color: #06b6d4;
    }

    .stat-content h3 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .stat-content p {
        font-size: 12px;
        color: #6b7280;
    }

    .card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .card-body {
        padding: 16px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .form-group label {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }

    .form-control {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-outline {
        background: white;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    .btn-outline:hover {
        background: #f9fafb;
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #059669;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .btn-info {
        background: #3b82f6;
        color: white;
    }

    .btn-info:hover {
        background: #2563eb;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table thead tr {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .admin-table th {
        padding: 12px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }

    .admin-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }

    .admin-table tbody tr:hover {
        background: #f9fafb;
    }

    .sort-button {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        color: #374151;
        font-weight: 600;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .sort-button:hover {
        color: #3b82f6;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-primary {
        background: #ede9fe;
        color: #5b21b6;
    }

    .badge-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .action-buttons {
        display: flex;
        gap: 4px;
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state h4 {
        margin: 0 0 8px 0;
        color: #374151;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        gap: 4px;
        margin-top: 16px;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: white;
        border-radius: 8px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #6b7280;
    }

    .modal-body {
        padding: 20px;
    }

    .detail-section {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .detail-section:last-child {
        border-bottom: none;
    }

    .detail-section h3 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-label {
        font-size: 12px;
        color: #6b7280;
        font-weight: 600;
    }

    .detail-value {
        font-size: 14px;
        color: #1f2937;
    }

    .info-box {
        background: #f0f9ff;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .info-box p {
        margin: 6px 0;
    }

    .note-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 12px;
        font-size: 14px;
        line-height: 1.6;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 16px 20px;
        border-top: 1px solid #e5e7eb;
    }
</style>
