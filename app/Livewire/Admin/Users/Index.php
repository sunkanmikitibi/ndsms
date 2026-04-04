<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.admin')]
#[Title('Users')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;

    public ?int $editId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public array $selectedRoles = [];
    public ?int $deleteId = null;

    protected function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . ($this->editId ?? 'NULL'),
            'password'      => $this->editId ? 'nullable|min:8' : 'required|min:8',
            'selectedRoles' => 'array',
        ];
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterRole(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['editId', 'name', 'email', 'password', 'selectedRoles']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $user                = User::with('roles')->findOrFail($id);
        $this->editId        = $user->id;
        $this->name          = $user->name;
        $this->email         = $user->email;
        $this->password      = '';
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->showModal     = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            $user        = User::findOrFail($this->editId);
            $user->name  = $this->name;
            $user->email = $this->email;
            if ($this->password) {
                $user->password = bcrypt($this->password);
            }
            $user->save();
        } else {
            $user = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => bcrypt($this->password),
            ]);
        }

        $user->syncRoles($this->selectedRoles);
        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: 'User saved successfully.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId       = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        if ($this->deleteId) {
            User::findOrFail($this->deleteId)->delete();
            $this->dispatch('toast', type: 'success', message: 'User deleted.');
        }
        $this->showDeleteModal = false;
    }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->filterRole, fn($q) => $q->role($this->filterRole))
            ->latest()
            ->paginate(15);

        $roles = Role::orderBy('name')->get();

        return view('livewire.admin.users.index', compact('users', 'roles'));
    }
}
