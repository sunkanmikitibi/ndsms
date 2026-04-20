<?php

namespace App\Livewire\Portal;

use App\Models\Street;
use App\Models\StreetRevalidation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.portal')]
#[Title('Apply for Street Revalidation')]
class StreetRevalidationForm extends Component
{
    use WithFileUploads;

    // Tab selection
    public string $tab = 'existing'; // existing or new

    // For existing street
    public $street_id = null;
    public array $streets = [];
    public ?Street $selectedStreet = null;

    // For new revalidation details
    public string $street_name = '';
    public string $town = '';
    public string $reason = '';
    public string $current_status = 'active';

    // Supporting documents
    public array $supporting_documents = [];
    public $uploadedDocuments = [];

    // UI States
    public int $step = 1;
    public bool $submitted = false;
    public ?StreetRevalidation $lastRevalidation = null;
    public float $feeAmount = 0;

    protected $rules = [
        'street_name'      => 'required|string|max:255',
        'town'             => 'required|string|max:100',
        'reason'           => 'required|string|max:1000',
        'current_status'   => 'required|in:active,inactive,disputed,under_review',
        'supporting_documents' => 'nullable|array|max:5',
        'supporting_documents.*' => 'file|max:10240', // 10MB per file
    ];

    public function mount()
    {
        $this->streets = Street::where('status', 'active')->get()->toArray();
        $this->feeAmount = \App\Models\FeeSchedule::getFeeAmount('street_revalidation') ?? 2500;
    }

    public function setTab($tabName)
    {
        $this->tab = $tabName;
        $this->reset(['street_id', 'street_name', 'town', 'reason', 'step', 'submitted']);
    }

    public function selectStreet($streetId)
    {
        $this->street_id = $streetId;
        $this->selectedStreet = Street::find($streetId);

        if ($this->selectedStreet) {
            $this->street_name = $this->selectedStreet->name;
            $this->town = $this->selectedStreet->town;
        }
    }

    public function clearStreetSelection()
    {
        $this->street_id = null;
        $this->selectedStreet = null;
    }

    public function addSupportingDocument()
    {
        $this->validate([
            'uploadedDocuments.*' => 'file|max:10240',
        ]);

        foreach ($this->uploadedDocuments as $document) {
            $path = $document->store('revalidation-documents', 'public');
            $this->supporting_documents[] = $path;
        }

        $this->uploadedDocuments = [];
        $this->dispatch('toast', type: 'success', message: 'Document uploaded successfully.');
    }

    public function removeDocument($index)
    {
        unset($this->supporting_documents[$index]);
        $this->supporting_documents = array_values($this->supporting_documents);
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            if ($this->tab === 'existing') {
                $this->validate([
                    'street_id' => 'required|exists:streets,id',
                ]);
            } else {
                $this->validate([
                    'street_name' => 'required|string|max:255',
                    'town'        => 'required|string|max:100',
                ]);
            }
        } elseif ($this->step === 2) {
            $this->validate([
                'reason'         => 'required|string|max:1000',
                'current_status' => 'required|in:active,inactive,disputed,under_review',
            ]);
        }

        $this->step++;
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submit()
    {
        // Final validation
        $this->validate([
            'reason'         => 'required|string|max:1000',
            'current_status' => 'required|in:active,inactive,disputed,under_review',
        ]);

        $this->lastRevalidation = StreetRevalidation::create([
            'user_id'                => auth()->id(),
            'street_id'              => $this->street_id,
            'street_name'            => $this->street_name,
            'town'                   => $this->town,
            'reason'                 => $this->reason,
            'current_status'         => $this->current_status,
            'supporting_documents'   => $this->supporting_documents,
            'status'                 => 'pending',
        ]);

        // Dispatch payment initialization
        $this->dispatch('initiate-revalidation-payment', 
            streetRevalidationId: $this->lastRevalidation->id,
        );

        $this->reset();
        $this->submitted = true;
        $this->dispatch('toast', type: 'success', message: 'Street revalidation request submitted. Proceed to payment.');
    }

    public function newApplication()
    {
        $this->reset();
        $this->submitted = false;
        $this->lastRevalidation = null;
        $this->tab = 'existing';
    }

    public function render()
    {
        return view('livewire.portal.street-revalidation-form');
    }
}
