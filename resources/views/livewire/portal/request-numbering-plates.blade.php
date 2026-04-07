<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Street Numbering Plates</h1>
            <p class="text-gray-600 dark:text-gray-400">Request production and installation of street numbering plates
            </p>
        </div>

        <!-- Success Message -->
        @if ($submitted && $lastRequest)
            <div class="bg-gradient-to-r from-green-400 to-emerald-500 rounded-lg shadow-lg p-8 mb-8 text-white">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <h2 class="text-2xl font-bold">Request Submitted Successfully!</h2>
                </div>

                <div class="bg-white/20 rounded-lg p-4 mb-4">
                    <p class="text-sm mb-2 opacity-90">Your Reference Number:</p>
                    <p class="text-3xl font-mono font-bold">{{ $lastRequest->reference_number }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-white/20 rounded-lg p-4">
                        <p class="text-sm opacity-90">Quantity: <strong>{{ $lastRequest->quantity_requested }}
                                plates</strong></p>
                        <p class="text-sm opacity-90">Estimated Cost:
                            <strong>₦{{ number_format($lastRequest->getTotalCost(), 2) }}</strong></p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-4">
                        <p class="text-sm opacity-90">Status: <strong>{{ $lastRequest->getStatusLabel() }}</strong></p>
                        <p class="text-sm opacity-90">Delivery:
                            {{ $lastRequest->installation_date_requested?->format('M d, Y') ?? 'To be scheduled' }}</p>
                    </div>
                </div>

                <p class="text-sm mb-4 opacity-90">
                    Your request has been submitted for review. You will receive a notification when it's approved and
                    ready for production. An email receipt has been sent to your registered email address.
                </p>

                <button wire:click="newRequest"
                    class="w-full bg-white text-green-600 hover:bg-gray-100 font-bold py-3 px-4 rounded-lg transition duration-200">
                    Submit Another Request
                </button>
            </div>
        @else
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex justify-between mb-4">
                    <div class="text-center flex-1">
                        <div
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                            1
                        </div>
                        <p class="text-sm mt-2 font-medium {{ $step >= 1 ? 'text-blue-600' : 'text-gray-500' }}">Street
                            Details</p>
                    </div>
                    <div class="flex-1 flex items-center px-2">
                        <div class="flex-1 h-1 {{ $step >= 2 ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                    </div>
                    <div class="text-center flex-1">
                        <div
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                            2
                        </div>
                        <p class="text-sm mt-2 font-medium {{ $step >= 2 ? 'text-blue-600' : 'text-gray-500' }}">
                            Specifications</p>
                    </div>
                    <div class="flex-1 flex items-center px-2">
                        <div class="flex-1 h-1 {{ $step >= 3 ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
                    </div>
                    <div class="text-center flex-1">
                        <div
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                            3
                        </div>
                        <p class="text-sm mt-2 font-medium {{ $step >= 3 ? 'text-blue-600' : 'text-gray-500' }}">
                            Installation</p>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 md:p-8">

                <!-- Step 1: Street Selection -->
                @if ($step === 1)
                    <div class="animate-in">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Step 1: Select Street</h2>

                        <!-- Street Selection Options -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Choose from
                                existing streets or enter details</label>

                            @if (count($streets) > 0)
                                <div class="mb-6">
                                    <label
                                        class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Select
                                        from existing streets:</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-64 overflow-y-auto">
                                        @foreach ($streets as $street)
                                            <button type="button" wire:click="selectStreet({{ $street['id'] }})"
                                                class="text-left p-3 border-2 rounded-lg transition {{ $street_id === $street['id'] ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/30' : 'border-gray-300 dark:border-gray-600 hover:border-gray-400' }}">
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $street['name'] }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Ward:
                                                    {{ $street['ward'] }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Divider -->
                            <div class="relative my-6">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">OR</span>
                                </div>
                            </div>

                            <!-- Manual Entry -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enter
                                    street details manually:</label>
                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Street
                                            Name *</label>
                                        <input type="text" wire:model="street_name" placeholder="e.g., Ikeja Road"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @error('street_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ward
                                            *</label>
                                        <input type="text" wire:model="ward" placeholder="e.g., Lagos Island"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @error('ward')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($street_id)
                            <button type="button" wire:click="clearStreetSelection"
                                class="text-blue-600 hover:text-blue-800 text-sm mb-4">
                                Clear Selection
                            </button>
                        @endif
                    </div>
                @endif

                <!-- Step 2: Plate Specifications -->
                @if ($step === 2)
                    <div class="animate-in">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Step 2: Plate Specifications
                        </h2>

                        <div class="space-y-6">
                            <!-- Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of
                                    Plates Required *</label>
                                <input type="number" wire:model.live="quantity_requested" min="1" max="100"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('quantity_requested')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Plate Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Plate
                                    Type *</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach ($plateTypes as $key => $label)
                                        <label
                                            class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition {{ $plate_type === $key ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/30' : 'border-gray-300 dark:border-gray-600' }}">
                                            <input type="radio" wire:model.live="plate_type"
                                                value="{{ $key }}" class="w-4 h-4 text-blue-600">
                                            <span
                                                class="ml-3 font-medium text-gray-900 dark:text-white">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('plate_type')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Material -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Material
                                    *</label>
                                <div class="space-y-2">
                                    @foreach ($materials as $key => $label)
                                        <label
                                            class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition {{ $material === $key ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/30' : 'border-gray-300 dark:border-gray-600' }}">
                                            <input type="radio" wire:model.live="material"
                                                value="{{ $key }}" class="w-4 h-4 text-blue-600">
                                            <span
                                                class="ml-3 font-medium text-gray-900 dark:text-white">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('material')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Design Variant -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Design
                                    Variant</label>
                                <select wire:model="design_variant"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @foreach ($designVariants as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Estimated Cost -->
                            @if ($estimatedCost)
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Estimated Cost for
                                        {{ $quantity_requested }} plate(s):</p>
                                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                        ₦{{ number_format($estimatedCost, 2) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Note: Final cost may vary
                                        based on approval and customizations</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Step 3: Installation & Delivery -->
                @if ($step === 3)
                    <div class="animate-in">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Step 3: Installation &
                            Delivery</h2>

                        <div class="space-y-6">
                            <!-- Installation Address -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Installation
                                    Address</label>
                                <textarea wire:model="installation_address" rows="3" placeholder="Where should the plates be installed?"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                @error('installation_address')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Installation Date -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred
                                    Installation Date</label>
                                <input type="date" wire:model="installation_date_requested"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('installation_date_requested')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Delivery Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Delivery
                                    Address *</label>
                                <textarea wire:model="delivery_address" rows="3" placeholder="Where should we deliver the plates?"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                @error('delivery_address')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Contact Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact
                                    Phone Number *</label>
                                <input type="tel" wire:model="contact_phone" placeholder="+234 XXX XXX XXXX"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('contact_phone')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Summary -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Street:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $street_name }}
                                            ({{ $ward }})</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Quantity:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $quantity_requested }}
                                            plates</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Type:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $plateTypes[$plate_type] ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Material:</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $materials[$material] ?? 'Unknown' }}</span>
                                    </div>
                                    <div
                                        class="border-t border-gray-300 dark:border-gray-600 pt-2 mt-3 flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300 font-semibold">Estimated
                                            Total:</span>
                                        <span
                                            class="text-lg font-bold text-blue-600">₦{{ number_format($estimatedCost, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-8">
                    <button type="button" wire:click="previousStep" @if ($step === 1) disabled @endif
                        class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        ← Previous
                    </button>

                    @if ($step < 3)
                        <button type="button" wire:click="nextStep"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Next →
                        </button>
                    @else
                        <button type="button" wire:click="submit"
                            class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Submit Request
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
