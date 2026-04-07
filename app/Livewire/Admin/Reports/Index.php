<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Address;
use App\Models\Street;
use App\Models\StreetApplication;
use App\Models\Payment;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Reports & Analytics')]
class Index extends Component
{
    public string $filterDateRange = '30days';

    public function mount()
    {
        // Allow access to super-admin or users with view reports permission
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('view reports')) {
            abort(403, 'Unauthorized access to reports.');
        }
    }

    public function getDateRangeProperty()
    {
        return match ($this->filterDateRange) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            'all' => now()->subYears(5),
            default => now()->subDays(30),
        };
    }

    public function getAnalyticsProperty()
    {
        $startDate = $this->dateRange;

        return [
            'total_streets' => Street::count(),
            'total_addresses' => Address::count(),
            'total_users' => User::count(),
            'total_revenue' => Payment::where('status', 'success')
                ->where('created_at', '>=', $startDate)
                ->sum('amount'),
            'recent_streets' => Street::where('created_at', '>=', $startDate)->count(),
            'recent_addresses' => Address::where('created_at', '>=', $startDate)->count(),
            'recent_applications' => StreetApplication::where('created_at', '>=', $startDate)->count(),
            'pending_approvals' => StreetApplication::where('status', 'pending')->count(),
            'approved_applications' => StreetApplication::where('status', 'approved')->count(),
        ];
    }

    public function getStreetDataProperty()
    {
        $startDate = $this->dateRange;

        return [
            'total' => Street::count(),
            'by_type' => Street::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'recent' => Street::where('created_at', '>=', $startDate)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ];
    }

    public function getAddressDataProperty()
    {
        $startDate = $this->dateRange;

        return [
            'total' => Address::count(),
            'by_status' => Address::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get(),
            'recent' => Address::where('created_at', '>=', $startDate)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ];
    }

    public function getApplicationDataProperty()
    {
        $startDate = $this->dateRange;

        return [
            'total' => StreetApplication::count(),
            'pending' => StreetApplication::where('status', 'pending')->count(),
            'approved' => StreetApplication::where('status', 'approved')->count(),
            'rejected' => StreetApplication::where('status', 'rejected')->count(),
            'recent' => StreetApplication::where('created_at', '>=', $startDate)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ];
    }

    public function getPaymentDataProperty()
    {
        $startDate = $this->dateRange;

        return [
            'total_transactions' => Payment::where('created_at', '>=', $startDate)->count(),
            'total_amount' => Payment::where('created_at', '>=', $startDate)->sum('amount'),
            'successful' => Payment::where('created_at', '>=', $startDate)
                ->where('status', 'success')
                ->count(),
            'successful_amount' => Payment::where('created_at', '>=', $startDate)
                ->where('status', 'success')
                ->sum('amount'),
            'failed' => Payment::where('created_at', '>=', $startDate)
                ->where('status', 'failed')
                ->count(),
            'recent' => Payment::where('created_at', '>=', $startDate)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ];
    }

    public function exportReport(string $type)
    {
        if (!auth()->user()->hasPermissionTo('export reports')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to export data.',
            ]);
            return;
        }

        $startDate = $this->dateRange;

        $csv = '';

        match ($type) {
            'streets' => $csv = $this->exportStreets($startDate),
            'addresses' => $csv = $this->exportAddresses($startDate),
            'applications' => $csv = $this->exportApplications($startDate),
            'payments' => $csv = $this->exportPayments($startDate),
        };

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, "{$type}-report-" . now()->format('Y-m-d-His') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function exportStreets($startDate)
    {
        $csv = "Name,Code,Ward,Type,Status,Created\n";
        $streets = Street::where('created_at', '>=', $startDate)->get();

        foreach ($streets as $street) {
            $csv .= "\"{$street->name}\",\"{$street->code}\",\"{$street->ward}\",\"{$street->type}\",\"{$street->status}\",\"{$street->created_at->format('Y-m-d H:i')}\"\n";
        }

        return $csv;
    }

    private function exportAddresses($startDate)
    {
        $csv = "House Number,Street,Ward,Owner,Owner Phone,Status,Created\n";
        $addresses = Address::where('created_at', '>=', $startDate)->with('street')->get();

        foreach ($addresses as $address) {
            $csv .= "\"{$address->house_number}\",\"{$address->street?->name}\",\"{$address->ward}\",\"{$address->owner_name}\",\"{$address->owner_phone}\",\"{$address->status}\",\"{$address->created_at->format('Y-m-d H:i')}\"\n";
        }

        return $csv;
    }

    private function exportApplications($startDate)
    {
        $csv = "Street Name,Ward,Type,Status,Submitted By,Created\n";
        $apps = StreetApplication::where('created_at', '>=', $startDate)->with('user')->get();

        foreach ($apps as $app) {
            $csv .= "\"{$app->street_name}\",\"{$app->ward}\",\"{$app->type}\",\"{$app->status}\",\"{$app->user?->email}\",\"{$app->created_at->format('Y-m-d H:i')}\"\n";
        }

        return $csv;
    }

    private function exportPayments($startDate)
    {
        $csv = "Reference,Amount,Currency,Status,Gateway,Date\n";
        $payments = Payment::where('created_at', '>=', $startDate)->get();

        foreach ($payments as $payment) {
            $csv .= "\"{$payment->reference}\",\"{$payment->amount}\",\"{$payment->currency}\",\"{$payment->status}\",\"{$payment->gateway}\",\"{$payment->created_at->format('Y-m-d H:i')}\"\n";
        }

        return $csv;
    }

    public function render()
    {
        return view('livewire.admin.reports.index', [
            'analytics' => $this->analytics,
            'streetData' => $this->streetData,
            'addressData' => $this->addressData,
            'applicationData' => $this->applicationData,
            'paymentData' => $this->paymentData,
        ]);
    }
}
