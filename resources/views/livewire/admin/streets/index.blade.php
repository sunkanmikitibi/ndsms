<div>
<div class="page-header">
    <div>
        <h2><i class="fas fa-road" style="color:var(--accent);margin-right:10px;"></i>Streets</h2>
        <p>Manage all registered streets across towns</p>
    </div>
    @can('manage addresses')
    <div style="display:flex;gap:12px;">
        <button wire:click="$set('showImportModal', true)" class="btn btn-outline"><i class="fas fa-file-import"></i> Bulk Import</button>
        <button wire:click="openCreate" class="btn btn-primary"><i class="fas fa-plus"></i> Add Street</button>
    </div>
    @endcan
</div>

<div class="toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by street name or code…">
    </div>
    <select wire:model.live="filterTown" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);color:var(--text-primary);font-family:'Outfit',sans-serif;font-size:14px;">
        <option value="">All Towns</option>
        @foreach($towns as $town)
            <option value="{{ $town }}">{{ $town }}</option>
        @endforeach
    </select>
</div>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Street Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Town</th>
                <th>Addresses</th>
                <th>Status</th>
                @can('manage addresses')<th>Actions</th>@endcan
            </tr>
        </thead>
        <tbody>
            @forelse($streets as $street)
            <tr>
                <td><span style="font-family:'Space Mono',monospace;font-size:12px;color:var(--accent);">{{ $street->code ?? '—' }}</span></td>
                <td style="font-weight:600;">{{ $street->name }}</td>
                <td><span class="town-badge" style="background:var(--info-light);color:var(--info);">{{ ucfirst($street->type) }}</span></td>
                <td>{{ $street->town }}</td>
                <td><span style="font-family:'Space Mono',monospace;font-weight:700;">{{ $street->addresses_count }}</span></td>
                <td><span class="status-badge {{ $street->status }}">{{ ucfirst($street->status) }}</span></td>
                @can('manage addresses')
                <td>
                    <div style="display:flex;gap:6px;">
                        <button wire:click="openEdit({{ $street->id }})" class="btn btn-outline btn-sm btn-edit"><i class="fas fa-pen"></i></button>
                        <button wire:click="confirmDelete({{ $street->id }})" class="btn btn-outline btn-sm" style="border-color:var(--danger);color:var(--danger);"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
                @endcan
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fas fa-road"></i>
                        <h4>No streets found</h4>
                        <p>Add streets to start registering addresses.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-wrapper">
        <p>{{ $streets->total() }} streets total</p>
        <div class="pagination-links">{{ $streets->links() }}</div>
    </div>
</div>

@if($showModal)
<div class="modal-overlay open" wire:click.self="$set('showModal', false)">
    <div class="modal" style="max-width:560px;">
        <h3><i class="fas fa-road" style="color:var(--accent);"></i> {{ $editId ? 'Edit Street' : 'Register Street' }}</h3>
        <form wire:submit="save">
            <div class="form-grid">
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Street Name</label>
                    <input wire:model="name" type="text" placeholder="e.g. Nnewi Road">
                    @error('name')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select wire:model="type">
                        <option value="street">Street</option>
                        <option value="avenue">Avenue</option>
                        <option value="road">Road</option>
                        <option value="lane">Lane</option>
                        <option value="close">Close</option>
                        <option value="crescent">Crescent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Town</label>
                    <input wire:model="town" type="text" placeholder="e.g. Abagana Town">
                    @error('town')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select wire:model="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Description (optional)</label>
                    <textarea wire:model="description" rows="3" placeholder="Brief description…" style="resize:vertical;"></textarea>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Street</button>
            </div>
        </form>
    </div>
</div>
@endif

@if($showDeleteModal)
<div class="modal-overlay open" wire:click.self="$set('showDeleteModal', false)">
    <div class="modal" style="max-width:400px;text-align:center;">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--danger-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:var(--danger);">
            <i class="fas fa-trash"></i>
        </div>
        <h3 style="justify-content:center;">Delete Street?</h3>
        <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">All addresses linked to this street may be affected.</p>
        <div class="modal-actions" style="justify-content:center;">
            <button wire:click="$set('showDeleteModal', false)" class="btn btn-outline">Cancel</button>
            <button wire:click="deleteStreet" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
        </div>
    </div>
</div>
@endif

<!-- Import Modal -->
@if($showImportModal)
<div class="modal-overlay open" wire:click.self="$set('showImportModal', false)">
    <div class="modal" style="max-width:500px;">
        <h3><i class="fas fa-file-import" style="color:var(--accent);"></i> Bulk Import Streets</h3>
        <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">Upload a CSV file to import multiple streets at once.</p>
        
        <div style="background:var(--bg-card);padding:16px;border-radius:var(--radius-md);margin-bottom:20px;border:1px solid var(--border);">
            <h5 style="margin-bottom:8px;font-size:13px;color:var(--text-primary);">CSV Format:</h5>
            <code style="font-size:12px;display:block;background:var(--bg-input);padding:8px;border-radius:4px;color:var(--accent);">name, town, type, description, status</code>
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
