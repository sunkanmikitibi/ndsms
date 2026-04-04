<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-receipt" style="color:var(--accent);margin-right:10px;"></i>Fee Schedule Management</h2>
            <p>Set and manage service fees for all platform services</p>
        </div>
        @canany(['manage fee schedules', 'role:super-admin'])
            <button wire:click="openCreate" class="btn btn-primary"><i class="fas fa-plus"></i> New Fee Schedule</button>
        @endcanany
    </div>

    <!-- Filters -->
    <div class="toolbar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by service name…">
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <label style="font-size:13px;color:var(--text-secondary);">Status:</label>
            <select wire:model.live="filterStatus"
                style="padding:6px 10px;border-radius:6px;border:1px solid var(--border);font-size:13px;">
                <option value="all">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </div>

    <!-- Fee Schedule Table -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Service Type</th>
                    <th>Service Name</th>
                    <th>Amount</th>
                    <th>Effective Period</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fees as $fee)
                    <tr>
                        <td>
                            <div
                                style="font-size:12px;color:var(--text-secondary);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
                                {{ $fee->service_type }}
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div
                                    style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,var(--accent-light),var(--accent));display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;">{{ $fee->service_name }}</div>
                                    <div style="font-size:11px;color:var(--text-secondary);margin-top:2px;">
                                        {{ $fee->description }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:700;font-size:14px;color:var(--accent);">
                                {{ $fee->getFormattedAmount() }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size:12px;color:var(--text-secondary);">
                                @if ($fee->effective_from)
                                    <div>From: {{ $fee->effective_from->format('M d, Y H:i') }}</div>
                                @else
                                    <div>From: <span style="text-decoration:line-through;">—</span></div>
                                @endif
                                @if ($fee->effective_to)
                                    <div>To: {{ $fee->effective_to->format('M d, Y H:i') }}</div>
                                @else
                                    <div style="color:var(--success);">Ongoing</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span
                                style="padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;background:{{ $fee->status === 'active' ? 'var(--success-light)' : ($fee->status === 'inactive' ? 'var(--warning-light)' : 'var(--bg-input)') }};color:{{ $fee->status === 'active' ? 'var(--success)' : ($fee->status === 'inactive' ? 'var(--warning)' : 'var(--text-secondary)') }};">
                                {{ ucfirst($fee->status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                @canany(['manage fee schedules', 'role:super-admin'])
                                    <button wire:click="toggleStatus({{ $fee->id }})" class="btn btn-outline btn-sm"
                                        style="border-color:{{ $fee->status === 'active' ? 'var(--warning)' : 'var(--success)' }};color:{{ $fee->status === 'active' ? 'var(--warning)' : 'var(--success)' }};">
                                        <i class="fas fa-{{ $fee->status === 'active' ? 'pause' : 'play' }}"></i>
                                    </button>
                                    <button wire:click="openEdit({{ $fee->id }})"
                                        class="btn btn-outline btn-sm btn-edit">
                                        <i class="fas fa-pen"></i> Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $fee->id }})" class="btn btn-outline btn-sm"
                                        style="border-color:var(--danger);color:var(--danger);">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <span style="font-size:12px;color:var(--text-secondary);">View only</span>
                                @endcanany
                            </div>
                        </td>
                        </button>
    </div>
    </td>
    </tr>
@empty
    <tr>
        <td colspan="6">
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <h4>No fee schedules yet</h4>
                <p>Create your first fee schedule to enable charging for services.</p>
            </div>
        </td>
    </tr>
    @endforelse
    </tbody>
    </table>
    <div class="pagination-wrapper">
        <p>{{ $fees->total() }} fee schedules total</p>
        <div class="pagination-links">{{ $fees->links() }}</div>
    </div>
</div>

<!-- Create/Edit Modal -->
@if ($showModal)
    <div class="modal-overlay open" wire:click.self="closeModal">
        <div class="modal" style="max-width:680px;width:95%;">
            <h3><i class="fas fa-receipt" style="color:var(--accent);"></i>
                {{ $editId ? 'Edit Fee Schedule' : 'Create New Fee Schedule' }}</h3>
            <form wire:submit="save">
                <!-- Service Type Selection -->
                <div class="form-group" style="margin-bottom:20px;">
                    <label>Service Type <span style="color:var(--danger);">*</span></label>
                    <select wire:model="serviceType"
                        style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
                        <option value="">— Select Service Type —</option>
                        @foreach ($serviceTypes as $typeKey => $typeName)
                            <option value="{{ $typeKey }}">{{ $typeName }}</option>
                        @endforeach
                    </select>
                    @error('serviceType')
                        <span
                            style="color:var(--danger);font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Name -->
                <div class="form-group" style="margin-bottom:20px;">
                    <label>Service Name <span style="color:var(--danger);">*</span></label>
                    <input wire:model="serviceName" type="text" placeholder="e.g., Address Indexing Request">
                    @error('serviceName')
                        <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group" style="margin-bottom:20px;">
                    <label>Description (Optional)</label>
                    <textarea wire:model="description" placeholder="Describe this service…" rows="2" style="resize:vertical;"></textarea>
                    @error('description')
                        <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Two-column layout for amount and currency -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:20px;">
                    <!-- Base Amount -->
                    <div class="form-group">
                        <label>Fee Amount (NGN) <span style="color:var(--danger);">*</span></label>
                        <input wire:model.number="baseAmount" type="number" placeholder="0.00" step="0.01"
                            min="0.01">
                        @error('baseAmount')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Currency -->
                    <div class="form-group">
                        <label>Currency <span style="color:var(--danger);">*</span></label>
                        <select wire:model="currency"
                            style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
                            <option value="NGN">Nigerian Naira (NGN)</option>
                            <option value="USD">US Dollar (USD)</option>
                            <option value="EUR">Euro (EUR)</option>
                        </select>
                        @error('currency')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Status Selection -->
                <div class="form-group" style="margin-bottom:20px;">
                    <label>Status <span style="color:var(--danger);">*</span></label>
                    <select wire:model="status"
                        style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="archived">Archived</option>
                    </select>
                    @error('status')
                        <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Effective Dates -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:20px;">
                    <div class="form-group">
                        <label>Effective From (Optional)</label>
                        <input wire:model="effectiveFrom" type="datetime-local" placeholder="YYYY-MM-DD HH:mm">
                        @error('effectiveFrom')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Effective To (Optional)</label>
                        <input wire:model="effectiveTo" type="datetime-local" placeholder="YYYY-MM-DD HH:mm">
                        @error('effectiveTo')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div
                    style="background:var(--bg-input);padding:12px;border-radius:6px;margin-bottom:20px;font-size:12px;color:var(--text-secondary);">
                    <i class="fas fa-info-circle" style="margin-right:6px;color:var(--accent);"></i>
                    Leave date fields empty for ongoing fees. Set dates to limit fee validity period.
                </div>

                <div class="modal-actions">
                    <button type="button" wire:click="closeModal" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Fee
                        Schedule</button>
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
            <h3 style="justify-content:center;">Delete Fee Schedule?</h3>
            <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">
                This action cannot be undone. The fee schedule will be permanently removed.
            </p>
            <div class="modal-actions" style="justify-content:center;">
                <button wire:click="$set('showDeleteModal', false)" class="btn btn-outline">Cancel</button>
                <button wire:click="deleteFee" class="btn btn-danger"><i class="fas fa-trash"></i>
                    Delete</button>
            </div>
        </div>
    </div>
@endif
</div>
