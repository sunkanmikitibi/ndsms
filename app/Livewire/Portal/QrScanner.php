<?php

namespace App\Livewire\Portal;

use App\Models\Address;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('QR Scanner')]
class QrScanner extends Component
{
    public string $manualCode = '';
    /**
     * Scanned address instance or ID. Tests may set an integer id, so allow int as well.
     *
     * @var Address|int|null
     */
    public Address|int|null $scannedAddress = null;
    public bool $isScanning = false;
    public string $scanError = '';
    public ?string $lastScannedCode = null;

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

    #[On('qr-code-detected')]
    public function onQrCodeDetected(string $qrData): void
    {
        // Prevent duplicate scanning
        if ($this->lastScannedCode === $qrData) {
            return;
        }

        $this->lastScannedCode = $qrData;
        $this->scanQrCode($qrData);
    }

    public function scanQrCode(string $qrData): void
    {
        $this->scanError = '';

        try {
            $cleanCode = trim($qrData);

            if (empty($cleanCode)) {
                return;
            }

            // Attempt to find address by QR code data
            $address = Address::where('qr_code', $cleanCode)
                ->orWhere('code', $cleanCode)
                ->orWhere('house_number', $cleanCode)
                ->first();

            if ($address) {
                $this->scannedAddress = $address->load('street', 'user');
                $this->manualCode = '';
                $this->isScanning = false;

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'QR code scanned successfully!',
                ]);
            } else {
                $this->scanError = 'Address not found for code: ' . substr($cleanCode, 0, 20);
            }
        } catch (\Exception $e) {
            $this->scanError = 'Error scanning QR code: ' . $e->getMessage();
            logger()->error('QR Scanner error', ['error' => $e->getMessage()]);
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
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Address found successfully!',
                ]);
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
        $this->lastScannedCode = null;
    }

    public function stopScanning(): void
    {
        $this->isScanning = false;
        $this->scanError = '';
        $this->dispatch('stop-camera');
    }

    public function startScanning(): void
    {
        $this->isScanning = true;
        $this->clearResult();
        $this->dispatch('start-camera');
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
                'message' => 'Address verified successfully! ✓',
            ]);

            $this->clearResult();
            $this->isScanning = false;
        } catch (\Exception $e) {
            $this->scanError = 'Error verifying address: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.portal.qr-scanner');
    }
}
