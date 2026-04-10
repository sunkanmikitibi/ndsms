<div>
    <div class="page-header" style="text-align:center;display:block;">
        <h2 style="font-size:28px;margin-bottom:8px;">
            <i class="fas fa-edit" style="color:var(--accent);margin-right:12px;"></i>Register Your Address
        </h2>
        <p>Submit your property details for official address registration in Njikoka LGA</p>
    </div>

    <!-- Registration Type Toggle -->
    <div class="register-type-row">
        <button wire:click="setRegistrationType('single')" class="type-btn {{ $registrationType === 'single' ? 'active' : '' }}">
            <i class="fas fa-user"></i> Single
        </button>
        <button wire:click="setRegistrationType('bulk')" class="type-btn {{ $registrationType === 'bulk' ? 'active' : '' }}">
            <i class="fas fa-users"></i> Bulk Registration
        </button>
    </div>

    @if($submitted)
        <div class="card" style="max-width:580px;margin:0 auto;text-align:center;padding:40px;">
            <div style="width:72px;height:72px;background:var(--accent-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:var(--accent);">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 style="font-size:22px;font-weight:700;margin-bottom:8px;">Registration Submitted!</h3>
            <p style="color:var(--text-secondary);margin-bottom:20px;">Your registration request has been received and is pending review.</p>
            
            <div style="background:var(--bg-input);padding:20px;border-radius:var(--radius);margin-bottom:24px;border:1px dashed var(--border);">
                @if($registrationType === 'bulk')
                    <span style="font-size:11px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;display:block;margin-bottom:4px;">Batch Reference (Primary)</span>
                @else
                    <span style="font-size:11px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;display:block;margin-bottom:4px;">Your Reference Code</span>
                @endif
                <span style="font-size:24px;font-family:'Space Mono',monospace;font-weight:800;color:var(--accent);">{{ $reference_code }}</span>
                @if($registrationType === 'bulk')
                    <p style="font-size:12px;margin-top:8px;color:var(--text-secondary);">Registered {{ count($bulkAddresses) }} properties. Use individual codes from your dashboard for tracking.</p>
                @else
                    <p style="font-size:12px;margin-top:8px;color:var(--text-secondary);">Save this code to track your registration status.</p>
                @endif
            </div>

            <div style="display:flex;gap:12px;justify-content:center;">
                <button wire:click="newRegistration" class="btn btn-outline"><i class="fas fa-plus"></i> Register Another</button>
                <a href="{{ route('portal.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
            </div>
        </div>
    @else
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-line"></div>
            <div class="step-item {{ $step == 1 ? 'active' : ($step > 1 ? 'completed' : '') }}">
                <div class="step-circle">1</div>
                <div class="step-label">Personal</div>
            </div>
            <div class="step-item {{ $step == 2 ? 'active' : ($step > 2 ? 'completed' : '') }}">
                <div class="step-circle">2</div>
                <div class="step-label">Location</div>
            </div>
            <div class="step-item {{ $step == 3 ? 'active' : ($step > 3 ? 'completed' : '') }}">
                <div class="step-circle">3</div>
                <div class="step-label">Details</div>
            </div>
            <div class="step-item {{ $step == 4 ? 'active' : '' }}">
                <div class="step-circle">4</div>
                <div class="step-label">Review</div>
            </div>
        </div>

        <div class="card" style="max-width:640px;margin:0 auto;">
            <!-- Step 1: Personal Information -->
            @if($step === 1)
                <div class="form-step">
                    <h3 style="font-size:18px;font-weight:700;margin-bottom:24px;display:flex;align-items:center;">
                        <i class="fas fa-user" style="margin-right:10px;color:var(--accent);"></i>Personal Information
                    </h3>
                    <div class="form-group" style="margin-bottom:20px;">
                        <label>APPLICANT FULL NAME * <span class="smart-input-meta"><i class="fas fa-magic"></i> SMART</span></label>
                        <input wire:model="applicant_name" type="text" placeholder="e.g., Okafor Chinedu">
                        @error('applicant_name')<span style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>PHONE NUMBER * <span class="smart-input-meta"><i class="fas fa-magic"></i> SMART</span></label>
                        <input wire:model="applicant_phone" type="text" placeholder="e.g., 0801234 5678">
                        @error('applicant_phone')<span style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</span>@enderror
                    </div>
                </div>
            @endif

            <!-- Step 2: Location Details -->
            @if($step === 2)
                <div class="form-step">
                    <h3 style="font-size:18px;font-weight:700;margin-bottom:24px;display:flex;align-items:center;">
                        <i class="fas fa-map-marker-alt" style="margin-right:10px;color:var(--accent);"></i>Location Details
                    </h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>TOWN *</label>
                            <input wire:model="town" type="text" placeholder="e.g., Abagana Town 1">
                            @error('town')<span style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</span>@enderror
                        </div>
                        @if($registrationType === 'single')
                            <div class="form-group">
                                <label>HOUSE NUMBER *</label>
                                <input wire:model="house_number" type="text" placeholder="e.g., 14A or Plot 5">
                                @error('house_number')<span style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</span>@enderror
                            </div>
                        @else
                            <div class="form-group" style="grid-column: 1/-1;">
                                <label style="display:flex; justify-content:space-between; align-items:center;">
                                    <span>LIST ALL HOUSE NUMBERS *</span>
                                    <button type="button" wire:click="addAddressRow" style="background:var(--accent-light);color:var(--accent);border:none;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:700;cursor:pointer;">
                                        <i class="fas fa-plus"></i> Add Row
                                    </button>
                                </label>
                                <div style="display:flex; flex-direction:column; gap:8px; margin-top:8px;">
                                    @foreach($bulkAddresses as $index => $row)
                                        <div style="display:flex; gap:8px; align-items:center;">
                                            <div style="flex:1;">
                                                <input wire:model="bulkAddresses.{{ $index }}.house_number" type="text" placeholder="House #{{ $index + 1 }} (e.g., 14B)">
                                            </div>
                                            @if(count($bulkAddresses) > 1)
                                                <button type="button" wire:click="removeAddressRow({{ $index }})" style="color:var(--danger); background:none; border:none; padding:8px; cursor:pointer;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                        @error('bulkAddresses.'.$index.'.house_number')<span style="color:var(--danger);font-size:10px;">{{ $message }}</span>@enderror
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="form-group" style="grid-column: 1/-1;">
                            <label>STREET *</label>
                            <select wire:model="street_id">
                                <option value="">-- Select a Registered Street --</option>
                                @foreach($streets as $street)
                                    <option value="{{ $street->id }}">{{ $street->name }} ({{ $street->town }})</option>
                                @endforeach
                            </select>
                            @error('street_id')<span style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</span>@enderror
                        </div>

                        <!-- Map Coordinates Selection (WOW Style) -->
                        <div class="form-group" style="grid-column: 1/-1;" x-data="{
                            map: null,
                            marker: null,
                            tracking: false,
                            captured: false,
                            initMap() {
                                let defaultLat = 6.1667;
                                let defaultLng = 7.0000;
                                this.map = L.map($refs.mapContainer, { zoomControl: false }).setView([defaultLat, defaultLng], 12);
                                
                                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                                    attribution: '© OpenStreetMap contributors'
                                }).addTo(this.map);

                                L.control.zoom({ position: 'bottomright' }).addTo(this.map);

                                if ($wire.latitude && $wire.longitude) {
                                    this.setMarker($wire.latitude, $wire.longitude);
                                    this.map.setView([$wire.latitude, $wire.longitude], 17);
                                    this.captured = true;
                                }

                                this.map.on('click', (e) => {
                                    this.setMarker(e.latlng.lat, e.latlng.lng);
                                    $wire.set('latitude', e.latlng.lat);
                                    $wire.set('longitude', e.latlng.lng);
                                    this.captured = true;
                                });
                            },
                            setMarker(lat, lng) {
                                if (this.marker) this.map.removeLayer(this.marker);
                                let customIcon = L.divIcon({
                                    className: 'gps-marker-icon',
                                    html: '<div style=\'width:20px;height:20px;background:var(--accent);border:3px solid #fff;border-radius:50%;box-shadow:0 0 15px rgba(0,0,0,0.3);position:relative;\'><div style=\'position:absolute;width:10px;height:10px;background:var(--accent);border-radius:50%;top:5px;left:5px;animation:ping 1.5s infinite;\'></div></div>',
                                    iconSize: [20, 20],
                                    iconAnchor: [10, 10]
                                });
                                this.marker = L.marker([lat, lng], { icon: customIcon }).addTo(this.map);
                            },
                            locateMe() {
                                this.tracking = true;
                                if (navigator.geolocation) {
                                    navigator.geolocation.getCurrentPosition((position) => {
                                        let lat = position.coords.latitude;
                                        let lng = position.coords.longitude;
                                        this.setMarker(lat, lng);
                                        this.map.setView([lat, lng], 17);
                                        $wire.set('latitude', lat);
                                        $wire.set('longitude', lng);
                                        this.captured = true;
                                        this.tracking = false;
                                    }, () => {
                                        alert('Geolocation failed.');
                                        this.tracking = false;
                                    }, { enableHighAccuracy: true });
                                } else {
                                    alert('Geolocation not supported.');
                                    this.tracking = false;
                                }
                            }
                        }" x-init="initMap()">
                            <label style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                <span style="font-weight:700;letter-spacing:0.5px;">GEOSPATIAL LOCATION DATA</span>
                                <div x-show="captured" style="font-size:10px;color:var(--success);font-weight:700;display:flex;align-items:center;gap:4px;">
                                    <i class="fas fa-check-circle"></i> COORDINATES CAPTURED
                                </div>
                            </label>
                            
                            <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius); padding:16px; margin-bottom:16px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                                    <div>
                                        <div style="font-size:9px;color:var(--text-secondary);font-weight:700;margin-bottom:4px;text-transform:uppercase;">Latitude</div>
                                        <div style="background:var(--bg-input);padding:8px 12px;border-radius:4px;font-family:'Space Mono',monospace;font-size:13px;border:1px solid var(--border);color:var(--accent);">
                                            {{ $latitude ?: 'Not Captured' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div style="font-size:9px;color:var(--text-secondary);font-weight:700;margin-bottom:4px;text-transform:uppercase;">Longitude</div>
                                        <div style="background:var(--bg-input);padding:8px 12px;border-radius:4px;font-family:'Space Mono',monospace;font-size:13px;border:1px solid var(--border);color:var(--accent);">
                                            {{ $longitude ?: 'Not Captured' }}
                                        </div>
                                    </div>
                                </div>

                                <div wire:ignore>
                                    <div x-ref="mapContainer" style="height:320px; border-radius:6px; border:1px solid var(--border); position:relative; overflow:hidden;">
                                        <div style="position:absolute;top:10px;right:10px;z-index:1000;display:flex;flex-direction:column;gap:8px;">
                                            <button type="button" @click="locateMe()" :disabled="tracking"
                                                style="width:40px;height:40px;background:#fff;border:none;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.15);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--accent);transition:all 0.2s;"
                                                :style="tracking ? 'opacity:0.5;cursor:wait;' : ''"
                                                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                                                <i class="fas fa-crosshairs" :class="tracking ? 'fa-spin' : ''"></i>
                                            </button>
                                        </div>
                                        <div style="position:absolute;bottom:10px;left:10px;z-index:1000;background:rgba(255,255,255,0.9);padding:6px 12px;border-radius:20px;font-size:10px;font-weight:600;box-shadow:0 2px 8px rgba(0,0,0,0.1);backdrop-filter:blur(4px);">
                                            <i class="fas fa-info-circle" style="color:var(--accent);margin-right:4px;"></i> Use map or GPS button
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            @endif

            <!-- Step 3: Owner Details & Fees -->
            @if($step === 3)
                <div class="form-step">
                    <h3 style="font-size:18px;font-weight:700;margin-bottom:24px;display:flex;align-items:center;">
                        <i class="fas fa-file-invoice-dollar" style="margin-right:10px;color:var(--accent);"></i>Owner Information & Fees
                    </h3>
                    <div class="form-group" style="margin-bottom:20px;">
                        <label>OWNER FULL NAME (IF DIFFERENT)</label>
                        <input wire:model="owner_name" type="text" placeholder="Enter owner name">
                        @error('owner_name')<span style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:24px;">
                        <label>OWNER PHONE</label>
                        <input wire:model="owner_phone" type="text" placeholder="Enter owner phone">
                        @error('owner_phone')<span style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</span>@enderror
                    </div>

                    <div style="background:var(--accent-gold-light);padding:20px;border-radius:var(--radius);border-left:4px solid var(--accent-gold);">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <span style="font-size:12px;font-weight:700;color:var(--accent-gold);text-transform:uppercase;">Registration Fee</span>
                                <h4 style="font-size:24px;font-family:'Space Mono',monospace;margin-top:2px;">₦2,000</h4>
                            </div>
                            <i class="fas fa-receipt" style="font-size:32px;color:var(--accent-gold);opacity:0.3;"></i>
                        </div>
                        <p style="font-size:12px;margin-top:10px;color:var(--text-secondary);">Fee covers official verification and digital certificate generation.</p>
                    </div>

                    <div style="margin-top: 24px;">
                        <h4 style="font-size:14px;font-weight:700;margin-bottom:8px;color:var(--text-secondary);text-transform:uppercase;">Select Payment Method</h4>
                        <div class="payment-option-grid">
                            <label class="payment-option-label">
                                <input type="radio" wire:model="payment_method" value="paystack">
                                <div class="payment-option-card">
                                    <i class="fas fa-credit-card" style="color:#09A5DB;"></i>
                                    <span>Paystack</span>
                                </div>
                            </label>
                            <label class="payment-option-label">
                                <input type="radio" wire:model="payment_method" value="flutterwave">
                                <div class="payment-option-card">
                                    <i class="fas fa-wallet" style="color:#f5a623;"></i>
                                    <span>Flutterwave</span>
                                </div>
                            </label>
                            <label class="payment-option-label">
                                <input type="radio" wire:model="payment_method" value="bank_transfer">
                                <div class="payment-option-card">
                                    <i class="fas fa-university"></i>
                                    <span>Bank Transfer</span>
                                </div>
                            </label>
                        </div>
                        @error('payment_method')<span style="color:var(--danger);font-size:12px;">{{ $message }}</span>@enderror
                    </div>
                </div>
            @endif

            <!-- Step 4: Review -->
            @if($step === 4)
                <div class="form-step">
                    <h3 style="font-size:18px;font-weight:700;margin-bottom:24px;">
                        <i class="fas fa-check-double" style="margin-right:10px;color:var(--accent);"></i>Review Registration
                    </h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;background:var(--bg-input);padding:20px;border-radius:var(--radius);font-size:14px;">
                        <div>
                            <span style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;">Applicant</span>
                            <p style="font-weight:600;margin-top:2px;">{{ $applicant_name }}</p>
                            <p style="font-size:12px;color:var(--text-secondary);">{{ $applicant_phone }}</p>
                        </div>
                        <div>
                            <span style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;">Address</span>
                            @if($registrationType === 'single')
                                <p style="font-weight:600;margin-top:2px;">{{ $house_number }}, {{ $streets->find($street_id)?->name ?? 'N/A' }}</p>
                            @else
                                <p style="font-weight:600;margin-top:2px;">{{ count($bulkAddresses) }} Properties on {{ $streets->find($street_id)?->name ?? 'N/A' }}</p>
                                <div style="font-size:11px; color:var(--text-secondary); margin-top:4px; max-height:60px; overflow-y:auto;">
                                    Numbers: {{ implode(', ', array_column($bulkAddresses, 'house_number')) }}
                                </div>
                            @endif
                            <p style="font-size:12px;color:var(--text-secondary);">{{ $town }}</p>
                        </div>
                        <div style="grid-column:1/-1;border-top:1px solid var(--border);padding-top:15px;margin-top:15px; display:flex; justify-content:space-between; flex-wrap:wrap; gap:16px;">
                            <div>
                                <span style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;">Registration Type</span>
                                <p style="font-weight:600;margin-top:2px;">{{ ucfirst($registrationType) }} Registration</p>
                            </div>
                            <div>
                                <span style="font-size:10px;text-transform:uppercase;color:var(--text-secondary);font-weight:700;">Payment Method</span>
                                <p style="font-weight:600;margin-top:2px; text-transform:capitalize;">{{ str_replace('_', ' ', $payment_method) }}</p>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:20px;display:flex;align-items:center;gap:10px;padding:12px;background:var(--accent-light);border-radius:var(--radius-sm);color:var(--accent);font-size:12px;">
                        <i class="fas fa-info-circle"></i>
                        <span>By submitting, you agree to official verification of this property.</span>
                    </div>
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div style="margin-top:32px;display:flex;justify-content:space-between;padding-top:24px;border-top:1px solid var(--border);">
                @if($step > 1)
                    <button wire:click="prevStep" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                @else
                    <div></div>
                @endif

                @if($step < 4)
                    <button wire:click="nextStep" class="btn btn-primary" style="padding-inline:36px;">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                @else
                    <button wire:click="submit" class="btn btn-primary" style="padding-inline:40px;" wire:loading.attr="disabled">
                        <span wire:loading.remove>Submit Registration <i class="fas fa-paper-plane"></i></span>
                        <span wire:loading><i class="fas fa-spinner fa-spin"></i> Submitting...</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    <!-- Tracking Section -->
    <div class="tracking-footer">
        <div class="track-card">
            <h4><i class="fas fa-search" style="color:var(--accent-gold);"></i> Track Your Request</h4>
            <div class="track-input-group">
                <input wire:model="search_code" type="text" placeholder="Enter reference code (e.g., REG-EF234A)">
                <button wire:click="track" class="track-btn">
                    <i class="fas fa-search"></i> Track
                </button>
            </div>
        </div>
    </div>
</div>
