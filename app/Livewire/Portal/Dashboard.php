<?php

namespace App\Livewire\Portal;

use App\Models\Address;
use App\Models\StreetApplication;
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

        $myRequests     = StreetApplication::where('user_id', $user->id)->latest()->get();
        $myAddresses    = Address::where('owner_name', $user->name)->latest()->get();

        $stats = [
            'requests'     => $myRequests->count(),
            'addresses'    => $myAddresses->count(),
            'certificates' => $myRequests->where('status', 'approved')->count(),
        ];

        return view('livewire.portal.dashboard', compact('user', 'stats', 'myRequests', 'myAddresses'));
    }
}
