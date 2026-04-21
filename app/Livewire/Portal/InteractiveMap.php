<?php

namespace App\Livewire\Portal;

use App\Models\Address;
use App\Models\Street;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Interactive Town Map')]
class InteractiveMap extends Component
{
    public string $selectedTown = '';
    public string $searchQuery = '';

    public function getMapDataProperty()
    {
        $streetsQuery = Street::where('status', 'active');
        $addressesQuery = Address::where('status', 'approved');

        if ($this->selectedTown) {
            $streetsQuery->where('town', $this->selectedTown);
            $addressesQuery->where('town', $this->selectedTown);
        }

        if ($this->searchQuery) {
            $streetsQuery->where('name', 'ilike', "%{$this->searchQuery}%");
            $addressesQuery->where(function($q) {
                $q->where('house_number', 'ilike', "%{$this->searchQuery}%")
                  ->orWhere('owner_name', 'ilike', "%{$this->searchQuery}%");
            });
        }

        return [
            'streets' => $streetsQuery->get(['id', 'name', 'town', 'latitude', 'longitude', 'code']),
            'addresses' => $addressesQuery->get(['id', 'house_number', 'street_id', 'town', 'latitude', 'longitude']),
        ];
    }

    public function getTownsProperty()
    {
        return Street::distinct()->pluck('town')->filter()->values();
    }

    public function render()
    {
        return view('livewire.portal.map', [
            'towns' => $this->towns,
            'mapData' => $this->mapData,
        ]);
    }
}
