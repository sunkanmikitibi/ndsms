<?php

namespace App\Livewire\Portal;

use App\Models\Address;
use App\Models\Street;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Register Address')]
class RegisterAddress extends Component
{
    public int $step = 1;
    public string $registrationType = 'single'; // single or bulk
    
    // Step 1: Personal
    public string $applicant_name = '';
    public string $applicant_phone = '';
    
    // Step 2: Location (Single)
    public string $house_number = '';
    public $street_id = '';
    public string $ward = '';
    public $latitude = null;
    public $longitude = null;

    // Step 2: Location (Bulk)
    public array $bulkAddresses = []; // array of ['house_number' => '']
    
    // Step 3: Details (Owner info often same as applicant)
    public string $owner_name = '';
    public string $owner_phone = '';
    public string $payment_method = 'paystack';
    
    public bool $submitted = false;
    public string $reference_code = ''; // Main reference or first reference
    public array $reference_codes = []; // For bulk
    public string $search_code = '';

    public function mount()
    {
        if (auth()->check()) {
            $this->applicant_name = auth()->user()->name;
            $this->applicant_phone = auth()->user()->phone ?? '';
            $this->owner_name = $this->applicant_name;
            $this->owner_phone = $this->applicant_phone;
        }

        // Initialize bulk with one row
        $this->bulkAddresses = [
            ['house_number' => ''],
        ];
    }

    public function setRegistrationType($type)
    {
        $this->registrationType = $type;
    }

    public function addAddressRow()
    {
        $this->bulkAddresses[] = ['house_number' => ''];
    }

    public function removeAddressRow(int $index)
    {
        if (count($this->bulkAddresses) > 1) {
            unset($this->bulkAddresses[$index]);
            $this->bulkAddresses = array_values($this->bulkAddresses);
        }
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'applicant_name' => 'required|string|max:255',
                'applicant_phone' => 'required|string|max:20',
            ]);
            // Default owner info to applicant info if not set
            if (empty($this->owner_name)) $this->owner_name = $this->applicant_name;
            if (empty($this->owner_phone)) $this->owner_phone = $this->applicant_phone;
        } elseif ($this->step === 2) {
            $rules = [
                'street_id'    => 'required|exists:streets,id',
                'ward'         => 'required|string|max:100',
            ];

            if ($this->registrationType === 'single') {
                $rules['house_number'] = 'required|string|max:50';
            } else {
                $rules['bulkAddresses'] = 'required|array|min:1';
                $rules['bulkAddresses.*.house_number'] = 'required|string|max:50';
            }

            $this->validate($rules);
        }
        
        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submit(): void
    {
        $this->validate([
            'owner_name'     => 'required|string|max:255',
            'owner_phone'    => 'nullable|string|max:20',
            'payment_method' => 'required|in:paystack,flutterwave,bank_transfer',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
        ]);

        if ($this->registrationType === 'single') {
            $this->processSingleRegistration();
        } else {
            $this->processBulkRegistration();
        }

        $this->submitted = true;
        $this->dispatch('toast', type: 'success', message: 'Address registration request(s) submitted.');
    }

    protected function processSingleRegistration(): void
    {
        // Check if address already exists on this street
        $exists = Address::where('street_id', $this->street_id)
            ->where('house_number', $this->house_number)
            ->exists();

        if ($exists) {
            $this->addError('house_number', 'This house number is already registered on the selected street.');
            $this->step = 2;
            return;
        }

        $this->reference_code = 'REG-' . strtoupper(bin2hex(random_bytes(3)));

        Address::create([
            'applicant_name'  => $this->applicant_name,
            'applicant_phone' => $this->applicant_phone,
            'house_number'    => $this->house_number,
            'street_id'       => $this->street_id,
            'ward'            => $this->ward,
            'latitude'        => $this->latitude,
            'longitude'       => $this->longitude,
            'owner_name'      => $this->owner_name,
            'owner_phone'     => $this->owner_phone,
            'payment_method'  => $this->payment_method,
            'reference_code'  => $this->reference_code,
            'status'          => auth()->check() && auth()->user()->hasRole('field-officer') ? 'active' : 'pending',
        ]);
    }

    protected function processBulkRegistration(): void
    {
        $this->reference_codes = [];
        
        foreach ($this->bulkAddresses as $row) {
            $ref = 'REG-' . strtoupper(bin2hex(random_bytes(3)));
            $this->reference_codes[] = $ref;
            if (count($this->reference_codes) === 1) $this->reference_code = $ref; // Display first one

            Address::create([
                'applicant_name'  => $this->applicant_name,
                'applicant_phone' => $this->applicant_phone,
                'house_number'    => $row['house_number'],
                'street_id'       => $this->street_id,
                'ward'            => $this->ward,
                'latitude'        => $this->latitude, // Shared GPS for bulk
                'longitude'       => $this->longitude, // Shared GPS for bulk
                'owner_name'      => $this->owner_name,
                'owner_phone'     => $this->owner_phone,
                'payment_method'  => $this->payment_method,
                'reference_code'  => $ref,
                'status'          => auth()->check() && auth()->user()->hasRole('field-officer') ? 'active' : 'pending',
            ]);
        }
    }

    public function track()
    {
        if (empty($this->search_code)) return;
        
        $address = Address::where('reference_code', $this->search_code)->first();
        
        if ($address) {
            $this->dispatch('toast', type: 'info', message: "Request Status: " . ucfirst($address->status));
        } else {
            $this->dispatch('toast', type: 'error', message: "Reference code not found.");
        }
    }

    public function newRegistration(): void
    {
        $this->reset(['step', 'house_number', 'street_id', 'ward', 'owner_name', 'owner_phone', 'payment_method', 'applicant_name', 'applicant_phone', 'submitted', 'reference_code', 'reference_codes', 'bulkAddresses']);
        $this->mount();
    }

    public function render()
    {
        $streets = Street::where('status', 'active')->orderBy('name')->get();
        return view('livewire.portal.register-address', compact('streets'));
    }
}
