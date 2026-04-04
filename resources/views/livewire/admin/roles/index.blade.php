<div>
<div class="page-header">
    <div>
        <h2><i class="fas fa-shield-alt" style="color:var(--accent);margin-right:10px;"></i>Roles & Permissions</h2>
        <p>Define roles and granular permission assignments for RBAC</p>
    </div>
    <button wire:click="openCreate" class="btn btn-primary"><i class="fas fa-plus"></i> New Role</button>
</div>

<!-- Search -->
<div class="toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search roles…">
    </div>
</div>

<!-- Roles Table -->
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Role Name</th>
                <th>Permissions</th>
                <th>Users</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-gold));display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <span style="font-weight:700;">{{ $role->name }}</span>
                    </div>
                </td>
                <td>
                    <div style="display:flex;flex-wrap:wrap;gap:4px;max-width:400px;">
                        @forelse($role->permissions->take(5) as $perm)
                            <span style="background:var(--accent-light);color:var(--accent);padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">{{ $perm->name }}</span>
                        @empty
                            <span style="color:var(--text-secondary);font-size:12px;">No permissions</span>
                        @endforelse
                        @if($role->permissions->count() > 5)
                            <span style="background:var(--bg-input);padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;color:var(--text-secondary);">+{{ $role->permissions->count() - 5 }} more</span>
                        @endif
                    </div>
                </td>
                <td>
                    <span class="ward-badge">{{ $role->users->count() }} users</span>
                </td>
                <td>
                    <div style="display:flex;gap:6px;">
                        <button wire:click="openEdit({{ $role->id }})" class="btn btn-outline btn-sm btn-edit"><i class="fas fa-pen"></i> Edit</button>
                        <button wire:click="confirmDelete({{ $role->id }})" class="btn btn-outline btn-sm" style="border-color:var(--danger);color:var(--danger);"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">
                    <div class="empty-state">
                        <i class="fas fa-shield-alt"></i>
                        <h4>No roles yet</h4>
                        <p>Create your first role to get started with RBAC.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-wrapper">
        <p>{{ $roles->total() }} roles total</p>
        <div class="pagination-links">{{ $roles->links() }}</div>
    </div>
</div>

<!-- Create/Edit Modal -->
@if($showModal)
<div class="modal-overlay open" wire:click.self="$set('showModal', false)">
    <div class="modal" style="max-width:680px;width:95%;">
        <h3><i class="fas fa-shield-alt" style="color:var(--accent);"></i> {{ $editId ? 'Edit Role' : 'Create New Role' }}</h3>
        <form wire:submit="save">
            <div class="form-group" style="margin-bottom:20px;">
                <label>Role Name</label>
                <input wire:model="roleName" type="text" placeholder="e.g. editor, field-officer, reviewer">
                @error('roleName')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label style="margin-bottom:10px;display:block;">Assign Permissions</label>
                @foreach($permissions as $group => $perms)
                <div style="margin-bottom:16px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--accent);margin-bottom:8px;">
                        <i class="fas fa-folder" style="margin-right:4px;"></i> {{ ucfirst($group) }}
                    </div>
                    <div class="perm-grid">
                        @foreach($perms as $perm)
                        <div class="perm-item {{ in_array($perm->name, $selectedPermissions) ? 'checked' : '' }}">
                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $perm->name }}" id="perm_{{ $perm->id }}">
                            <label for="perm_{{ $perm->id }}" style="cursor:pointer; flex-grow:1; user-select:none;">{{ $perm->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="modal-actions">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Role</button>
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
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 style="justify-content:center;">Delete Role?</h3>
        <p style="color:var(--text-secondary);font-size:14px;margin-bottom:20px;">Users with this role will lose associated permissions immediately.</p>
        <div class="modal-actions" style="justify-content:center;">
            <button wire:click="$set('showDeleteModal', false)" class="btn btn-outline">Cancel</button>
            <button wire:click="deleteRole" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
        </div>
    </div>
</div>
@endif
</div>
