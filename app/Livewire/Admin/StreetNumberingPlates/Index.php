<?php

namespace App\Livewire\Admin\StreetNumberingPlates;

use App\Models\StreetNumberingPlate;
use App\Models\User;
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
    public string $filterTown = '';
    public string $filterType = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    public ?StreetNumberingPlate $selectedRequest = null;
    public bool $showDetail = false;
    public bool $showApproveModal = false;
    public bool $showRejectModal = false;
    public bool $showAssignModal = false;
    public bool $showStatusModal = false;

    public string $approvalNotes = '';
    public string $rejectionReason = '';
    public int $assignedUserId = 0;
    public string $estimatedCompletionDate = '';
    public string $productionNotes = '';
    public string $newStatus = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterTown' => ['except' => ''],
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
                    ->orWhere('town', 'ilike', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterTown) {
            $query->where('town', 'ilike', "%{$this->filterTown}%");
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

    public function openAssignModal(StreetNumberingPlate $request)
    {
        $this->selectedRequest = $request;
        $this->assignedUserId = $request->assigned_to ?? 0;
        $this->estimatedCompletionDate = $request->estimated_completion_date?->format('Y-m-d') ?? '';
        $this->productionNotes = $request->production_notes ?? '';
        $this->showAssignModal = true;
    }

    public function openStatusModal(StreetNumberingPlate $request)
    {
        $this->selectedRequest = $request;
        $this->newStatus = $request->status;
        $this->showStatusModal = true;
    }

    public function assignToProduction()
    {
        if (!$this->selectedRequest || !$this->assignedUserId) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select a production team member',
            ]);
            return;
        }

        try {
            $this->selectedRequest->assignToProduction(
                $this->assignedUserId,
                $this->estimatedCompletionDate ?: null,
                $this->productionNotes
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Request assigned to production team successfully",
            ]);

            $this->showAssignModal = false;
            $this->assignedUserId = 0;
            $this->estimatedCompletionDate = '';
            $this->productionNotes = '';
            $this->selectedRequest = null;
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error assigning to production: ' . $e->getMessage(),
            ]);
        }
    }

    public function updateStatus()
    {
        if (!$this->selectedRequest) {
            return;
        }

        try {
            // Handle status-specific logic
            switch ($this->newStatus) {
                case 'in_production':
                    if (!$this->selectedRequest->production_started_at) {
                        $this->selectedRequest->startProduction();
                    } else {
                        $this->selectedRequest->update(['status' => $this->newStatus]);
                    }
                    break;
                case 'ready':
                    $this->selectedRequest->markReady();
                    if (!$this->selectedRequest->production_completed_at) {
                        $this->selectedRequest->completeProduction();
                    }
                    break;
                case 'delivered':
                    $this->selectedRequest->markDelivered();
                    break;
                case 'installed':
                    $this->selectedRequest->markInstalled();
                    break;
                case 'completed':
                    $this->selectedRequest->complete();
                    break;
                default:
                    $this->selectedRequest->update(['status' => $this->newStatus]);
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Status updated to {$this->getStatuses()[$this->newStatus]}",
            ]);

            $this->showStatusModal = false;
            $this->newStatus = '';
            $this->selectedRequest = null;
            $this->resetPage();
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
        $this->filterTown = '';
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

    public function exportToCsv()
    {
        $filename = 'street_numbering_plates_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Reference Number',
                'Street Name',
                'Town',
                'Plate Type',
                'Quantity',
                'Status',
                'Total Cost',
                'Requester',
                'Phone',
                'Email',
                'Submitted Date',
                'Last Updated'
            ]);

            // Get all requests (not paginated for export)
            $query = StreetNumberingPlate::with('user');

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

            if ($this->filterTown) {
                $query->where('town', 'ilike', "%{$this->filterTown}%");
            }

            if ($this->filterType) {
                $query->where('plate_type', $this->filterType);
            }

            $requests = $query->orderBy($this->sortBy, $this->sortDir)->get();

            // CSV data rows
            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->reference_number,
                    $request->street_name,
                    $request->town,
                    $request->getPlateTypeLabel(),
                    $request->quantity_requested,
                    ucfirst($request->status),
                    '₦' . number_format($request->getTotalCost(), 2),
                    $request->user->name ?? 'N/A',
                    $request->user->phone ?? 'N/A',
                    $request->user->email ?? 'N/A',
                    $request->created_at->format('Y-m-d H:i:s'),
                    $request->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStatuses(): array
    {
        return [
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'awaiting_payment' => 'Awaiting Payment',
            'in_production' => 'In Production',
            'ready' => 'Ready for Delivery',
            'delivered' => 'Delivered',
            'installed' => 'Installed',
            'completed' => 'Completed',
        ];
    }

    public function render()
    {
        $requests = $this->getRequests();
        
        $stats = [
            'total' => StreetNumberingPlate::count(),
            'pending' => StreetNumberingPlate::where('status', 'pending')->count(),
            'approved' => StreetNumberingPlate::where('status', 'approved')->count(),
            'inProduction' => StreetNumberingPlate::where('status', 'in_production')->count(),
            'ready' => StreetNumberingPlate::where('status', 'ready')->count(),
            'completed' => StreetNumberingPlate::whereIn('status', ['installed', 'completed'])->count(),
        ];

        $statuses = $this->getStatuses();

        $plateTypes = [
            'standard' => 'Standard',
            'reflective' => 'Reflective',
            'illuminated' => 'Illuminated',
            'digital' => 'Digital',
        ];

        // Get production team members (admins and field officers)
        $productionTeam = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'field-officer']);
        })->orderBy('name')->get();

        return view('livewire.admin.street-numbering-plates.index', [
            'requests' => $requests,
            'stats' => $stats,
            'statuses' => $statuses,
            'plateTypes' => $plateTypes,
            'productionTeam' => $productionTeam,
        ]);
    }
}

