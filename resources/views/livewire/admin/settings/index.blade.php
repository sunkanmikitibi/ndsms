<div class="p-4 md:p-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">System Settings</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Configure application settings and preferences</p>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6 overflow-x-auto">
        <button wire:click="$set('activeTab', 'general')"
            class="px-4 py-3 font-medium border-b-2 transition {{ $activeTab === 'general' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
            <i class="fas fa-cog mr-2"></i> General
        </button>
        <button wire:click="$set('activeTab', 'email')"
            class="px-4 py-3 font-medium border-b-2 transition {{ $activeTab === 'email' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
            <i class="fas fa-envelope mr-2"></i> Email
        </button>
        <button wire:click="$set('activeTab', 'payment')"
            class="px-4 py-3 font-medium border-b-2 transition {{ $activeTab === 'payment' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
            <i class="fas fa-credit-card mr-2"></i> Payment
        </button>
        <button wire:click="$set('activeTab', 'features')"
            class="px-4 py-3 font-medium border-b-2 transition {{ $activeTab === 'features' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
            <i class="fas fa-toggle-on mr-2"></i> Features
        </button>
    </div>

    <!-- General Settings -->
    @if ($activeTab === 'general')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">General Settings</h2>

            <form wire:submit="saveGeneralSettings" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Application
                            Name</label>
                        <input type="text" wire:model="appName"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email
                            Address</label>
                        <input type="email" wire:model="appEmail"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone
                            Number</label>
                        <input type="text" wire:model="appPhone"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Optional">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Physical
                        Address</label>
                    <textarea wire:model="appAddress" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Optional"></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Email Settings -->
    @if ($activeTab === 'email')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Email Configuration</h2>

            <form wire:submit="saveEmailSettings" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mail Driver</label>
                    <select wire:model="mailDriver"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="log">Log</option>
                        <option value="smtp">SMTP</option>
                        <option value="sendmail">Sendmail</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From
                            Email</label>
                        <input type="email" wire:model="mailFrom"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Host</label>
                        <input type="text" wire:model="mailHost"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP Port</label>
                        <input type="number" wire:model="mailPort"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                        <input type="text" wire:model="mailUsername"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                    <input type="password" wire:model="mailPassword"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Save Email Settings
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Payment Settings -->
    @if ($activeTab === 'payment')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Configuration</h2>

            <form wire:submit="savePaymentSettings" class="space-y-6">
                <div
                    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        <i class="fas fa-info-circle mr-2"></i>
                        Configure Paystack payment gateway credentials
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Environment</label>
                    <select wire:model="paystackEnv"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="test">Test</option>
                        <option value="live">Live</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Public Key</label>
                    <input type="password" wire:model="paystackPublicKey"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="pk_****">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave blank to keep current value</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Secret Key</label>
                    <input type="password" wire:model="paystackSecretKey"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="sk_****">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave blank to keep current value</p>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Save Payment Settings
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Feature Flags -->
    @if ($activeTab === 'features')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Feature Flags</h2>

            <form wire:submit="saveFeatureFlags" class="space-y-6">
                <div class="space-y-4">
                    <div
                        class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">Address Indexing</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Google Maps address indexing service
                            </p>
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="enableAddressIndexing" class="w-5 h-5">
                            <span
                                class="ml-2 text-sm {{ $enableAddressIndexing ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $enableAddressIndexing ? 'Enabled' : 'Disabled' }}
                            </span>
                        </label>
                    </div>

                    <div
                        class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">Street Revalidation</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Street verification and revalidation
                                service</p>
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="enableStreetRevalidation" class="w-5 h-5">
                            <span
                                class="ml-2 text-sm {{ $enableStreetRevalidation ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $enableStreetRevalidation ? 'Enabled' : 'Disabled' }}
                            </span>
                        </label>
                    </div>

                    <div
                        class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">QR Scanner</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Mobile QR code scanning (beta)</p>
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="enableQrScanner" class="w-5 h-5">
                            <span
                                class="ml-2 text-sm {{ $enableQrScanner ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $enableQrScanner ? 'Enabled' : 'Disabled' }}
                            </span>
                        </label>
                    </div>

                    <div
                        class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">AI Address Lookup</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Natural language address search (beta)
                            </p>
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="enableAiLookup" class="w-5 h-5">
                            <span
                                class="ml-2 text-sm {{ $enableAiLookup ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $enableAiLookup ? 'Enabled' : 'Disabled' }}
                            </span>
                        </label>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Save Feature Flags
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
