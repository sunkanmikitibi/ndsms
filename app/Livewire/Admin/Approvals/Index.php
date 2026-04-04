<?php

namespace App\Livewire\Admin\Approvals;

use App\Models\Address;
use App\Models\StreetApplication;
use App\Models\FieldReport;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;

#[Layout('components.layouts.admin')]
#[Title('Approvals')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'pending';
    public string $filterType = ''; // 'application', 'field_report', or 'address'

    public bool $showModal = false;
    public ?int $viewId = null;
    public string $viewType = 'application'; // 'application', 'field_report', or 'address'
    public string $adminNote = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function setFilter(string $status): void
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function viewApplication(int $id, string $type = 'application'): void
    {
        $this->viewId    = $id;
        $this->viewType  = $type;
        
        $model = match($type) {
            'application' => StreetApplication::find($id),
            'field_report' => FieldReport::find($id),
            'address' => Address::find($id),
            default => null
        };
        
        $this->adminNote = $model?->admin_note ?? '';
        $this->showModal = true;
    }

    public function approve(int $id, string $type = 'application'): void
    {
        $model = match($type) {
            'application' => StreetApplication::findOrFail($id),
            'field_report' => FieldReport::findOrFail($id),
            'address' => Address::findOrFail($id),
            default => throw new \Exception('Invalid type')
        };
        
        $model->update([
            'status'      => 'approved',
            'admin_note'  => $this->adminNote,
            'reviewed_at' => now(),
        ]);
        
        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: ucfirst(str_replace('_', ' ', $type)) . ' approved.');
    }

    public function reject(int $id, string $type = 'application'): void
    {
        $model = match($type) {
            'application' => StreetApplication::findOrFail($id),
            'field_report' => FieldReport::findOrFail($id),
            'address' => Address::findOrFail($id),
            default => throw new \Exception('Invalid type')
        };
        
        $model->update([
            'status'      => 'rejected',
            'admin_note'  => $this->adminNote,
            'reviewed_at' => now(),
        ]);
        
        $this->showModal = false;
        $this->dispatch('toast', type: 'error', message: ucfirst(str_replace('_', ' ', $type)) . ' rejected.');
    }

    public function markAwaitingPayment(int $id): void
    {
        StreetApplication::findOrFail($id)->update([
            'status'     => 'awaiting_payment',
            'admin_note' => $this->adminNote,
        ]);
        $this->showModal = false;
        $this->dispatch('toast', type: 'info', message: 'Marked as awaiting payment.');
    }

    public function render()
    {
        $applications = StreetApplication::with('user')
            ->when($this->search, fn($q) => $q->where('street_name', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType === 'application', fn($q) => $q->where('status', '!=', 'none'))
            ->latest()
            ->get();

        $fieldReports = FieldReport::with('user')
            ->when($this->search, fn($q) => $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->get();

        $addressRequests = Address::with('street')
            ->when($this->search, fn($q) => $q->where('house_number', 'like', "%{$this->search}%")
                ->orWhere('applicant_name', 'like', "%{$this->search}%")
                ->orWhereHas('street', fn($s) => $s->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->get();

        // Merge and sort for the UI
        $allItems = $applications->map(fn($item) => [
            'id' => $item->id,
            'type' => 'application',
            'display_type' => 'Street Application',
            'title' => $item->street_name,
            'user_name' => $item->user?->name ?? 'User',
            'status' => $item->status,
            'created_at' => $item->created_at,
        ])->concat($fieldReports->map(fn($item) => [
            'id' => $item->id,
            'type' => 'field_report',
            'display_type' => 'Field Report (' . strtoupper(str_replace('_', ' ', $item->type)) . ')',
            'title' => $item->data['street_name'] ?? ($item->data['proposed_name'] ?? 'New Street Suggestion'),
            'user_name' => $item->user?->name ?? 'Agent',
            'status' => $item->status,
            'created_at' => $item->created_at,
        ]))->concat($addressRequests->map(fn($item) => [
            'id' => $item->id,
            'type' => 'address',
            'display_type' => 'Address Registration',
            'title' => $item->house_number . ', ' . ($item->street?->name ?? 'Unknown Street'),
            'user_name' => $item->applicant_name ?? 'Applicant',
            'status' => $item->status,
            'created_at' => $item->created_at,
        ]))->sortByDesc('created_at');

        $counts = [
            'pending'          => StreetApplication::where('status', 'pending')->count() + FieldReport::where('status', 'pending')->count() + Address::where('status', 'pending')->count(),
            'approved'         => StreetApplication::where('status', 'approved')->count() + FieldReport::where('status', 'approved')->count() + Address::where('status', 'approved')->count(),
            'rejected'         => StreetApplication::where('status', 'rejected')->count() + FieldReport::where('status', 'rejected')->count() + Address::where('status', 'rejected')->count(),
            'awaiting_payment' => StreetApplication::where('status', 'awaiting_payment')->count(),
        ];

        $viewItem = null;
        if ($this->viewId) {
            $viewItem = match($this->viewType) {
                'application' => StreetApplication::with('user')->find($this->viewId),
                'field_report' => FieldReport::with('user')->find($this->viewId),
                'address' => Address::with('street')->find($this->viewId),
                default => null
            };
        }

        return view('livewire.admin.approvals.index', [
            'items' => $allItems,
            'counts' => $counts,
            'viewItem' => $viewItem
        ]);
    }
}
