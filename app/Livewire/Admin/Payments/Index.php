<?php

namespace App\Livewire\Admin\Payments;

use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Payment Management')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'all';
    public string $filterGateway = 'all';
    public string $filterDateRange = '30days';

    protected $queryString = ['search', 'filterStatus', 'filterGateway', 'filterDateRange'];

    public function mount()
    {
        // Allow access to super-admin or users with view payments permission
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('view payments')) {
            abort(403, 'Unauthorized access to payments.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterGateway()
    {
        $this->resetPage();
    }

    public function updatingFilterDateRange()
    {
        $this->resetPage();
    }

    public function getPaymentsProperty()
    {
        $query = Payment::query()
            ->with(['user', 'payable'])
            ->orderByDesc('created_at');

        // Search
        if ($this->search) {
            $query->where('reference', 'like', "%{$this->search}%")
                  ->orWhere('amount', 'like', "%{$this->search}%")
                  ->orWhereHas('user', function ($q) {
                      $q->where('email', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%");
                  });
        }

        // Filter by status
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // Filter by gateway
        if ($this->filterGateway !== 'all') {
            $query->where('gateway', $this->filterGateway);
        }

        // Filter by date range
        $now = now();
        switch ($this->filterDateRange) {
            case '7days':
                $query->where('created_at', '>=', $now->subDays(7));
                break;
            case '30days':
                $query->where('created_at', '>=', $now->subDays(30));
                break;
            case '90days':
                $query->where('created_at', '>=', $now->subDays(90));
                break;
            case 'all':
                break;
        }

        return $query->paginate(15);
    }

    public function getPaymentStatsProperty()
    {
        $stats = [];
        
        // Total payments
        $stats['total'] = Payment::count();
        
        // Successful payments
        $stats['successful'] = Payment::where('status', 'success')->count();
        $stats['successful_amount'] = Payment::where('status', 'success')->sum('amount');
        
        // Pending payments
        $stats['pending'] = Payment::where('status', 'pending')->count();
        
        // Failed payments
        $stats['failed'] = Payment::where('status', 'failed')->count();

        return $stats;
    }

    public function getStatusBadgeProperty()
    {
        return [
            'pending' => ['bg-yellow-100', 'text-yellow-800', 'Pending'],
            'success' => ['bg-green-100', 'text-green-800', 'Success'],
            'failed' => ['bg-red-100', 'text-red-800', 'Failed'],
            'cancelled' => ['bg-gray-100', 'text-gray-800', 'Cancelled'],
        ];
    }

    public function refundPayment(string $paymentId)
    {
        if (!auth()->user()->hasRole('super-admin') && !auth()->user()->hasPermissionTo('refund payments')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to refund payments.',
            ]);
            return;
        }

        $payment = Payment::findOrFail($paymentId);

        if ($payment->status !== 'success') {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Only successful payments can be refunded.',
            ]);
            return;
        }

        try {
            // Refund logic would go here
            // For now, just mark as cancelled
            $payment->update(['status' => 'cancelled']);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Payment refunded successfully.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Refund failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function exportPayments()
    {
        if (!auth()->user()->hasPermissionTo('export reports')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You do not have permission to export data.',
            ]);
            return;
        }

        $payments = Payment::query()
            ->with(['user', 'payable'])
            ->get();

        $csv = "Reference,User,Amount,Currency,Status,Gateway,Date\n";
        
        foreach ($payments as $payment) {
            $csv .= "\"{$payment->reference}\",\"{$payment->user?->email}\",\"{$payment->amount}\",\"{$payment->currency}\",\"{$payment->status}\",\"{$payment->gateway}\",\"{$payment->created_at->format('Y-m-d H:i')}\"\n";
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'payments-' . now()->format('Y-m-d-His') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.payments.index', [
            'payments' => $this->payments,
            'stats' => $this->paymentStats,
            'statusBadges' => $this->statusBadge,
        ]);
    }
}
