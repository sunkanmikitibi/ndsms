<?php

namespace App\Livewire\Admin\AddressIndexing;

use App\Models\AddressIndexingRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Address Indexing Requests')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    public bool $showDetail = false;
    public bool $showApproveModal = false;
    public bool $showRejectModal = false;

    public ?AddressIndexingRequest $selectedRequest = null;
    public string $approvalNotes = '';
    public string $rejectionReason = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        return [
            'total' => AddressIndexingRequest::count(),
            'pending' => AddressIndexingRequest::where('status', 'pending')->count(),
            'approved' => AddressIndexingRequest::where('status', 'approved')->count(),
            'rejected' => AddressIndexingRequest::where('status', 'rejected')->count(),
            'verified' => AddressIndexingRequest::where('status', 'verified')->count(),
        ];
    }

    public function getRequestsProperty()
    {
        $query = AddressIndexingRequest::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('address_line', 'like', "%{$this->search}%")
                  ->orWhere('house_number', 'like', "%{$this->search}%")
                  ->orWhere('owner_name', 'like', "%{$this->search}%")
                  ->orWhere('applicant_name', 'like', "%{$this->search}%")
                  ->orWhere('owner_phone', 'like', "%{$this->search}%")
                  ->orWhere('applicant_phone', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $query->orderBy($this->sortBy, $this->sortDir);

        return $query->with('user')->paginate($this->perPage);
    }

    public function toggleSort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function viewDetail(int $id)
    {
        $this->selectedRequest = AddressIndexingRequest::with('user')->findOrFail($id);
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedRequest = null;
    }

    public function openApproveModal(int $id)
    {
        $this->selectedRequest = AddressIndexingRequest::findOrFail($id);
        $this->approvalNotes = '';
        $this->showApproveModal = true;
    }

    public function openRejectModal(int $id)
    {
        $this->selectedRequest = AddressIndexingRequest::findOrFail($id);
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function approve()
    {
        try {
            if (!$this->selectedRequest) {
                return;
            }

            $this->selectedRequest->update([
                'status' => 'approved',
                'admin_note' => $this->approvalNotes,
                'reviewed_at' => now(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Request approved successfully!',
            ]);

            $this->showApproveModal = false;
            $this->showDetail = false;
            $this->selectedRequest = null;
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function reject()
    {
        try {
            if (!$this->selectedRequest || !$this->rejectionReason) {
                return;
            }

            $this->selectedRequest->update([
                'status' => 'rejected',
                'admin_note' => $this->rejectionReason,
                'reviewed_at' => now(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Request rejected successfully!',
            ]);

            $this->showRejectModal = false;
            $this->showDetail = false;
            $this->selectedRequest = null;
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.address-indexing.index', [
            'stats' => $this->stats,
            'requests' => $this->requests,
            'statuses' => [
                'pending' => 'Pending Review',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'verified' => 'Verified',
                'indexed' => 'Indexed',
            ],
        ]);
    }
}
