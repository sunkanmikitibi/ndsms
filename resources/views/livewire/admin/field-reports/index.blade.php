<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-tasks" style="color:var(--accent);margin-right:10px;"></i>Field Reports</h2>
            <p>Review and manage field reports submitted by field officers</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="toolbar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search reports…">
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <label style="font-size:13px;color:var(--text-secondary);">Status:</label>
            <select wire:model.live="filterStatus"
                style="padding:6px 10px;border-radius:6px;border:1px solid var(--border);font-size:13px;">
                <option value="all">All</option>
                <option value="pending">Pending</option>
                <option value="reviewed">Reviewed</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Report Title</th>
                    <th>Location</th>
                    <th>Reporter</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>
                            <div style="font-weight:700;">{{ $report->title }}</div>
                            @if ($report->description)
                                <div style="font-size:11px;color:var(--text-secondary);margin-top:2px;">
                                    {{ substr($report->description, 0, 50) }}...</div>
                            @endif
                        </td>
                        <td>{{ $report->location }}</td>
                        <td>
                            <div>{{ $report->user?->name }}</div>
                            <div style="font-size:11px;color:var(--text-secondary);">{{ $report->user?->phone }}</div>
                        </td>
                        <td>
                            <span
                                style="padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;text-transform:uppercase;background:{{ $report->status === 'reviewed' ? 'var(--success-light)' : ($report->status === 'closed' ? 'var(--bg-input)' : 'var(--warning-light)') }};color:{{ $report->status === 'reviewed' ? 'var(--success)' : ($report->status === 'closed' ? 'var(--text-secondary)' : 'var(--warning)') }};">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--text-secondary);">
                            {{ $report->created_at->format('M d, Y') }}
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button wire:click="openView({{ $report->id }})"
                                    class="btn btn-outline btn-sm btn-edit">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button wire:click="confirmDelete({{ $report->id }})" class="btn btn-outline btn-sm"
                                    style="border-color:var(--danger);color:var(--danger);">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-tasks"></i>
                                <h4>No reports found</h4>
                                <p>There are no field reports to display.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            <p>{{ $reports->total() }} reports total</p>
            <div class="pagination-links">{{ $reports->links() }}</div>
        </div>
    </div>

    <!-- View/Edit Modal -->
    @if ($showModal && $viewId)
        <div class="modal-overlay open" wire:click.self="closeModal">
            <div class="modal" style="max-width:600px;width:95%;">
                @php $report = \App\Models\FieldReport::find($viewId) @endphp
                <h3><i class="fas fa-tasks" style="color:var(--accent);"></i> Review Field Report</h3>

                <div style="margin-bottom:20px;">
                    <div style="margin-bottom:15px;">
                        <label
                            style="font-size:11px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;">Title</label>
                        <div style="font-weight:700;margin-top:4px;">{{ $report?->title }}</div>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label
                            style="font-size:11px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;">Location</label>
                        <div style="font-weight:700;margin-top:4px;">{{ $report?->location }}</div>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label
                            style="font-size:11px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;">Reporter</label>
                        <div style="font-weight:700;margin-top:4px;">{{ $report?->user?->name }}</div>
                        <div style="font-size:12px;color:var(--text-secondary);">{{ $report?->user?->phone }}</div>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label
                            style="font-size:11px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;">Description</label>
                        <div
                            style="font-size:12px;margin-top:4px;padding:10px;background:var(--bg-input);border-radius:6px;">
                            {{ $report?->description }}</div>
                    </div>
                </div>

                <form wire:submit="updateStatus">
                    <div class="form-group" style="margin-bottom:20px;">
                        <label>Report Status <span style="color:var(--danger);">*</span></label>
                        <select wire:model="reportStatus"
                            style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
                            <option value="pending">Pending</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label>Admin Note (Optional)</label>
                        <textarea wire:model="adminNote" placeholder="Add any notes about this report…" rows="3" style="resize:vertical;"></textarea>
                    </div>

                    <div class="modal-actions">
                        <button type="button" wire:click="closeModal" class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update
                            Status</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirm -->
    @if ($showDeleteModal)
        <div class="modal-overlay open" wire:click.self="$set('showDeleteModal', false)">
            <div class="modal" style="max-width:400px;text-align:center;">
                <div
                    style="width:64px;height:64px;border-radius:50%;background:var(--danger-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:var(--danger);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 style="justify-content:center;">Delete Report?</h3>
                <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">This action cannot be undone.
                </p>
                <div class="modal-actions" style="justify-content:center;">
                    <button wire:click="$set('showDeleteModal', false)" class="btn btn-outline">Cancel</button>
                    <button wire:click="deleteReport" class="btn btn-danger"><i class="fas fa-trash"></i>
                        Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
