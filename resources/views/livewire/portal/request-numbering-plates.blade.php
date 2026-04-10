<div>
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2><i class="fas fa-sign" style="color:var(--accent);margin-right:10px;"></i>Request Street Numbering Plates
            </h2>
            <p>Request production and installation of street numbering plates for Njikoka LGA</p>
        </div>
    </div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Street Numbering Plates</h1>
        <p class="text-gray-600 dark:text-gray-400">Request production and installation of street numbering plates
        </p>
    </div>

    <!-- Success Message -->
    @if ($submitted && $lastRequest)
        <div class="card"
            style="max-width:600px;margin:0 auto;text-align:center;padding:40px;background:var(--accent-light);border:1px solid var(--accent);">
            <div
                style="width:72px;height:72px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:#fff;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 style="font-size:22px;font-weight:700;margin-bottom:8px;color:var(--accent);">Request Submitted
                Successfully!</h3>
            <p style="color:var(--text-secondary);margin-bottom:20px;">Your numbering plates request has been received
                and is pending review.</p>

            <div
                style="background:var(--bg-input);border-radius:var(--radius);padding:20px;margin-bottom:24px;border:1px dashed var(--border);text-align:left;">
                <span
                    style="font-size:11px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;display:block;margin-bottom:6px;">Reference
                    Number</span>
                <span
                    style="font-size:24px;font-family:'Space Mono',monospace;font-weight:800;color:var(--accent);">{{ $lastRequest->reference_number }}</span>

                <div
                    style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
                    <div>
                        <span
                            style="font-size:11px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;display:block;margin-bottom:4px;">Quantity</span>
                        <span
                            style="font-size:16px;font-weight:600;color:var(--text-primary);">{{ $lastRequest->quantity_requested }}
                            plates</span>
                    </div>
                    <div>
                        <span
                            style="font-size:11px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;display:block;margin-bottom:4px;">Estimated
                            Cost</span>
                        <span
                            style="font-size:16px;font-weight:600;color:var(--text-primary);">₦{{ number_format($lastRequest->getTotalCost(), 2) }}</span>
                    </div>
                    <div>
                        <span
                            style="font-size:11px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;display:block;margin-bottom:4px;">Status</span>
                        <span class="status-badge pending">{{ $lastRequest->getStatusLabel() }}</span>
                    </div>
                    <div>
                        <span
                            style="font-size:11px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;display:block;margin-bottom:4px;">Delivery
                            Date</span>
                        <span
                            style="font-size:14px;color:var(--text-primary);">{{ $lastRequest->installation_date_requested?->format('M d, Y') ?? 'To be scheduled' }}</span>
                    </div>
                </div>
            </div>

            <p style="font-size:13px;color:var(--text-secondary);margin-bottom:24px;">You will receive a notification
                when your request is approved and ready for production. An email receipt has been sent to your
                registered email address.</p>

            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <button wire:click="newRequest" class="btn btn-primary"><i class="fas fa-plus"></i> Submit Another
                    Request</button>
                <a href="{{ route('portal.dashboard') }}" class="btn btn-outline">Go to Dashboard</a>
            </div>
        </div>
    @else
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-line"></div>
            <div class="step-item {{ $step == 1 ? 'active' : ($step > 1 ? 'completed' : '') }}">
                <div class="step-number">1</div>
                <div class="step-label">Street Details</div>
            </div>
            <div class="step-item {{ $step == 2 ? 'active' : ($step > 2 ? 'completed' : '') }}">
                <div class="step-number">2</div>
                <div class="step-label">Specifications</div>
            </div>
            <div class="step-item {{ $step == 3 ? 'active' : ($step > 3 ? 'completed' : '') }}">
                <div class="step-number">3</div>
                <div class="step-label">Installation</div>
            </div>
        </div>

        <!-- Form Container -->
        <div class="card">
            <!-- Step 1: Street Selection -->
            @if ($step === 1)
                <h3 style="font-size:18px;font-weight:700;margin-bottom:20px;"><i class="fas fa-road"
                        style="color:var(--accent);margin-right:8px;"></i>Step 1: Select Street</h3>

                <div style="margin-bottom:20px;">
                    <label
                        style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:12px;">Street
                        Selection</label>

                    @if (count($streets) > 0)
                        <label
                            style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;margin-bottom:8px;letter-spacing:0.5px;">Select
                            from existing streets:</label>
                        <div
                            style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;margin-bottom:20px;max-height:300px;overflow-y:auto;padding-right:8px;">
                            @foreach ($streets as $street)
                                <button type="button" wire:click="selectStreet({{ $street['id'] }})"
                                    style="text-align:left;padding:12px;border:2px solid {{ $street_id === $street['id'] ? 'var(--accent)' : 'var(--border)' }};border-radius:var(--radius-sm);background:{{ $street_id === $street['id'] ? 'var(--accent-light)' : 'transparent' }};cursor:pointer;transition:all var(--transition);"
                                    class="hover:border-accent">
                                    <p style="font-weight:600;color:var(--text-primary);margin-bottom:2px;">
                                        {{ $street['name'] }}</p>
                                    <p style="font-size:12px;color:var(--text-secondary);">Town: {{ $street['town'] }}
                                    </p>
                                </button>
                            @endforeach
                        </div>

                        @if ($street_id)
                            <button type="button" wire:click="clearStreetSelection"
                                style="color:var(--accent);font-size:12px;cursor:pointer;text-decoration:underline;">
                                Clear Selection
                            </button>
                        @endif

                        <div style="position:relative;margin:20px 0;display:flex;align-items:center;">
                            <div style="flex:1;height:1px;background:var(--border);"></div>
                            <span
                                style="padding:0 12px;font-size:12px;color:var(--text-secondary);font-weight:600;text-transform:uppercase;">OR</span>
                            <div style="flex:1;height:1px;background:var(--border);"></div>
                        </div>
                    @endif

                    <label
                        style="display:block;font-size:12px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;margin-bottom:8px;letter-spacing:0.5px;">Enter
                        street details manually:</label>

                    <div class="form-grid">
                        <div class="form-group" style="grid-column:1/-1;">
                            <label>Street Name *</label>
                            <input wire:model="street_name" type="text" placeholder="e.g., Ikeja Road">
                            @error('street_name')
                                <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label>Town *</label>
                            <input wire:model="town" type="text" placeholder="e.g., Abagana Town">
                            @error('town')
                                <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2: Plate Specifications -->
            @if ($step === 2)
                <h3 style="font-size:18px;font-weight:700;margin-bottom:20px;"><i class="fas fa-cog"
                        style="color:var(--accent);margin-right:8px;"></i>Step 2: Plate Specifications</h3>

                <div class="form-grid">
                    <!-- Quantity -->
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Number of Plates Required *</label>
                        <input wire:model.live="quantity_requested" type="number" min="1" max="100"
                            placeholder="1-100">
                        @error('quantity_requested')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Plate Type -->
                    <div class="form-group" style="grid-column:1/-1;">
                        <label style="margin-bottom:12px;">Plate Type *</label>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;">
                            @foreach ($plateTypes as $key => $label)
                                <button type="button" wire:click="$set('plate_type', '{{ $key }}')"
                                    style="text-align:left;padding:12px;border:2px solid {{ $plate_type === $key ? 'var(--accent)' : 'var(--border)' }};border-radius:var(--radius-sm);background:{{ $plate_type === $key ? 'var(--accent-light)' : 'transparent' }};cursor:pointer;transition:all var(--transition);font-weight:500;color:var(--text-primary);"
                                    class="hover:border-accent">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                        @error('plate_type')
                            <span
                                style="color:var(--danger);font-size:12px;margin-top:6px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Material -->
                    <div class="form-group" style="grid-column:1/-1;">
                        <label style="margin-bottom:12px;">Material *</label>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:8px;">
                            @foreach ($materials as $key => $label)
                                <button type="button" wire:click="$set('material', '{{ $key }}')"
                                    style="text-align:left;padding:12px;border:2px solid {{ $material === $key ? 'var(--accent)' : 'var(--border)' }};border-radius:var(--radius-sm);background:{{ $material === $key ? 'var(--accent-light)' : 'transparent' }};cursor:pointer;transition:all var(--transition);font-weight:500;color:var(--text-primary);"
                                    class="hover:border-accent">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                        @error('material')
                            <span
                                style="color:var(--danger);font-size:12px;margin-top:6px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Design Variant -->
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Design Variant</label>
                        <select wire:model="design_variant">
                            @foreach ($designVariants as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estimated Cost -->
                    @if ($estimatedCost)
                        <div
                            style="grid-column:1/-1;background:var(--accent-light);border:1px solid var(--accent);border-radius:var(--radius-sm);padding:16px;">
                            <p
                                style="font-size:12px;color:var(--text-secondary);margin-bottom:6px;text-transform:uppercase;font-weight:700;letter-spacing:0.5px;">
                                Estimated Cost</p>
                            <p style="font-size:28px;font-weight:800;color:var(--accent);margin-bottom:8px;">
                                ₦{{ number_format($estimatedCost, 2) }}</p>
                            <p style="font-size:11px;color:var(--text-secondary);">For {{ $quantity_requested }}
                                plate(s). Final cost may vary based on approval and customizations.</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Step 3: Installation & Delivery -->
            @if ($step === 3)
                <h3 style="font-size:18px;font-weight:700;margin-bottom:20px;"><i class="fas fa-truck"
                        style="color:var(--accent);margin-right:8px;"></i>Step 3: Installation & Delivery</h3>

                <div class="form-grid">
                    <!-- Installation Address -->
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Installation Address</label>
                        <textarea wire:model="installation_address" rows="3" placeholder="Where should the plates be installed?"
                            style="resize:vertical;"></textarea>
                        @error('installation_address')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Installation Date -->
                    <div class="form-group">
                        <label>Preferred Installation Date</label>
                        <input wire:model="installation_date_requested" type="date">
                        @error('installation_date_requested')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Delivery Address -->
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Delivery Address *</label>
                        <textarea wire:model="delivery_address" rows="3" placeholder="Where should we deliver the plates?"
                            style="resize:vertical;"></textarea>
                        @error('delivery_address')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Contact Phone -->
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Contact Phone Number *</label>
                        <input wire:model="contact_phone" type="tel" placeholder="+234 800 000 0000">
                        @error('contact_phone')
                            <span style="color:var(--danger);font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Order Summary -->
                    <div
                        style="grid-column:1/-1;background:var(--bg-input);border-radius:var(--radius-sm);padding:16px;border:1px solid var(--border);">
                        <h4 style="font-weight:700;color:var(--text-primary);margin-bottom:12px;">Order Summary</h4>

                        <div
                            style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px dashed var(--border);">
                            <div>
                                <span
                                    style="color:var(--text-secondary);display:block;font-size:11px;text-transform:uppercase;font-weight:700;margin-bottom:2px;">Street</span>
                                <span style="color:var(--text-primary);font-weight:600;">{{ $street_name }}
                                    ({{ $town }})</span>
                            </div>
                            <div>
                                <span
                                    style="color:var(--text-secondary);display:block;font-size:11px;text-transform:uppercase;font-weight:700;margin-bottom:2px;">Quantity</span>
                                <span style="color:var(--text-primary);font-weight:600;">{{ $quantity_requested }}
                                    plates</span>
                            </div>
                            <div>
                                <span
                                    style="color:var(--text-secondary);display:block;font-size:11px;text-transform:uppercase;font-weight:700;margin-bottom:2px;">Type</span>
                                <span
                                    style="color:var(--text-primary);font-weight:600;">{{ $plateTypes[$plate_type] ?? 'Unknown' }}</span>
                            </div>
                            <div>
                                <span
                                    style="color:var(--text-secondary);display:block;font-size:11px;text-transform:uppercase;font-weight:700;margin-bottom:2px;">Material</span>
                                <span
                                    style="color:var(--text-primary);font-weight:600;">{{ $materials[$material] ?? 'Unknown' }}</span>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span
                                style="color:var(--text-secondary);font-weight:700;text-transform:uppercase;font-size:11px;">Total
                                Estimated Cost</span>
                            <span
                                style="font-size:22px;font-weight:800;color:var(--accent);">₦{{ number_format($estimatedCost, 2) }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div
                style="display:flex;justify-content:space-between;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border);">
                <button type="button" wire:click="previousStep" @if ($step === 1) disabled @endif
                    class="btn btn-outline" style="flex:1;">
                    <i class="fas fa-chevron-left"></i> Previous
                </button>

                @if ($step < 3)
                    <button type="button" wire:click="nextStep" class="btn btn-primary" style="flex:1;">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                @else
                    <button type="button" wire:click="submit" class="btn btn-accent" style="flex:1;">
                        <i class="fas fa-check"></i> Submit Request
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
    .hover\:border-accent:hover {
        border-color: var(--accent) !important;
    }
</style>
