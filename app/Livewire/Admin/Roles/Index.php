<?php

namespace App\Livewire\Admin\Roles;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.admin')]
#[Title('Roles & Permissions')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;

    public ?int $editId = null;
    public string $roleName = '';
    public array $selectedPermissions = [];
    public ?int $deleteId = null;

    protected $rules = [
        'roleName'            => 'required|string|max:100',
        'selectedPermissions' => 'array',
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['editId', 'roleName', 'selectedPermissions']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $role                      = Role::with('permissions')->findOrFail($id);
        $this->editId              = $role->id;
        $this->roleName            = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->showModal           = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            $role       = Role::findOrFail($this->editId);
            $role->name = $this->roleName;
            $role->save();
        } else {
            $role = Role::create(['name' => $this->roleName, 'guard_name' => 'web']);
        }

        $role->syncPermissions($this->selectedPermissions);
        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: 'Role saved successfully.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId       = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRole(): void
    {
        if ($this->deleteId) {
            Role::findOrFail($this->deleteId)->delete();
            $this->dispatch('toast', type: 'success', message: 'Role deleted.');
        }
        $this->showDeleteModal = false;
    }

    public function render()
    {
        $roles = Role::with(['permissions', 'users'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            $parts = explode(' ', $p->name);
            return $parts[1] ?? 'general';
        });

        return view('livewire.admin.roles.index', compact('roles', 'permissions'));
    }
}
