<?php

namespace App\Livewire\Admin;

use App\Models\Address;
use App\Models\Street;
use App\Models\StreetApplication;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_addresses'   => Address::count(),
            'total_streets'     => Street::count(),
            'pending_approvals' => StreetApplication::where('status', 'pending')->count(),
            'total_users'       => User::count(),
        ];

        $recent_applications = StreetApplication::with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.admin.dashboard', compact('stats', 'recent_applications'));
    }
}
