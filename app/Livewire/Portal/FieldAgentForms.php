<?php

namespace App\Livewire\Portal;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

use App\Models\FieldReport;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.portal')]
#[Title('Field Agent Forms')]
class FieldAgentForms extends Component
{
    public string $activeForm = 'form-a';

    // Form Data
    public array $formA = [];
    public array $formB = [];

    public function mount()
    {
        // Initialize Form A defaults
        $this->formA = [
            'agent_name' => Auth::user()->name,
            'agent_id' => '',
            'phone' => Auth::user()->phone ?? '',
            'visit_date' => date('Y-m-d'),
            'towns' => [],
            'start_time' => '',
            'end_time' => '',
            'street_name' => '',
            'street_code' => '',
            'local_name' => '',
            'landmark' => '',
            'street_type' => [],
            'gps' => [
                'start_lat' => '', 'start_lng' => '',
                'end_lat' => '', 'end_lng' => ''
            ],
            'estimated_length' => '',
            'estimated_width' => '',
            'surface_type' => [],
            'condition' => '',
            'infrastructure' => [],
            'property_counts' => [
                'residential' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'commercial' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'government' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'religious' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'educational' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'empty' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'construction' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
            ],
            'signage_match' => '',
            'signage_incorrect_name' => '',
            'issues' => [],
            'observation_notes' => '',
            'recommendations' => '',
            'media_log' => [],
            'outcome' => '',
        ];

        // Initialize Form B defaults
        $this->formB = [
            'agent_name' => Auth::user()->name,
            'agent_id' => '',
            'survey_date' => date('Y-m-d'),
            'town' => '',
            'area' => '',
            'nearest_street' => '',
            'proposed_name' => '',
            'proposed_type' => [],
            'dead_end' => '',
            'description' => '',
            'waypoints' => [
                ['label' => 'Start Point', 'lat' => '', 'lng' => '', 'note' => ''],
                ['label' => 'Midpoint 1', 'lat' => '', 'lng' => '', 'note' => ''],
                ['label' => 'Midpoint 2', 'lat' => '', 'lng' => '', 'note' => ''],
                ['label' => 'End Point', 'lat' => '', 'lng' => '', 'note' => ''],
            ],
            'estimated_length' => '',
            'estimated_width' => '',
            'surface_type' => [],
            'property_counts' => [
                'residential_completed' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'residential_construction' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'commercial' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'government' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'religious_educational' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
                'empty' => ['left' => 0, 'right' => 0, 'total' => 0, 'notes' => ''],
            ],
            'justification' => '',
            'estimated_households' => '',
            'estimated_population' => '',
            'connecting_streets' => '',
            'community_facilities' => '',
            'media_log' => [],
        ];
    }

    public function swapForm($form)
    {
        $this->activeForm = $form;
    }

    public function submitFormA()
    {
        // Simple validation example
        $this->validate([
            'formA.agent_name' => 'required',
            'formA.street_name' => 'required',
        ], [
            'formA.agent_name.required' => 'Agent name is required.',
            'formA.street_name.required' => 'Official street name is required.',
        ]);

        FieldReport::create([
            'user_id' => Auth::id(),
            'type' => 'form_a',
            'data' => $this->formA,
            'status' => 'pending',
        ]);

        $this->dispatch('toast', type: 'success', message: 'Form A submitted successfully for admin approval.');
        $this->dispatch('form-submitted');
    }

    public function submitFormB()
    {
        $this->validate([
            'formB.agent_name' => 'required',
            'formB.town' => 'required',
        ], [
            'formB.agent_name.required' => 'Agent name is required.',
            'formB.town.required' => 'Town is required.',
        ]);

        FieldReport::create([
            'user_id' => Auth::id(),
            'type' => 'form_b',
            'data' => $this->formB,
            'status' => 'pending',
        ]);

        $this->dispatch('toast', type: 'success', message: 'Form B submitted successfully for admin approval.');
        $this->dispatch('form-submitted');
    }

    public function render()
    {
        return view('livewire.portal.field-agent-forms');
    }
}
