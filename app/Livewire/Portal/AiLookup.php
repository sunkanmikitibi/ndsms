<?php

namespace App\Livewire\Portal;

use App\Models\Address;
use App\Models\Street;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('AI Address Lookup')]
class AiLookup extends Component
{
    public string $query = '';
    public array $results = [];
    public bool $isSearching = false;
    public string $errorMessage = '';
    public ?Address $selectedAddress = null;

    protected $rules = [
        'query' => 'required|string|min:5|max:500',
    ];

    public function mount()
    {
        // Verify user is authenticated
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }
    }

    public function searchAddresses(): void
    {
        $this->validate();
        $this->isSearching = true;
        $this->errorMessage = '';
        $this->results = [];

        try {
            $searchTerms = array_filter(array_unique(preg_split('/[\s,]+/', strtolower($this->query))));

            if (empty($searchTerms)) {
                $this->errorMessage = 'Please enter a valid search query.';
                $this->isSearching = false;
                return;
            }

            // Build query for fuzzy matching
            $query = Address::with('street')
                ->where('status', 'approved');

            // Search across multiple fields
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    if (strlen($term) >= 2) {
                        $q->orWhere('house_number', 'like', "%{$term}%")
                          ->orWhere('owner_name', 'like', "%{$term}%")
                          ->orWhere('description', 'like', "%{$term}%")
                          ->orWhereHas('street', function ($sq) use ($term) {
                              $sq->where('name', 'like', "%{$term}%")
                                 ->orWhere('ward', 'like', "%{$term}%");
                          });
                    }
                }
            });

            $foundAddresses = $query
                ->limit(10)
                ->get()
                ->map(function ($address) {
                    return [
                        'id' => $address->id,
                        'house_number' => $address->house_number,
                        'street_name' => $address->street?->name ?? 'Unknown Street',
                        'ward' => $address->ward,
                        'owner_name' => $address->owner_name,
                        'owner_phone' => $address->owner_phone,
                        'status' => $address->status,
                        'coordinates' => $address->latitude && $address->longitude 
                            ? "{$address->latitude}, {$address->longitude}" 
                            : null,
                    ];
                })->toArray();

            if (!empty($foundAddresses)) {
                $this->results = $foundAddresses;
            } else {
                $this->errorMessage = 'No addresses found matching your search. Try a more specific search term.';
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Search failed: ' . $e->getMessage();
            logger()->error('AI Lookup search error', ['error' => $e->getMessage(), 'query' => $this->query]);
        }

        $this->isSearching = false;
    }

    public function selectResult(int $addressId): void
    {
        try {
            $this->selectedAddress = Address::with('street')->findOrFail($addressId);
        } catch (\Exception $e) {
            $this->errorMessage = 'Error loading address details: ' . $e->getMessage();
        }
    }

    public function clearSearch(): void
    {
        $this->query = '';
        $this->results = [];
        $this->selectedAddress = null;
        $this->errorMessage = '';
    }

    public function clearResult(): void
    {
        $this->selectedAddress = null;
    }

    public function render()
    {
        return view('livewire.portal.ai-lookup', [
            'results' => $this->results,
            'isSearching' => $this->isSearching,
            'errorMessage' => $this->errorMessage,
            'selectedAddress' => $this->selectedAddress,
        ]);
    }
}
