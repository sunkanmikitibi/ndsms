<?php

namespace App\Livewire\Admin\Permissions;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

#[Layout('components.layouts.admin')]
#[Title('Permissions')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;

    public ?int $editId = null;
    public string $permissionName = '';
    public string $permissionDescription = '';
    public ?int $deleteId = null;

    protected $rules = [
        'permissionName'        => 'required|string|max:100',
        'permissionDescription' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'permissionName.required' => 'Permission name is required',
        'permissionName.max'      => 'Permission name must not exceed 100 characters',
    ];

    public function updatingSearch(): void 
    { 
        $this->resetPage(); 
    }

    public function openCreate(): void
    {
        $this->reset(['editId', 'permissionName', 'permissionDescription']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $permission = Permission::findOrFail($id);
        
        $this->editId = $permission->id;
        $this->permissionName = $permission->name;
        $this->permissionDescription = $permission->description ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->editId) {
                $permission = Permission::findOrFail($this->editId);
                $permission->update([
                    'name' => $this->permissionName,
                    'description' => $this->permissionDescription,
                ]);
                $message = 'Permission updated successfully.';
            } else {
                Permission::create([
                    'name' => $this->permissionName,
                    'guard_name' => 'web',
                    'description' => $this->permissionDescription,
                ]);
                $message = 'Permission created successfully.';
            }

            $this->showModal = false;
            $this->dispatch('toast', type: 'success', message: $message);
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deletePermission(): void
    {
        try {
            if ($this->deleteId) {
                $permission = Permission::findOrFail($this->deleteId);
                
                // Detach from all roles first
                $permission->roles()->detach();
                
                // Delete the permission
                $permission->delete();
                
                $this->dispatch('toast', type: 'success', message: 'Permission deleted successfully.');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Error: ' . $e->getMessage());
        }

        $this->showDeleteModal = false;
        $this->resetPage();
    }

    public function render()
    {
        $permissions = Permission::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->with('roles')
            ->orderBy('guard_name')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.permissions.index', compact('permissions'));
    }
}
