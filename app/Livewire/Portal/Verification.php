<?php

namespace App\Livewire\Portal;

use App\Models\Address;
use App\Models\Street;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Verification')]
class Verification extends Component
{
    public string $lookup = '';
    public ?Address $result = null;
    public bool $searched = false;
    public bool $notFound = false;

    public function search(): void
    {
        $this->validate(['lookup' => 'required|string|min:2']);

        $this->result = Address::with('street')
            ->where('house_number', 'like', "%{$this->lookup}%")
            ->orWhereHas('street', fn($q) => $q->where('name', 'like', "%{$this->lookup}%"))
            ->first();

        $this->searched = true;
        $this->notFound = !$this->result;
    }

    public function reset_search(): void
    {
        $this->reset(['lookup', 'result', 'searched', 'notFound']);
    }

    public function render()
    {
        return view('livewire.portal.verification');
    }
}
