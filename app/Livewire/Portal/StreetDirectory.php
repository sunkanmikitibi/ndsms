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
    public string $filterWard = '';
    public string $filterType = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $streets = Street::withCount('addresses')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%"))
            ->when($this->filterWard, fn($q) => $q->where('ward', $this->filterWard))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->where('status', 'active')
            ->orderBy('name')
            ->paginate(18);

        $wards  = Street::where('status', 'active')->distinct()->pluck('ward')->sort()->values();
        $totals = [
            'streets'   => Street::where('status', 'active')->count(),
            'addresses' => \App\Models\Address::where('status', 'active')->count(),
            'wards'     => Street::where('status', 'active')->distinct('ward')->count('ward'),
        ];

        return view('livewire.portal.street-directory', compact('streets', 'wards', 'totals'));
    }
}
