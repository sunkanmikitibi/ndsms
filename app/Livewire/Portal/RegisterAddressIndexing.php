<?php

namespace App\Livewire\Portal;

use App\Models\AddressIndexingRequest;
use App\Models\FeeSchedule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.portal')]
#[Title('Apply for Address Indexing')]
class RegisterAddressIndexing extends Component
{
    use WithFileUploads;

    // Applicant Info
    public string $applicant_name = '';
    public string $applicant_phone = '';

    // Address Info
    public string $address_line = '';
    public string $house_number = '';
    public $latitude = null;
    public $longitude = null;
    public string $description = '';

    // Owner Info
    public string $owner_name = '';
    public string $owner_phone = '';

    // Property Images
    public array $property_images = [];
    public $uploadedImages = [];

    // UI States
    public int $step = 1;
    public bool $submitted = false;
    public bool $reviewing = false;
    public ?float $feeAmount = null;
    public ?AddressIndexingRequest $lastRequest = null;

    protected $rules = [
        'applicant_name'   => 'required|string|max:255',
        'applicant_phone'  => 'required|string|max:20',
        'address_line'     => 'required|string|max:500',
        'house_number'     => 'required|string|max:50',
        'latitude'         => 'required|numeric|between:-90,90',
        'longitude'        => 'required|numeric|between:-180,180',
        'owner_name'       => 'required|string|max:255',
        'owner_phone'      => 'required|string|max:20',
        'description'      => 'nullable|string|max:1000',
        'property_images'  => 'nullable|array|max:5',
        'property_images.*' => 'image|max:5120', // 5MB per image
    ];

    public function mount()
    {
        if (auth()->check()) {
            $this->applicant_name = auth()->user()->name;
            $this->applicant_phone = auth()->user()->phone ?? '';
            $this->owner_name = $this->applicant_name;
            $this->owner_phone = $this->applicant_phone;
        }

        $this->feeAmount = FeeSchedule::getFeeAmount('address_indexing') ?? 3000;
    }

    public function addPropertyImage()
    {
        // This will be handled by file upload in the view
        $this->validate([
            'uploadedImages.*' => 'image|max:5120',
        ]);

        // Collect uploaded image paths
        foreach ($this->uploadedImages as $image) {
            $path = $image->store('property-images', 'public');
            $this->property_images[] = $path;
        }

        $this->uploadedImages = [];
        $this->dispatch('toast', type: 'success', message: 'Images uploaded successfully.');
    }

    public function removeImage($index)
    {
        unset($this->property_images[$index]);
        $this->property_images = array_values($this->property_images);
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'applicant_name'  => 'required|string|max:255',
                'applicant_phone' => 'required|string|max:20',
            ]);
        } elseif ($this->step === 2) {
            $this->validate([
                'address_line' => 'required|string|max:500',
                'house_number' => 'required|string|max:50',
                'latitude'     => 'required|numeric|between:-90,90',
                'longitude'    => 'required|numeric|between:-180,180',
            ]);
        } elseif ($this->step === 3) {
            $this->validate([
                'owner_name'  => 'required|string|max:255',
                'owner_phone' => 'required|string|max:20',
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
        $this->validate();

        if ($this->feeAmount > 0) {
            $this->reviewing = true;
            return;
        }

        $this->finalizeRequest();
    }

    public function payAndSubmit()
    {
        $this->validate();
        $this->finalizeRequest();
    }

    public function goBackToForm()
    {
        $this->reviewing = false;
    }

    protected function finalizeRequest(): void
    {
        $this->lastRequest = AddressIndexingRequest::create([
            'user_id'           => auth()->id(),
            'applicant_name'    => $this->applicant_name,
            'applicant_phone'   => $this->applicant_phone,
            'address_line'      => $this->address_line,
            'house_number'      => $this->house_number,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'owner_name'        => $this->owner_name,
            'owner_phone'       => $this->owner_phone,
            'description'       => $this->description,
            'property_images'   => $this->property_images,
            'status'            => $this->feeAmount > 0 ? 'awaiting_payment' : 'pending',
        ]);

        if ($this->feeAmount > 0) {
            $this->dispatch('initiate-indexing-payment', $this->lastRequest->id);
        }

        $this->clearForm();

        $this->submitted = true;
        $this->reviewing = false;
        $this->dispatch('toast', type: 'success', message: 'Address indexing request submitted. Proceed to payment.');
    }

    protected function clearForm(): void
    {
        $this->step = 1;
        $this->address_line = '';
        $this->house_number = '';
        $this->latitude = null;
        $this->longitude = null;
        $this->description = '';
        $this->property_images = [];
        $this->uploadedImages = [];
        $this->reviewing = false;

        if (auth()->check()) {
            $this->applicant_name = auth()->user()->name;
            $this->applicant_phone = auth()->user()->phone ?? '';
            $this->owner_name = $this->applicant_name;
            $this->owner_phone = $this->applicant_phone;
        }
    }

    public function newApplication()
    {
        $this->step = 1;
        $this->reviewing = false;
        $this->submitted = false;
        $this->lastRequest = null;
        $this->address_line = '';
        $this->house_number = '';
        $this->latitude = null;
        $this->longitude = null;
        $this->description = '';
        $this->property_images = [];
        $this->uploadedImages = [];

        if (auth()->check()) {
            $this->applicant_name = auth()->user()->name;
            $this->applicant_phone = auth()->user()->phone ?? '';
            $this->owner_name = $this->applicant_name;
            $this->owner_phone = $this->applicant_phone;
        }

        $this->feeAmount = FeeSchedule::getFeeAmount('address_indexing') ?? 0;
    }

    public function render()
    {
        return view('livewire.portal.register-address-indexing');
    }
}
