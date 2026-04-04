<div>
<div class="page-header">
    <div>
        <h2><i class="fas fa-map-marker-alt" style="color:var(--accent);margin-right:10px;"></i>Addresses</h2>
        <p>Manage all registered residential and commercial addresses</p>
    </div>
    @can('manage addresses')
    <div style="display:flex;gap:12px;">
        <button wire:click="$set('showImportModal', true)" class="btn btn-outline"><i class="fas fa-file-import"></i> Bulk Import</button>
        <button wire:click="openCreate" class="btn btn-primary"><i class="fas fa-plus"></i> Add Address</button>
    </div>
    @endcan
</div>

<!-- Toolbar -->
<div class="toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by owner or house number…">
    </div>
    <div style="display:flex;gap:8px;">
        <select wire:model.live="filterWard" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);color:var(--text-primary);font-family:'Outfit',sans-serif;font-size:14px;">
            <option value="">All Wards</option>
            @foreach($wards as $ward)
                <option value="{{ $ward }}">{{ $ward }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);color:var(--text-primary);font-family:'Outfit',sans-serif;font-size:14px;">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="pending">Pending</option>
        </select>
    </div>
</div>

<!-- Table -->
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>House No.</th>
                <th>Street</th>
                <th>Ward</th>
                <th>Owner</th>
                <th>Phone</th>
                <th>Status</th>
                @can('manage addresses')<th>Actions</th>@endcan
            </tr>
        </thead>
        <tbody>
            @forelse($addresses as $addr)
            <tr>
                <td><span style="font-family:'Space Mono',monospace;font-weight:700;color:var(--accent);">{{ $addr->house_number }}</span></td>
                <td>{{ $addr->street?->name ?? '—' }}</td>
                <td><span class="ward-badge">{{ $addr->ward }}</span></td>
                <td style="font-weight:600;">{{ $addr->owner_name }}</td>
                <td style="color:var(--text-secondary);">{{ $addr->owner_phone ?? '—' }}</td>
                <td><span class="status-badge {{ $addr->status }}">{{ ucfirst($addr->status) }}</span></td>
                @can('manage addresses')
                <td>
                    <div style="display:flex;gap:6px;">
                        <button wire:click="openEdit({{ $addr->id }})" class="btn btn-outline btn-sm btn-edit"><i class="fas fa-pen"></i></button>
                        <button wire:click="confirmDelete({{ $addr->id }})" class="btn btn-outline btn-sm" style="border-color:var(--danger);color:var(--danger);"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
                @endcan
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fas fa-map-marker-alt"></i>
                        <h4>No addresses found</h4>
                        <p>Adjust your filters or add a new address.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-wrapper">
        <p>Showing {{ $addresses->firstItem() }}–{{ $addresses->lastItem() }} of {{ $addresses->total() }} addresses</p>
        <div class="pagination-links">{{ $addresses->links() }}</div>
    </div>
</div>

<!-- Create/Edit Modal -->
@if($showModal)
<div class="modal-overlay open" wire:click.self="$set('showModal', false)">
    <div class="modal" style="max-width:580px;">
        <h3><i class="fas fa-map-marker-alt" style="color:var(--accent);"></i> {{ $editId ? 'Edit Address' : 'Register Address' }}</h3>
        <form wire:submit="save">
            <div class="form-grid">
                <div class="form-group">
                    <label>House Number</label>
                    <input wire:model="house_number" type="text" placeholder="e.g. 14A">
                    @error('house_number')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Street</label>
                    <select wire:model="street_id">
                        <option value="">Select street…</option>
                        @foreach($streets as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                    @error('street_id')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Ward</label>
                    <input wire:model="ward" type="text" placeholder="e.g. Abagana Ward">
                    @error('ward')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Owner Name</label>
                    <input wire:model="owner_name" type="text" placeholder="Full name">
                    @error('owner_name')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Owner Phone</label>
                    <input wire:model="owner_phone" type="text" placeholder="Optional">
                </div>
                <div class="form-group">
                    <label>Applicant Name</label>
                    <input wire:model="applicant_name" type="text" placeholder="Who is applying?">
                </div>
                <div class="form-group">
                    <label>Applicant Phone</label>
                    <input wire:model="applicant_phone" type="text" placeholder="Applicant contact">
                </div>
                <div class="form-group">
                    <label>Reference Code</label>
                    <input wire:model="reference_code" type="text" placeholder="Unique reference">
                    @error('reference_code')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select wire:model="status">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Address</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Delete Confirm -->
@if($showDeleteModal)
<div class="modal-overlay open" wire:click.self="$set('showDeleteModal', false)">
    <div class="modal" style="max-width:400px;text-align:center;">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--danger-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:var(--danger);">
            <i class="fas fa-trash"></i>
        </div>
        <h3 style="justify-content:center;">Delete Address?</h3>
        <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">This action cannot be undone.</p>
        <div class="modal-actions" style="justify-content:center;">
            <button wire:click="$set('showDeleteModal', false)" class="btn btn-outline">Cancel</button>
            <button wire:click="deleteAddress" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
        </div>
    </div>
</div>
@endif

<!-- Import Modal -->
@if($showImportModal)
<div class="modal-overlay open" wire:click.self="$set('showImportModal', false)">
    <div class="modal" style="max-width:500px;">
        <h3><i class="fas fa-file-import" style="color:var(--accent);"></i> Bulk Import Addresses</h3>
        <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">Upload a CSV file to import multiple addresses at once.</p>
        
        <div style="background:var(--bg-card);padding:16px;border-radius:var(--radius-md);margin-bottom:20px;border:1px solid var(--border);">
            <h5 style="margin-bottom:8px;font-size:13px;color:var(--text-primary);">CSV Format:</h5>
            <code style="font-size:12px;display:block;background:var(--bg-input);padding:8px;border-radius:4px;color:var(--accent);">house_number, street_id, ward, owner_name, owner_phone, applicant_name, applicant_phone, reference_code</code>
            <a href="#" wire:click.prevent="downloadSample" style="display:inline-block;margin-top:12px;font-size:13px;color:var(--accent);text-decoration:none;"><i class="fas fa-download"></i> Download Sample CSV</a>
        </div>

        <form wire:submit.prevent="import">
            <div class="form-group">
                <label>Select CSV File</label>
                <input type="file" wire:model="importFile" class="form-control" style="padding:10px;">
                @error('importFile')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="modal-actions" style="margin-top:24px;">
                <button type="button" wire:click="$set('showImportModal', false)" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <i class="fas fa-upload" wire:loading.remove></i>
                    <span wire:loading.remove>Import Data</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Importing…</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif
</div>
