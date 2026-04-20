<?php

namespace App\Livewire\Portal;

use App\Mail\StreetNumberingPlateRequest;
use App\Models\Street;
use App\Models\StreetNumberingPlate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

#[Layout('components.layouts.portal')]
#[Title('Request Street Numbering Plates')]
class RequestNumberingPlates extends Component
{
    // Step tracking
    public int $step = 1;

    // Step 1: Street Selection
    public $street_id = null;
    public string $street_name = '';
    public string $town = '';
    public array $streets = [];

    // Step 2: Plate Specifications
    public int $quantity_requested = 1;
    public string $plate_type = 'standard';
    public string $material = 'aluminum';
    public string $design_variant = 'default';

    // Step 3: Installation & Delivery
    public string $installation_address = '';
    public $installation_date_requested = null;
    public string $delivery_address = '';
    public string $contact_phone = '';

    // UI States
    public bool $submitted = false;
    public ?StreetNumberingPlate $lastRequest = null;
    public ?float $estimatedCost = null;

    protected $rules = [
        'street_id' => 'nullable|exists:streets,id',
        'street_name' => 'required|string|max:255',
        'town' => 'required|string|max:100',
        'quantity_requested' => 'required|integer|min:1|max:100',
        'plate_type' => 'required|in:standard,reflective,illuminated,digital',
        'material' => 'required|in:aluminum,steel,stainless,plastic,composite',
        'design_variant' => 'nullable|string|max:100',
        'installation_address' => 'nullable|string|max:500',
        'installation_date_requested' => 'nullable|date|after_or_equal:today',
        'delivery_address' => 'required|string|max:500',
        'contact_phone' => 'required|string|max:20',
    ];

    public function mount()
    {
        if (auth()->check()) {
            $this->delivery_address = auth()->user()->address ?? '';
            $this->contact_phone = auth()->user()->phone ?? '';
        }

        $this->streets = Street::where('status', 'active')->orderBy('name')->get()->toArray();
        $this->calculateEstimatedCost();
    }

    public function selectStreet($streetId)
    {
        $this->street_id = $streetId;
        $street = Street::find($streetId);

        if ($street) {
            $this->street_name = $street->name;
            $this->town = $street->town;
        }
    }

    public function clearStreetSelection()
    {
        $this->street_id = null;
        $this->street_name = '';
        $this->town = '';
    }

    public function calculateEstimatedCost()
    {
        // Cost structure example: base cost + material surcharge + type surcharge
        $baseCost = 2500; // ₦2,500 per plate base cost

        $typeSurcharge = match($this->plate_type) {
            'standard' => 0,
            'reflective' => 1500,
            'illuminated' => 8000,
            'digital' => 15000,
            default => 0,
        };

        $materialSurcharge = match($this->material) {
            'aluminum' => 0,
            'steel' => 1000,
            'stainless' => 3000,
            'plastic' => -500,
            'composite' => 2000,
            default => 0,
        };

        $costPerPlate = $baseCost + $typeSurcharge + $materialSurcharge;
        $this->estimatedCost = $costPerPlate * $this->quantity_requested;
    }

    public function updatedQuantityRequested()
    {
        $this->calculateEstimatedCost();
    }

    public function updatedPlateType()
    {
        $this->calculateEstimatedCost();
    }

    public function updatedMaterial()
    {
        $this->calculateEstimatedCost();
    }

    public function nextStep()
    {
        logger()->info('Next step called', [
            'current_step' => $this->step,
            'street_name' => $this->street_name,
            'town' => $this->town,
            'quantity_requested' => $this->quantity_requested,
            'plate_type' => $this->plate_type,
            'material' => $this->material,
        ]);

        try {
            if ($this->step === 1) {
                $this->validate([
                    'street_name' => 'required|string|max:255',
                    'town' => 'required|string|max:100',
                ]);
            } elseif ($this->step === 2) {
                $this->validate([
                    'quantity_requested' => 'required|integer|min:1|max:100',
                    'plate_type' => 'required|in:standard,reflective,illuminated,digital',
                    'material' => 'required|in:aluminum,steel,stainless,plastic,composite',
                ]);
            }

            if ($this->step < 3) {
                $this->step++;
                logger()->info('Step incremented', ['new_step' => $this->step]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->error('Next step validation failed', [
                'step' => $this->step,
                'errors' => $e->errors(),
            ]);
            throw $e;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submit()
    {
        // Debug logging
        logger()->info('Submit method called', [
            'step' => $this->step,
            'user_id' => auth()->id(),
            'street_name' => $this->street_name,
            'town' => $this->town,
            'quantity_requested' => $this->quantity_requested,
            'plate_type' => $this->plate_type,
            'material' => $this->material,
            'delivery_address' => $this->delivery_address,
            'contact_phone' => $this->contact_phone,
        ]);

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->error('Validation failed', [
                'errors' => $e->errors(),
                'step' => $this->step,
            ]);
            throw $e;
        }

        try {
            $referenceNumber = 'PLATE-' . strtoupper(bin2hex(random_bytes(4))) . '-' . now()->format('ymd');

            $this->lastRequest = StreetNumberingPlate::create([
                'user_id' => auth()->id(),
                'street_id' => $this->street_id,
                'street_name' => $this->street_name,
                'town' => $this->town,
                'quantity_requested' => $this->quantity_requested,
                'plate_type' => $this->plate_type,
                'material' => $this->material,
                'design_variant' => $this->design_variant,
                'installation_address' => $this->installation_address,
                'installation_date_requested' => $this->installation_date_requested,
                'delivery_address' => $this->delivery_address,
                'approx_cost' => $this->estimatedCost,
                'reference_number' => $referenceNumber,
                'status' => 'pending',
            ]);

            // Send confirmation email
            Mail::to(auth()->user()->email)->send(new StreetNumberingPlateRequest($this->lastRequest));

            // Dispatch payment initialization
            $this->dispatch('initiate-plate-payment', [
                'request_id' => $this->lastRequest->id,
                'amount' => $this->estimatedCost,
                'reference' => $referenceNumber,
            ]);

            $this->submitted = true;
        } catch (\Exception $e) {
            logger()->error('Numbering plate request error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error submitting request: ' . $e->getMessage(),
            ]);
        }
    }

    public function newRequest()
    {
        $this->reset();
        $this->submitted = false;
        $this->lastRequest = null;
        $this->mount();
    }

    public function render()
    {
        return view('livewire.portal.request-numbering-plates', [
            'plateTypes' => [
                'standard' => 'Standard Metal Plate',
                'reflective' => 'Reflective Plate (High Visibility)',
                'illuminated' => 'Illuminated Plate (LED)',
                'digital' => 'Digital Display Plate',
            ],
            'materials' => [
                'aluminum' => 'Aluminum (Lightweight)',
                'steel' => 'Galvanized Steel (Durable)',
                'stainless' => 'Stainless Steel (Premium)',
                'plastic' => 'High-Impact Plastic (Budget)',
                'composite' => 'Composite Material (Modern)',
            ],
            'designVariants' => [
                'default' => 'Default Design',
                'bold' => 'Bold Numbers',
                'modern' => 'Modern Font',
                'traditional' => 'Traditional Design',
            ],
            'streets' => $this->streets,
            'estimatedCost' => $this->estimatedCost,
        ]);
    }
}
