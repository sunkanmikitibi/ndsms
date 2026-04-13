<?php

namespace App\Livewire\Admin\FieldReports;

use App\Models\FieldReport;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Field Reports')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'all';
    public bool $showModal = false;
    public bool $showDeleteModal = false;

    public ?int $viewId = null;
    public ?int $deleteId = null;
    public string $reportStatus = 'pending';
    public string $adminNote = '';

    protected $rules = [
        'reportStatus' => 'required|in:pending,reviewed,closed',
        'adminNote'    => 'nullable|string|max:500',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function getReportsProperty()
    {
        $query = FieldReport::query();

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('location', 'like', "%{$this->search}%");
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
        $report = FieldReport::findOrFail($id);
        
        $this->viewId = $report->id;
        $this->reportStatus = $report->status;
        $this->adminNote = $report->admin_note ?? '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['viewId', 'reportStatus', 'adminNote']);
    }

    public function updateStatus()
    {
        $this->validate();

        try {
            $report = FieldReport::findOrFail($this->viewId);
            $report->update([
                'status'     => $this->reportStatus,
                'admin_note' => $this->adminNote,
            ]);

            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Report status updated successfully!',
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

    public function deleteReport()
    {
        try {
            if ($this->deleteId) {
                FieldReport::findOrFail($this->deleteId)->delete();
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Report deleted successfully!',
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
        return view('livewire.admin.field-reports.index', [
            'reports' => $this->reports,
        ]);
    }
}
