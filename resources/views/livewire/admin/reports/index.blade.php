<div class="p-4 md:p-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reports & Analytics</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">System-wide statistics and insights</p>
    </div>

    <!-- Date Range Filter -->
    <div class="mb-6 flex justify-between items-center">
        <div class="flex gap-2">
            <select wire:model.live="filterDateRange"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="7days">Last 7 Days</option>
                <option value="30days">Last 30 Days</option>
                <option value="90days">Last 90 Days</option>
                <option value="all">All Time</option>
            </select>
        </div>
    </div>

    <!-- Main Analytics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Users</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $analytics['total_users'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Streets</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $analytics['total_streets'] }}</div>
            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $analytics['recent_streets'] }} recent</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Addresses</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $analytics['total_addresses'] }}</div>
            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $analytics['recent_addresses'] }} recent</div>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 shadow">
            <div class="text-green-600 dark:text-green-400 text-sm font-medium">Total Revenue</div>
            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">
                ₦{{ number_format($analytics['total_revenue'], 2) }}</div>
        </div>
    </div>

    <!-- Application Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 shadow">
            <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">Pending Approvals</div>
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $analytics['pending_approvals'] }}
            </div>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 shadow">
            <div class="text-green-600 dark:text-green-400 text-sm font-medium">Approved Applications</div>
            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">
                {{ $analytics['approved_applications'] }}</div>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6 shadow">
            <div class="text-yellow-600 dark:text-yellow-400 text-sm font-medium">Recent Applications</div>
            <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">
                {{ $analytics['recent_applications'] }}</div>
        </div>
    </div>

    <!-- Payment Stats & Export -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">Total Transactions</span>
                    <span
                        class="font-semibold text-gray-900 dark:text-white">{{ $paymentData['total_transactions'] }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">Successful</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">{{ $paymentData['successful'] }}
                        (₦{{ number_format($paymentData['successful_amount'], 2) }})</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400">Failed</span>
                    <span class="font-semibold text-red-600 dark:text-red-400">{{ $paymentData['failed'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Export Reports</h3>
            <div class="grid grid-cols-2 gap-2">
                <button wire:click="exportReport('streets')"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-road"></i> Streets
                </button>
                <button wire:click="exportReport('addresses')"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-home"></i> Addresses
                </button>
                <button wire:click="exportReport('applications')"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-file"></i> Applications
                </button>
                <button wire:click="exportReport('payments')"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-credit-card"></i> Payments
                </button>
            </div>
        </div>
    </div>

    <!-- Street Types Distribution -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Streets by Type</h3>
            @if ($streetData['by_type']->count() > 0)
                <div class="space-y-3">
                    @foreach ($streetData['by_type'] as $item)
                        <div
                            class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400 capitalize">{{ $item->type }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $item->count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No street data available</p>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Addresses by Status</h3>
            @if ($addressData['by_status']->count() > 0)
                <div class="space-y-3">
                    @foreach ($addressData['by_status'] as $item)
                        <div
                            class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400 capitalize">{{ $item->status }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $item->count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No address data available</p>
            @endif
        </div>
    </div>

    <!-- Application Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Status Breakdown</h3>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center pb-4">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $applicationData['total'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Total</div>
            </div>
            <div class="text-center pb-4 border-l border-r border-gray-200 dark:border-gray-700">
                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $applicationData['pending'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pending</div>
            </div>
            <div class="text-center pb-4">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $applicationData['approved'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Approved</div>
            </div>
        </div>
    </div>
</div>
