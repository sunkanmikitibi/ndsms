<?php

namespace App\Livewire\Portal;

use App\Models\Address;
use App\Models\Street;
use App\Services\FeeService;
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
    public string $town = '';
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
                'town'         => 'required|string|max:100',
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
            $address = $this->processSingleRegistration();

            // If payment required, set awaiting_payment and initiate payment flow
            if ($address && $address->status === 'awaiting_payment') {
                $feeService = app(FeeService::class);
                $amount = $feeService->getFeeAmount('address_registration') ?? 1000;
                $this->dispatch('initiate-payment', $address->id, $amount);
            }
        } else {
            $addresses = $this->processBulkRegistration();

            if (!empty($addresses)) {
                $first = $addresses[0];
                if ($first->status === 'awaiting_payment') {
                    $feeService = app(FeeService::class);
                    $amount = $feeService->getFeeAmount('address_registration') ?? 1000;
                    $this->dispatch('initiate-payment', $first->id, $amount);
                }
            }
        }

        $this->submitted = true;
        $this->dispatch('toast', type: 'success', message: 'Address registration request(s) submitted.');
    }

    protected function processSingleRegistration(): ?Address
    {
        // Check if address already exists on this street
        $exists = Address::where('street_id', $this->street_id)
            ->where('house_number', $this->house_number)
            ->exists();

        if ($exists) {
            $this->addError('house_number', 'This house number is already registered on the selected street.');
            $this->step = 2;
            return null;
        }

        $this->reference_code = 'REG-' . strtoupper(bin2hex(random_bytes(3)));

        $address = Address::create([
            'applicant_name'  => $this->applicant_name,
            'applicant_phone' => $this->applicant_phone,
            'house_number'    => $this->house_number,
            'street_id'       => $this->street_id,
            'town'            => $this->town,
            'latitude'        => $this->latitude,
            'longitude'       => $this->longitude,
            'owner_name'      => $this->owner_name,
            'owner_phone'     => $this->owner_phone,
            'payment_method'  => $this->payment_method,
            'reference_code'  => $this->reference_code,
            'status'          => auth()->check() && auth()->user()->hasRole('field-officer') ? 'active' : (
                in_array($this->payment_method, ['paystack','flutterwave','bank_transfer']) ? 'awaiting_payment' : 'pending'
            ),
        ]);

        return $address;
    }

    protected function processBulkRegistration(): array
    {
        $this->reference_codes = [];
        $created = [];

        foreach ($this->bulkAddresses as $row) {
            $ref = 'REG-' . strtoupper(bin2hex(random_bytes(3)));
            $this->reference_codes[] = $ref;
            if (count($this->reference_codes) === 1) $this->reference_code = $ref; // Display first one

            $addr = Address::create([
                'applicant_name'  => $this->applicant_name,
                'applicant_phone' => $this->applicant_phone,
                'house_number'    => $row['house_number'],
                'street_id'       => $this->street_id,
                'town'            => $this->town,
                'latitude'        => $this->latitude, // Shared GPS for bulk
                'longitude'       => $this->longitude, // Shared GPS for bulk
                'owner_name'      => $this->owner_name,
                'owner_phone'     => $this->owner_phone,
                'payment_method'  => $this->payment_method,
                'reference_code'  => $ref,
                'status'          => auth()->check() && auth()->user()->hasRole('field-officer') ? 'active' : (
                    in_array($this->payment_method, ['paystack','flutterwave','bank_transfer']) ? 'awaiting_payment' : 'pending'
                ),
            ]);

            $created[] = $addr;
        }

        return $created;
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
        $this->reset(['step', 'house_number', 'street_id', 'town', 'owner_name', 'owner_phone', 'payment_method', 'applicant_name', 'applicant_phone', 'submitted', 'reference_code', 'reference_codes', 'bulkAddresses']);
        $this->mount();
    }

    public function render()
    {
        $streets = Street::where('status', 'active')->orderBy('name')->get();
        return view('livewire.portal.register-address', compact('streets'));
    }
}
