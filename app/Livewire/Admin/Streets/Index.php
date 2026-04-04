<?php

namespace App\Livewire\Admin\Streets;

use App\Models\Street;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('components.layouts.admin')]
#[Title('Streets')]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterWard = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showImportModal = false;
    public ?int $editId = null;
    public ?int $deleteId = null;
    public $importFile;

    public string $name = '';
    public string $ward = '';
    public string $type = 'street';
    public string $description = '';
    public string $status = 'active';

    protected $rules = [
        'name'        => 'required|string|max:255',
        'ward'        => 'required|string|max:100',
        'type'        => 'required|in:street,avenue,road,lane,close,crescent',
        'description' => 'nullable|string',
        'status'      => 'required|in:active,inactive',
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['editId', 'name', 'ward', 'description']);
        $this->status    = 'active';
        $this->type      = 'street';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $street            = Street::findOrFail($id);
        $this->editId      = $id;
        $this->name        = $street->name;
        $this->ward        = $street->ward;
        $this->type        = $street->type;
        $this->description = $street->description ?? '';
        $this->status      = $street->status;
        $this->showModal   = true;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'name'        => $this->name,
            'ward'        => $this->ward,
            'type'        => $this->type,
            'description' => $this->description ?: null,
            'status'      => $this->status,
        ];
        if ($this->editId) {
            Street::findOrFail($this->editId)->update($data);
        } else {
            Street::create(array_merge($data, [
                'code' => 'STR-' . strtoupper(substr(preg_replace('/\s+/', '', $this->name), 0, 4)) . '-' . rand(1000, 9999),
            ]));
        }
        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: 'Street saved.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId       = $id;
        $this->showDeleteModal = true;
    }

    public function deleteStreet(): void
    {
        if ($this->deleteId) {
            Street::findOrFail($this->deleteId)->delete();
            $this->dispatch('toast', type: 'success', message: 'Street deleted.');
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

        // Expected header: name, ward, type, description, status
        
        $count = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($file)) !== false) {
            $rowNum++;
            if (count($row) < 2) continue;

            $data = [
                'name'        => $row[0] ?? null,
                'ward'        => $row[1] ?? null,
                'type'        => $row[2] ?? 'street',
                'description' => $row[3] ?? null,
                'status'      => $row[4] ?? 'active',
            ];

            $validator = Validator::make($data, [
                'name'        => 'required|string|max:255',
                'ward'        => 'required|string|max:100',
                'type'        => 'required|in:street,avenue,road,lane,close,crescent',
                'status'      => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                $errors[] = "Row {$rowNum}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            Street::create(array_merge($data, [
                'code' => 'STR-' . strtoupper(substr(preg_replace('/\s+/', '', $data['name']), 0, 4)) . '-' . rand(1000, 9999),
            ]));
            $count++;
        }

        fclose($file);
        $this->showImportModal = false;
        $this->reset('importFile');

        if (count($errors) > 0) {
            $this->dispatch('toast', type: 'warning', message: "Imported {$count} records. Errors: " . count($errors));
        } else {
            $this->dispatch('toast', type: 'success', message: "Successfully imported {$count} streets.");
        }
    }

    public function downloadSample(): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="street_sample.csv"',
        ];

        $columns = ['name', 'ward', 'type', 'description', 'status'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Main Street', 'Ward 1', 'street', 'Primary access road', 'active']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        $streets = Street::withCount('addresses')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%"))
            ->when($this->filterWard, fn($q) => $q->where('ward', $this->filterWard))
            ->latest()
            ->paginate(20);

        $wards = Street::distinct()->pluck('ward')->sort()->values();

        return view('livewire.admin.streets.index', compact('streets', 'wards'));
    }
}
