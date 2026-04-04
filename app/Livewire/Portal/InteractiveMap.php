<?php

namespace App\Livewire\Portal;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Interactive Map')]
class InteractiveMap extends Component
{
    public function render()
    {
        return view('livewire.portal.map');
    }
}
