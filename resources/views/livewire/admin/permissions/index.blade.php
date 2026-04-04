<div>
    <div class="page-header">
        <div>
            <h2><i class="fas fa-lock" style="color:var(--accent);margin-right:10px;"></i>Permissions</h2>
            <p>Manage granular permissions that can be assigned to roles</p>
        </div>
        <button wire:click="openCreate" class="btn btn-primary"><i class="fas fa-plus"></i> New Permission</button>
    </div>

    <!-- Search -->
    <div class="toolbar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search permissions…">
        </div>
    </div>

    <!-- Permissions Table -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Permission Name</th>
                    <th>Description</th>
                    <th>Assigned Roles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div
                                    style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--accent-light),var(--accent));display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;">{{ $permission->name }}</div>
                                    <div style="font-size:11px;color:var(--text-secondary);">
                                        {{ $permission->guard_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="color:var(--text-secondary);font-size:13px;max-width:300px;">
                                {{ $permission->description ?? '—' }}
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;flex-wrap:wrap;gap:4px;max-width:300px;">
                                @forelse($permission->roles->take(3) as $role)
                                    <span
                                        style="background:var(--accent-light);color:var(--accent);padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600;">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span style="color:var(--text-secondary);font-size:12px;">—</span>
                                @endforelse
                                @if ($permission->roles->count() > 3)
                                    <span
                                        style="background:var(--bg-input);padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600;color:var(--text-secondary);">
                                        +{{ $permission->roles->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button wire:click="openEdit({{ $permission->id }})"
                                    class="btn btn-outline btn-sm btn-edit">
                                    <i class="fas fa-pen"></i> Edit
                                </button>
                                <button wire:click="confirmDelete({{ $permission->id }})" class="btn btn-outline btn-sm"
                                    style="border-color:var(--danger);color:var(--danger);">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="fas fa-lock"></i>
                                <h4>No permissions yet</h4>
                                <p>Create your first permission to get started with role-based access control.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrapper">
            <p>{{ $permissions->total() }} permissions total</p>
            <div class="pagination-links">{{ $permissions->links() }}</div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if ($showModal)
        <div class="modal-overlay open" wire:click.self="$set('showModal', false)">
            <div class="modal" style="max-width:600px;width:95%;">
                <h3><i class="fas fa-lock" style="color:var(--accent);"></i>
                    {{ $editId ? 'Edit Permission' : 'Create New Permission' }}</h3>
                <form wire:submit="save">
                    <div class="form-group" style="margin-bottom:20px;">
                        <label>Permission Name</label>
                        <input wire:model="permissionName" type="text"
                            placeholder="e.g. view addresses, create reports, delete users"
                            style="font-family: monospace; font-size: 13px;">
                        @error('permissionName')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                        <div style="font-size:11px;color:var(--text-secondary);margin-top:4px;">
                            Use lowercase with spaces. Recommended format: <code
                                style="background:var(--bg-input);padding:2px 4px;border-radius:3px;">action
                                resource</code>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description (Optional)</label>
                        <textarea wire:model="permissionDescription" placeholder="Describe what this permission allows users to do…"
                            rows="3" style="resize: vertical;"></textarea>
                        @error('permissionDescription')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="modal-actions">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save
                            Permission</button>
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
                <h3 style="justify-content:center;">Delete Permission?</h3>
                <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">
                    This permission will be removed from all roles that have it assigned.
                </p>
                <div class="modal-actions" style="justify-content:center;">
                    <button wire:click="$set('showDeleteModal', false)" class="btn btn-outline">Cancel</button>
                    <button wire:click="deletePermission" class="btn btn-danger"><i class="fas fa-trash"></i>
                        Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
