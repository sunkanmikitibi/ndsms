<div class="p-4 md:p-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payment Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">View and manage all payment transactions</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Payments</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 shadow">
            <div class="text-green-600 dark:text-green-400 text-sm font-medium">Successful</div>
            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $stats['successful'] }}</div>
            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                ₦{{ number_format($stats['successful_amount'], 2) }}</div>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6 shadow">
            <div class="text-yellow-600 dark:text-yellow-400 text-sm font-medium">Pending</div>
            <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-6 shadow">
            <div class="text-red-600 dark:text-red-400 text-sm font-medium">Failed</div>
            <div class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $stats['failed'] }}</div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" wire:model.live.debounce="search" placeholder="Reference/Amount/Email"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select wire:model.live="filterStatus"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Gateway Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gateway</label>
                <select wire:model.live="filterGateway"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All</option>
                    <option value="paystack">Paystack</option>
                    <option value="manual">Manual</option>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                <select wire:model.live="filterDateRange"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="7days">Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="90days">Last 90 Days</option>
                    <option value="all">All Time</option>
                </select>
            </div>
        </div>

        <!-- Export Button -->
        <div class="mt-4">
            <button wire:click="exportPayments"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-gray-900 dark:text-white">Reference</th>
                        <th class="px-6 py-3 font-semibold text-gray-900 dark:text-white">User</th>
                        <th class="px-6 py-3 font-semibold text-gray-900 dark:text-white">Amount</th>
                        <th class="px-6 py-3 font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-3 font-semibold text-gray-900 dark:text-white">Gateway</th>
                        <th class="px-6 py-3 font-semibold text-gray-900 dark:text-white">Date</th>
                        <th class="px-6 py-3 font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 text-gray-900 dark:text-white font-mono text-xs">
                                {{ $payment->reference }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">
                                <div class="font-medium">{{ $payment->user?->name }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ $payment->user?->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white font-semibold">
                                ₦{{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    [$bgColor, $textColor, $label] = $statusBadges[$payment->status] ?? [
                                        'bg-gray-100',
                                        'text-gray-800',
                                        'Unknown',
                                    ];
                                @endphp
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $bgColor }} {{ $textColor }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white text-sm capitalize">
                                {{ $payment->payment_method }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white text-sm">
                                {{ $payment->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('portal.payments.show', $payment) }}"
                                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition">View</a>

                                    @if (!empty($payment->metadata['proofs'] ?? []) && $payment->status === 'pending')
                                        <button wire:click="verifyProof('{{ $payment->id }}')"
                                            class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition">Verify</button>
                                    @endif

                                    <button wire:click="refundPayment('{{ $payment->id }}')"
                                        @if ($payment->status !== 'success') disabled @endif
                                        class="@if ($payment->status === 'success') px-3 py-1 bg-red-600 hover:bg-red-700 @else px-3 py-1 bg-gray-400 @endif text-white text-xs rounded transition">
                                        Refund
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p class="mt-2">No payments found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600">
            {{ $payments->links() }}
        </div>
    </div>
</div>
