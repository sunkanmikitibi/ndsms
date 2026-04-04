<?php

namespace App\Livewire\Admin\Addresses;

use App\Models\Address;
use App\Models\Street;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.admin')]
#[Title('Addresses')]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterWard = '';
    public string $filterStatus = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showImportModal = false;
    public ?int $editId = null;
    public ?int $deleteId = null;
    public $importFile;

    public string $applicant_name = '';
    public string $applicant_phone = '';
    public string $reference_code = '';

    public string $house_number = '';
    public string $street_id = '';
    public string $ward = '';
    public string $owner_name = '';
    public string $owner_phone = '';
    public string $status = 'active';

    protected function rules(): array
    {
        return [
            'house_number' => 'required|string|max:50',
            'street_id'    => 'required|exists:streets,id',
            'ward'         => 'required|string|max:100',
            'owner_name'   => 'required|string|max:255',
            'owner_phone'  => 'nullable|string|max:20',
            'status'       => 'required|in:active,inactive,pending',
            'applicant_name'  => 'nullable|string|max:255',
            'applicant_phone' => 'nullable|string|max:20',
            'reference_code'  => 'nullable|string|max:50|unique:addresses,reference_code,' . $this->editId,
        ];
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['editId', 'house_number', 'street_id', 'ward', 'owner_name', 'owner_phone', 'applicant_name', 'applicant_phone', 'reference_code']);
        $this->status    = 'active';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $addr               = Address::findOrFail($id);
        $this->editId       = $id;
        $this->house_number = $addr->house_number;
        $this->street_id    = (string) $addr->street_id;
        $this->ward         = $addr->ward;
        $this->owner_name   = $addr->owner_name;
        $this->owner_phone  = $addr->owner_phone ?? '';
        $this->applicant_name  = $addr->applicant_name ?? '';
        $this->applicant_phone = $addr->applicant_phone ?? '';
        $this->reference_code  = $addr->reference_code ?? '';
        $this->status       = $addr->status;
        $this->showModal    = true;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'house_number'    => $this->house_number,
            'street_id'       => $this->street_id,
            'ward'            => $this->ward,
            'owner_name'      => $this->owner_name,
            'owner_phone'     => $this->owner_phone ?: null,
            'applicant_name'  => $this->applicant_name ?: null,
            'applicant_phone' => $this->applicant_phone ?: null,
            'reference_code'  => $this->reference_code ?: null,
            'status'          => $this->status,
        ];
        $this->editId
            ? Address::findOrFail($this->editId)->update($data)
            : Address::create($data);
        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: 'Address saved.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId       = $id;
        $this->showDeleteModal = true;
    }

    public function deleteAddress(): void
    {
        if ($this->deleteId) {
            Address::findOrFail($this->deleteId)->delete();
            $this->dispatch('toast', type: 'success', message: 'Address deleted.');
        }
        $this->showDeleteModal = false;
    }

    public function import(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $this->importFile->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        // Expected header: house_number, street_id, ward, owner_name, owner_phone, applicant_name, applicant_phone, reference_code
        // Validate header minimally or just skip it
        
        $count = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($file)) !== false) {
            $rowNum++;
            if (count($row) < 3) continue; // Basic check

            $data = [
                'house_number'    => $row[0] ?? null,
                'street_id'       => $row[1] ?? null,
                'ward'            => $row[2] ?? null,
                'owner_name'      => $row[3] ?? null,
                'owner_phone'     => $row[4] ?? null,
                'applicant_name'  => $row[5] ?? null,
                'applicant_phone' => $row[6] ?? null,
                'reference_code'  => $row[7] ?? null,
                'status'          => 'active',
            ];

            $validator = Validator::make($data, [
                'house_number' => 'required|string|max:50',
                'street_id'    => 'required|exists:streets,id',
                'ward'         => 'required|string|max:100',
                'owner_name'   => 'required|string|max:255',
                'reference_code' => 'nullable|string|max:50|unique:addresses,reference_code',
            ]);

            if ($validator->fails()) {
                $errors[] = "Row {$rowNum}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            Address::create($data);
            $count++;
        }

        fclose($file);
        $this->showImportModal = false;
        $this->reset('importFile');

        if (count($errors) > 0) {
            $this->dispatch('toast', type: 'warning', message: "Imported {$count} records. Errors: " . count($errors));
            // Log errors or display them? For now just a toast.
        } else {
            $this->dispatch('toast', type: 'success', message: "Successfully imported {$count} addresses.");
        }
    }

    public function downloadSample(): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="address_sample.csv"',
        ];

        $columns = ['house_number', 'street_id', 'ward', 'owner_name', 'owner_phone', 'applicant_name', 'applicant_phone', 'reference_code'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Add a sample row
            fputcsv($file, ['10A', '1', 'Ward 1', 'John Doe', '0123456789', 'Jane Smith', '0987654321', 'REF001']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $addresses = Address::with('street')
            ->when($this->search, fn($q) => $q->where('owner_name', 'like', "%{$this->search}%")
                ->orWhere('house_number', 'like', "%{$this->search}%"))
            ->when($this->filterWard, fn($q) => $q->where('ward', $this->filterWard))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(20);

        $wards   = Address::distinct()->pluck('ward')->sort()->values();
        $streets = Street::orderBy('name')->get();

        return view('livewire.admin.addresses.index', compact('addresses', 'wards', 'streets'));
    }
}
