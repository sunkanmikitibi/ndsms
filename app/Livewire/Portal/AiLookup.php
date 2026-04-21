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

    public function updatedQuery()
    {
        if (strlen($this->query) < 3) {
            $this->results = [];
            return;
        }

        $this->performSearch();
    }

    public function performSearch()
    {
        $this->isSearching = true;
        
        // Simulate "AI" processing time for better UX
        // usleep(300000); 

        $keywords = explode(' ', strtolower($this->query));
        $keywords = array_filter($keywords, fn($k) => strlen($k) > 2);

        $foundStreets = Street::where('status', 'active')
            ->where(function($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('name', 'like', "%{$word}%")
                      ->orWhere('town', 'like', "%{$word}%");
                }
            })
            ->get();

        $foundAddresses = Address::where('status', 'approved')
            ->with('street')
            ->where(function($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('owner_name', 'like', "%{$word}%")
                      ->orWhere('house_number', 'like', "%{$word}%")
                      ->orWhere('town', 'like', "%{$word}%");
                }
            })
            ->get();

        $this->results = [];

        // Score and Merge Results
        foreach ($foundStreets as $street) {
            $score = $this->calculateScore($street->name . ' ' . $street->town, $keywords);
            $this->results[] = [
                'type' => 'street',
                'title' => $street->name,
                'subtitle' => $street->town . ' · ' . $street->code,
                'score' => $score,
                'id' => $street->id,
                'metadata' => [
                    'town' => $street->town,
                    'code' => $street->code,
                    'type' => $street->type,
                ]
            ];
        }

        foreach ($foundAddresses as $address) {
            $score = $this->calculateScore($address->owner_name . ' ' . $address->house_number . ' ' . $address->town, $keywords);
            $this->results[] = [
                'type' => 'address',
                'title' => $address->house_number . ', ' . ($address->street->name ?? 'Unknown Street'),
                'subtitle' => $address->owner_name . ' · ' . $address->town,
                'score' => $score,
                'id' => $address->id,
                'metadata' => [
                    'owner' => $address->owner_name,
                    'house_number' => $address->house_number,
                    'town' => $address->town,
                ]
            ];
        }

        // Sort by score
        usort($this->results, fn($a, $b) => $b['score'] <=> $a['score']);
        
        // Take top 8
        $this->results = array_slice($this->results, 0, 8);

        $this->isSearching = false;
    }

    private function calculateScore(string $text, array $keywords): int
    {
        $text = strtolower($text);
        if (empty($keywords)) return 0;
        
        $matches = 0;
        foreach ($keywords as $word) {
            if (str_contains($text, $word)) {
                $matches++;
            }
        }
        
        return (int)(($matches / count($keywords)) * 100);
    }

    public function render()
    {
        return view('livewire.portal.ai-lookup');
    }
}
