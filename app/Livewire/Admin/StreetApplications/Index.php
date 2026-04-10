<?php

namespace App\Livewire\Admin\StreetApplications;

use App\Models\StreetApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Street Applications')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'all';
    public bool $showModal = false;
    public bool $showDeleteModal = false;

    public ?int $editId = null;
    public ?int $deleteId = null;
    public string $applicationStatus = 'pending';
    public string $adminNote = '';

    protected $rules = [
        'applicationStatus' => 'required|in:pending,approved,rejected',
        'adminNote'        => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->authorize('view', auth()->user());
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function getApplicationsProperty()
    {
        $query = StreetApplication::query();

        if ($this->search) {
            $query->where('street_name', 'like', "%{$this->search}%")
                  ->orWhere('town', 'like', "%{$this->search}%")
                  ->orWhere('applicant_name', 'like', "%{$this->search}%");
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        return $query
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function openView(int $id)
    {
        $application = StreetApplication::findOrFail($id);
        
        $this->editId = $application->id;
        $this->applicationStatus = $application->status;
        $this->adminNote = $application->admin_note ?? '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editId', 'applicationStatus', 'adminNote']);
    }

    public function updateStatus()
    {
        $this->validate();

        try {
            $application = StreetApplication::findOrFail($this->editId);
            $application->update([
                'status'      => $this->applicationStatus,
                'admin_note'  => $this->adminNote,
                'reviewed_at' => now(),
            ]);

            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Application status updated successfully!',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function confirmDelete(int $id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteApplication()
    {
        try {
            if ($this->deleteId) {
                StreetApplication::findOrFail($this->deleteId)->delete();
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Application deleted successfully!',
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }

        $this->showDeleteModal = false;
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.street-applications.index', [
            'applications' => $this->applications,
        ]);
    }
}
