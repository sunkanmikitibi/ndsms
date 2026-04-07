<div class="p-4 md:p-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Ward Map</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Geographic distribution of streets and addresses across wards
        </p>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Ward Select -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Ward</label>
                <select wire:model.live="selectedWard"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- All Wards --</option>
                    @foreach ($wards as $ward)
                        <option value="{{ $ward }}">{{ $ward }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Street Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Street Type</label>
                <select wire:model.live="filterType"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Types</option>
                    @foreach ($streetTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Export Button -->
            <div class="flex items-end">
                <button wire:click="exportWardData"
                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fas fa-download"></i> Export Data
                </button>
            </div>
        </div>
    </div>

    <!-- Coverage Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
            <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Selected Ward</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                {{ $selectedWard ?: 'All Wards' }}
            </div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 shadow">
            <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">Streets</div>
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $mapData['street_count'] }}</div>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 shadow">
            <div class="text-green-600 dark:text-green-400 text-sm font-medium">Addresses</div>
            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $mapData['address_count'] }}
            </div>
        </div>
        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-6 shadow">
            <div class="text-purple-600 dark:text-purple-400 text-sm font-medium">Coverage</div>
            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-2">{{ $mapData['coverage'] }}%</div>
        </div>
    </div>

    <!-- Map Placeholder -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 overflow-hidden">
        <div class="w-full h-96 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
            <div class="text-center">
                <i class="fas fa-map text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Interactive map view (requires Google Maps API integration)
                </p>
                @if ($selectedWard)
                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Ward: <span
                            class="font-semibold">{{ $selectedWard }}</span></p>
                @endif
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                    Showing {{ $mapData['street_count'] }} streets and {{ $mapData['address_count'] }} addresses
                </p>
            </div>
        </div>
    </div>

    <!-- Streets & Addresses List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Streets -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-road mr-2"></i> Streets
                </h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                @forelse($streets as $street)
                    <div class="px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $street->name }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="inline-block mr-2"><i class="fas fa-tag"></i> {{ $street->code }}</span>
                            <span class="inline-block"><i class="fas fa-map-marker"></i> {{ $street->ward }}</span>
                        </div>
                        <div class="text-xs mt-1">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                {{ $street->type }}
                            </span>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full ml-2 {{ $street->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $street->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-2xl mb-2"></i>
                        <p>No streets found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Addresses -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-home mr-2"></i> Addresses
                </h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                @forelse($addresses as $address)
                    <div class="px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $address->house_number }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span><i class="fas fa-road mr-1"></i> {{ $address->street?->name }}</span>
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            <span><i class="fas fa-user"></i> {{ $address->owner_name }}</span>
                        </div>
                        <div class="text-xs mt-1">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full {{ $address->status === 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' }}">
                                {{ $address->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-2xl mb-2"></i>
                        <p>No addresses found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
