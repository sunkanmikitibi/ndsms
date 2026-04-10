<?php

namespace App\Livewire\Admin\StreetApplications;

use App\Models\StreetApplication;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Street Applications Production Workflow')]
class ProductionWorkflow extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterFieldOfficer = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    public ?StreetApplication $selectedApplication = null;
    public bool $showDetail = false;
    public bool $showCompleteModal = false;
    public bool $showAssignModal = false;

    public string $completionNotes = '';
    public array $completionPhotos = [];
    public ?int $assignedFieldOfficerId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterFieldOfficer' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDir' => ['except' => 'desc'],
    ];

    public function getApplications()
    {
        $query = StreetApplication::with(['user', 'assignedFieldOfficer'])
            ->whereIn('status', ['approved', 'assigned', 'in_inspection']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference_number', 'ilike', "%{$this->search}%")
                    ->orWhere('street_name', 'ilike', "%{$this->search}%")
                    ->orWhere('town', 'ilike', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterFieldOfficer) {
            $query->whereHas('assignedFieldOfficer', function ($q) {
                $q->where('name', 'ilike', "%{$this->filterFieldOfficer}%");
            });
        }

        return $query->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function viewDetail(StreetApplication $application)
    {
        $this->selectedApplication = $application;
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedApplication = null;
        $this->completionNotes = '';
        $this->completionPhotos = [];
        $this->assignedFieldOfficerId = null;
    }

    public function openCompleteModal(StreetApplication $application)
    {
        $this->selectedApplication = $application;
        $this->showCompleteModal = true;
    }

    public function openAssignModal(StreetApplication $application)
    {
        $this->selectedApplication = $application;
        $this->assignedFieldOfficerId = $application->assigned_field_officer_id;
        $this->showAssignModal = true;
    }

    public function assignFieldOfficer()
    {
        if (!$this->selectedApplication || !$this->assignedFieldOfficerId) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select a field officer',
            ]);
            return;
        }

        try {
            $this->selectedApplication->update([
                'assigned_field_officer_id' => $this->assignedFieldOfficerId,
                'status' => 'assigned',
                'assigned_at' => now(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Application {$this->selectedApplication->reference_number} assigned successfully",
            ]);

            $this->showAssignModal = false;
            $this->assignedFieldOfficerId = null;
            $this->selectedApplication = null;
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error assigning field officer: ' . $e->getMessage(),
            ]);
        }
    }

    public function markAsInspected()
    {
        if (!$this->selectedApplication) {
            return;
        }

        try {
            $this->selectedApplication->update([
                'status' => 'in_inspection',
                'inspection_started_at' => now(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Application {$this->selectedApplication->reference_number} marked as under inspection",
            ]);

            $this->selectedApplication = null;
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error updating status: ' . $e->getMessage(),
            ]);
        }
    }

    public function completeInspection()
    {
        if (!$this->selectedApplication) {
            return;
        }

        try {
            $this->selectedApplication->update([
                'status' => 'inspected',
                'inspection_completed_at' => now(),
                'inspection_notes' => $this->completionNotes,
                'completion_photos' => $this->completionPhotos,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Inspection completed for {$this->selectedApplication->reference_number}",
            ]);

            $this->showCompleteModal = false;
            $this->completionNotes = '';
            $this->completionPhotos = [];
            $this->selectedApplication = null;
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error completing inspection: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterFieldOfficer = '';
        $this->resetPage();
    }

    public function toggleSort(string $column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function render()
    {
        $applications = $this->getApplications();
        $fieldOfficers = User::role('field-officer')->get();

        $stats = [
            'total' => StreetApplication::whereIn('status', ['approved', 'assigned', 'in_inspection', 'inspected'])->count(),
            'approved' => StreetApplication::where('status', 'approved')->count(),
            'assigned' => StreetApplication::where('status', 'assigned')->count(),
            'inInspection' => StreetApplication::where('status', 'in_inspection')->count(),
            'inspected' => StreetApplication::where('status', 'inspected')->count(),
        ];

        $statuses = [
            'approved' => 'Approved',
            'assigned' => 'Assigned to Officer',
            'in_inspection' => 'Under Inspection',
            'inspected' => 'Inspection Complete',
        ];

        return view('livewire.admin.street-applications.production-workflow', [
            'applications' => $applications,
            'stats' => $stats,
            'statuses' => $statuses,
            'fieldOfficers' => $fieldOfficers,
        ]);
    }
}