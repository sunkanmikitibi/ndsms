<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-file-alt" style="color:var(--accent);margin-right:10px;"></i>Street Applications</h2>
            <p>Manage street registration applications submitted by users</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="toolbar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search applications…">
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <label style="font-size:13px;color:var(--text-secondary);">Status:</label>
            <select wire:model.live="filterStatus"
                style="padding:6px 10px;border-radius:6px;border:1px solid var(--border);font-size:13px;">
                <option value="all">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Street Name</th>
                    <th>Applicant</th>
                    <th>Town</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td>
                            <div style="font-weight:700;">{{ $app->street_name }}</div>
                            @if ($app->description)
                                <div style="font-size:11px;color:var(--text-secondary);margin-top:2px;">
                                    {{ substr($app->description, 0, 50) }}...</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $app->user?->name }}</div>
                            <div style="font-size:11px;color:var(--text-secondary);">{{ $app->user?->phone }}</div>
                        </td>
                        <td>{{ $app->town }}</td>
                        <td>
                            <span
                                style="padding:3px 8px;background:var(--bg-input);border-radius:6px;font-size:11px;font-weight:600;">
                                {{ ucfirst($app->type) }}
                            </span>
                        </td>
                        <td>
                            <span
                                style="padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;text-transform:uppercase;background:{{ $app->status === 'approved' ? 'var(--success-light)' : ($app->status === 'rejected' ? 'var(--danger-light)' : 'var(--warning-light)') }};color:{{ $app->status === 'approved' ? 'var(--success)' : ($app->status === 'rejected' ? 'var(--danger)' : 'var(--warning)') }};">
                                {{ ucfirst($app->status) }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--text-secondary);">
                            {{ $app->created_at->format('M d, Y') }}
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button wire:click="openView({{ $app->id }})"
                                    class="btn btn-outline btn-sm btn-edit">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button wire:click="confirmDelete({{ $app->id }})" class="btn btn-outline btn-sm"
                                    style="border-color:var(--danger);color:var(--danger);">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-file-alt"></i>
                                <h4>No applications found</h4>
                                <p>There are no street applications to display.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            <p>{{ $applications->total() }} applications total</p>
            <div class="pagination-links">{{ $applications->links() }}</div>
        </div>
    </div>

    <!-- View/Edit Modal -->
    @if ($showModal && $editId)
        <div class="modal-overlay open" wire:click.self="closeModal">
            <div class="modal" style="max-width:600px;width:95%;">
                @php $application = \App\Models\StreetApplication::find($editId) @endphp
                <h3><i class="fas fa-file-alt" style="color:var(--accent);"></i> Review Application</h3>

                <div style="margin-bottom:20px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:15px;">
                        <div>
                            <label
                                style="font-size:11px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;">Street
                                Name</label>
                            <div style="font-weight:700;margin-top:4px;">{{ $application?->street_name }}</div>
                        </div>
                        <div>
                            <label
                                style="font-size:11px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;">Town</label>
                            <div style="font-weight:700;margin-top:4px;">{{ $application?->town }}</div>
                        </div>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label
                            style="font-size:11px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;">Applicant</label>
                        <div style="font-weight:700;margin-top:4px;">{{ $application?->user?->name }}</div>
                        <div style="font-size:12px;color:var(--text-secondary);">{{ $application?->user?->phone }}
                        </div>
                    </div>
                    <div style="margin-bottom:15px;">
                        <label
                            style="font-size:11px;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;">Description</label>
                        <div
                            style="font-size:12px;margin-top:4px;padding:10px;background:var(--bg-input);border-radius:6px;">
                            {{ $application?->description }}</div>
                    </div>
                </div>

                <form wire:submit="updateStatus">
                    <div class="form-group" style="margin-bottom:20px;">
                        <label>Application Status <span style="color:var(--danger);">*</span></label>
                        <select wire:model="applicationStatus"
                            style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label>Admin Note (Optional)</label>
                        <textarea wire:model="adminNote" placeholder="Add any notes about this application…" rows="3"
                            style="resize:vertical;"></textarea>
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
                <h3 style="justify-content:center;">Delete Application?</h3>
                <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">This action cannot be undone.
                </p>
                <div class="modal-actions" style="justify-content:center;">
                    <button wire:click="$set('showDeleteModal', false)" class="btn btn-outline">Cancel</button>
                    <button wire:click="deleteApplication" class="btn btn-danger"><i class="fas fa-trash"></i>
                        Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
