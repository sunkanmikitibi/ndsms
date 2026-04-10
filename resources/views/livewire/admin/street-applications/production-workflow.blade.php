<div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
        <div>
            <h1 class="admin-title">Street Applications Production Workflow</h1>
            <p class="admin-subtitle">Manage field officer assignments and track inspection progress</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-list"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Applications</p>
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
            <div class="stat-icon purple"><i class="fas fa-user-check"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['assigned'] }}</h3>
                <p>Assigned to Officers</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-search"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['inInspection'] }}</h3>
                <p>Under Inspection</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fas fa-check-double"></i></div>
            <div class="stat-content">
                <h3>{{ $stats['inspected'] }}</h3>
                <p>Inspection Complete</p>
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
                    <label>Search (Reference, Street, Town)</label>
                    <input type="text" wire:model.live="search" placeholder="e.g., APP-ABC123 or Ikeja Road"
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
                    <label>Field Officer</label>
                    <input type="text" wire:model.live="filterFieldOfficer" placeholder="e.g., John Doe"
                        class="form-control">
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
            <h3>Applications ({{ $applications->total() }})</h3>
            <span class="text-sm text-gray-500">Page {{ $applications->currentPage() }} of
                {{ $applications->lastPage() }}</span>
        </div>
        <div class="card-body">
            @if ($applications->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>No applications found</h4>
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
                                <th>Applicant</th>
                                <th>Field Officer</th>
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
                            @foreach ($applications as $app)
                                <tr>
                                    <td class="font-mono text-sm font-bold">{{ $app->reference_number }}</td>
                                    <td>
                                        <div class="font-semibold">{{ $app->street_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $app->town }}</div>
                                    </td>
                                    <td>
                                        <div class="font-semibold">{{ $app->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $app->user->phone }}</div>
                                    </td>
                                    <td>
                                        @if ($app->assignedFieldOfficer)
                                            <div class="font-semibold">{{ $app->assignedFieldOfficer->name }}</div>
                                            <div class="text-xs text-gray-500">
                                                Assigned: {{ $app->assigned_at?->format('M d, Y') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'approved' => 'success',
                                                'assigned' => 'info',
                                                'in_inspection' => 'warning',
                                                'inspected' => 'success',
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$app->status] ?? 'secondary' }}">
                                            {{ $statuses[$app->status] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="text-sm">{{ $app->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button wire:click="viewDetail({{ $app->id }})"
                                                class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if ($app->status === 'approved')
                                                <button wire:click="openAssignModal({{ $app->id }})"
                                                    class="btn btn-sm btn-primary" title="Assign Officer">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            @elseif ($app->status === 'assigned')
                                                <button wire:click="markAsInspected({{ $app->id }})"
                                                    class="btn btn-sm btn-warning" title="Start Inspection">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            @elseif ($app->status === 'in_inspection')
                                                <button wire:click="openCompleteModal({{ $app->id }})"
                                                    class="btn btn-sm btn-success" title="Complete Inspection">
                                                    <i class="fas fa-check"></i>
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
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Detail Modal -->
    @if ($showDetail && $selectedApplication)
        <div class="modal-overlay" @click="$wire.closeDetail()">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h2>Application Details</h2>
                    <button type="button" wire:click="closeDetail" class="btn-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Reference & Status -->
                    <div class="detail-section">
                        <h3>Application Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Reference Number</span>
                                <span
                                    class="detail-value font-mono">{{ $selectedApplication->reference_number }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <span class="detail-value">
                                    <span
                                        class="badge badge-{{ $statusColors[$selectedApplication->status] ?? 'secondary' }}">
                                        {{ $statuses[$selectedApplication->status] ?? 'Unknown' }}
                                    </span>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Submitted</span>
                                <span
                                    class="detail-value">{{ $selectedApplication->created_at->format('F j, Y \a\t H:i') }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Applicant</span>
                                <span class="detail-value">{{ $selectedApplication->user->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Street Information -->
                    <div class="detail-section">
                        <h3>Street Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Street Name</span>
                                <span class="detail-value">{{ $selectedApplication->street_name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Town</span>
                                <span class="detail-value">{{ $selectedApplication->town }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Street Type</span>
                                <span class="detail-value">{{ ucfirst($selectedApplication->street_type) }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">GPS Coordinates</span>
                                <span class="detail-value font-mono">
                                    {{ $selectedApplication->start_latitude }},
                                    {{ $selectedApplication->start_longitude }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Field Officer Assignment -->
                    @if ($selectedApplication->assignedFieldOfficer)
                        <div class="detail-section">
                            <h3>Field Officer Assignment</h3>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Assigned Officer</span>
                                    <span
                                        class="detail-value">{{ $selectedApplication->assignedFieldOfficer->name }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Assigned Date</span>
                                    <span
                                        class="detail-value">{{ $selectedApplication->assigned_at?->format('F j, Y \a\t H:i') }}</span>
                                </div>
                                @if ($selectedApplication->inspection_started_at)
                                    <div class="detail-item">
                                        <span class="detail-label">Inspection Started</span>
                                        <span
                                            class="detail-value">{{ $selectedApplication->inspection_started_at->format('F j, Y \a\t H:i') }}</span>
                                    </div>
                                @endif
                                @if ($selectedApplication->inspection_completed_at)
                                    <div class="detail-item">
                                        <span class="detail-label">Inspection Completed</span>
                                        <span
                                            class="detail-value">{{ $selectedApplication->inspection_completed_at->format('F j, Y \a\t H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Inspection Notes -->
                    @if ($selectedApplication->inspection_notes)
                        <div class="detail-section">
                            <h3>Inspection Notes</h3>
                            <div class="inspection-notes">
                                {{ $selectedApplication->inspection_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Assign Field Officer Modal -->
    @if ($showAssignModal && $selectedApplication)
        <div class="modal-overlay" @click="$wire.closeDetail()">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h2>Assign Field Officer</h2>
                    <button type="button" wire:click="closeDetail" class="btn-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p>Assign a field officer to inspect this street application:</p>
                    <strong>{{ $selectedApplication->reference_number }} -
                        {{ $selectedApplication->street_name }}</strong>

                    <div class="form-group" style="margin-top: 20px;">
                        <label>Field Officer</label>
                        <select wire:model="assignedFieldOfficerId" class="form-control">
                            <option value="">Select Field Officer</option>
                            @foreach ($fieldOfficers as $officer)
                                <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeDetail" class="btn btn-outline">Cancel</button>
                    <button wire:click="assignFieldOfficer" class="btn btn-primary">Assign Officer</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Complete Inspection Modal -->
    @if ($showCompleteModal && $selectedApplication)
        <div class="modal-overlay" @click="$wire.closeDetail()">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h2>Complete Inspection</h2>
                    <button type="button" wire:click="closeDetail" class="btn-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p>Mark inspection as complete for:</p>
                    <strong>{{ $selectedApplication->reference_number }} -
                        {{ $selectedApplication->street_name }}</strong>

                    <div class="form-group" style="margin-top: 20px;">
                        <label>Inspection Notes</label>
                        <textarea wire:model="completionNotes" class="form-control" rows="4"
                            placeholder="Enter inspection findings, measurements, or other relevant notes..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeDetail" class="btn btn-outline">Cancel</button>
                    <button wire:click="completeInspection" class="btn btn-success">Complete Inspection</button>
                </div>
            </div>
        </div>
    @endif
</div>
