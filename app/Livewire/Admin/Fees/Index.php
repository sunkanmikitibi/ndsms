<?php

namespace App\Livewire\Admin\Fees;

use App\Models\FeeSchedule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Fee Schedule Management')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'active';
    public bool $showModal = false;
    public bool $showDeleteModal = false;

    public ?int $editId = null;
    public string $serviceType = '';
    public string $serviceName = '';
    public string $description = '';
    public float $baseAmount = 0;
    public string $currency = 'NGN';
    public string $status = 'active';
    public ?string $effectiveFrom = '';
    public ?string $effectiveTo = '';
    public ?int $deleteId = null;

    protected $rules = [
        'serviceType'   => 'required|string|max:50',
        'serviceName'   => 'required|string|max:255',
        'description'   => 'nullable|string|max:500',
        'baseAmount'    => 'required|numeric|min:0.01|max:999999.99',
        'currency'      => 'required|string|max:3',
        'status'        => 'required|in:active,inactive,archived',
        'effectiveFrom' => 'nullable|date_format:Y-m-d H:i',
        'effectiveTo'   => 'nullable|date_format:Y-m-d H:i|after_or_equal:effectiveFrom',
    ];

    protected $messages = [
        'baseAmount.required' => 'Fee amount is required',
        'baseAmount.numeric'  => 'Fee amount must be a number',
        'baseAmount.min'      => 'Fee amount must be at least 0.01',
        'serviceName.required' => 'Service name is required',
    ];

    public function mount()
    {
        // Allow access to super-admin or users with view fee schedules permission
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('view fee schedules')) {
            abort(403, 'Unauthorized access to fee schedules.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function getFeeSchedulesProperty()
    {
        $query = FeeSchedule::query();

        if ($this->search) {
            $query->where('service_name', 'like', "%{$this->search}%")
                  ->orWhere('service_type', 'like', "%{$this->search}%");
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        return $query
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function getServiceTypesProperty()
    {
        return FeeSchedule::SERVICE_TYPES;
    }

    public function openCreate()
    {
        // Check permission to manage fee schedules (super-admin and those with manage permission)
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('manage fee schedules')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to create fee schedules.',
            ]);
            return;
        }

        $this->reset([
            'editId',
            'serviceType',
            'serviceName',
            'description',
            'baseAmount',
            'currency',
            'status',
            'effectiveFrom',
            'effectiveTo',
        ]);
        $this->currency = 'NGN';
        $this->status = 'active';
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        // Check permission to manage fee schedules (super-admin and those with manage permission)
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('manage fee schedules')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to edit fee schedules.',
            ]);
            return;
        }

        $fee = FeeSchedule::findOrFail($id);

        $this->editId = $fee->id;
        $this->serviceType = $fee->service_type;
        $this->serviceName = $fee->service_name;
        $this->description = $fee->description ?? '';
        $this->baseAmount = (float) $fee->base_amount;
        $this->currency = $fee->currency;
        $this->status = $fee->status;
        $this->effectiveFrom = $fee->effective_from?->format('Y-m-d H:i');
        $this->effectiveTo = $fee->effective_to?->format('Y-m-d H:i');
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset([
            'editId',
            'serviceType',
            'serviceName',
            'description',
            'baseAmount',
            'currency',
            'status',
            'effectiveFrom',
            'effectiveTo',
        ]);
    }

    public function save()
    {
        // Check permission to manage fee schedules (super-admin and those with manage permission)
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('manage fee schedules')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to save fee schedules.',
            ]);
            return;
        }

        $this->validate();

        try {
            $data = [
                'service_type'   => $this->serviceType,
                'service_name'   => $this->serviceName,
                'description'    => $this->description,
                'base_amount'    => $this->baseAmount,
                'currency'       => $this->currency,
                'status'         => $this->status,
                'effective_from' => $this->effectiveFrom ? now()->parse($this->effectiveFrom) : null,
                'effective_to'   => $this->effectiveTo ? now()->parse($this->effectiveTo) : null,
            ];

            if ($this->editId) {
                FeeSchedule::findOrFail($this->editId)->update($data);
                $message = 'Fee schedule updated successfully!';
            } else {
                FeeSchedule::create($data);
                $message = 'Fee schedule created successfully!';
            }

            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message,
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
        // Check permission to manage fee schedules (super-admin and those with manage permission)
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('manage fee schedules')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to delete fee schedules.',
            ]);
            return;
        }

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteFee()
    {
        // Double-check permission before deleting (super-admin and those with manage permission)
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('manage fee schedules')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to delete fee schedules.',
            ]);
            $this->showDeleteModal = false;
            return;
        }

        try {
            if ($this->deleteId) {
                FeeSchedule::findOrFail($this->deleteId)->delete();
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Fee schedule deleted successfully!',
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

    public function toggleStatus(int $id)
    {
        try {
            $fee = FeeSchedule::findOrFail($id);
            $newStatus = $fee->status === 'active' ? 'inactive' : 'active';
            $fee->update(['status' => $newStatus]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Fee schedule marked as {$newStatus}.",
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.fees.index', [
            'fees' => $this->feeSchedules,
            'serviceTypes' => $this->serviceTypes,
        ]);
    }
}
