<?php

namespace App\Livewire\Admin\Approvals;

use App\Models\Address;
use App\Models\StreetApplication;
use App\Models\FieldReport;
use App\Models\AddressIndexingRequest;
use App\Models\StreetRevalidation;
use App\Models\StreetNumberingPlate;
use App\Models\Complaint;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use App\Services\SmsNotificationService;
use App\Services\InAppNotificationService;
use Illuminate\Support\Facades\Auth;

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

    protected function notifyCitizenStatusChange(object $model, string $type, string $newStatus, ?string $note = null): void
    {
        // Best-effort; never block admin workflow on notifications.
        try {
            $userId = $model->user_id ?? null;
            if (!$userId) {
                return;
            }

            $service = app(InAppNotificationService::class);

            $ref = match ($type) {
                'address' => $model->reference_code ?? ('#' . $model->id),
                'plate' => $model->reference_number ?? ('#' . $model->id),
                default => '#' . $model->id,
            };

            $titlePrefix = match ($type) {
                'application' => 'Street Application',
                'field_report' => 'Field Report',
                'address' => 'Address Registration',
                'indexing' => 'Address Indexing',
                'revalidation' => 'Street Revalidation',
                'plate' => 'Plate Request',
                'complaint' => 'Complaint',
                default => ucfirst(str_replace('_', ' ', $type)),
            };

            $actionUrl = match ($type) {
                'application' => route('portal.register-street'),
                'field_report' => route('portal.dashboard'),
                'address' => route('portal.register-address'),
                'indexing' => route('portal.register-address-indexing'),
                'revalidation' => route('portal.street-revalidation'),
                'plate' => route('portal.request-numbering-plates', ['ref' => $model->reference_number ?? null]),
                'complaint' => route('portal.complaints'),
                default => route('portal.dashboard'),
            };

            $meta = ['type' => $type, 'id' => $model->id, 'status' => $newStatus, 'reference' => $ref];

            if ($newStatus === 'approved') {
                $service->notifyApproval(
                    $userId,
                    "{$titlePrefix} Approved",
                    "Your {$titlePrefix} ({$ref}) has been approved.",
                    $actionUrl,
                    $meta
                );
            } elseif ($newStatus === 'rejected') {
                $service->notifyRejection(
                    $userId,
                    "{$titlePrefix} Rejected",
                    "Your {$titlePrefix} ({$ref}) was rejected." . ($note ? " Reason: {$note}" : ''),
                    $actionUrl,
                    array_merge($meta, $note ? ['reason' => $note] : [])
                );
            } elseif ($newStatus === 'awaiting_payment') {
                $service->notifyPayment(
                    $userId,
                    'Payment Required',
                    "Your {$titlePrefix} ({$ref}) is awaiting payment to proceed.",
                    route('portal.payments.index'),
                    $meta
                );
            } elseif ($type === 'complaint' && $newStatus === 'resolved') {
                $service->notifySuccess(
                    $userId,
                    'Complaint Resolved',
                    "Your complaint ({$ref}) has been resolved. A response has been provided.",
                    $actionUrl,
                    $meta
                );
            } else {
                $service->notifyInfo(
                    $userId,
                    "{$titlePrefix} Updated",
                    "Your {$titlePrefix} ({$ref}) status is now: " . str_replace('_', ' ', $newStatus) . ".",
                    $actionUrl,
                    $meta
                );
            }

            $this->dispatch('notification-created');
        } catch (\Throwable) {
            // swallow
        }
    }

    protected function notifyCitizenNote(object $model, string $type, string $note): void
    {
        try {
            $userId = $model->user_id ?? null;
            if (!$userId) return;

            $service = app(InAppNotificationService::class);

            $ref = match ($type) {
                'address' => $model->reference_code ?? ('#' . $model->id),
                'plate' => $model->reference_number ?? ('#' . $model->id),
                default => '#' . $model->id,
            };

            $titlePrefix = match ($type) {
                'application' => 'Street Application',
                'field_report' => 'Field Report',
                'address' => 'Address Registration',
                'indexing' => 'Address Indexing',
                'revalidation' => 'Street Revalidation',
                'plate' => 'Plate Request',
                'complaint' => 'Complaint',
                default => ucfirst(str_replace('_', ' ', $type)),
            };

            $actionUrl = match ($type) {
                'application' => route('portal.register-street'),
                'address' => route('portal.register-address'),
                'indexing' => route('portal.register-address-indexing'),
                'revalidation' => route('portal.street-revalidation'),
                'plate' => route('portal.request-numbering-plates', ['ref' => $model->reference_number ?? null]),
                'complaint' => route('portal.complaints'),
                default => route('portal.dashboard'),
            };

            $service->notifyInfo(
                $userId,
                'New Admin Note',
                "An official note was added to your {$titlePrefix} ({$ref}). Please log in to view it.",
                $actionUrl,
                ['type' => $type, 'id' => $model->id, 'reference' => $ref]
            );

            $this->dispatch('notification-created');
        } catch (\Throwable) {
            // swallow
        }
    }

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
            'application'  => StreetApplication::find($id),
            'field_report' => FieldReport::find($id),
            'address'      => Address::find($id),
            'indexing'     => AddressIndexingRequest::find($id),
            'revalidation' => StreetRevalidation::find($id),
            'plate'        => StreetNumberingPlate::find($id),
            'complaint'    => Complaint::find($id),
            default        => null
        };
        
        $this->adminNote = $model?->admin_note ?? ($model?->admin_notes ?? ($model?->admin_response ?? ''));
        $this->showModal = true;
    }

    public function approve(int $id, string $type = 'application'): void
    {
        $model = match($type) {
            'application'  => StreetApplication::findOrFail($id),
            'field_report' => FieldReport::findOrFail($id),
            'address'      => Address::findOrFail($id),
            'indexing'     => AddressIndexingRequest::findOrFail($id),
            'revalidation' => StreetRevalidation::findOrFail($id),
            'plate'        => StreetNumberingPlate::findOrFail($id),
            'complaint'    => Complaint::findOrFail($id),
            default        => throw new \Exception('Invalid type')
        };
        
        if ($type === 'complaint') {
            $model->update([
                'status'         => 'resolved',
                'admin_response' => $this->adminNote,
                'admin_id'       => Auth::id(),
                'responded_at'   => now(),
            ]);
            $this->notifyCitizenStatusChange($model, $type, 'resolved', $this->adminNote);
        } else {
            $model->update([
                'status'      => 'approved',
                'admin_note'  => $this->adminNote,
                'admin_notes' => $this->adminNote, // Handle plate's naming convention
                'reviewed_at' => now(),
            ]);
            $this->notifyCitizenStatusChange($model, $type, 'approved', $this->adminNote);
        }
        
        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: ucfirst(str_replace('_', ' ', $type)) . ' ' . ($type === 'complaint' ? 'resolved' : 'approved') . '.');
    }

    public function reject(int $id, string $type = 'application'): void
    {
        $model = match($type) {
            'application'  => StreetApplication::findOrFail($id),
            'field_report' => FieldReport::findOrFail($id),
            'address'      => Address::findOrFail($id),
            'indexing'     => AddressIndexingRequest::findOrFail($id),
            'revalidation' => StreetRevalidation::findOrFail($id),
            'plate'        => StreetNumberingPlate::findOrFail($id),
            'complaint'    => Complaint::findOrFail($id),
            default        => throw new \Exception('Invalid type')
        };
        
        $status = ($type === 'complaint') ? 'closed' : 'rejected';

        $model->update([
            'status'      => $status,
            'admin_note'  => $this->adminNote,
            'admin_notes' => $this->adminNote,
            'reviewed_at' => now(),
        ]);

        $this->notifyCitizenStatusChange($model, $type, $status, $this->adminNote);
        
        $this->showModal = false;
        $this->dispatch('toast', type: 'error', message: ucfirst(str_replace('_', ' ', $type)) . ' ' . $status . '.');
    }

    public function markAwaitingPayment(int $id, string $type = 'application'): void
    {
        $model = match($type) {
            'application'  => StreetApplication::findOrFail($id),
            'address'      => Address::findOrFail($id),
            'indexing'     => AddressIndexingRequest::findOrFail($id),
            'revalidation' => StreetRevalidation::findOrFail($id),
            'plate'        => StreetNumberingPlate::findOrFail($id),
            default        => throw new \Exception('Invalid type for payment')
        };

        $model->update([
            'status'      => 'awaiting_payment',
            'admin_note'  => $this->adminNote,
            'admin_notes' => $this->adminNote,
            'user_note'   => null, // Clear user note when moving to payment
        ]);

        $this->notifyCitizenStatusChange($model, $type, 'awaiting_payment', $this->adminNote);
        
        $this->showModal = false;
        $this->dispatch('toast', type: 'info', message: 'Marked as awaiting payment.');
    }

    public function saveNoteOnly(SmsNotificationService $smsService): void
    {
        if (!$this->viewId) return;

        $model = match($this->viewType) {
            'application'  => StreetApplication::findOrFail($this->viewId),
            'field_report' => FieldReport::findOrFail($this->viewId),
            'address'      => Address::findOrFail($this->viewId),
            'indexing'     => AddressIndexingRequest::findOrFail($this->viewId),
            'revalidation' => StreetRevalidation::findOrFail($this->viewId),
            'plate'        => StreetNumberingPlate::findOrFail($this->viewId),
            'complaint'    => Complaint::findOrFail($this->viewId),
            default        => throw new \Exception('Invalid type')
        };

        if ($this->viewType === 'complaint') {
            $model->update(['admin_response' => $this->adminNote]);
        } elseif ($this->viewType === 'plate') {
            $model->update(['admin_notes' => $this->adminNote]);
        } else {
            $model->update(['admin_note' => $this->adminNote]);
        }

        if (trim($this->adminNote) !== '') {
            $this->notifyCitizenNote($model, $this->viewType, $this->adminNote);
        }

        // Trigger Notification
        $user = $model->user;
        if ($user && $user->phone) {
            $ref = match($this->viewType) {
                'address' => $model->reference_code,
                'plate'   => $model->reference_number,
                default   => "#" . $model->id
            };
            
            $smsService->sendNoteNotification($user->phone, ucfirst($this->viewType), $ref);
        }
        
        $this->dispatch('toast', type: 'success', message: 'Note saved and citizen notified.');
    }

    public function render()
    {
        $applications = StreetApplication::with(['user', 'payment'])
            ->when($this->search, fn($q) => $q->where('street_name', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->get();

        $fieldReports = FieldReport::with(['user', 'payment'])
            ->when($this->search, fn($q) => $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->get();

        $addressRequests = Address::with(['street', 'payment'])
            ->when($this->search, fn($q) => $q->where('house_number', 'like', "%{$this->search}%")
                ->orWhere('applicant_name', 'like', "%{$this->search}%")
                ->orWhereHas('street', fn($s) => $s->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->get();

        $indexingRequests = AddressIndexingRequest::with(['user', 'payment'])
            ->when($this->search, fn($q) => $q->where('address_line', 'like', "%{$this->search}%")
                ->orWhere('applicant_name', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->get();

        $revalidationRequests = StreetRevalidation::with(['user', 'payment'])
            ->when($this->search, fn($q) => $q->where('street_name', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->get();

        $plateRequests = StreetNumberingPlate::with(['user', 'payment'])
            ->when($this->search, fn($q) => $q->where('street_name', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->get();

        $complaints = Complaint::with(['user'])
            ->when($this->search, fn($q) => $q->where('subject', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")))
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
            'payment_status' => $item->payment?->status ?? 'unpaid',
            'created_at' => $item->created_at,
        ])->concat($fieldReports->map(fn($item) => [
            'id' => $item->id,
            'type' => 'field_report',
            'display_type' => 'Field Agent Report',
            'title' => $item->data['street_name'] ?? ($item->data['proposed_name'] ?? 'Street Proposal'),
            'user_name' => $item->user?->name ?? 'Agent',
            'status' => $item->status,
            'payment_status' => 'exempt',
            'created_at' => $item->created_at,
        ]))->concat($addressRequests->map(fn($item) => [
            'id' => $item->id,
            'type' => 'address',
            'display_type' => 'Address Registration',
            'title' => $item->house_number . ', ' . ($item->street?->name ?? 'Unknown Street'),
            'user_name' => $item->applicant_name ?? 'Applicant',
            'status' => $item->status,
            'payment_status' => $item->payment?->status ?? 'unpaid',
            'created_at' => $item->created_at,
        ]))->concat($indexingRequests->map(fn($item) => [
            'id' => $item->id,
            'type' => 'indexing',
            'display_type' => 'Address Indexing',
            'title' => $item->address_line,
            'user_name' => $item->applicant_name ?? 'Applicant',
            'status' => $item->status,
            'payment_status' => $item->payment?->status ?? 'unpaid',
            'created_at' => $item->created_at,
        ]))->concat($revalidationRequests->map(fn($item) => [
            'id' => $item->id,
            'type' => 'revalidation',
            'display_type' => 'Street Revalidation',
            'title' => $item->street_name,
            'user_name' => $item->user?->name ?? 'User',
            'status' => $item->status,
            'payment_status' => $item->payment?->status ?? 'unpaid',
            'created_at' => $item->created_at,
        ]))->concat($plateRequests->map(fn($item) => [
            'id' => $item->id,
            'type' => 'plate',
            'display_type' => 'Plate Request',
            'title' => 'Plates for ' . $item->street_name,
            'user_name' => $item->user?->name ?? 'User',
            'status' => $item->status,
            'payment_status' => $item->payment?->status ?? 'unpaid',
            'created_at' => $item->created_at,
        ]))->concat($complaints->map(fn($item) => [
            'id' => $item->id,
            'type' => 'complaint',
            'display_type' => 'Feedback / Complaint',
            'title' => $item->subject,
            'user_name' => $item->user?->name ?? 'User',
            'status' => $item->status,
            'payment_status' => 'exempt',
            'created_at' => $item->created_at,
        ]))->sortByDesc('created_at');

        $counts = [
            'pending'          => StreetApplication::where('status', 'pending')->count() + FieldReport::where('status', 'pending')->count() + Address::where('status', 'pending')->count() + AddressIndexingRequest::where('status', 'pending')->count() + StreetRevalidation::where('status', 'pending')->count() + StreetNumberingPlate::where('status', 'pending')->count() + Complaint::where('status', 'new')->count(),
            'approved'         => StreetApplication::where('status', 'approved')->count() + FieldReport::where('status', 'approved')->count() + Address::where('status', 'approved')->count() + AddressIndexingRequest::where('status', 'approved')->count() + StreetRevalidation::where('status', 'approved')->count() + StreetNumberingPlate::where('status', 'approved')->count() + Complaint::where('status', 'resolved')->count(),
            'rejected'         => StreetApplication::where('status', 'rejected')->count() + FieldReport::where('status', 'rejected')->count() + Address::where('status', 'rejected')->count() + AddressIndexingRequest::where('status', 'rejected')->count() + StreetRevalidation::where('status', 'rejected')->count() + StreetNumberingPlate::where('status', 'rejected')->count(),
            'awaiting_payment' => StreetApplication::where('status', 'awaiting_payment')->count() + Address::where('status', 'awaiting_payment')->count() + AddressIndexingRequest::where('status', 'awaiting_payment')->count() + StreetRevalidation::where('status', 'awaiting_payment')->count() + StreetNumberingPlate::where('status', 'awaiting_payment')->count(),
        ];

        $viewItem = null;
        if ($this->viewId) {
            $viewItem = match($this->viewType) {
                'application'  => StreetApplication::with(['user', 'payment'])->find($this->viewId),
                'field_report' => FieldReport::with(['user', 'payment'])->find($this->viewId),
                'address'      => Address::with(['street', 'payment'])->find($this->viewId),
                'indexing'     => AddressIndexingRequest::with(['user', 'payment'])->find($this->viewId),
                'revalidation' => StreetRevalidation::with(['user', 'payment'])->find($this->viewId),
                default        => null
            };
        }

        return view('livewire.admin.approvals.index', [
            'items' => $allItems,
            'counts' => $counts,
            'viewItem' => $viewItem
        ]);
    }
}
