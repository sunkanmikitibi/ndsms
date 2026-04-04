<?php

namespace App\Livewire\Portal;

use App\Models\StreetApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Complaints & Feedback')]
class Complaints extends Component
{
    public string $type = '';
    public string $subject = '';
    public string $message = '';
    public bool $submitted = false;

    protected $rules = [
        'type'    => 'required|string',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|min:20|max:2000',
    ];

    public function selectType(string $type): void
    {
        $this->type = $type;
    }

    public function submit(): void
    {
        $this->validate();
        // In a real app, save to a complaints table
        $this->submitted = true;
        $this->dispatch('toast', type: 'success', message: 'Your complaint has been submitted. We will respond within 3 working days.');
    }

    public function newComplaint(): void
    {
        $this->reset();
        $this->submitted = false;
    }

    public function render()
    {
        return view('livewire.portal.complaints');
    }
}
