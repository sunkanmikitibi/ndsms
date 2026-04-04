<?php

namespace App\Livewire\Portal;

use App\Models\FeeSchedule as FeeScheduleModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.portal')]
#[Title('Fee Schedule')]
class FeeSchedule extends Component
{
    public function getActiveFeesProperty()
    {
        return FeeScheduleModel::active()
            ->orderBy('service_name')
            ->get();
    }

    public function getGroupedFeesProperty()
    {
        return $this->activeFees->groupBy('service_type');
    }

    public function render()
    {
        return view('livewire.portal.fee-schedule', [
            'fees' => $this->activeFees,
            'groupedFees' => $this->groupedFees,
        ]);
    }
}
