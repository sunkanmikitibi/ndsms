<?php

namespace App\Livewire\Admin\StreetNumberingPlates;

use App\Models\StreetNumberingPlate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Street Numbering Plates Management')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterWard = '';
    public string $filterType = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    public ?StreetNumberingPlate $selectedRequest = null;
    public bool $showDetail = false;
    public bool $showApproveModal = false;
    public bool $showRejectModal = false;

    public string $approvalNotes = '';
    public string $rejectionReason = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterWard' => ['except' => ''],
        'filterType' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDir' => ['except' => 'desc'],
    ];

    public function getRequests()
    {
        $query = StreetNumberingPlate::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference_number', 'ilike', "%{$this->search}%")
                    ->orWhere('street_name', 'ilike', "%{$this->search}%")
                    ->orWhere('ward', 'ilike', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterWard) {
            $query->where('ward', 'ilike', "%{$this->filterWard}%");
        }

        if ($this->filterType) {
            $query->where('plate_type', $this->filterType);
        }

        return $query->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function viewDetail(StreetNumberingPlate $request)
    {
        $this->selectedRequest = $request;
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedRequest = null;
        $this->approvalNotes = '';
        $this->rejectionReason = '';
    }

    public function openApproveModal(StreetNumberingPlate $request)
    {
        $this->selectedRequest = $request;
        $this->showApproveModal = true;
    }

    public function openRejectModal(StreetNumberingPlate $request)
    {
        $this->selectedRequest = $request;
        $this->showRejectModal = true;
    }

    public function approve()
    {
        if (!$this->selectedRequest) {
            return;
        }

        try {
            $this->selectedRequest->approve($this->approvalNotes);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Request {$this->selectedRequest->reference_number} approved successfully",
            ]);

            $this->showApproveModal = false;
            $this->approvalNotes = '';
            $this->selectedRequest = null;
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error approving request: ' . $e->getMessage(),
            ]);
        }
    }

    public function reject()
    {
        if (!$this->selectedRequest || !$this->rejectionReason) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Rejection reason is required',
            ]);

            return;
        }

        try {
            $this->selectedRequest->reject($this->rejectionReason);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Request {$this->selectedRequest->reference_number} rejected",
            ]);

            $this->showRejectModal = false;
            $this->rejectionReason = '';
            $this->selectedRequest = null;
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error rejecting request: ' . $e->getMessage(),
            ]);
        }
    }

    public function updateStatus(StreetNumberingPlate $request, string $newStatus)
    {
        try {
            $request->update(['status' => $newStatus]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Status updated to {$newStatus}",
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error updating status: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterWard = '';
        $this->filterType = '';
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
        $requests = $this->getRequests();
        $stats = [
            'total' => StreetNumberingPlate::count(),
            'pending' => StreetNumberingPlate::pending()->count(),
            'approved' => StreetNumberingPlate::approved()->count(),
            'inProduction' => StreetNumberingPlate::inProduction()->count(),
            'ready' => StreetNumberingPlate::ready()->count(),
            'completed' => StreetNumberingPlate::completed()->count(),
        ];

        $statuses = [
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'in_production' => 'In Production',
            'ready' => 'Ready for Delivery',
            'delivered' => 'Delivered',
            'installed' => 'Installed',
            'completed' => 'Completed',
        ];

        $plateTypes = [
            'standard' => 'Standard',
            'reflective' => 'Reflective',
            'illuminated' => 'Illuminated',
            'digital' => 'Digital',
        ];

        return view('livewire.admin.street-numbering-plates.index', [
            'requests' => $requests,
            'stats' => $stats,
            'statuses' => $statuses,
            'plateTypes' => $plateTypes,
        ]);
    }
}
