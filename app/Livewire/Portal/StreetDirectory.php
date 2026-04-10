<?php

namespace App\Livewire\Portal;

use App\Models\Street;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.portal')]
#[Title('Street Directory')]
class StreetDirectory extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterTown = '';
    public string $filterType = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $streets = Street::withCount('addresses')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%"))
            ->when($this->filterTown, fn($q) => $q->where('town', $this->filterTown))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->where('status', 'active')
            ->orderBy('name')
            ->paginate(18);

        $towns  = Street::where('status', 'active')->distinct()->pluck('town')->sort()->values();
        $totals = [
            'streets'   => Street::where('status', 'active')->count(),
            'addresses' => \App\Models\Address::where('status', 'active')->count(),
            'towns'     => Street::where('status', 'active')->distinct('town')->count('town'),
        ];

        return view('livewire.portal.street-directory', [
            'streets' => $streets,
            'towns' => $towns,
            'totals' => $totals,
        ]);
    }
}
