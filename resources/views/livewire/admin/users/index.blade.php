<div>
<div class="page-header">
    <div>
        <h2><i class="fas fa-users" style="color:var(--accent);margin-right:10px;"></i>Users</h2>
        <p>Manage system users and role assignments</p>
    </div>
    <button wire:click="openCreate" class="btn btn-primary"><i class="fas fa-plus"></i> Add User</button>
</div>

<!-- Toolbar -->
<div class="toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users…">
    </div>
    <div style="display:flex;gap:8px;">
        <select wire:model.live="filterRole" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-input);color:var(--text-primary);font-family:'Outfit',sans-serif;font-size:14px;">
            <option value="">All Roles</option>
            @foreach(\Spatie\Permission\Models\Role::orderBy('name')->get() as $r)
                <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Table -->
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent-gold));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span style="font-weight:600;">{{ $user->name }}</span>
                    </div>
                </td>
                <td style="color:var(--text-secondary);">{{ $user->email }}</td>
                <td>
                    @forelse($user->roles as $role)
                        <span class="town-badge" style="margin-right:4px;background:var(--accent-gold-light);color:var(--accent-gold);">{{ $role->name }}</span>
                    @empty
                        <span style="color:var(--text-secondary);font-size:12px;">No role</span>
                    @endforelse
                </td>
                <td style="color:var(--text-secondary);font-size:12px;">{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <div style="display:flex;gap:6px;">
                        <button wire:click="openEdit({{ $user->id }})" class="btn btn-outline btn-sm btn-edit"><i class="fas fa-pen"></i></button>
                        <button wire:click="confirmDelete({{ $user->id }})" class="btn btn-outline btn-sm btn-danger" style="border-color:var(--danger);color:var(--danger);"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h4>No users found</h4>
                        <p>Try adjusting your search or filters.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-wrapper">
        <p>Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users</p>
        <div class="pagination-links">{{ $users->links() }}</div>
    </div>
</div>

<!-- Create/Edit Modal -->
@if($showModal)
<div class="modal-overlay open" wire:click.self="$set('showModal', false)">
    <div class="modal" style="max-width:560px;">
        <h3><i class="fas fa-user-circle" style="color:var(--accent);"></i> {{ $editId ? 'Edit User' : 'Add New User' }}</h3>
        <form wire:submit="save">
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name</label>
                    <input wire:model="name" type="text" placeholder="Enter full name">
                    @error('name')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input wire:model="email" type="email" placeholder="user@example.com">
                    @error('email')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Password {{ $editId ? '(leave blank to keep)' : '' }}</label>
                    <input wire:model="password" type="password" placeholder="Min. 8 characters">
                    @error('password')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group" style="margin-top:16px;">
                <label>Assign Roles</label>
                <div class="perm-grid" style="margin-top:8px;">
                    @foreach($roles as $role)
                    <div class="perm-item {{ in_array($role->name, $selectedRoles) ? 'checked' : '' }}">
                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role->name }}" id="role_{{ $role->id }}">
                        <label for="role_{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" wire:click="$set('showModal', false)" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save User</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Delete Confirm Modal -->
@if($showDeleteModal)
<div class="modal-overlay open" wire:click.self="$set('showDeleteModal', false)">
    <div class="modal" style="max-width:400px;text-align:center;">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--danger-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:var(--danger);">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 style="justify-content:center;">Delete User?</h3>
        <p style="color:var(--text-secondary);font-size:14px;">This action cannot be undone. All data associated with this user will be permanently removed.</p>
        <div class="modal-actions" style="justify-content:center;">
            <button wire:click="$set('showDeleteModal', false)" class="btn btn-outline">Cancel</button>
            <button wire:click="deleteUser" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
        </div>
    </div>
</div>
@endif
</div>
