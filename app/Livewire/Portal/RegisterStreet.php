<?php

namespace App\Livewire\Portal;

use App\Models\Street;
use App\Models\StreetApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Register Street')]
class RegisterStreet extends Component
{
    public string $street_name = '';
    public string $town = '';
    public string $type = 'street';
    public string $description = '';
    public $start_latitude = null;
    public $start_longitude = null;
    public $end_latitude = null;
    public $end_longitude = null;
    public $distance = null;
    public bool $submitted = false;
    public ?StreetApplication $lastApplication = null;
    public float $feeAmount = 0;

    public function mount()
    {
        $this->feeAmount = \App\Models\FeeSchedule::getFeeAmount('street_registration') ?? 5000;
    }

    protected $rules = [
        'street_name'     => 'required|string|max:255',
        'town'            => 'required|string|max:100',
        'type'            => 'required|in:street,avenue,road,lane,close,crescent',
        'description'     => 'nullable|string|max:1000',
        'start_latitude'  => 'nullable|numeric',
        'start_longitude' => 'nullable|numeric',
        'end_latitude'    => 'nullable|numeric',
        'end_longitude'   => 'nullable|numeric',
        'distance'        => 'nullable|numeric',
    ];

    public function submit(): void
    {
        $this->validate();

        // Check for duplicate
        $exists = StreetApplication::where('street_name', $this->street_name)
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($exists) {
            $this->addError('street_name', 'A street with this name already has a pending or approved application.');
            return;
        }

        $this->lastApplication = StreetApplication::create([
            'user_id'         => auth()->id(),
            'street_name'     => $this->street_name,
            'town'            => $this->town,
            'type'            => $this->type,
            'description'     => $this->description,
            'start_latitude'  => $this->start_latitude,
            'start_longitude' => $this->start_longitude,
            'end_latitude'    => $this->end_latitude,
            'end_longitude'   => $this->end_longitude,
            'distance'        => $this->distance,
            'status'          => auth()->check() && auth()->user()->hasRole('field-officer') ? 'active' : 'awaiting_payment',
        ]);

        $this->reset(['street_name', 'town', 'type', 'description', 'start_latitude', 'start_longitude', 'end_latitude', 'end_longitude', 'distance']);
        $this->submitted = true;
        $this->dispatch('toast', type: 'success', message: 'Street application submitted successfully.');
        // Initiate payment for street registration
        $this->dispatch('initiate-street-payment', $this->lastApplication->id);
    }

    public function newApplication(): void
    {
        $this->submitted = false;
        $this->lastApplication = null;
    }

    public function render()
    {
        return view('livewire.portal.register-street');
    }
}
