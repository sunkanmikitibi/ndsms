<?php

namespace App\Livewire\Portal;

use App\Models\Address;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('QR Scanner')]
class QrScanner extends Component
{
    public string $manualCode = '';
    public ?Address $scannedAddress = null;
    public bool $isScanning = false;
    public string $scanError = '';

    protected $rules = [
        'manualCode' => 'required|string|max:255',
    ];

    public function mount()
    {
        // Verify user is authenticated
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }
    }

    public function scanQrCode(string $qrData): void
    {
        $this->scanError = '';
        
        try {
            // Attempt to find address by QR code data
            $address = Address::where('qr_code', $qrData)
                ->orWhere('code', $qrData)
                ->orWhere('house_number', $qrData)
                ->first();

            if ($address) {
                $this->scannedAddress = $address->load('street', 'user');
                $this->manualCode = '';
            } else {
                $this->scanError = 'Address not found for QR code: ' . $qrData;
            }
        } catch (\Exception $e) {
            $this->scanError = 'Error scanning QR code: ' . $e->getMessage();
        }
    }

    public function searchManualCode(): void
    {
        $this->validate();
        $this->scanError = '';

        try {
            // Search for address by code, house number, or street name
            $address = Address::where('code', $this->manualCode)
                ->orWhere('house_number', $this->manualCode)
                ->orWhere('qr_code', $this->manualCode)
                ->first();

            if ($address) {
                $this->scannedAddress = $address->load('street', 'user');
            } else {
                $this->scanError = 'No address found with code: ' . $this->manualCode;
            }
        } catch (\Exception $e) {
            $this->scanError = 'Error searching for address: ' . $e->getMessage();
        }
    }

    public function clearResult(): void
    {
        $this->scannedAddress = null;
        $this->manualCode = '';
        $this->scanError = '';
    }

    public function verifyAddress(): void
    {
        if (!$this->scannedAddress) {
            $this->scanError = 'No address selected for verification.';
            return;
        }

        try {
            // Update address with verification timestamp
            $this->scannedAddress->update([
                'last_verified_at' => now(),
                'verified_by_id' => auth()->id(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Address verified successfully!',
            ]);

            $this->clearResult();
        } catch (\Exception $e) {
            $this->scanError = 'Error verifying address: ' . $e->getMessage();
        }
    }

    public function toggleScanning(): void
    {
        $this->isScanning = !$this->isScanning;
        if (!$this->isScanning) {
            $this->clearResult();
        }
    }

    public function render()
    {
        return view('livewire.portal.qr-scanner');
    }
}
