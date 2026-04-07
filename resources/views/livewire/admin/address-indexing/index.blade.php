<div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
        <div>
            <h1 class="admin-title">Address Indexing Requests</h1>
            <p class="admin-subtitle">Review and manage address indexing applications</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-map-pin"></i></div>
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
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['rejected'] }}</h3>
                <p>Rejected</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-check-double"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['verified'] }}</h3>
                <p>Verified</p>
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
                    <label>Search (Address, House #, Owner, Applicant)</label>
                    <input type="text" wire:model.live="search" placeholder="e.g., Ikeja Road or 123"
                        class="form-control">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select wire:model.live="filterStatus" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="verified">Verified</option>
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
                                    <button wire:click="toggleSort('address_line')" class="sort-button">
                                        Address
                                        @if ($sortBy === 'address_line')
                                            <i class="fas fa-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th>House #</th>
                                <th>Owner</th>
                                <th>
                                    <button wire:click="toggleSort('status')" class="sort-button">
                                        Status
                                        @if ($sortBy === 'status')
                                            <i class="fas fa-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
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
                                    <td>
                                        <div class="font-semibold">{{ $req->address_line }}</div>
                                        @if ($req->description)
                                            <div class="text-xs text-gray-500">
                                                {{ substr($req->description, 0, 40) }}...</div>
                                        @endif
                                    </td>
                                    <td class="font-mono font-semibold">{{ $req->house_number ?? '-' }}</td>
                                    <td>
                                        <div class="font-semibold text-sm">{{ $req->owner_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $req->owner_phone }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'verified' => 'info',
                                                'indexed' => 'secondary',
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$req->status] ?? 'secondary' }}">
                                            {{ $statuses[$req->status] ?? 'Unknown' }}
                                        </span>
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
            <div class="modal-content" @click.stop style="max-width: 700px; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header">
                    <h2>Request Details</h2>
                    <button type="button" wire:click="closeDetail" class="btn-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Address Information -->
                    <div class="detail-section">
                        <h3>Address Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item" style="grid-column: 1/-1;">
                                <span class="detail-label">Address Line</span>
                                <span class="detail-value">{{ $selectedRequest->address_line }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">House Number</span>
                                <span class="detail-value">{{ $selectedRequest->house_number ?? '-' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Latitude</span>
                                <span
                                    class="detail-value font-mono text-sm">{{ number_format($selectedRequest->latitude, 6) }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Longitude</span>
                                <span
                                    class="detail-value font-mono text-sm">{{ number_format($selectedRequest->longitude, 6) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Owner Information -->
                    <div class="detail-section">
                        <h3>Property Owner</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Owner Name</span>
                                <span class="detail-value">{{ $selectedRequest->owner_name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone Number</span>
                                <span class="detail-value font-mono">{{ $selectedRequest->owner_phone }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Applicant Information -->
                    <div class="detail-section">
                        <h3>Request Applicant</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Applicant Name</span>
                                <span class="detail-value">{{ $selectedRequest->applicant_name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone Number</span>
                                <span class="detail-value font-mono">{{ $selectedRequest->applicant_phone }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Submitted By</span>
                                <span class="detail-value">{{ $selectedRequest->user?->name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Date Submitted</span>
                                <span
                                    class="detail-value">{{ $selectedRequest->created_at->format('F j, Y \a\t H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if ($selectedRequest->description)
                        <div class="detail-section">
                            <h3>Description</h3>
                            <div class="note-box">{{ $selectedRequest->description }}</div>
                        </div>
                    @endif

                    <!-- Property Images -->
                    @if ($selectedRequest->property_images && count($selectedRequest->property_images) > 0)
                        <div class="detail-section">
                            <h3>Property Images</h3>
                            <div class="image-gallery">
                                @foreach ($selectedRequest->property_images as $image)
                                    <div class="image-thumbnail">
                                        <img src="{{ Storage::url($image) }}" alt="Property image">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Status & Review -->
                    <div class="detail-section">
                        <h3>Review Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <span class="detail-value">
                                    <span
                                        class="badge badge-{{ ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'verified' => 'info'][$selectedRequest->status] ?? 'secondary' }}">
                                        {{ $statuses[$selectedRequest->status] ?? 'Unknown' }}
                                    </span>
                                </span>
                            </div>
                            @if ($selectedRequest->reviewed_at)
                                <div class="detail-item">
                                    <span class="detail-label">Reviewed At</span>
                                    <span
                                        class="detail-value">{{ $selectedRequest->reviewed_at->format('F j, Y \a\t H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    @if ($selectedRequest->admin_note)
                        <div class="detail-section">
                            <h3>Admin Notes</h3>
                            <div class="note-box">{{ $selectedRequest->admin_note }}</div>
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
                        You are about to approve this address indexing request:
                    </p>
                    <div class="info-box">
                        <p><strong>Address:</strong> {{ $selectedRequest->address_line }}</p>
                        <p><strong>House #:</strong> {{ $selectedRequest->house_number ?? '-' }}</p>
                        <p><strong>Owner:</strong> {{ $selectedRequest->owner_name }}</p>
                        <p><strong>Location:</strong> {{ number_format($selectedRequest->latitude, 4) }},
                            {{ number_format($selectedRequest->longitude, 4) }}</p>
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
                        You are about to reject this address indexing request. A reason is required.
                    </p>
                    <div class="info-box">
                        <p><strong>Address:</strong> {{ $selectedRequest->address_line }}</p>
                        <p><strong>Owner:</strong> {{ $selectedRequest->owner_name }}</p>
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
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
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

    .stat-icon.red {
        background-color: #ef4444;
    }

    .stat-icon.purple {
        background-color: #8b5cf6;
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
        font-family: inherit;
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

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
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

    .image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }

    .image-thumbnail {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
        aspect-ratio: 1;
    }

    .image-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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

    .text-sm {
        font-size: 14px;
    }

    .text-gray-500 {
        color: #6b7280;
    }

    .font-semibold {
        font-weight: 600;
    }

    .font-mono {
        font-family: 'Courier New', monospace;
    }

    .mb-4 {
        margin-bottom: 16px;
    }
</style>
