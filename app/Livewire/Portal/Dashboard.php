<?php

namespace App\Livewire\Portal;

use App\Models\Address;
use App\Models\StreetApplication;
use App\Models\AddressIndexingRequest;
use App\Models\StreetRevalidation;
use App\Models\FieldReport;
use App\Models\Complaint;
use App\Models\StreetNumberingPlate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public string $activeTab = 'profile';

    // Edit profile
    public bool $editingProfile = false;
    public string $name = '';
    public string $phone = '';
    public string $town = '';

    // Reply Logic
    public bool $showReplyModal = false;
    public string $replyTargetType = '';
    public int $replyTargetId = 0;
    public string $userReply = '';

    public function mount(): void
    {
        $this->name  = auth()->user()->name;
        $this->phone = auth()->user()->phone ?? '';
        $this->town  = auth()->user()->town ?? '';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function openReplyModal(int $id, string $type): void
    {
        $this->replyTargetId = $id;
        $this->replyTargetType = $type;
        $this->showReplyModal = true;
        $this->userReply = '';
    }

    public function submitReply(): void
    {
        $this->validate(['userReply' => 'required|min:3']);

        $model = match($this->replyTargetType) {
            'application'  => StreetApplication::findOrFail($this->replyTargetId),
            'address'      => Address::findOrFail($this->replyTargetId),
            'indexing'     => AddressIndexingRequest::findOrFail($this->replyTargetId),
            'revalidation' => StreetRevalidation::findOrFail($this->replyTargetId),
            'plate'        => StreetNumberingPlate::findOrFail($this->replyTargetId),
            'field_report' => FieldReport::findOrFail($this->replyTargetId),
            'complaint'    => Complaint::findOrFail($this->replyTargetId),
            default        => throw new \Exception('Invalid request type')
        };

        // Update with reply and reset status to pending (per user instruction)
        $updateData = [
            'user_note' => $this->userReply,
        ];

        if ($this->replyTargetType === 'complaint') {
             $updateData['status'] = 'in_progress'; // Complaints use different states
        } else {
             $updateData['status'] = 'pending';
        }

        $model->update($updateData);

        $this->showReplyModal = false;
        $this->dispatch('toast', type: 'success', message: 'Your reply has been submitted and the request status reset to pending.');
    }

    // Wrapper methods to emit events from Blade without using `$emit` inline
    public function emitInitiateStreet(int $requestId): void
    {
        $this->dispatch('initiate-street-payment', $requestId);
    }

    public function emitInitiatePaymentAddress(int $addressId): void
    {
        $this->dispatch('initiate-payment-address', $addressId);
    }

    public function saveProfile(): void
    {
        $this->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'town'  => 'nullable|string|max:100',
        ]);

        auth()->user()->update([
            'name'  => $this->name,
            'phone' => $this->phone,
            'town'  => $this->town,
        ]);

        $this->editingProfile = false;
        $this->dispatch('toast', type: 'success', message: 'Profile updated successfully.');
    }

    public function render()
    {
        $user = auth()->user();

        // Fetch All Request Types
        $applications = StreetApplication::where('user_id', $user->id)->get()->map(fn($item) => [
            'id' => $item->id, 'type' => 'application', 'title' => $item->street_name, 'sub' => $item->town, 
            'status' => $item->status, 'admin_note' => $item->admin_note, 'user_note' => $item->user_note, 'date' => $item->created_at
        ]);

        $indexing = AddressIndexingRequest::where('user_id', $user->id)->get()->map(fn($item) => [
            'id' => $item->id, 'type' => 'indexing', 'title' => 'Indexing: ' . $item->address_line, 'sub' => $item->house_number,
            'status' => $item->status, 'admin_note' => $item->admin_note, 'user_note' => $item->user_note, 'date' => $item->created_at
        ]);

        $plates = StreetNumberingPlate::where('user_id', $user->id)->get()->map(fn($item) => [
            'id' => $item->id, 'type' => 'plate', 'title' => 'Plate: ' . $item->street_name, 'sub' => $item->plate_type,
            'status' => $item->status, 'admin_note' => $item->admin_notes, 'user_note' => $item->user_note, 'date' => $item->created_at
        ]);

        $revalidations = StreetRevalidation::where('user_id', $user->id)->get()->map(fn($item) => [
            'id' => $item->id, 'type' => 'revalidation', 'title' => 'Revalidation: ' . $item->street_name, 'sub' => $item->reason,
            'status' => $item->status, 'admin_note' => $item->admin_note, 'user_note' => $item->user_note, 'date' => $item->created_at
        ]);

        $complaints = Complaint::where('user_id', $user->id)->get()->map(fn($item) => [
            'id' => $item->id, 'type' => 'complaint', 'title' => 'Complaint: ' . $item->subject, 'sub' => $item->type,
            'status' => $item->status, 'admin_note' => $item->admin_response, 'user_note' => $item->user_note, 'date' => $item->created_at
        ]);

        $myRequests = collect()
            ->concat($applications)
            ->concat($indexing)
            ->concat($plates)
            ->concat($revalidations)
            ->concat($complaints)
            ->sortByDesc('date');

        $myAddresses = Address::where('owner_name', $user->name)
            ->orWhere('user_id', $user->id)
            ->latest()->get();

        $stats = [
            'requests'     => $myRequests->count(),
            'addresses'    => $myAddresses->count(),
            'certificates' => $applications->where('status', 'approved')->count(),
        ];

        return view('livewire.portal.dashboard', compact('user', 'stats', 'myRequests', 'myAddresses'));
    }
}
