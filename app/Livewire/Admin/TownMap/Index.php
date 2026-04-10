<?php

namespace App\Livewire\Admin\TownMap;

use App\Models\Street;
use App\Models\Address;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Town Map')]
class Index extends Component

{
    public string $selectedTown = '';
    public string $filterType = 'all';

    public function mount()
    {
        // Allow access to super-admin or users with view reports permission
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('view reports')) {
            abort(403, 'Unauthorized access to town map.');
        }
    }

    public function getTownsProperty()
    {
        return Street::select('town')
            ->distinct()
            ->pluck('town')
            ->sort()
            ->values();
    }

    public function getStreetsProperty()
    {
        $query = Street::query();

        if ($this->selectedTown) {
            $query->where('town', $this->selectedTown);
        }

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        return $query->with('addresses')->get();
    }

    public function getAddressesProperty()
    {
        $query = Address::query();

        if ($this->selectedTown) {
            $query->where('town', $this->selectedTown);
        }

        return $query->with('street')->get();
    }

    public function getMapDataProperty()
    {
        $streets = $this->streets;
        $addresses = $this->addresses;

        return [
            'streets' => $streets,
            'addresses' => $addresses,
            'street_count' => $streets->count(),
            'address_count' => $addresses->count(),
            'coverage' => $addresses->count() > 0 ? round(($addresses->where('approval_status', 'approved')->count() / $addresses->count()) * 100, 1) : 0,
        ];
    }

    public function getStreetTypesProperty()
    {
        return Street::select('type')
            ->where('town', $this->selectedTown)
            ->distinct()
            ->pluck('type');
    }

    public function exportTownData()
    {
        if (!auth()->user()->hasPermissionTo('export reports')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to export data.',
            ]);
            return;
        }

        $streets = $this->streets;
        $addresses = $this->addresses;

        $csv = "Type,Name/Location,Coordinates,Status\n";

        foreach ($streets as $street) {
            $coords = $street->latitude && $street->longitude ? "{$street->latitude}, {$street->longitude}" : 'N/A';
            $csv .= "STREET,\"{$street->name}\",\"{$coords}\",\"{$street->status}\"\n";
        }

        foreach ($addresses as $address) {
            $coords = $address->latitude && $address->longitude ? "{$address->latitude}, {$address->longitude}" : 'N/A';
            $csv .= "ADDRESS,\"{$address->house_number} {$address->street?->name}\",\"{$coords}\",\"{$address->status}\"\n";
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'town-map-' . ($this->selectedTown ?: 'all') . '-' . now()->format('Y-m-d-His') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.town-map.index', [
            'towns' => $this->towns,
            'streets' => $this->streets,
            'addresses' => $this->addresses,
            'mapData' => $this->mapData,
            'streetTypes' => $this->streetTypes,
        ]);
    }
}
